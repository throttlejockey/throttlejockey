<?php
// include('config.php');
if (file_exists($root . 'oc_bootstrap.php')) {
    require_once($root . 'oc_bootstrap.php');
    // print "config.php Found!";
} else {
    print ('oc_bootstrap.php not found :(');
}


$query = $db->query("
            SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
            ");


            print "<pre>";
            print "OC 3.0.3.6 Categories". "<br>";
            print "---". "<br>";
            print "</pre>";

        foreach ($query->rows as $result) {
            $opts = getProductOptions($result['product_id'], $db, $customer, $config);
            ksort($opts[2]);
            $product_option_datax[ $opts[0] ] = [$opts[2]];
        }

        print "</pre>";

        ksort($product_option_datax);
        //     print_r($product_option_datax);
        // print "</pre>";


        print "<pre>";
            foreach ($product_option_datax as $key => $value) {
                // print "$ProdsOptions";
                print "Product: $key </br>";

                if($value) {

                    // print_r ($value);

                    foreach ($value[0] as $key2 => $value2) {

                        print "  - Option: $key2 </br>";

                        if($value2) {
                            asort($value2);
                            foreach ($value2 as $k3 => $v3) {
                                print "    - $v3 </br>";
                            }

                            print "</br>";
                        }

                    }
                    // print_r($product_option_datax);
                }

            }
        print "</pre>";


        // $p = implode("<br>", $product_option_datax);
        // print "<hr><br>";
        print "<pre>";
            // print $p;
        print "</pre>";




    function getProductOptions($product_id, $db, $customer, $config) {
        $product_option_data = array();
        $productNameQ = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $product_id");
        $productName = $productNameQ->row['name']; //['row']['name'];
        $prodOptArr = [];
        $stringOut = "";

        // print_r($productName);
        $stringOut .=  "<br/>$productName<br>";

        $poptQuery = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "'";
        // print $poptQuery;

        $product_option_query = $db->query($poptQuery);


        // print $product_option_query;
        // print_r($product_option_query);
        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $povQuery = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'"; //" AND ovd.language_id = '" . (int)$config->get('config_language_id') . "' ORDER BY ov.sort_order";
            $product_option_value_query = $db->query($povQuery);

            // print "  + " . $product_option['name'] . " <br>";
            $stringOut .= "  + " . $product_option['name'] . " <br>";
            // print "    values: <br>"; //" $product_option_value['name'] <br>";

            foreach ($product_option_value_query->rows as $product_option_value) {

                $prodOptArr[$product_option['name']][] = $product_option_value['name'];
                // print "    - ". $product_option_value['name'] . " <br>";
                $stringOut .= "    - ". $product_option_value['name'] . " <br>";

                // print  $product_option."<br>";
                // $product_option_value_data[] = array(
                //     'product_option_value_id' => $product_option_value['product_option_value_id'],
                //     'option_value_id'         => $product_option_value['option_value_id'],
                //     'name'                    => $product_option_value['name'],
                //     'image'                   => $product_option_value['image'],
                //     'quantity'                => $product_option_value['quantity'],
                //     'subtract'                => $product_option_value['subtract'],
                //     'price'                   => $product_option_value['price'],
                //     'price_prefix'            => $product_option_value['price_prefix'],
                //     'weight'                  => $product_option_value['weight'],
                //     'weight_prefix'           => $product_option_value['weight_prefix']
                // );
            }

            // print "<br>";
            $stringOut .= "<br>";
            // $product_option_data[] = array(
            //     // 'product_name'         => $productName,
            //     'product_option_id'    => $product_option['product_option_id'],
            //     'product_option_value' => $product_option_value_data,
            //     'option_id'            => $product_option['option_id'],
            //     'name'                 => $product_option['name'],
            //     'type'                 => $product_option['type'],
            //     'value'                => $product_option['value'],
            //     'required'             => $product_option['required']
            // );
        }

        return $product_option_data = [$productName, $stringOut, $prodOptArr]; //['product_name'] = $productName;
    }


 ?>