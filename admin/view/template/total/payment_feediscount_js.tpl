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
?>
<script type="text/javascript">

var lastRules = ['fees', 'discounts'];

<?php foreach (array('fees', 'discounts') as $valuetype) { ?>
	lastRules['<?php echo $valuetype; ?>'] = <?php echo count(${$valuetype}) + 1; ?>;
<?php } ?>
	
$(".<?php echo $extension_id; ?> .payment-rules").on("click", ".add-item", function()
{
	$(this).closest(".<?php echo $extension_id; ?> .payment-rules").find(".rule-empty").remove();
	
	table = $(this).closest(".<?php echo $extension_id; ?> .payment-rules").attr('id');
	values = table.split("-");
	valuetype = values[1];
	
	html  = "<tr id='rule-" + valuetype + "-" + lastRules[valuetype] + "' class='item-row'>";
	
	html += "<td class='text-left rule-payment'>";
	html += "<select name='<?php echo $fieldbase; ?>_" + valuetype + "[" + lastRules[valuetype] + "][payment]' class='form-control'>";
	html += "<option value=''><?php echo $select_payment; ?></option>";
			
	<?php foreach ($payments as $payment) { ?>
		html += "<option value='<?php echo $payment[0]; ?>'><?php echo $payment[1]; ?></option>";
	<?php } ?>
			
	html += "</select>";
	html += "</td>";
	
	html += "<td class='text-left rule-group'>";
	html += "<select name='<?php echo $fieldbase; ?>_" + valuetype + "[" + lastRules[valuetype] + "][group]' class='form-control'>";
	html += "<option value=''><?php echo $select_group; ?></option>";
			
	<?php foreach ($customer_groups as $customer_group) { ?>
		html += "<option value='<?php echo $customer_group[0]; ?>'><?php echo $customer_group[1]; ?></option>";
	<?php } ?>
			
	html += "</select>";
	html += "</td>";
	
	html += "<td class='text-left rule-subtotal'>";
	html += "<input type='text' name='<?php echo $fieldbase; ?>_" + valuetype + "[" + lastRules[valuetype] + "][subtotal]' class='form-control' value='' placeholder='<?php echo $column_subtotal; ?>' />";
	html += "</td>";
	
	html += "<td class='text-left rule-value'>";
	html += "<input type='text' name='<?php echo $fieldbase; ?>_" + valuetype + "[" + lastRules[valuetype] + "][value]' class='form-control' value='' placeholder='<?php echo $column_value; ?>' />";
	html += "</td>";
	
	html += "<td class='text-center rule-actions'>";
	html += "<button type='button' data-toggle='tooltip' title='<?php echo $button_delete_rule; ?>' class='btn btn-danger delete-item'><i class='fa fa-minus-circle'></i></button>";
	html += "</td>";
	
	html += "</tr>";
	
	$(this).closest(".<?php echo $extension_id; ?> .payment-rules").find("tbody").append(html);
	
	lastRules[valuetype]++;
});

$(".<?php echo $extension_id; ?> .payment-rules").on("click", ".delete-item", function()
{
	if (!$(this).closest(".item-row").is(':only-child')) {
		$(this).closest(".item-row").remove();
	} else {
		$(this).closest(".<?php echo $extension_id; ?> .payment-rules").find("tbody").html("<tr class='rule-empty'><td colspan='5' class='text-center'><?php echo $message_rules_empty; ?></td></tr>");
	}
});

</script>