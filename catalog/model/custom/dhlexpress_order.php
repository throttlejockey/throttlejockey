<?php
class ModelCustomDhlExpressOrder extends Model
{
    public function getOrdersByStatusId($status, $days, $token)
    {
        if (preg_match('/[^a-z0-9]/i', $token)) {
            return array();
        }
        $columns_query     = "SHOW COLUMNS FROM `" . DB_PREFIX . "api_session` LIKE 'token'";
        $token_column_name = 'session_id';
        try {
            $columns_db_result = $this->db->query($columns_query);
            $columns_info      = $columns_db_result->row;
            if ($columns_info) {
                $token_column_name = 'token';
            }
        }
        catch (Exception $ex) {
        }
        $auth_query = "SELECT o.* FROM `" . DB_PREFIX . "api_session` AS o " . "WHERE o." . $token_column_name . " = '" . $token . "' " . "AND DATE_ADD(o.date_modified, INTERVAL 120 MINUTE) >= NOW()";
        try {
            $auth_db_result = $this->db->query($auth_query);
            $auth_info      = $auth_db_result->row;
            if ($auth_info) {
                // Verified
            } else {
                return array();
            }
        }
        catch (Exception $ex) {
        }
        $query = "SELECT o.* FROM `" . DB_PREFIX . "order` AS o " . "WHERE o.order_status_id = '" . (int) $status . "' " . "AND (o.date_modified BETWEEN '" . $days . "' AND NOW())";
        try {
            $order_query = $this->db->query($query);
        }
        catch (Exception $ex) {
            $error = $ex;
        }
        if (!empty($error)) {
            $json['error'] = $ex;
        }

        if ($order_query->num_rows) {
            foreach ($order_query->rows as $key => $value) {
                $row_id = $key;
                $order_id = $order_query->rows[$key]['order_id'];

                try {
                    $products = $this->db->query("SELECT op.*, p.sku, p.weight FROM `" . DB_PREFIX . "order_product` as op JOIN " . DB_PREFIX . "product as p USING (product_id) WHERE op.order_id = '$order_id'");

                        $order_query->rows[$key]['line_item'] = $products->rows;

                        $item_counter = 0;
                        foreach ($products->rows as $product) {
                            try {
                                $product_options = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

                                if (!is_null($product_options)) {
                                    $order_query->rows[$row_id]['line_item'][$item_counter]['product_options'] = $product_options->rows;

                                    $product_option_value_counter = 0;
                                    foreach ($product_options->rows as $product_option) {
                                        $product_option_value_id = $product_option['product_option_value_id'];
                                        $product_option_id = $product_option['product_option_id'];

                                        $product_option_values = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option_id . "' AND product_option_value_id = '" . (int)$product_option_value_id . "'");

                                        $order_query->rows[$row_id]['line_item'][$item_counter]['product_options'][$product_option_value_counter]['product_options_value'] = $product_option_values->rows;

                                        unset($product_option_value_id);
                                        unset($product_option_id);
                                        unset($product_option_values);
                                        $product_option_value_counter++;
                                    }
                                }
                                unset($product_options);
                                $item_counter++;    
                            } catch (Exception $ex) {

                            }
                        }
                }
                catch (Exception $ex) {

                }

                unset($order_id);
                unset($products);
            }
            return $order_query->rows;
        } else {
            return array();
        }
    }
    public function updateOrder($order_id, $tracking_details, $order_status_id, $notify, $override, $token)
    {
        if (preg_match('/[^a-z0-9]/i', $token)) {
            return array();
        }
        $columns_query     = "SHOW COLUMNS FROM `" . DB_PREFIX . "api_session` LIKE 'token'";
        $token_column_name = 'session_id';
        try {
            $columns_db_result = $this->db->query($columns_query);
            $columns_info      = $columns_db_result->row;
            if ($columns_info) {
                $token_column_name = 'token';
            }
        }
        catch (Exception $ex) {
        }
        $auth_query = "SELECT o.* FROM `" . DB_PREFIX . "api_session` AS o " . "WHERE o." . $token_column_name . " = '" . $token . "' " . "AND DATE_ADD(o.date_modified, INTERVAL 120 MINUTE) >= NOW()";
        try {
            $auth_db_result = $this->db->query($auth_query);
            $auth_info      = $auth_db_result->row;
            if ($auth_info) {
                // Verified
            } else {
                return array();
            }
        }
        catch (Exception $ex) {
        }
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if ($order_info) {
            $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $tracking_details, $notify, $override);
            $json['success'] = $this->language->get('Order has been updated successfully.');
            return $json;
        }
        return array();
    }
}