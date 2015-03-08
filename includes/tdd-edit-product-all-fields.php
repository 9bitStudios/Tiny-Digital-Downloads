<?php if ( !defined('ABSPATH') ) exit;
    global $wpdb;
    $table = nbs_tdd_get_products_table(); 
    
    if(isset($_POST['submit'])) {
        
        $_POST = array_map( 'stripslashes_deep', $_POST );        
        
        if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
            exit;
        }        
        
        $updateValues = array(
            'id' => $_POST['productID'],
            'timestamp' => date("Y-m-d H:i:s"),
            'product_name' => $_POST['product_name'],        
            'download_file' => $_POST['download_file'],
            'sales' => $_POST['sales'],
            'earnings' => $_POST['earnings'],
        );
        
        $whereValues = array(
            'id' => $_POST['originalID']
        );        
        
        nbs_tdd_edit_item($table, $updateValues, $whereValues, 'tdd-products.php', 3);
    }        
    
    if(isset($_GET["id"]) && is_numeric($_GET["id"]))
        $productID = $_GET["id"];
    else 
        wp_redirect(nbs_tdd_go_to_page('tiny-digital-downloads/tdd.php'));    
    
    $sql = "SELECT * FROM $table  WHERE id='".$productID."'";
    $result = $wpdb->get_row($sql, ARRAY_A);

echo '<div class="nbs-plugin-wrap">';

echo '<h2>'.__("Edit Product All Fields (Only Use For Testing and Error Cases)",  "ninebit").': '.$result["product_name"].'</h2>';

echo '<form id="tddForm" action="'.nbs_tdd_go_to_page("tdd-edit-product-all-fields.php", false).'" method="post">';
    
echo '<input type="hidden" name="originalID" id="originalID" value="'.$result["id"].'" class="required" />
        
        <h3>'.__("ID", "ninebit").'</h3>
        <input type="text" name="productID" id="productID" value="'.$result["id"].'" class="required text-medium" />        
        
        <h3>'.__("Product Name", "ninebit").'</h3>
        <input type="text"  name="product_name" id="productName" value="'.$result["product_name"].'" class="required text-medium" />
            
        <h3>'.__("File", "ninebit").'</h3>
        <input type="text"  name="download_file" id="downloadFile" value="'.$result["download_file"].'" class="required text-large" />

        <h3>'.__("Sales", "ninebit").'</h3>
        <input type="text"  name="sales" id="sales" value="'.$result["sales"].'" class="required text-medium" />

        <h3>'.__("Earnings", "ninebit").'</h3>
        <input type="text"  name="earnings" id="earnings" value="'.$result["earnings"].'" class="required text-medium" />
        
        
        <div class="clearout"></div>'
                    
        .nbs_tdd_get_submit_button_set();
        wp_nonce_field("externalAction","externalVerify");    
        
    echo '</form>
    </div>';

?>