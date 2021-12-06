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

if (version_compare(VERSION, '2.2', '<')) {
    class PaymentFeeDiscountHelper extends Model
    {
    	public function getTotal(&$total_data, &$total, &$taxes)
    	{		    		
    		$data = array('totals' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);

    		$this->calculateTotal($data);
    	}
    }
} else {
    class PaymentFeeDiscountHelper extends Model
    {
    	public function getTotal($data)
    	{		    		
    		$this->calculateTotal($data);
    	}    
    }
}

class ModelTotalPaymentFeeDiscount extends PaymentFeeDiscountHelper
{
	private $folder = "total";
	private $extension = "payment_feediscount";
	private $fieldbase = '';	
	private $path = '';	
		
	public function calculateTotal($data)
	{
		$total_data =& $data['totals'];
		$total =& $data['total'];
		$taxes =& $data['taxes'];
				
		if (!isset($this->session->data['payment_method'])) return;
		
		$this->language->load($this->folder.'/'.$this->extension);

		if (version_compare(VERSION, '2.3', '>=')) $this->path = 'extension';

		$this->fieldbase = (version_compare(VERSION, '3.0', '>=') ? $this->folder.'_' : '').$this->extension;

		// Get cart subtotal
			
		$subtotal = $this->cart->getSubTotal();
			
		if (!$subtotal) return;
			
 		if (!$total) $total = $subtotal;
		
		// Get customer groups
		 	
		$customer_group = $this->customer->getGroupId();
				
		foreach (array('fees', 'discounts') as $valuetype) {
			$items = $this->config->get($this->fieldbase.'_'.$valuetype);
			
			if (!$items) continue;
							
			$basevalue = '';
						
			foreach ($items as $item) {
				if ($item['payment'] != $this->session->data['payment_method']['code']) continue;
				if ($item['group'] && ($item['group'] != $customer_group)) continue;
				
				list($minimum, $maximum) = array_pad(explode('-', $item['subtotal']), 2, '');

				if ($item['value'] && (!$minimum || ($subtotal >= $minimum)) && (!$maximum || ($subtotal <= $maximum))) {
					$basevalue = $item['value'];
					break 2;
				}
			}
		}
		
		if ($basevalue !== '') {
			if (strpos($basevalue, '%') !== false) {
				$value = $total * (trim($basevalue, '%'))/100;
				$roundby = $this->config->get($this->fieldbase.'_round');
				if ($roundby) $value = ceil($value / $roundby) * $roundby;
			} else {
				$value = $basevalue;
			}
			
			$value = ($valuetype == 'discounts' ? -abs($value) : abs($value));
		
			// Check inactive
		
			if ($this->config->get($this->fieldbase.'_inactive_'.$valuetype)) {
				$inactive_items = array();
				
				foreach ($this->config->get($this->fieldbase.'_inactive_'.$valuetype) as $inactive) {
					$inactive_values = explode(':', $inactive);
					
					if (($inactive_values[0] == 'payment') && !empty($this->session->data['payment_method'])) {
						if ($inactive_values[1] == $this->session->data['payment_method']['code']) return;
					} elseif (($inactive_values[0] == 'shipping') && !empty($this->session->data['shipping_method'])) {
						$shipping_method = explode('.', $this->session->data['shipping_method']['code']);
						
						if ($inactive_values[1] == $shipping_method[0]) return;
					} elseif ($inactive_values[0] == 'total') {
						$inactive_items[] = $inactive_values[1];
					}
				}
								
				if (!empty($inactive_items)) {
					$this->load->model('extension/extension');
					$totals = $this->model_extension_extension->getExtensions('total');
				
					$inactive_data = array();
					$inactive_total = 0;
					$inactive_taxes = $this->cart->getTaxes();
										
					foreach ($totals as $item) {
						if (($item['code'] != $this->extension) && $this->config->get($item['code'].'_status')) {
							$this->load->model(($this->path ? $this->path.'/' : '').'total/'.$item['code']);
							
							if (version_compare(VERSION, '2.2', '<')) {	
								$this->{'model_'.($this->path ? $this->path.'_' : '').'total_'.$item['code']}->getTotal($inactive_data, $inactive_total, $inactive_taxes);
							} else {
								$data = array('totals' => &$inactive_data, 'total' => &$inactive_total, 'taxes' => &$inactive_taxes);
								$this->{'model_'.($this->path ? $this->path.'_' : '').'total_'.$item['code']}->getTotal($data);
							}
						}
					}

					foreach ($inactive_data as $item) {						
						if (in_array($item['code'], $inactive_items)) return;
					}
				}
			}
			
			// Create title
								
			$title = "Payment fee"; //$this->language->get($this->extension.'_'.$valuetype);
					
			$add_name = $this->config->get($this->fieldbase.'_add_name');
			$add_value = $this->config->get($this->fieldbase.'_add_value');

			if ($add_name || $add_value) {
				$info = array();
						
				if ($add_name) $info[] = $this->session->data['payment_method']['title'];
				if ($add_value && strpos($basevalue, '%')) $info[] = $basevalue;
				
				if ($info) $title .= " (".implode(", ", $info).")";
			}
			
			// Recalculate taxes
			
			if ($this->config->get($this->fieldbase.'_tax_class')) {
				$tax_rates = $this->tax->getRates($value, $this->config->get($this->fieldbase.'_tax_class'));
	
				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) $taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					else $taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
				}
			}

			$total += $value;
					
			// Hide total

			if ($this->config->get($this->fieldbase.'_hide_total')) {
				foreach ($total_data as $key => $item) {
					if ($item['code'] == $this->config->get($this->fieldbase.'_add_to')) {
						$newvalue = $item['value'] + $value;
						$total_data[$key]['text'] = $this->currency->format($newvalue, $this->config->get('config_currency'));
       					$total_data[$key]['value'] = $newvalue;
				 				
				 		if ($this->config->get($this->fieldbase.'_add_info')) {
							$total_data[$key]['title'] = $this->language->get($this->extension.'_hidden_title');
				 		}
				 		
				 		return;
			 		}
				}
			}
			
			$total_data[] = array(
				'code' => $this->extension,
		     	'title' => $title,
    		    'text' => ($value < 0 ? "-" : "").$this->currency->format(abs($value), $this->config->get('config_currency')),
        		'value' => $value,
				'sort_order' => $this->config->get($this->fieldbase.'_sort_order'));
		}
	}	
}

?>