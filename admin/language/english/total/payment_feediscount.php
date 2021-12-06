<?php
/*------------------------------------------------------------------------
# Payment Fee or Discount
# ------------------------------------------------------------------------
# The Krotek
# Copyright (C) 2011-2020 The Krotek. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: https://thekrotek.com
# Support: support@thekrotek.com
-------------------------------------------------------------------------*/

$_['heading_title']    				= "Payment Fee or Discount";
$_['heading_version']   			= "3.0.0";

$_['text_success']    				= 'Success: You have modified Payment Fee or Discount!';

/* Tabs */

$_['tab_general']   				= "General";

$_['tab_fees']   					= "Fees";
$_['help_fees']   					= "Customer Group - apply to selected customer group only; Order Subtotal - minimum and maxumum order subtotal values, when fee or discount is available (e.g.: 100, 200-300); Value - percent or fixed value (e.g.: 3%, -5%, 10, -20).";

$_['tab_discounts']   				= "Discounts";
$_['help_discounts']   				= "Customer Group - apply to selected customer group only; Order Subtotal - minimum and maxumum order subtotal values, when fee or discount is available (e.g.: 100, 200-300); Value - percent or fixed value (e.g.: 3%, -5%, 10, -20).";

/* General */

$_['entry_round']     				= "Round By";
$_['help_round']         			= "Fee or Discount will be rounded to given value (e.g: if set to 10 - 271.52 will be rounded to 280).";

$_['entry_add_name']     			= "Add Name to Title";
$_['help_add_name']   				= "Selected payment method's name will be added to title.";

$_['entry_add_value']     			= "Add Value to Title";
$_['help_add_value']   				= "Fee or discount value will be added to title (when in percent).";

$_['entry_hide_total']     			= "Hide Total";
$_['help_hide_total']     			= "Hides Payment Fee or Discount total from the list and adds its value to selected total.";

$_['entry_add_to']					= "Total to Add";
$_['help_add_to']					= "When Hide Total is enabled, fee or discount value will be added to selected total.";

$_['entry_add_info']     			= "Add Info to Total";
$_['help_add_info']   				= "Additional information about fee or discount will be added to selected total's title.";

$_['entry_inactive_fees'] 			= "Fee Inactive With";
$_['help_inactive_fees']  			= "Fee will not be applied, if any of the selected totals are active in current order.";

$_['entry_inactive_discounts'] 		= "Discount Inactive With";
$_['help_inactive_discounts']  		= "Discount will not be applied, if any of the selected totals are active in current order.";

/* Fees & Discounts */

$_['column_payment']   				= "Payment";
$_['column_group']   				= "Customer Group";
$_['column_subtotal']   			= "Order Subtotal";
$_['column_value']   				= "Value";

$_['select_payment']   				= "-- Select payment --";
$_['select_group']   				= "-- Select group --";

/* Buttons */

$_['button_add_rule']  				= "Add rule";
$_['button_delete_rule']  			= "Delete rule";

/* Messages */

$_['message_rules_empty']			= "No payment rules were created yet. Click Add Rule button below to create new rule.";

/* Errors */

$_['error_range']  					= "Error: %s value is invalid. Must be a single positive numerical value or range (e.g: 10, 10-20 etc).";

/* Generic language strings */

$_['heading_latest']   				= "You have the latest version: %s";
$_['heading_future']   				= "Wow! You have version %s and it's from THE FUTURE!";
$_['heading_update']   				= "A new version available: %s. Click <a href='https://thekrotek.com/profile/my-orders' title='Download new version' target='_blank'>here</a> to download.";

$_['entry_license']					= "License ID";
$_['help_license']					= "Your order ID on TheKrotek.com or OpenCart.com site.";

$_['entry_debug']  					= "Debug";
$_['help_debug']  					= "Essential information will be saved in the log for additional debugging. Disable, when not needed!";

$_['entry_version']					= "Check Version";
$_['help_version']					= "Disable, if settings page loads too slow or connection errors displayed.";

$_['entry_stores']					= "Stores";
$_['help_stores']					= "Extension will work for selected stores only (empty - all stores)";

$_['entry_customer_groups'] 		= "Customer Groups";
$_['help_customer_groups'] 	 		= "Extension will work for selected groups only (empty - all groups and guests).";

$_['entry_geo_zone']   				= "Geo Zone";
$_['help_geo_zone']   				= "Extension will work for selected geo zone only.";

$_['entry_tax_class']  				= "Tax Class";
$_['help_tax_class']   				= "Tax class, which will be applied for this extension";

$_['entry_status']     				= "Status";
$_['help_status']   				= "Enable or disable this extension";

$_['entry_sort_order'] 				= "Sort Order";
$_['help_sort_order']   			= "Position in the list of extensions of the same type.";

$_['text_edit_title']    	   		= "Edit %s";
$_['text_remove_all']				= "Remove all";
$_['text_none']   	    			= "--- None ---";

$_['text_extension']		 		= "Extensions";
$_['text_total']    				= "Total";
$_['text_module']    				= "Modules";
$_['text_shipping']    				= "Shipping";
$_['text_payment']    				= "Payment";
$_['text_feed']           	  		= "Feeds";

$_['button_apply']      			= "Apply";
$_['button_help']      				= "Help";

$_['text_content_top']    			= "Content Top";
$_['text_content_bottom'] 			= "Content Bottom";
$_['text_column_left']    			= "Column Left";
$_['text_column_right']   			= "Column Right";

$_['entry_module_layout']   		= "Layout:";
$_['entry_module_position'] 		= "Position:";
$_['entry_module_status']  			= "Status:";
$_['entry_module_sort']    			= "Sort Order:";

$_['message_success']     			= "Success: You have modified %s!";

$_['error_permission'] 				= "Warning: You do not have permission to modify %s!";
$_['error_license_empty'] 			= "License ID is missing. Please, enter your order ID from our site or from OpenCart.com.";
$_['error_version_data'] 			= "Impossible to get version information: Data for this product is not found.";
$_['error_version_disabled'] 		= "Impossible to get version information: Version check is disabled.";
$_['error_empty'] 					= "Error: %s value can't be empty.";
$_['error_numerical'] 				= "Error: %s value should be numerical.";
$_['error_percent'] 				= "Error: %s value should be numerical or in percent.";
$_['error_positive'] 				= "Error: %s value should be zero or more.";
$_['error_date'] 					= "Error: %s has wrong date format.";
$_['error_curl']      				= "cURL error: (%s) %s. Fix it (if necessary) and try to reinstall.";

?>