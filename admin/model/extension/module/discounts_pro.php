<?php
    class ModelExtensionModuleDiscountsPro extends Model {

        const array_order = array(
            'sort' => 'name',
            'order' => 'ASC',
        );

        public function _lang($lang_key) {
            return $this->language->get($lang_key);
        }
        public function validate_permiss() {
            if (!$this->user->hasPermission('modify', $this->real_extension_type.'/'.$this->extension_name)) {
                if(!empty($this->request->post['no_exit']))
                {
                    $array_return = array(
                        'error' => true,
                        'message' => $this->language->get('error_permission')
                    );
                    echo json_encode($array_return); die;
                }
                else
                    throw new Exception($this->language->get('error_permission'));

                return false;
            }
            return true;
        }
        public function exception($message) {
            throw new Exception($message);
        }

        function get_table_orders(){
            return array(
                $this->language->get('thead_order_id'),
                $this->language->get('thead_order_date'),
                $this->language->get('thead_order_total'),
                $this->language->get('thead_order_discount'),
                $this->language->get('thead_order_tile'),
            );
        }

        function get_categories_select(){
            //Load categories
            $this->load->model('catalog/category');
            $categories = $this->model_catalog_category->getCategories(self::array_order);

            $categories_select = array(
                '' => $this->language->get('all_categories')
            );

            foreach ($categories as $key => $cat) {
                $categories_select[$cat['category_id']] = $cat['name'];
            }
            return $categories_select;
         }

         function get_manufacturers_select(){
            //Load manufacturers
            $this->load->model('catalog/manufacturer');
            $manufacturers = $this->model_catalog_manufacturer->getManufacturers(self::array_order);

            $manufacturers_select = array(
                '' => $this->language->get('all_manufacturers')
            );

            foreach ($manufacturers as $key => $man) {
                $manufacturers_select[$man['manufacturer_id']] = $man['name'];
            }

            return $manufacturers_select;
         }

        function get_customer_groups_select(){
            //Load customers
            $customer_groups = $this->model_extension_devmanextensions_tools->getCustomerGroups();

            $customer_groups_select = array(
                '' => $this->language->get('all_customer_group')
            );

            foreach ($customer_groups as $key => $cg) {
                $customer_groups_select[$cg['customer_group_id']] = $cg['name'];
            }
            return $customer_groups_select;
        }

        function get_orders_with_discount($discount_type) {
            $results = $this->db->query("SELECT
                ord.order_id,
                ord.date_added,
                ord.total,
                ot.value as discount,
                ot.title
                FROM " . DB_PREFIX . "order ord
                LEFT JOIN " . DB_PREFIX . "order_total ot ON(ord.order_id = ot.order_id)
                WHERE
                ord.order_status_id > 0
                AND ot.code = 'discounts_pro" . "_" . $discount_type . "'
                ORDER BY ord.date_added DESC");

            if (!empty($results->rows)) {
                foreach ($results->rows as $key => $value) {
                    $value['title'] = '<a target="_blank" href="index.php?route=sale/order/info&'.$this->token_name.'=' . $this->session->data[$this->token_name] . '&order_id=' . $value['order_id'] . '">' . $value['title'] . '</a>';
                    $value['total'] = $this->currency->format($value['total'], $this->config->get('config_currency'));
                    $value['discount'] = $this->currency->format($value['discount'], $this->config->get('config_currency'));
                    $results->rows[$key] = array_values($value);
                }
            }
            return !empty($results->rows) ? $results->rows : array();
        }
    }
?>