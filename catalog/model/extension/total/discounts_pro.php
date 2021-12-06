<?php
class ModelExtensionTotalDiscountsPro extends Model {
    public function getTotal($total) {
        $cart_products = $this->cart->getProducts();

        $this->load->model('account/customer_group');
        $customer_groups = $this->model_account_customer_group->getCustomerGroups();

        $customer_group_id = !empty($this->customer->getGroupId()) ? $this->customer->getGroupId() : 1;

        $discounts_types = array('category', 'manufacturer', 'product');

        foreach ($discounts_types as $key => $disc_type) {

            $discount = array();

            if($disc_type == 'category')
            {
                $function_name = 'group_products_by_categories';
                $key_id = 'category_id';
            }
            elseif($disc_type == 'manufacturer')
            {
                $function_name = 'group_products_by_manufacturers';
                $key_id = 'manufacturer_id';
            }
            elseif($disc_type == 'product')
            {
                $function_name = 'group_products_by_products';
                $key_id = 'product_id';
            }

            $config = unserialize(base64_decode($this->config->get('discounts_pro_'.$disc_type)));
            
            if(!empty($config) && !empty($cart_products) && $this->config->get('discounts_pro_status') && $this->config->get('discounts_pro_'.$disc_type.'_status'))
            {

                if($disc_type != 'product')
                    $products_group = $this->{$function_name}($cart_products);

                foreach ($config as $key => $conf) {
                    if(!empty($conf['status']))
                    {
                        $unlimited = (empty($conf['from']) && empty($conf['to'])) || (date('Y-m-d') >= $conf['from'] && empty($conf['to']));
                        $in_time = (date('Y-m-d') >= $conf['from']) && (date('Y-m-d') <= $conf['to']);
                        $customer_group = empty($conf['customer_group']) || (!empty($conf['customer_group']) && $conf['customer_group'] == $customer_group_id);

                        if( ($unlimited || $in_time) && $customer_group && !empty($conf['discount']) && !empty($conf['unit']))
                        {
                            if($disc_type != 'product')  //Discounts by category or manufacturer
                            {
                                foreach ($products_group as $id_type => &$pbc) {
                                    $all_categories_manufacturers = empty($conf[$key_id]);
                                    $in_category_manufacturer = !empty($conf[$key_id]) && $id_type == $conf[$key_id];
                                    $number_correct = $pbc['num_total'] >= $conf['unit'];
                                    if ($number_correct && ($all_categories_manufacturers || $in_category_manufacturer))
                                    {
                                        $discount = $this->get_discounts($conf, $pbc);
                                        if(!empty($discount['total']))
                                        {
                                            $total['totals'][] = array(
                                                'code'       => 'discounts_pro_'.$disc_type,
                                                'title'      => !empty($conf['message'][$this->config->get('config_language_id')]) ? $conf['message'][$this->config->get('config_language_id')].' x'.$discount['num_discounts'] : 'Discount'.' x'.$discount['num_discounts'],
                                                'text'       => '-'.$this->currency->format($discount['total'], $this->session->data['currency']),
                                                'value'      => '-'.$discount['total'],
                                                'sort_order' => !empty($conf['sort_order']) && is_numeric($conf['sort_order']) ? $conf['sort_order'] : 3
                                            );
                                            $total['total'] -= $discount['total'];

                                            //Devman Extensions - info@devmanextensions.com - 2017-03-01 18:36:58 - Remove repeats products that was included in previus discounts
                                            unset($products_group[$id_type]);
                                            $this->remove_discount_repeats($products_group, $pbc);
                                        }
                                    }
                                }
                            }
                            else //Discount by products
                            {
                                $product_with_discount = array(
                                    'num_total' => 0,
                                    'products' => array()
                                );

                                foreach ($cart_products as $key => $prod) {
                                    if(in_array($prod['product_id'], $conf['products']))
                                    {
                                        $product_with_discount['num_total'] += $prod['quantity'];
                                        $product_with_discount['products'][] = $prod;
                                    }
                                }
                                
                                if(!empty($product_with_discount['products']))
                                    $product_with_discount['products'] = $this->subval_sort($product_with_discount['products'], 'price');
                                
                                if ($product_with_discount['num_total'] >= $conf['unit'])
                                {
                                    $discount = $this->get_discounts($conf, $product_with_discount);

                                    if(!empty($discount['total']))
                                    {
                                        $total['totals'][] = array(
                                            'code'       => 'discounts_pro_'.$disc_type,
                                            'title'      => !empty($conf['message'][$this->config->get('config_language_id')]) ? $conf['message'][$this->config->get('config_language_id')].' x'.$discount['num_discounts'] : 'Discount'.' x'.$discount['num_discounts'],
                                            'text'       => '-'.$this->currency->format($discount['total'], $this->session->data['currency']),
                                            'value'      => '-'.$discount['total'],
                                            'sort_order' => !empty($conf['sort_order']) && is_numeric($conf['sort_order']) ? $conf['sort_order'] : 3
                                        );
                                        $total['total'] -= $discount['total'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if( $this->config->get('discounts_pro_merge_status') )
        {
            // $this->log->write(  $total );
            $tmpArr=[];
            $valArr=[];
            $count=1;
            foreach( $total['totals'] as $item ) {

                
                $_code  = $item['code'];          
                $_split_title = explode("x", $item['title']);
                $_title = $_split_title[0];
                $_discount_count = array_pop($_split_title);
                $_text  = $item['text'];
                $_value = $item['value'];
                $_sort_order = $item['sort_order'];
    
                // This key exists already.
                if( array_key_exists($_title, $valArr) ) {
                    // $this->log->write( "$_title Exists.");
                    if( array_key_exists('value', $valArr[$_title] ) ) {
                        // $this->log->write( "$_value exists.");  
                        $valArr[$_title]['value'] = $valArr[$_title]['value'] + $_value;
                        $valArr[$_title]['count'] = $valArr[$_title]['count'] + $_discount_count;
                    }  

                } else {
                    $valArr[$_title]['value'] = $_value;
                    $valArr[$_title]['count'] = $_discount_count;
                    // $this->log->write( "$_title created.");
                }

                $valArr[$_title]['code'] = $_code;
                $valArr[$_title]['title'] = $_title;
                $valArr[$_title]['text'] = $_text;
                $valArr[$_title]['sort_order'] = $_sort_order;
            
                // $this->log->write( "discount_count: $_discount_count" );
                // $this->log->write(  $valArr );
                $count++;
            }
            
            $nTotals = [];
            foreach ($valArr as $title => $array ) {
                $countText = $this->config->get('discounts_pro_merged_addnum') ? " x".$array['count'] : '';
                $array['title'] = $array['title'] . ' ' . $countText;
                $nTotals[] = $array;
            }
            $total['totals'] = $nTotals;
            // $this->log->write($nTotals);
        } // discounts_pro_merge_status


    }

    public function group_products_by_categories($products)
    {
        $categories = array();

        foreach ($products as $key => $pro) {
            $found = false;

            $product_categories = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = ".$pro['product_id']);
            $pro['categories'] = $product_categories->rows;

            foreach ($pro['categories'] as $key2 => $cat)
            {
                if (!isset($categories[$cat['category_id']]))
                {
                    $categories[$cat['category_id']] = array();
                    $categories[$cat['category_id']]['num_total'] = 0;
                }

                $categories[$cat['category_id']]['num_total'] += $pro['quantity'];
                $categories[$cat['category_id']]['products'][] = $pro;
            }
        }

        foreach ($categories as $category_id => $pro_cat) {
            $categories[$category_id]['products'] = $this->subval_sort($pro_cat['products'], 'price');
        }            

        return $categories;
    }

    public function group_products_by_manufacturers($products)
    {
        $final_products = array();

        foreach ($products as $key => $pro) {

            $product_manufacturer = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "product WHERE product_id = ".$pro['product_id']);

            if(!empty($product_manufacturer->row['manufacturer_id']))
            {
                $manufacturer_id = $product_manufacturer->row['manufacturer_id'];

                if(!isset($final_products[$manufacturer_id]))
                {
                    $final_products[$manufacturer_id]['num_total'] = 0;
                    $final_products[$manufacturer_id]['products'] = array();
                }

                $final_products[$manufacturer_id]['num_total'] += $pro['quantity'];
                $final_products[$manufacturer_id]['products'][] = $pro;
            }
        }

        foreach ($final_products as $product_id => $pro_cat) {
            $final_products[$product_id]['products'] = $this->subval_sort($pro_cat['products'], 'price');
        }

        return $final_products;
    }

    public function get_discounts($conf_disc, $products)
    {
        $num_discounts = floor($products['num_total']/$conf_disc['unit']);

        $less_price = 99999999999999999999999;

        $discount = array('total' => 0, 'num_discounts' => 0);

        foreach ($products['products'] as $key => $pro) 
        {
            if ($num_discounts != 0)
            {
                $less_price = $pro['price'];

                $product_discounts = floor($pro['quantity']/$conf_disc['unit']);

                if ($product_discounts < 1 || empty($conf_disc['repeat']))
                    $product_discounts = 1;

                $num_discounts -= $product_discounts;

                if (!empty($conf_disc['include_tax']))
                    $price = $this->tax->calculate($pro['price'], $pro['tax_class_id'], $this->config->get('config_tax'));
                else
                    $price = $pro['price'];

                if($conf_disc['type'] == 'percentage' && !empty($conf_disc['discount']))
                {
                    $discount['total'] += (($price * $conf_disc['discount']) / 100)*$product_discounts;
                    $discount['num_discounts'] += $product_discounts;
                }
                elseif($conf_disc['type'] == 'fixed' && !empty($conf_disc['discount']))
                {
                    $temp_discount = $conf_disc['discount']*$product_discounts;

                    $discount['total'] += !empty($conf_disc['include_tax']) ? $this->tax->calculate($temp_discount, $pro['tax_class_id'], $this->config->get('config_tax')) : $temp_discount;

                    $discount['num_discounts'] += $product_discounts;
                }
            }
        }

        return $discount;
    }

    public function remove_discount_repeats(&$array_discounts, $discount_applied)
    {
        $prev_discount_products = array();

        foreach ($discount_applied['products'] as $key => $prod) {
           $prev_discount_products[] = $prod['product_id'];
        }

        foreach ($array_discounts as $key => $discounts) {
            foreach ($discounts['products'] as $key2 => $prod) {
                if(in_array($prod['product_id'], $prev_discount_products) && !empty($array_discounts[$key]) && array_key_exists('num_total', $array_discounts[$key]))
                {
                    $array_discounts[$key]['num_total'] -= $prod['quantity'];
                    if($array_discounts[$key]['num_total'] == 0)
                        unset($array_discounts[$key]);
                }
            }
        }
    }

    function subval_sort($a,$subkey) {
        foreach($a as $k=>$v) {
            $b[$k] = strtolower($v[$subkey]);
        }

        asort($b);

        foreach($b as $key=>$val) {
            $c[] = $a[$key];
        }

        return $c;
    }     
}
?>