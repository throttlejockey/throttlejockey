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

    print "<pre>";
    print "OC 3.0.3.6 Categories". "<br>";
    print "---". "<br>";


    // $query = $db->query("SELECT * FROM oc_category_description ocd");
    // $query = $db->query("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c.parent_id, c.sort_order FROM oc_category_path cp LEFT JOIN oc_category c ON (cp.path_id = c.category_id) LEFT JOIN oc_category_description cd1 ON (c.category_id = cd1.category_id) LEFT JOIN oc_category_description cd2 ON (cp.category_id = cd2.category_id) GROUP BY cp.category_id ORDER BY name");
    $query = $db->query("SELECT ojss.skin_id, ojss.setting_name, ojss.serialized, ojss.setting_value, ojs.skin_name FROM oc_journal3_skin_setting ojss INNER JOIN oc_journal3_skin ojs ON ojs.skin_id = ojss.skin_id");
    $categories = $query->rows;
    print_r($query->rows);
    // uasort($categories, 'compareName');