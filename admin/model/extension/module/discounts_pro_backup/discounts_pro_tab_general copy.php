<?php
    class ModelExtensionModuleDiscountsProTabGeneral extends ModelExtensionModuleDiscountsPro
    {
        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->language($this->real_extension_type.'/discounts_pro_tab_general');
            
        }

        public function get_fields() {
            $this->document->addStyle('view/stylesheet/devmanextensions/discounts_pro/tab_general.css?'.date('Ymdhis'));
            $this->document->addScript('view/javascript/devmanextensions/discounts_pro/tab_general.js?'.date('Ymdhis'));

            $fields = array(
                array(
                    'label' => $this->language->get('status'),
                    'type' => 'boolean',
                    'name' => 'status'
                ),                
            );

            return $fields;
        }

        public function _send_custom_variables_to_view($variables) {

            return $variables;
        }

        public function _check_ajax_function($function_name) {
            if($function_name == 'xxxxx') {
                $this->xxxxx();
            }
        }
    }
?>