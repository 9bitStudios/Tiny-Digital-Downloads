<?php if ( !defined('ABSPATH') ) exit;
echo '<div class="nbs-plugin-wrap">';

nbs_tdd_display_message();

   echo '<h2>'.__("Sales", "ninebit").'</h2>

        <table class="widefat">
        <thead>
            <tr>
                <th>'.__("Sale ID", "ninebit").'</th>
                <th>'.__("Product ID", "ninebit").'</th>
                <th>'.__("Item Name", "ninebit").'</th>                
                <th>'.__("Price", "ninebit").'</th>
                <th>'.__("Date", "ninebit").'</th>
                <th>'.__("Payment Code", "ninebit").'</th>
                <th>'.__("Active?", "ninebit").'</th>                
                <th></th>
            </tr>
        </thead>
        <tbody>';
 
        echo nbs_tdd_get_sales_rows();
 
 
echo '</tbody>
        <tfoot>
            <tr>
                <th>'.__("Sale ID", "ninebit").'</th>            
                <th>'.__("Product ID", "ninebit").'</th>
                <th>'.__("Item Name", "ninebit").'</th>                
                <th>'.__("Price", "ninebit").'</th>
                <th>'.__("Date", "ninebit").'</th>
                <th>'.__("Payment Code", "ninebit").'</th>
                <th>'.__("Active?", "ninebit").'</th>                    
                <th></th>
            </tr>
        </tfoot>
        </table>';
        
echo '</div>';