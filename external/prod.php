<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SHOW_IDS', false);

// ftp://ftp.throttlejockey.com//oc3.throttlejockey.com
$root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';
$root = "/home1/throttle/oc3.throttlejockey.com/";
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

$prod_id = 0;
if ( $_GET && $_GET['prod_id'] ) {
    $prod_id = $_GET['prod_id'];
} else {
    print "Add ?prod_id=#####";
    return;
}



$query = $db->query("
    SELECT p.product_id, pd.name FROM product p INNER JOIN product_description pd ON p.product_id = pd.product_id WHERE p.product_id = $prod_id ORDER BY pd.name
    ");

$products = [];

print '<pre>';
print "OC 3.0.3.6 Products". "<br>";
print "---". "<br>";

foreach ($query->rows as $product) {
    $products[$product['name']] = $product['product_id'];
}

$p3View = '$products3 = ' . var_export($products, 1);
print $p3View;

$p3File = "<? \n\r" . '$products3 = ' . var_export($products, 1) . "\n\r?>";

$file = './data/prods3.php';
$data = $p3File;
file_put_contents($file, $data);
 
  
?>