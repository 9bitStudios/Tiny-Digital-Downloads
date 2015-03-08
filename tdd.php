<?php
/* 
Plugin Name: Tiny Digital Downloads
Plugin URI: http://www.9bitstudios.com
Description: An extremely lightweight plugin for selling digital downloads...
Author: 9bit 
Version: 1.0.0 
Author URI: http://www.9bitstudios.com
*/  
 
if ( !defined('ABSPATH') ) exit;
 
class TinyDigitalDownloads { 

    function __construct() {

    register_activation_hook(__FILE__,  array($this, 'activation'));
    register_deactivation_hook(__FILE__,  array($this, 'deactivation'));
    add_action('init',  array($this, 'load_config'));
    add_action('init',  array($this, 'load_functions'));
    add_action('admin_menu', array($this,'add_admin_menu')); 
    add_action('admin_menu', array($this, 'admin_register_head'));
    add_action('admin_footer',  array($this, 'admin_register_footer'));  
    add_filter('page_template', array($this,'nbs_tdd_set_page_templates'));

    }
    
    /**** ACTIVATION ****/
    
    function activation() { 
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
        global $wpdb;
        
        $table_name = $wpdb->prefix.'tdd_products';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          product_name VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
          sales INT(11) COLLATE utf8_general_ci NOT NULL,
          earnings DECIMAL(10,2) COLLATE utf8_general_ci NOT NULL,
          timestamp datetime NOT NULL,
          download_file VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
          UNIQUE KEY id (id)
        );";
        
