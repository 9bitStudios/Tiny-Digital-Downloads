<?php if ( !defined('ABSPATH') ) exit;

    /**
     * Add Item
     */    

    function nbs_tdd_add_item($table, $insertValues, $pageRedirect = null, $message = null) {
        
        global $wpdb;
        $wpdbAttempt = $wpdb->insert($table, $insertValues);
        
        if(!$pageRedirect)
            $pageRedirect = 'tiny-digital-downloads/tdd.php';
        nbs_tdd_standard_process_redirect($wpdbAttempt, $pageRedirect, $message);
    }
    
    /**
     * Edit Item
     */        
    
    function nbs_tdd_edit_item($table, $updateValues, $whereValues, $pageRedirect = null, $message = null) {
        global $wpdb;
        $wpdbAttempt = $wpdb->update($table, $updateValues, $whereValues);
        
        if(!$pageRedirect)
            $pageRedirect = 'tiny-digital-downloads/tdd.php';        
        nbs_tdd_standard_process_redirect($wpdbAttempt, $pageRedirect, $message);
    }    
    
    /**
     * Delete Item
     */    
    
    function nbs_tdd_delete_item($table, $id, $pageRedirect = null, $message = null) {
        global $wpdb;
        $wpdbAttempt = $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id=%d", $id));
        
        if(!$pageRedirect)
            $pageRedirect = 'tiny-digital-downloads/tdd.php';
            
        nbs_tdd_standard_process_redirect($wpdbAttempt, $pageRedirect , $message);
    }
    
    /**
     * Get Message
     */        
    
    function nbs_tdd_get_message($id) {
    
        $message  = array(
            "0" =>     array("message" => __('There was an error in the process', 'ninebit')),
            "1" =>     array("message" => __('Success', 'ninebit')),
            "2" =>     array("message" => __('Product added successfully', 'ninebit')),
            "3" =>     array("message" => __('Product updated successfully', 'ninebit')),
            "4" =>     array("message" => __('Product deleted successfully', 'ninebit')),
            "5" =>     array("message" => __('Sale updated successfully', 'ninebit')),             
            "6" =>     array("message" => __('Sale deleted successfully', 'ninebit')),              
            "7" => array("message" => __('Settings saved', 'ninebit')),
            "8" => array("message" => __('Settings reset', 'ninebit')), 
        );
        return $message[$id]['message'];
    }    
    
    
    function nbs_tdd_display_message() {

        if (isset($_REQUEST['success'])) {
            echo '<div id="message" class="fade nbs-success-message" style="margin-left:0px;">
                    <strong>'.nbs_tdd_get_message($_GET['message']).'</strong>
                </div>';
        }
        if (isset($_REQUEST['error'])) { 
            echo '<div id="message" class="fade nbs-error-message" style="margin-left:0px;">
                    <strong>'.nbs_tdd_get_message($_GET['message']).'</strong>
                </div>';
        }
    
    }
    
    /**
     * Get Products Table
     */        
    
    function nbs_tdd_get_products_table() {    
        global $wpdb;
        $table = $wpdb->prefix.'tdd_products';
        return $table;
    }
    
    /**
     * Get Sales Table
     */        
    
    function nbs_tdd_get_sales_table() {    
        global $wpdb;
        $table = $wpdb->prefix.'tdd_sales';
        return $table;
    }    
    
    /**
     * Go to Page in Admin Area
     */        
    
    function nbs_tdd_go_to_page($page, $header = true) {    
    
        if(!$header)
            $ext = '&noheader=true';
        else
            $ext = '';
    
        if(!$page)
            $page = 'tdd-home.php';
        
        $url = admin_url().'admin.php?page='.$page.$ext;
        return $url;
        
    }    

    /**
     * Get Product Rows
     */        
    
    function nbs_tdd_get_products_rows() {
                
        global $wpdb;
        $rows = '';
        $table = nbs_tdd_get_products_table();
        $sql = "SELECT * FROM $table";
        $results = $wpdb->get_results($sql, ARRAY_A);
        
        foreach ($results as $result) { 
            
            $editLink = add_query_arg('id', $result['id'], nbs_tdd_go_to_page("tdd-edit-product.php"));    
            $deleteLink = add_query_arg('id', $result['id'], nbs_tdd_go_to_page("tdd-delete-product.php"));
            
            $rows .= '
            <tr>
                <td>'.$result["id"].'</td>
                <td>'.$result["product_name"].'</td>
                <td>'.$result["download_file"].' - <small><a href="'.$result["download_file"].'">'.__('Test Download', 'ninebit').'</a></small></td>
                <td>'.$result["sales"].'</td>
                <td>'.NBS_WPPLUGIN_TDD_CURRENCY_SYMBOL.$result["earnings"].'</td>                
                <td><a href="'.$editLink.'">'.__('Edit', 'ninebit').'</a>&nbsp;|&nbsp;
                    <a href="'.$deleteLink.'">'.__('Delete', 'ninebit').'</a>
                </td>
            </tr>
            ';
        }
        
        return $rows;
    } 

    /**
     * Get Sales Rows
     */        
    
    function nbs_tdd_get_sales_rows() {
        
        global $wpdb;
        $rows = '';
        $table = nbs_tdd_get_sales_table();
        $sql = "SELECT * FROM $table";
        $results = $wpdb->get_results($sql, ARRAY_A);
        
        foreach ($results as $result) { 
        
            $editLink = add_query_arg('id', $result['id'], nbs_tdd_go_to_page("tdd-edit-sale.php"));
            $deleteLink = add_query_arg('id', $result['id'], nbs_tdd_go_to_page("tdd-delete-sale.php"));
        
        
            $rows .= '
            <tr>
                <td>'.$result["id"].'</td>            
                <td>'.$result["product_id"].'</td>
                <td>'.$result["item_name"].'</td>                
                <td>'.$result["price"].'</td>
                <td>'.$result["date"].'</td>
                <td>'.$result["payment_code"].'</td>
                <td>'.nbs_tdd_get_active_element($result["active"]).'</td>
                <td><a href="'.$editLink.'">'.__('Edit', 'ninebit').'</a>&nbsp;|&nbsp;<a href="'.$deleteLink.'">'.__('Delete', 'ninebit').'</a></td>
            </tr>
            ';
        }
        
        return $rows;
    }

    /**
     * Get Active Checkbox
     */        
    
    function nbs_tdd_get_active_element($val) {
        
        if($val == 1)
            return '<input type="checkbox" name="active" id="active" value="1" checked>';
        else
            return '<input type="checkbox" name="active" id="active" value="1">';
        
    } 
    
    /**
     * Get Active Value (used for checking when editing items)
     */        
    
    function nbs_tdd_get_active_value($val) {
        
        if($val == 1)
            return 1;
        else
            return 0;
        
    }     
    
    
    /**
     * Send Email
     */        
    
    function nbs_tdd_send_mail($payer_email, $code, $item_name = '') {
    
        $email = NBS_WPPLUGIN_TDD_REPLY_EMAIL;    
            
        $subject = __('Thank you for your purchase', 'ninebit');
        $body = __('Thank you for your purchasing '.$item_name.'. Please use the following link below to download your file. If you have any problems with the download please contact support.', 'ninebit').'<br /><br />';
        $body .= __('Purchase Code', 'ninebit').': '.$code.'<br /><br />'.        
                '<a href="'.site_url().'/digital-downloads/download?code='.$code.'">'.__("Download File", "ninebit").'</a> | <a href="'.NBS_WPPLUGIN_TDD_CONTACT_URL.'">Contact Support</a> (if needed)';
        
        $headers = 'From: <'.$email.'>' . "\r\n" . 'Reply-To: ' .$email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $sentMail = mail($payer_email, $subject, $body, $headers);
        
        if($sentMail) {
            return true;
        }
        else {
            $message = __('An error occured while sending email', 'ninebit');
            nbs_tdd_log_error($message);
            return false;
        } 
        
            
    }    
    
    /**
     * Get Downloader IP
     */        
    
    function nbs_tdd_get_ip() {
    
        $ip = '';
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "Unknown";
            
        return $ip;
    }     
    
    /**
     * Redirect
     */        
    
    function nbs_tdd_standard_process_redirect($wpdbAttempt, $page = null, $message = 1) {
        
        if(!$page)
            $page = 'tiny-digital-downloads/tdd.php';
        
        if($wpdbAttempt === FALSE ) {
            $queryParams = array('error' => 'true', 'message' => 0);
            $messageLink = add_query_arg($queryParams, nbs_tdd_go_to_page($page));
        }
        else {
            $queryParams = array('success' => 'true', 'message' => $message);
            $messageLink = add_query_arg($queryParams, nbs_tdd_go_to_page($page)); 
        }
        
        wp_redirect($messageLink);        
        exit;
    }    
    
    function nbs_plugin_tdd_error_redirect($message = null) {
    
        if(get_option('nbs_tdd_error_page_url')) {
            nbs_tdd_log_error($message);
            $errorPage = get_option('nbs_tdd_error_page_url');
            wp_redirect($errorPage);
            exit;
        }
        else {
            echo '<h4>'.__('An error has occured in the process and the request could not be completed. Please contact support', 'ninebit').'</h4>';
            exit;
        }    
    }
    
    /**
     * Get Sale Data (from payment code... used for download log file)
     */        
    
    function nbs_tdd_get_sale_data($code) {
    
        global $wpdb;
        $sales_table_name = nbs_tdd_get_sales_table();
        $sql = $wpdb->prepare("SELECT * FROM $sales_table_name WHERE payment_code='".$code."'");
        $row = $wpdb->get_row($sql, ARRAY_A);    
        return $row;
    }    
    
    /**
     * Get Product Row from Payment Code
     */        
    
    function nbs_tdd_get_product_from_payment_code($code) {
    
        global $wpdb;
        $sales_table_name = nbs_tdd_get_sales_table();
        $products_table_name = nbs_tdd_get_products_table();
        
        // get the product id from the sales table
        $sql = $wpdb->prepare("SELECT * FROM $sales_table_name WHERE payment_code='".$code."'");
        $row = $wpdb->get_row($sql, ARRAY_A);    
        $id = $row['product_id'];
        
        // is this sale active? if so continue, if not bail
        if($row['active'] != 1)
            return false;
        
        // get the product of that id
        $sql = $wpdb->prepare("SELECT * FROM $products_table_name WHERE id='".$id."'");
        $row = $wpdb->get_row($sql, ARRAY_A);    
        
        return $row;
    }
    
    /**
     * Get Product Row from ID
     */        
    
    function nbs_tdd_get_product_from_id($id) {
    
        global $wpdb;
        $products_table_name = nbs_tdd_get_products_table();
        
        // get the row of the product with the download code
        $sql = $wpdb->prepare("SELECT * FROM $products_table_name WHERE id='".$id."'");
        $row = $wpdb->get_row($sql, ARRAY_A);    
        return $row;
    }

    /**
     * Update Product Sales Stats
     */        
    
    function nbs_tdd_update_product_sales_stats($id, $sales, $earnings) {
    
        global $wpdb;
        $products_table_name = nbs_tdd_get_products_table();
        
        $updateValues = array(
            'sales' => $sales,
            'earnings' => $earnings,        
        );
        $whereValues = array(
            'id' => $id
        );        
        
        $wpdbAttempt = $wpdb->update($products_table_name, $updateValues, $whereValues);
        
        if(!$wpdbAttempt) {
        
            $message = __('Failed to update sales data in products table. Please investigate and update manually', 'ninebit');
            nbs_tdd_log_error($message);
        }
        
    }    
    
    /**
     * Add Sale to Sales Table
     */    

    function nbs_tdd_add_sale($product_id, $item_name, $price, $date, $payment_code, $active) {
    
        global $wpdb;
    
        $table = nbs_tdd_get_sales_table();
    
        $insertValues = array(
            'product_id' => $product_id,
            'item_name' => $item_name,            
            'price' => $price,
            'date' =>  $date,
            'payment_code' => $payment_code,
            'active' => $active,
        );        
        $wpdbAttempt = $wpdb->insert($table, $insertValues);
        
        if(!$wpdbAttempt) {
            $message = __('Failed add sale record to database. Check online product for sales info.','ninebit');
            nbs_tdd_log_error($message);
        }
    }    
    
    /**
     * Get Total Sales
     */        
    
    function nbs_tdd_get_total_sales() {
    
        global $wpdb;
        $sales_table = nbs_tdd_get_sales_table();
        $sql = "SELECT * FROM $sales_table";
        $sum = $wpdb->get_results($sql, ARRAY_A);
        $price = 0.00;
        
        foreach($sum as $result) {
            $price += $result['price'];
        }
        $price = number_format($price, 2, '.', ',');
        
        return $price;
    }    
    
    /**
     * Thank You Page
     */        
    
    function nbs_tdd_thank_you_redirect() {
    
        if(get_option('nbs_tdd_thank_you_page_url')) {
            $thanksPage = get_option('nbs_tdd_thank_you_page_url');
            wp_redirect($thanksPage);
            exit;
        }
        else {
            echo '<h4>'.__('Thank you', 'ninebit').'</h4>';
            exit;
        }    
    }    
    
    /**
     * Log Error
     */            
    
    function nbs_tdd_log_error($message) {
    
        if(!$message)
            $message = __("Unknown Error", "ninebit");
            
        file_put_contents(WP_PLUGIN_DIR.'/'.NBS_WPPLUGIN_TDD_FOLDER.'/logs/error.txt', date('D M d, Y G:i a').": ".$message."\r\n", FILE_APPEND);
    }
    
    /**
     * Log Download
     */            
        
    
    function nbs_tdd_log_download($message) {
    
        if($message) {
            file_put_contents(WP_PLUGIN_DIR.'/'.NBS_WPPLUGIN_TDD_FOLDER.'/logs/downloads.txt', date('D M d, Y G:i a').": ".$message."\r\n", FILE_APPEND);
        }
    }    
    
    /**
     * Log Test
     */            
        
    
    function nbs_tdd_log_test($message) {
    
        if(NBS_WPPLUGIN_TDD_TEST_MODE) {
            if($message) {
                file_put_contents(WP_PLUGIN_DIR.'/'.NBS_WPPLUGIN_TDD_FOLDER.'/logs/test.txt', date('D M d, Y G:i a').": ".$message."\r\n", FILE_APPEND);
            }
        }
    
    }    
    
    /**
     * Submit Buttons
     */            
        
    
    function nbs_tdd_get_submit_button_set() {
        
        $cancelPage = nbs_tdd_go_to_page('tiny-digital-downloads/tdd.php');
        
        $buttons = '<input type="submit" class="button-primary" name="submit" value="'.__("Submit", "ninebit").'" /> 
        <a href="'.$cancelPage.'" class="button-primary">'.__("Cancel", "ninebit").'</a>';
        
        return $buttons;
    }    
    
    /**
     * Confirm Buttons
     */            
        
    
    function nbs_tdd_get_confirm_button_set() {
        
        $cancelPage = nbs_tdd_go_to_page('tiny-digital-downloads/tdd.php');
        
        $buttons = '<input type="submit" class="button-primary" name="submit" value="'.__("Yes", "ninebit").'" /> 
        <a href="'.$cancelPage.'" class="button-primary">'.__("No", "ninebit").'</a>';
        
        return $buttons;
    }    

    /**
     * Flush Tables
     */            
        
    
    function nbs_tdd_flush_tables() {
        
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."tdd_products" );
        $wpdb->query( "DROP TABLE IF EXISTS ".$wpdb->prefix."tdd_sales" );
    }
    
    /**
     * Flush Options
     */            
    
    function nbs_tdd_flush_options() {
        delete_option('nbs_tdd_generated_page_ids');
        delete_option('nbs_tdd_download_page_id');
        delete_option('nbs_tdd_paypal_ipn_page_id');
        delete_option('nbs_tdd_error_page_url');
        delete_option('nbs_tdd_thank_you_page_url');
        delete_option('nbs_tdd_sender_email');    
    }
    
    /**
     * Flush Pages
     */        
    
    function nbs_tdd_flush_pages() {
    
        if(get_option('nbs_tdd_generated_page_ids'))
            $ids = get_option('nbs_tdd_generated_page_ids');

        $postIDs = explode(",", $ids);
        
        // delete custom created pages
        foreach($postIDs as $postID) {
            wp_delete_post($postID, true);    
        }    
    }    

?>