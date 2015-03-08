<?php if ( !defined('ABSPATH') ) exit;
echo '<div class="nbs-plugin-wrap">';

nbs_tdd_display_message();

echo '<h2>'.NBS_WPPLUGIN_TDD_MENU_TITLE.'</h2>';

echo '<h3>'.__("Total Earnings", "ninebit").'</h3>';

echo '<p>'.NBS_WPPLUGIN_TDD_CURRENCY_SYMBOL.nbs_tdd_get_total_sales().'</p>';

echo '<p><a href="'.nbs_tdd_go_to_page("tdd-products.php").'">'.__("View Products", "ninebit").'</a>&nbsp;|&nbsp;
		<a href="'.nbs_tdd_go_to_page("tdd-sales.php").'">'.__("View Sales", "ninebit").'</a>&nbsp;|&nbsp;
		<a href="'.nbs_tdd_go_to_page("tdd-add-product.php").'">'.__("Add New Product", "ninebit").'</a>
	<p>';

echo '</div>';