<?php
/*
Template Name: TDD PayPal IPN
*/
?>
<?php

    global $wpdb;
    global $post;

// STEP 1: Read POST data
 
// reading posted data from directly from $_POST causes serialization 
// issues with array data in POST
// reading raw POST data from input stream instead. 


    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    
    foreach ($raw_post_array as $keyval) {
        $keyval = explode ('=', $keyval);
        if (count($keyval) == 2)
            $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
    
// read the post from PayPal system and add 'cmd'

    $req = 'cmd=_notify-validate';
    if(function_exists('get_magic_quotes_gpc')) {
       $get_magic_quotes_exists = true;
    } 
    foreach ($myPost as $key => $value) {        
       if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
            $value = urlencode(stripslashes($value)); 
       } else {
            $value = urlencode($value);
       }
       $req .= "&$key=$value";
    }
 
 
// STEP 2: Post IPN data back to paypal to validate
 
    if(NBS_WPPLUGIN_TDD_TEST_MODE)
        $ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
    else
        $ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
        
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
 
    // In wamp like environments that do not come bundled with root authority certificates,
    // please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
    // of the certificate as shown below.
    // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');

    if( !($res = curl_exec($ch)) ) {
        // error_log("Got " . curl_error($ch) . " when processing IPN data");
        $errorMessage = __('A curl error occured when processing IPN data','ninebit');        
        nbs_tdd_log_error($errorMessage);
        curl_close($ch);
        exit;
    }
    curl_close($ch);
 
 
// STEP 3: Inspect IPN validation result and act accordingly
 
    if (strcmp ($res, "VERIFIED") == 0) {
        // check whether the payment_status is Completed
        // check that txn_id has not been previously processed
        // check that receiver_email is your Primary PayPal email
        // check that payment_amount/payment_currency are correct
        // process payment
     
        // assign posted variables to local variables
        $item_name = $_POST['item_name'];
        $item_number = $_POST['item_number'];
        $payment_amount = $_POST['mc_gross'];
        $payment_date = $_POST['payment_date'];
        $receiver_email = $_POST['receiver_email'];
        $payer_email = $_POST['payer_email'];
        $download_id = $_POST['custom'];

        // get the product row from the download code set in PayPal
        $product = nbs_tdd_get_product_from_id($download_id);
        
        if($product) {
        
            // log sale in custom table 
            $payment_timestamp = strtotime($payment_date);
            $payment_code = md5(uniqid());
            nbs_tdd_add_sale($product['id'], $item_name, $payment_amount, date("Y-m-d H:i:s", $payment_timestamp), $payment_code, 1);
            
            // update total sales and earnings in product table
            $updatedSales = $product['sales'] + 1;
            $updatedEarnings = $product['earnings'] + $payment_amount;
            nbs_tdd_update_product_sales_stats($product['id'], $updatedSales, $updatedEarnings);
            
            // send mail to payer email with download code
            nbs_tdd_send_mail($payer_email, $payment_code, $item_name);
        
            // redirect to thank you page
            nbs_tdd_thank_you_redirect();        
        
        }
        else {
            $errorMessage = __('IPN was received but product ID was invalid or non-existent in application. Contact buyer to resolve.','ninebit');
            nbs_plugin_tdd_error_redirect($errorMessage);
        }
        
        
    } else if (strcmp ($res, "INVALID") == 0) {
        
        $errorMessage = __('IPN was flagged as invalid by PayPal','ninebit');
        nbs_plugin_tdd_error_redirect($errorMessage);
        
    }
    
?>