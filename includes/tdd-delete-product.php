<?php if ( !defined('ABSPATH') ) exit;
    global $wpdb;
    $table = nbs_tdd_get_products_table(); 

    if(isset($_POST['submit'])) {
        
        if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
            exit;
        }            
        nbs_tdd_delete_item($table, $_POST['productID'], 'tdd-products.php', 4);    
    }    
    
    if(isset($_GET["id"]) && is_numeric($_GET["id"]))
        $productID = $_GET["id"];    
    else 
        wp_redirect(nbs_tdd_go_to_page('tiny-digital-downloads/tdd.php'));    
    
    
    $sql = "SELECT * FROM $table WHERE id='".$productID."'";
    $result = $wpdb->get_row($sql, ARRAY_A);

echo '<div class="nbs-plugin-wrap">';

echo '<h2>'.__("Are you sure you want to delete the product", "ninebit").': '.$result["product_name"].'?</h2>';
echo '<form action="'.nbs_tdd_go_to_page("tdd-delete-product.php", false).'" method="post">
    <input type="hidden" name="productID" id="productID" value="'. $result["id"].'" />';
    wp_nonce_field('externalAction','externalVerify');
echo nbs_tdd_get_confirm_button_set();
echo '</form>
</div>';
?>