<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SHOW_IDS', true);
// define('SHOW_IDS', false);

$root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';
$root = "/home/b16aa05/oc3.throttlejockey.com/";

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

print "<pre>";
print "OC 3.0.3.6 Categories". "<br>";
print "---". "<br>";

$query = $db->query("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c.parent_id, c.sort_order, c.image FROM oc_category_path cp LEFT JOIN oc_category c ON (cp.path_id = c.category_id) LEFT JOIN oc_category_description cd1 ON (c.category_id = cd1.category_id) LEFT JOIN oc_category_description cd2 ON (cp.category_id = cd2.category_id) GROUP BY cp.category_id ORDER BY name;");
$categories = $query->rows;
// uasort($categories, 'compareName');

// $picTest = "/home1/throttle/oc3.throttlejockey.com/image/data/2018TH_Truck-01.jpg";
// // $poo = isImage($picTest);
// $poo = is_image($picTest);
// print "$picTest valid? : $poo";'/home/b16aa05/public_html/'
// $rootPath = "/home/b16aa05/public_html/image/";
// $rIdx = 0;

$categoriesArr = [];

foreach ($categories as $result) {
    // print_r($result);
    $catName = mb_convert_encoding($result['name'], "HTML-ENTITIES", "UTF-8");
    $category_id  = $result['category_id'];
    
    $categoriesArr[$catName] = $category_id;

}

var_export($categoriesArr);
 

$p3View = '$categories3 = ' . var_export($categoriesArr, 1);
print $p3View;

$p3File = "<? \n\r" . '$categories3 = ' . var_export($categoriesArr, 1) . "\n\r?>";

$file = './data/cats3.php';
$data = $p3File;
file_put_contents($file, $data);
