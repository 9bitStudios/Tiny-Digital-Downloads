<?php if ( !defined('ABSPATH') ) exit;
    global $wpdb;
    $table = nbs_tdd_get_sales_table(); 
    
    if(isset($_POST['submit'])) {
        
        $_POST = array_map( 'stripslashes_deep', $_POST );        
        
        if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
            exit;
        }        
        
        $insertValues = array(
            'product_id' => $_POST['product_id'],    
            'item_name' => $_POST['item_name'],    
            'price' => $_POST['price'],
            'date' =>  date("Y-m-d H:i:s"),            
            'payment_code' => md5(uniqid()),
            'active' => 0
        );
        
        $whereValues = array(
            'id' => $_POST['saleID']
        );        
        
        nbs_tdd_add_item($table, $insertValues, 'tdd-sales.php', 5);
    }        
    

echo '<div class="nbs-plugin-wrap">';

echo '<h2>'.__("Add Sale (Only Use For Testing and Error Cases)",  "ninebit").'</h2>';

echo '<form id="tddForm" action="'.nbs_tdd_go_to_page("tdd-add-sale.php", false).'" method="post">
            
        <h3>'.__("Product ID", "ninebit").'</h3>
        <input type="text"  name="product_id" id="productID" value="" class="required text-medium" />            
        
        <h3>'.__("Item Name", "ninebit").'</h3>
        <input type="text"  name="item_name" id="itemName" value="" class="required text-medium" />
        
        <h3>'.__("Price", "ninebit").'</h3>
        <input type="text"  name="price" id="price" value="" class="required text-medium" />

        <div class="clearout"></div>'
                    
        .nbs_tdd_get_submit_button_set();
        wp_nonce_field("externalAction","externalVerify");    
        
    echo '</form>
    </div>';

?>