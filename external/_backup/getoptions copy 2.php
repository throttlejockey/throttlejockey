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

        $query = $db->query("
            SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
            ");
        // $query = $db->query("
        //     SELECT * FROM `oc_option` ORDER BY `oc_option`.`option_id` ASC
        //     ");


        // $rows = $query->rows;
        // print_r($rows);
        print('<pre>');
        print "OC 3.0.3.6 Options". "<br>";
        print "---". "<br>";

        foreach ($query->rows as $result) {
            $opts = getProductOptions($result['product_id'], $db, $customer, $config);

            ksort($opts[2]);
            // var_dump($opts[2]);
            // print_r($opts);
            $product_option_datax[ $opts[0] ] = [$opts[1], $opts[2]];
        }

        print('</pre>');

        ksort($product_option_datax);
        print "<pre>";
            print_r($product_option_datax);
        print "</pre>";

        $p = implode("<br>", $product_option_datax);
        // print "<hr><br>";
        print "<pre>";
            print $p;
        print "</pre>";



    function getProductOptions($product_id, $db, $customer, $config) {
        $product_option_data = array();
        $prodOptArr = [];
        $stringOut = "";

        $productNameQ = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $product_id");
        $productName = $productNameQ->row['name']; //['row']['name'];

        // print_r($productName);
        // print "<br/>$productName<br>";
        $stringOut .= "<br/>$productName<br>";

        $poptQuery = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "'";
        // print $poptQuery;

        $product_option_query = $db->query($poptQuery);


        // print $product_option_query;
        // print_r($product_option_query);

        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $povQuery = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'"; //" AND ovd.language_id = '" . (int)$config->get('config_language_id') . "' ORDER BY ov.sort_order";
            $product_option_value_query = $db->query($povQuery);


            $stringOut .= "  + " . $product_option['name'] . " <br>";
            // print "    values: <br>"; //" $product_option_value['name'] <br>";

            foreach ($product_option_value_query->rows as $product_option_value) {
                $prodOptArr[$product_option['name']][] = $product_option_value['name'];
                $stringOut .= "    - ". $product_option_value['name'] . " <br>";

            }
            // print_r($prodOptArr);
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
        // print $stringOut;
        return $product_option_data = [$productName, $stringOut, $prodOptArr]; //['product_name'] = $productName;
    }

 ?>