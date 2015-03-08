<?php
/*
Template Name: TDD Download Item
*/
?>
<?php

    global $wpdb;

    if(!isset($_GET["code"]) || !ctype_alnum($_GET["code"])) {

        nbs_plugin_tdd_error_redirect(__('Accessed download URL when download code parameter was not set', 'ninebit').' - '.nbs_tdd_get_ip());
    }
    else {
        
        $paymentCode = trim($_GET["code"]);
        
        $sale = nbs_tdd_get_sale_data($paymentCode); 
        $saleID = $sale['id'];
        $product = nbs_tdd_get_product_from_payment_code($paymentCode); 
        
        if($product) {
        
            $downloadFile = $product['download_file'];
            /*
            $ctype    = "application/zip";
            $file_name = basename($downloadFile);                
            header("Content-Type: ".$ctype."");                
            header('Content-Type: application/octet-stream');
            header("Content-Description: File Transfer"); 
            header("Content-disposition: attachment; filename=\"".$file_name."\";"); 
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: ".@filesize($downloadFile));
            readfile($downloadFile);    
            */
            nbs_tdd_log_download(__('Sale ID','ninebit').': '.$saleID.' '.__('Requester','ninebit').': '.nbs_tdd_get_ip());
            header("Robots: none");
            header("Location: ".$downloadFile."");
            
        }
        else {
            nbs_plugin_tdd_error_redirect(__('Accessed download URL when using invalid or inactive payment code parameter', 'ninebit').': '.$paymentCode.' - '.nbs_tdd_get_ip());
        }
    } 
?>