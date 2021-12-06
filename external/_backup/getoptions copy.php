<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// ftp://ftp.throttlejockey.com//oc3.throttlejockey.com
$root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';
$root = "/home1/throttle/oc3.throttlejockey.com/";

// print ( $root );
// print( getcwd() );

if (file_exists($root . 'config.php')) {
    require_once($root . 'config.php');
    // print "config.php Found!";
} else {
    print ('config.php not found :(');
}

if (file_exists($root . 'system/startup.php')) {
    require_once($root . 'system/startup.php');
    // print "startup.php Found!";
}

if (file_exists($root . 'system/library/cart/user.php')) {
    require_once($root . 'system/library/cart/user.php');
        // print "user.php Found!";
} else {
    // print "user.php not found !";
}

if (file_exists($root . 'catalog/model/catalog/product.php')) { ///library/cart/user.php')) {
    require_once($root . 'catalog/model/catalog/product.php');
        // print "product.php Found!";
} else {
    // print "product.php not found !";
}

$registry = new Registry();

$loader = new Loader($registry);
$registry->set('load', $loader);

$config = new Config();
$registry->set('config', $config);

$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$customer = new Cart\Customer($registry);

// SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC


        // $query = $db->query("SELECT * FROM " . DB_PREFIX . "oc_product" WHERE product_id = '" . (int)$product_id . "'");
        // $rows = $query->rows;

        // $query = $db->query("
        //     SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
        //     ");

        // $query = $db->query("
        //             SELECT * FROM `oc_option` ORDER BY `oc_option`.`option_id` ASC
        //             ");
        $query = $db->query("
            SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
            ");
        // $rows = $query->rows;
        // print_r($rows);
print('<pre>');
        print "OC 3.0.3.6 Options". "<br>";
        print "---". "<br>";
        // print_r($query->rows);
        foreach ($query->rows as $result) {

            $productNameQ = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = '". $result['product_id'] . "'");
            $productName = $productNameQ->row['name']; //['row']['name'];

            print $productName;

            $opts = getProductOptions($result['product_id'], $db, $customer, $config);
            $product_option_datax[ $result['product_id']] = $opts;

            // print_r ($opts);
            print "<hr>";
        }
            // print_r ($product_option_datax);

print('</pre>');


        // asort($product_option_datax);
        //     print_r($product_option_datax);
        // print "</pre>";

        $p = implode("<br>", $product_option_datax);
        // print "<hr><br>";
        print "<pre>";
            // print $p;
        print "</pre>";



    function getProductOptions($product_id, $db, $customer, $config) {
        $product_option_data = array();
        $productNameQ = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $product_id");
        $productName = $productNameQ->row['name']; //['row']['name'];

        // print_r($productName);
        // print $productName;
        $poptQuery = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "'";
        print $poptQuery;

        $product_option_query = $db->query($poptQuery);


        // print $product_option_query;
        // print_r($product_option_query);
        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $povQuery = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'"; //" AND ovd.language_id = '" . (int)$config->get('config_language_id') . "' ORDER BY ov.sort_order";
            $product_option_value_query = $db->query($povQuery);
            // $product_option_value_query = $db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$config->get('config_language_id') . "' ORDER BY ov.sort_order");
            // $povQuery = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'"; //" AND ovd.language_id = '" . (int)$config->get('config_language_id') . "' ORDER BY ov.sort_order";
            //SELECT * FROM oc_product_option_value pov LEFT JOIN oc_option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN oc_option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '95' AND pov.product_option_id = '628'Array

            // print($povQuery);
        // print_r($product_option_value_query);

            print "Product Name: $productName <br>";
            print "Product Option: " . $product_option['name'] . " <br>";
            print "Option Values: <br>"; //" $product_option_value['name'] <br>";

            foreach ($product_option_value_query->rows as $product_option_value) {

                print "    " . $product_option_value['name'] . " <br>";


                // print  $product_option."<br>";
                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'option_value_id'         => $product_option_value['option_value_id'],
                    'name'                    => $product_option_value['name'],
                    'image'                   => $product_option_value['image'],
                    'quantity'                => $product_option_value['quantity'],
                    'subtract'                => $product_option_value['subtract'],
                    'price'                   => $product_option_value['price'],
                    'price_prefix'            => $product_option_value['price_prefix'],
                    'weight'                  => $product_option_value['weight'],
                    'weight_prefix'           => $product_option_value['weight_prefix']
                );
            }

            $product_option_data[] = array(
                'product_option_id'    => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $product_option['option_id'],
                'name'                 => $product_option['name'],
                'type'                 => $product_option['type'],
                'value'                => $product_option['value'],
                'required'             => $product_option['required']
            );
        }

        return $product_option_data; //['product_name'] = $productName;
    }

     function getCategory($category_id, $db, $customer, $config) {

        if ($customer->isLogged()) {
            $customer_group_id = $customer->getCustomerGroupId();
        } else {
            $customer_group_id = $config->get('config_customer_group_id');
        }

        // print "PID: $product_id";

        $query = $db->query("SELECT `name` FROM `oc_category_description` WHERE category_id = $category_id");


        if ($query->num_rows) {
            // return array('')
            // return ($query->row['name']);
//            print "$category_id] " . $query->row['name'] . "<br>";
            return $query->row['name'];
            // return array(
            //     'product_id'       => $query->row['product_id'],
            //     'name'             => $query->row['name'],
            //     'description'      => $query->row['description'],
            //     'meta_description' => $query->row['meta_description'],
            //     'meta_keyword'     => $query->row['meta_keyword'],
            //     'tag'              => $query->row['tag'],
            //     'model'            => $query->row['model'],
            //     'sku'              => $query->row['sku'],
            //     'upc'              => $query->row['upc'],
            //     'ean'              => $query->row['ean'],
            //     'jan'              => $query->row['jan'],
            //     'isbn'             => $query->row['isbn'],
            //     'mpn'              => $query->row['mpn'],
            //     'location'         => $query->row['location'],
            //     'quantity'         => $query->row['quantity'],
            //     'stock_status'     => $query->row['stock_status'],
            //     'image'            => $query->row['image'],
            //     'manufacturer_id'  => $query->row['manufacturer_id'],
            //     'manufacturer'     => $query->row['manufacturer'],
            //     'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
            //     'special'          => $query->row['special'],
            //     'reward'           => $query->row['reward'],
            //     'points'           => $query->row['points'],
            //     'tax_class_id'     => $query->row['tax_class_id'],
            //     'date_available'   => $query->row['date_available'],
            //     'weight'           => $query->row['weight'],
            //     'weight_class_id'  => $query->row['weight_class_id'],
            //     'length'           => $query->row['length'],
            //     'width'            => $query->row['width'],
            //     'height'           => $query->row['height'],
            //     'length_class_id'  => $query->row['length_class_id'],
            //     'subtract'         => $query->row['subtract'],
            //     'rating'           => round($query->row['rating']),
            //     'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
            //     'minimum'          => $query->row['minimum'],
            //     'sort_order'       => $query->row['sort_order'],
            //     'status'           => $query->row['status'],
            //     'date_added'       => $query->row['date_added'],
            //     'date_modified'    => $query->row['date_modified'],
            //     'viewed'           => $query->row['viewed']
            // );
        } else {
            return false;
        }
    }

?>