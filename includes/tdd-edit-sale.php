<?php if ( !defined('ABSPATH') ) exit;
    global $wpdb;
    $table = nbs_tdd_get_sales_table(); 
    
    if(isset($_POST['submit'])) {
        
        $_POST = array_map( 'stripslashes_deep', $_POST );        
        
        if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
            exit;
        }        
            
        $activeVal = nbs_tdd_get_active_value($_POST['active']);
        
        $updateValues = array(
            'product_id' => $_POST['product_id'],    
            'item_name' => $_POST['item_name'],    
            'price' => $_POST['price'],        
            'payment_code' => $_POST['payment_code'],
            'active' => $activeVal
        );
        
        $whereValues = array(
            'id' => $_POST['saleID']
        );        
        
        nbs_tdd_edit_item($table, $updateValues, $whereValues, 'tdd-sales.php', 5);
    }        
    
    if(isset($_GET["id"]) && is_numeric($_GET["id"]))
        $saleID = $_GET["id"];
    else 
        wp_redirect(nbs_tdd_go_to_page('tiny-digital-downloads/tdd.php'));    
    
    $sql = "SELECT * FROM $table WHERE id='".$saleID."'";
    $result = $wpdb->get_row($sql, ARRAY_A);

echo '<div class="nbs-plugin-wrap">';

echo '<h2>'.__("Edit Sale",  "ninebit").': #'.$result["id"].'</h2>';

echo '<form id="tddForm" action="'.nbs_tdd_go_to_page("tdd-edit-sale.php", false).'" method="post">';
    
echo '<input type="hidden" name="saleID" id="saleID" value="'.$result["id"].'" class="required" />
            
        <h3>'.__("Product ID", "ninebit").'</h3>
        <input type="text"  name="product_id" id="productID" value="'.$result["product_id"].'" class="required text-medium" />            
        
        <h3>'.__("Item Name", "ninebit").'</h3>
        <input type="text"  name="item_name" id="itemName" value="'.$result["item_name"].'" class="required text-medium" />
        
        <h3>'.__("Price", "ninebit").'</h3>
        <input type="text"  name="price" id="price" value="'.$result["price"].'" class="required text-medium" />

        <h3>'.__("Payment Code", "ninebit").'</h3>
        <input type="text"  name="payment_code" id="paymentCode" value="'.$result["payment_code"].'" class="required text-medium" />        
        
        <h3>'.__("Active", "ninebit").'</h3>
        '.nbs_tdd_get_active_element($result["active"]).'
        
        
        <div class="clearout"></div>'
                    
        .nbs_tdd_get_submit_button_set();
        wp_nonce_field("externalAction","externalVerify");    
        
    echo '</form>
    </div>';

?>