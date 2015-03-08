<?php if ( !defined('ABSPATH') ) exit;
echo '<div class="nbs-plugin-wrap">';

nbs_tdd_display_message();

    echo '<h2>'.__("Products", "ninebit").'</h2>

        <table class="widefat">
        <thead>
            <tr>
                <th>'.__("ID", "ninebit").'</th>			
                <th>'.__("Product Name", "ninebit").'</th>
                <th>'.__("File", "ninebit").'</th>
                <th>'.__("Sales", "ninebit").'</th>
                <th>'.__("Earnings", "ninebit").'</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
     
		echo nbs_tdd_get_products_rows();
 
 
echo '</tbody>
        <tfoot>
            <tr>
                <th>'.__("ID", "ninebit").'</th>			
                <th>'.__("Product Name", "ninebit").'</th>
                <th>'.__("File", "ninebit").'</th>
                <th>'.__("Sales", "ninebit").'</th>
                <th>'.__("Earnings", "ninebit").'</th>					
                <th></th>
            </tr>
        </tfoot>
        </table>

		<div class="clearout"></div>
		<a href="'.nbs_tdd_go_to_page("tdd-add-product.php").'" class="button-primary">+ '.__("Add New Product", "ninebit").'</a>';
echo '</div>';