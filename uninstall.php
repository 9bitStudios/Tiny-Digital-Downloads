<?php if ( !defined('ABSPATH') ) exit;

/**** UNINSTALL ****/
    
    require_once 'includes/plugin-functions.php';
    
    // drop custom pages
    nbs_tdd_flush_pages();    
    
    // drop custom tables
    nbs_tdd_flush_tables();
    
    // delete options
    nbs_tdd_flush_options();

?>