<?php
    class ModelExtensionModuleDiscountsProTabProducts extends ModelExtensionModuleDiscountsPro
    {
        public function __construct($registry) {
            parent::__construct($registry);
            $this->load->language($this->real_extension_type.'/discounts_pro_tab_products');
        }

        public function get_fields() {
            $this->document->addStyle('view/stylesheet/devmanextensions/discounts_pro/tab_general.css?'.date('Ymdhis'));
            $this->document->addScript('view/javascript/devmanextensions/discounts_pro/tab_general.js?'.date('Ymdhis'));

            $fields = array(
                array(
                    'label' => $this->language->get('products_status'),
                    'type' => 'boolean',
                    'name' => 'product_status'
                ),
                array(
                    'type' => 'table_inputs',
                    'name' => 'product',
                    'class' => 'products',
                    'theads' => array(
                        $this->language->get('status'),
                        $this->language->get('products'),
                        $this->language->get('type'),
                        $this->language->get('customer_group'),
                        $this->language->get('repeat'),
                        $this->language->get('include_tax'),
                        $this->language->get('from'),
                        $this->language->get('to'),
                        $this->language->get('discount'),
                        $this->language->get('unit'),
                        $this->language->get('message'),
                        $this->language->get('sort_order')
                    ),
                    'value' => $this->config->get('product'),
                    'model_row' => array(
                        array(
                            'type' => 'boolean',
                            'name' => 'status'
                        ),
                        array(
                            'type' => 'products_autocomplete',
                            'name' => 'products'
                        ),
                        array(
                            'type' => 'select',
                            'options' => array(
                                'percentage' => $this->language->get('percentage'),
                                'fixed' => $this->language->get('fixed'),
                            ),
                            'name' => 'type'
                        ),
                        array(
                            'type' => 'select',
                            'options' => $this->get_customer_groups_select(),
                            'name' => 'customer_group'
                        ),
                        array(
                            'type' => 'boolean',
                            'name' => 'repeat'
                        ),
                        array(
                            'type' => 'boolean',
                            'name' => 'include_tax'
                        ),
                        array(
                            'type' => 'date',
                            'name' => 'from'
                        ),
                        array(
                            'type' => 'date',
                            'name' => 'to'
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'discount'
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'unit'
                        ),
                        array(
                            'type' => 'text',
                            'multilanguage' => true,
                            'name' => 'message'
                        ),
                        array(
                            'type' => 'text',
                            'name' => 'sort_order'
                        )
                    ) 
                ),
                array(
                    'type' => 'legend',
                    'text' => $this->language->get('discount_product_table')
                ),
                array(
                    'type' => 'table',
                    'theads' => $this->get_table_orders(),
                    'data' => $this->get_orders_with_discount('product'),
                )
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