<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SHOW_IDS', false);

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

        $query = $db->query("
            SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
            ");

        // $rows = $query->rows;
        print '<pre>';
        print "OC 3.0.3.6 Products". "<br>";
        print "---". "<br>";
        // print "UHM";
        // print_r($query->rows);
        $o = [];
        foreach ($query->rows as $result) {
            $pid=$result['product_id'];
            $pname = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $pid");
 
            // print_r ($result);
            $product_data[$result['product_id']] = $result['product_id'];
            $productInfo[$result['product_id']] = getProduct($result['product_id'], $db, $customer, $config);
 
        }
        print '</pre>';
        // $db->query("
        //      SELECT * FROM
        //      `".DB_PREFIX."`product`.
        //       ".DB_PREFIX."`ORDER BY `".DB_PREFIX."product_id" ASC) ENGINE=MyISAM DEFAULT CHARSET=utf8
        //         "
        // ");

    usort( $productInfo, 'compareName');
        
    foreach( $productInfo as $product) {
        $pIdStr = "";
        $product_id=$product['product_id'];
        if(SHOW_IDS) 
            $pIdStr = "$product_id] ";
        print $pIdStr . $product['name'] . "<br>";
    }
    // print('<pre>');
    // print_r($productInfo);
    // // echo implode("<br>", $productInfo);
    // // print_r($o);
    // // print_r($rows);
    // print('</pre>');

// $products = new ModelCatalogProduct();

// $user = new Cart::User($registry);
// $user = new User($registry);

// $user = new User();
// if ($user->login('chriswroe@gmail.com','Tacobell!23')) {
//     echo 'User was logged in successfully';
// } else {
//     echo 'User not found or username or password do not match.';
// }

     function getProduct($product_id, $db, $customer, $config) {

        if ($customer->isLogged()) {
            $customer_group_id = $customer->getCustomerGroupId();
        } else {
            $customer_group_id = $config->get('config_customer_group_id');
        }

        // print "PID: $product_id";

        $query = $db->query("SELECT * FROM `oc_product_description` WHERE product_id = $product_id");
        // $query = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $product_id");
        // $pname = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $product_id");



        if ($query->num_rows) {
            // print_r($query->num_rows);
            // return array('')
            // return ($query->row['name']);
            // $pIdStr = "";

            // if(SHOW_IDS) 
            //     $pIdStr = "$product_id] ";

            // print $pIdStr . $query->row['name'] . "<br>";
            return $query->row;
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
    function compareName($a, $b) {
        return strnatcmp(strtolower($a['name']), strtolower($b['name']));
    }
?>