<?php
/*
	$Project: Product Option Tooltips $
	$Author: karapuz  team<support@ka-station.com> $

	$Version: 2.0.0.5 $ ($Revision: 71 $)
*/

namespace extension\ka_extensions;

class ControllerKaOptionTooltips extends \KaInstaller {

	protected $extension_version = '2.0.0.5';
	protected $min_store_version = '3.0.0.0';
	protected $max_store_version = '3.0.3.9';
	protected $min_ka_extensions_version = '4.1.0.5';
	protected $max_ka_extensions_version = '4.1.1.9';
	
	protected $tables;
	
	//temporary variables
	protected $error;
	
	public function getTitle() {
		$version = $this->extension_version;
		$str = str_replace('{{version}}', $version, $this->language->get('extension_title'));
		return $str;
	}

	
	protected function onLoad() {

		$this->load->language('extension/ka_extensions/ka_option_tooltips/settings');
		
		$this->load->model('setting/setting');
		$this->load->model('user/user_group');
		
 		$this->tables = array(
 			'ka_option_tooltip' => array(
 				'fields' => array(
					"option_id" => array(
						"type" => "int(11)", 
					),
					"language_id" => array(
						"type" => "int(11)",
					),
					"tooltip" => array(
						"type" => "mediumtext",
					),
					"option_value_id" => array(
						"type" => "int(11)",
					),
				),
				'indexes' => array(
					'option_id' => array(
						'fields' => array('option_id'),
					),
					'option_value_id' => array(
						'fields' => array('option_value_id'),
					)
				)
			),
			
 			'ka_product_option_tooltip' => array(
 				'fields' => array(
					"product_option_id" => array(
						"type" => "int(11)",
					),
					"language_id" => array(
						"type" => "int(11)",
					),
					"tooltip" => array(
						"type" => "mediumtext",
					),
					"product_option_value_id" => array(
						"type" => "int(11)",
					),
					"product_id" => array(
						"type" => "int(11)",
					),
				),
				'indexes' => array(
					"fields" => array("product_option_id"),
					"product_option_value_id" => array("product_option_value_id"),
					"product_id" => array("product_id")
				),
			),
 		);

		$this->tables['ka_option_tooltip']['query'] = "
			CREATE TABLE `" . DB_PREFIX . "ka_option_tooltip` (  
				`option_id` int(11) NOT NULL,  
				`language_id` int(11) NOT NULL,
				`tooltip` mediumtext NOT NULL,
				`option_value_id` int(11) NOT NULL,  
				KEY `option_id` (`option_id`),  
				KEY `option_value_id` (`option_value_id`)
			) DEFAULT CHARSET=utf8
		";
		 		 			
		$this->tables['ka_product_option_tooltip']['query'] = "
			CREATE TABLE `" . DB_PREFIX . "ka_product_option_tooltip` (
				`product_option_id` int(11) NOT NULL,
				`language_id` int(11) NOT NULL,
				`tooltip` mediumtext NOT NULL,
				`product_option_value_id` int(11) NOT NULL,
				`product_id` int(11) NOT NULL,
				KEY `product_option_id` (`product_option_id`),
				KEY `product_option_value_id` (`product_option_value_id`),
				KEY `product_id` (`product_id`)
			) DEFAULT CHARSET=utf8
		";
				
		return true;
	}
	
	
	public function index() {

		$heading_title = $this->getTitle();
		$this->document->setTitle($heading_title);

		// handle autoinstall actions
		//
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if (!isset($this->request->post['ka_option_tooltips_show_global_tips_for_empty_options'])) {
				$this->request->post['ka_option_tooltips_show_global_tips_for_empty_options'] = '';
			}
			
			$this->model_setting_setting->editSetting('ka_option_tooltips', $this->request->post);
			$this->addTopMessage($this->language->get('Settings have been stored sucessfully.'));
									
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
			
		} elseif ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->data = $this->request->post;

		} else {
		
		}

		$this->data['heading_title']   = $heading_title;
	
		$this->data['button_save']     = $this->language->get('button_save');		
		$this->data['button_cancel']   = $this->language->get('button_cancel');

		$this->data['extension_version']        = $this->extension_version;
		$this->data['error'] = $this->error;
		
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['ka_option_tooltips_show_global_tips_for_empty_options'] = $this->config->get('ka_option_tooltips_show_global_tips_for_empty_options');
		
		$this->data['breadcrumbs'] = array();
   		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

  		$this->data['breadcrumbs'][] = array(
	 		'text'      => $this->language->get('Ka Extensions'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true),
 		);
		
 		$this->data['breadcrumbs'][] = array(
	 		'text'      => $heading_title,
			'href'      => $this->url->link('extension/ka_extensions/ka_option_tooltips', 'user_token=' . $this->session->data['user_token'], true),
 		);
		
		$this->data['action'] = $this->url->link('extension/ka_extensions/ka_option_tooltips', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);

		$this->template = 'extension/ka_extensions/ka_option_tooltips/settings';
		$this->children = array(
			'common/header',
			'common/column_left',
			'common/footer'
		);
				
		$this->setOutput();
	}

		
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/ka_extensions/ka_option_tooltips')) {
			$this->addTopMessage($this->language->get('error_permission'), 'E');
			return false;
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	

	public function install() {

		if (parent::install()) {
			$settings = array(
				'ka_option_tooltips_show_global_tips_for_empty_options' => 'Y'
			);
			$this->model_setting_setting->editSetting('ka_option_tooltips', $settings);			
			
			return true;
		} 
		
		return false;
	}

	
	public function uninstall() {
		$this->model_setting_setting->deleteSetting('ka_option_tooltips');
		return true;
	}	
}

class_alias(__NAMESPACE__ . '\ControllerKaOptionTooltips', 'ControllerExtensionKaExtensionsKaOptionTooltips');