           dbDelta($sql);    
            
        
        $table_name = $wpdb->prefix.'tdd_sales';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          product_id INT(11) COLLATE utf8_general_ci NOT NULL,
          item_name VARCHAR(255) COLLATE utf8_general_ci NOT NULL,          
          price DECIMAL(10,2) COLLATE utf8_general_ci NOT NULL,
          date datetime NOT NULL,
          payment_code VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
          active INT(2) COLLATE utf8_general_ci NOT NULL,
          UNIQUE KEY id (id)
        );";
        
           dbDelta($sql);    
    
        $idCollection = '';
    
        $ddPage = array(
            'post_title' => __('Digital Downloads', 'ninebit' ),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        );
        $mainPostID = wp_insert_post($ddPage);
        $idCollection .= $mainPostID.',';
        
        $ddPage = array(
            'post_title' => __('PayPal IPN', 'ninebit' ),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $mainPostID,
            'post_author' => 1,
        );        
        $id = wp_insert_post($ddPage);
        $idCollection .= $id.',';
        update_option('nbs_tdd_paypal_ipn_page_id', $id);
        
        $ddPage = array(
            'post_title' => __('Download', 'ninebit' ),
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $mainPostID,
            'post_author' => 1,
        );        
        $id = wp_insert_post($ddPage);
        $idCollection .= $id.',';        
        update_option('nbs_tdd_download_page_id', $id);
        
        $ddPage = array(
            'post_title' => __('Thank You', 'ninebit' ),
            'post_content' => __('Thank you for your purchase. Your download code is available in your e-mail.', 'ninebit' ),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $mainPostID,
            'post_author' => 1,
        );        
        $id = wp_insert_post($ddPage);
        $idCollection .= $id.',';        
        update_option('nbs_tdd_thank_you_page_url', get_permalink($id));
        
        $ddPage = array(
            'post_title' => __( 'Error', 'ninebit' ),
            'post_content' => __('There was an error in the process and the request could not be completed. If you think this is an error please contact support to resolve the situation.', 'ninebit' ),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => $mainPostID,
            'post_author' => 1,
        );        
        $id = wp_insert_post($ddPage);    
        $idCollection .= $id.'';
        update_option('nbs_tdd_error_page_url', get_permalink($id));
        update_option('nbs_tdd_generated_page_ids', $idCollection);
    
    }
    
    function nbs_tdd_set_page_templates($page_template)
    {
        $downloadID = get_option('nbs_tdd_download_page_id');
        $paypalID = get_option('nbs_tdd_paypal_ipn_page_id');
        
        if ( is_page($downloadID)) {
            $page_template = WP_PLUGIN_DIR.'/'.NBS_WPPLUGIN_TDD_FOLDER.'/templates/template-page-tdd-download.php';
        }
        
        if(is_page($paypalID)) {
            $page_template = WP_PLUGIN_DIR.'/'.NBS_WPPLUGIN_TDD_FOLDER.'/templates/template-page-tdd-paypal-ipn.php';
        }
        
        return $page_template;
    }    
    
    
    /**** DEACTIVATION ****/
    function deactivation() { 
        
        //nbs_tdd_flush_tables();    
        
        // drop custom pages
        nbs_tdd_flush_pages();    
        
        // delete options
        nbs_tdd_flush_options();

    }

    /**** LOAD CONFIG, CLASSES, & FUNCTIONS ****/
    function load_config() {
        require_once 'plugin-config.php';
    }
    
    function load_classes() {
        require_once 'includes/plugin-classes.php';
    }

    function load_functions() {
        require_once 'includes/plugin-functions.php';
    }    
    
    /**** REGISTER ADMIN HEADER ****/
    function admin_register_head() {     
        
        $pluginDirectory = plugins_url() .'/'. basename(dirname(__FILE__));
        wp_enqueue_style("nbs-tdd-main-css", $pluginDirectory . '/css/style.css');
        wp_enqueue_script('jquery');       
        wp_enqueue_script('tdd-js', $pluginDirectory . '/js/tdd.js');
    }    

    
    /**** REGISTER ADMIN FOOTER ****/
    function admin_register_footer() {
        $pluginDirectory = plugins_url() .'/'. basename(dirname(__FILE__));
        wp_enqueue_script("nbs-tdd-validate-js", $pluginDirectory . '/js/jquery.validate.min.js');
        
        echo '<script type="text/javascript">
        jQuery(document).ready(function ($) { 
          $("#tddForm").validate();
        });
        </script>       
        ';
    }    
    
    /**** REGISTER MENU & PAGES ****/
    function add_admin_menu() {  
        add_menu_page(NBS_WPPLUGIN_TDD_MENU_TITLE, NBS_WPPLUGIN_TDD_MENU_TITLE, NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, __FILE__, array($this,"home"));
        add_submenu_page(__FILE__, "".__('Products', 'ninebit')."", "".__('Products', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL,'tdd-products.php', array($this,"products")); 
        add_submenu_page(__FILE__, "".__('Add New Product', 'ninebit')."", "".__('Add New Product', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL,'tdd-add-product.php', array($this,"add_product")); 
        add_submenu_page(__FILE__, "".__('Sales', 'ninebit')."", "".__('Sales', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL,'tdd-sales.php', array($this,"sales")); 
        
        add_submenu_page(null, "".__('Edit Product', 'ninebit')."", "".__('Edit Product', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, 'tdd-edit-product.php',  array($this,"edit_product"));
        add_submenu_page(null, "".__('Edit Product', 'ninebit')."", "".__('Edit Product', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, 'tdd-edit-product-all-fields.php',  array($this,"edit_product_all"));
        add_submenu_page(null, "".__('Delete Product', 'ninebit')."", "".__('Delete Product', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, 'tdd-delete-product.php',  array($this,"delete_product"));
        
        add_submenu_page(null, "".__('Add Sale', 'ninebit')."", "".__('Add Sale', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, 'tdd-add-sale.php',  array($this,"add_sale"));
        add_submenu_page(null, "".__('Edit Sale', 'ninebit')."", "".__('Edit Sale', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, 'tdd-edit-sale.php',  array($this,"edit_sale"));        
        add_submenu_page(null, "".__('Delete Sale', 'ninebit')."", "".__('Delete Sale', 'ninebit')."",NBS_WPPLUGIN_TDD_PERMISSION_LEVEL, 'tdd-delete-sale.php',  array($this,"delete_sale"));
    }     

    function home() {  
        include('includes/tdd-home.php'); 
    }      

    function products() {  
        include('includes/tdd-products.php'); 
    }  
    
    function add_product() {  
        include('includes/tdd-add-product.php'); 
    }      

    function edit_product() {  
        include('includes/tdd-edit-product.php');  
    }

    function edit_product_all() {  
        include('includes/tdd-edit-product-all-fields.php');  
    }    
    
    function delete_product() {  
        include('includes/tdd-delete-product.php');  
    }
    
    function sales() {  
        include('includes/tdd-sales.php'); 
    }      
    
    function add_sale() {  
        include('includes/tdd-add-sale.php');  
    }    
    
    function edit_sale() {  
        include('includes/tdd-edit-sale.php');  
    }    
    
    function delete_sale() {  
        include('includes/tdd-delete-sale.php');  
    }         
    
    /**** SHORTCODES ****/

    /* None */
    
}
$nbs_tdd = new TinyDigitalDownloads();    

?>