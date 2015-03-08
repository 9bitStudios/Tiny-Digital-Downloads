<?php if ( !defined('ABSPATH') ) exit;
    $shortname = 'nbs_tdd';
    $options = array (
    
        array( "name" =>  __('General', 'ninebit'),
                   "type" => "section"),          

        array( "name" => __("Sender Email", 'ninebit'),
                   "desc" => __("Enter the email that you want to set as the sending From: email when sending download links to your customers.", 'ninebit'),
                   "id"   => $shortname."_sender_email",
                   "cssClasses" => "text-medium",
                   "hasTitle" => true,                 
                   "std"  => "admin@admin.com",
                   "type" => "text"),             


    );
    
    if ($_GET['page'] == basename(__FILE__)) {
        
        if(isset($_REQUEST['action'])) {
            
            if ($_REQUEST['action'] == 'save') {
                
                if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
                    exit;
                }                
                
                foreach ($options as $value) {
                    
                    if(isset($value['id'])) {    
                        if( isset( $_REQUEST[$value['id']])) { 
                            update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
                        } 
                        else { 
                            delete_option($value['id']); 
                        } 
                    }
                }
                $queryParams = array('success' => 'true', 'message' => '7');
                $messageLink = add_query_arg($queryParams, admin_url().'admin.php?page=tiny-digital-downloads/tdd.php');
                wp_redirect($messageLink);
                
                exit;
    
            } else if($_REQUEST['action'] == 'reset') {
                
                if(!wp_verify_nonce($_POST['externalVerify'], 'externalAction')) {
                    exit;
                }                
                
                foreach ($options as $value) {
                    if(isset($value['id'])) {
                    delete_option( $value['id'] ); 
                    }
                }
                $queryParams = array('success' => 'true', 'message' => '8');
                $messageLink = add_query_arg($queryParams, admin_url().'admin.php?page=tiny-digital-downloads/tdd.php');
                wp_redirect($messageLink);
                
                exit;
            }
        }
    }    

echo '<div class="nbs-plugin-wrap">  

<h2>'.NBS_WPPLUGIN_TDD_MENU_TITLE.' '.__("Settings", "ninebit").'</h2>';

echo '<form action="'.admin_url().'admin.php?page=tdd-settings.php&noheader=true" method="post">';

foreach ($options as $value) { 
       
    switch ( $value['type'] ) {
        case "section": 
            echo '<h4 class="nbs-plugin-section">'.$value['name'].'</h4>';
        break;    
        
        case "sub-section": 
            echo '<h4 class="nbs-plugin-sub-section">'.$value['name'].'</h4>';

        break;        
        
        case "checkbox":
        
            echo '<h4>'.$value["name"].'</h4>';
            
            if(get_option($value["id"])) { 
                $checked = "checked=\"checked\""; 
            } 
            else{ 
                $checked = ""; 
            }
            
            echo '<input type="checkbox" name="'.$value["id"].'" id="'.$value["id"].'" value="true" '.$checked.' />&nbsp;
            <small>'.$value["desc"].'</small>';                   
        
        break;

        case 'select': 
            
            echo '<h4>'.$value["name"].'</h4>';
            echo '<select name="'.$value['id'].'" id="'.$value['id'].'">';
                
                foreach ($value['options'] as $option) {
                    echo '<option';
                    if ( get_option( $value['id'] ) == $option) {
                        echo ' selected=selected';
                    }
                    elseif ($option == $value['std']) {
                        echo ' selected=selected';
                    }
                    echo '>';
                    echo $option;
                    echo '</option>';
                
                }
                
                echo '</select><br />';
                echo '<small>'.$value['desc'].'</small>';

           break;        
        
        case 'text': 

            if (get_option( $value["id"] ) != "") { 
                $textVal = get_option($value["id"] ); 
            } 
            else { 
                $textVal = $value["std"]; 
            }
            
            echo '<h4><?php echo $value["name"]; ?></h4>';
            echo '<input name="'.$value["id"].'" id="'.$value["id"].'" type="'.$value["type"].'" class="'.$value["cssClasses"].'" value="'.$textVal.'" /><br />';
            echo '<small>'.$value["desc"].'</small>';
            
        break;
    }
} 

echo '<div class="clearout"></div>

    <div class="nbs-pluginFormButtonWrap">
        <input id="nbs-pluginSaveButton" name="save" class="button-primary" type="submit" value="'.__("Save Changes", "ninebit").'" />
        <input type="hidden" name="action" value="save" />&nbsp;';
        wp_nonce_field("externalAction","externalVerify");
echo '</div>
</form>

<div class="nbs-pluginFormButtonWrap">
    <form method="post" action="'.admin_url().'admin.php?page=tdd-settings.php&noheader=true">
        <input id="nbs-pluginResetButton" name="reset" class="button-primary" type="submit" value="'.__("Reset", "ninebit").'" />
        <input type="hidden" name="action" value="reset" />';
        wp_nonce_field("externalAction","externalVerify");
echo '</form>
</div>

</div>';
?>