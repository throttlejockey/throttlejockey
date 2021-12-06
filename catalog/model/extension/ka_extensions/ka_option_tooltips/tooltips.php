<?php
/*
	$Project: Product Option Tooltips $
	$Author: karapuz  team<support@ka-station.com> $

	$Version: 2.0.0.5 $ ($Revision: 71 $)
*/

namespace extension\ka_extensions\ka_option_tooltips;

class ModelTooltips extends \KaModel {

	
	public function getOptionTooltip($option_id) {
		$ka_tooltip = '';

		$ka_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ka_option_tooltip`
			WHERE option_id = '$option_id'
			AND option_value_id = 0
			AND language_id = '" . (int)$this->config->get('config_language_id') . "' 
		");
			
		if (!empty($ka_query->row)) {
			$ka_tooltip = $ka_query->row['tooltip'];
		}
	
		return $ka_tooltip;
	}
	

	public function getValueTooltip($option_value_id) {
	
		$ka_value_tooltip = '';
		
		$ka_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ka_option_tooltip`
			WHERE 
				option_value_id = '$option_value_id'
				AND language_id = '" . (int)$this->config->get('config_language_id') . "' 
		");
		
		if (!empty($ka_query->row)) {
			$ka_value_tooltip = $ka_query->row['tooltip'];
		}
		
		return $ka_value_tooltip;
	}
	
	
	public function getProductOptionTooltip($product_option_id) {
		$ka_tooltip = '';

		$ka_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ka_product_option_tooltip`
			WHERE product_option_id = '$product_option_id'
			AND product_option_value_id = 0
			AND language_id = '" . (int)$this->config->get('config_language_id') . "' 
		");
		
		if (!empty($ka_query->row['tooltip'])) {
		
			$ka_tooltip = $ka_query->row['tooltip'];
			
		} elseif ($this->config->get('ka_option_tooltips_show_global_tips_for_empty_options')) {
			$ka_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ka_option_tooltip` ot
				INNER JOIN " . DB_PREFIX . "product_option_value pov ON ot.option_id = pov.option_id
				WHERE pov.product_option_id = '$product_option_id'
				AND ot.option_value_id = 0
				AND ot.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			");
			
			if (!empty($ka_query->row)) {
				$ka_tooltip = $ka_query->row['tooltip'];
			}
		}
	
		return $ka_tooltip;
	}

	
	public function getProductValueTooltip($product_option_value_id) {
	
		$ka_value_tooltip = '';
		
		$ka_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ka_product_option_tooltip`
			WHERE product_option_value_id = '$product_option_value_id'
			AND language_id = '" . (int)$this->config->get('config_language_id') . "' 
		");
		
		
		if (!empty($ka_query->row['tooltip'])) {
		
			$ka_value_tooltip = $ka_query->row['tooltip'];
			
		} elseif ($this->config->get('ka_option_tooltips_show_global_tips_for_empty_options')) {
		
			$ka_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "ka_option_tooltip` ot
				INNER JOIN " . DB_PREFIX . "product_option_value pov ON ot.option_value_id = pov.option_value_id
				WHERE 
					pov.product_option_value_id = '$product_option_value_id'
					AND ot.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			");
			
			if (!empty($ka_query->row)) {
				$ka_value_tooltip = $ka_query->row['tooltip'];
			}
		}
		
		return $ka_value_tooltip;
	}	
	
}

class_alias(__NAMESPACE__ . '\ModelTooltips', 'ModelExtensionKaExtensionsKaOptionTooltipsTooltips');