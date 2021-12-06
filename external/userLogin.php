<?php
// opcache_reset();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SHOW_IDS', false);


// print "<h1>";
// print "This is Update asdfasdfasdasdfw4345345fasdfasdfffff";
// print "</h1>";

// ftp://ftp.throttlejockey.com//oc3.throttlejockey.com
// $root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';

$root = "/home/b16aa05/oc3.throttlejockey.com/";

// print ( $root );
// print( getcwd() );

// Configuration
if (file_exists($root . 'config.php')) {
    require_once($root . 'config.php');
    // print "config.php Found!";
} else {
    print ('config.php not found :(');
}

// Install
// if (!defined('DIR_APPLICATION')) {
// 	header('Location: install/index.php');
// 	exit;
// }

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// start('catalog');




// if (file_exists($root . 'system/startup.php')) {
//     require_once($root . 'system/startup.php');
//     // print "startup.php Found!";
// }
// start('catalog');
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

$username = "chrisroe@throttlejockey.com";
$password = "tacobell3";

print "Customer is logged in? ";
// $customer->logout();
print $customer->isLogged();

if ($customer->login( $username, $password ) ) {
    echo 'User was logged in successfully';
} else {
    echo 'User not found or username or password do not match.';
}

print "<pre>";
print_r($customer);
print "</pre>";


print "Customer is logged in? ";
print $customer->isLogged();
// $customer->logout();
// $user = new Cart::User($registry);
// $user = new User($registry);

// $user = new User();
// if ($user->login('chriswroe@gmail.com','Tacobell!23')) {
//     echo 'User was logged in successfully';
// } else {
//     echo 'User not found or username or password do not match.';
// }

?>