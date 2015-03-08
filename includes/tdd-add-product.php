<?php if ( !defined('ABSPATH') ) exit;
    global $wpdb;
    
    if(isset($_POST['submit'])) {
        $_POST = array_map( 'stripslashes_deep', $_POST );
            
        if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
            exit;
        }        
        $insertValues = array(
            'timestamp' =>  date("Y-m-d H:i:s"),
            'product_name' => $_POST['product_name'],
            'download_file' => $_POST['download_file'],
        );
        
        nbs_tdd_add_item(nbs_tdd_get_products_table(), $insertValues, 'tdd-products.php', 2);    
    }

    echo '
    <div class="nbs-plugin-wrap">
    '; 
    echo '<h2>'.__("Add New Product", "ninebit").'</h2>
    
        <form id="tddForm" action="'.nbs_tdd_go_to_page("tdd-add-product.php", false).'" method="post">
            
            <h3>'.__("Product Name", "ninebit").'</h3>
            <input type="text"  name="product_name" id="productName" value="" class="required text-medium" />

            <h3>'.__("File", "ninebit").'</h3>
            <input type="text" name="download_file" id="downloadFile" value="" class="required text-large" />
            
            <div class="clearout"></div>'
                        
            .nbs_tdd_get_submit_button_set();
            wp_nonce_field("externalAction","externalVerify");    
        
    echo '</form>
    </div>';

?>    