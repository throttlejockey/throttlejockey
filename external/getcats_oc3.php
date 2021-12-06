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

print('<pre>');
print "OC 3.0.3.6 Categories". "<br>";
print "---". "<br>";
 

function getCategoriesForProduct($db, $product_id=null) {
    $product_categories = [];
    $pa = 0;
    $sql = "SELECT * FROM `" . DB_PREFIX . "product_to_category`";
 
    // $query = mysqli_query( $db, $sql );
    $query = $db->query($sql);

    $output_str = "";

    // For each row we look up the products name. 
    foreach ($query->rows as $result) {
        $product_id   = $result['product_id'];
        $category_id  = $result['category_id'];
        // $sort_order  = $result['sort_order'];
        $product_name_query = $db->query("SELECT `name` FROM `" . DB_PREFIX . "product_description` WHERE product_id = $product_id");
        $product_name = $product_name_query->row['name']; //['row']['name'];
 
        // print "ProductName : $product_name";
        // br();
        // print "ProductID : $product_id";
        // br();
        // print "CategoryID : $category_id";
        // br();
        // print "Sort Order : $sort_order";
        // br(2);

        // $q2 = $db->query( "SELECT * FROM `category_description` WHERE `category_id` = $category_id");
        $q2 = $db->query( "SELECT * FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = $category_id");
        // print_r ($q2->rows);
        // print_r($q2);
        // usort($q2, 'intcmp');

        foreach ($q2->rows as $cats) {
            $product_categories[$product_name][] = [ "pid" => $product_id, "category_id" => $category_id, "category_name" => $cats['name'] ];
            // print_r($cats);
        }
 
    }
    
    br();

    return $product_categories;

}


print "<pre>";


$pcats = getCategoriesForProduct( $db );

ksort($pcats);

// print_r($pcats);


foreach ($pcats as $product => $categories) {
    br();
    print "<b>$product</b>";
    br();
    // print "  <b>Categories</b>";
    uasort($categories, 'cmp');
    // print_r ($categories);
    br();
    if(!$categories) { continue; }

    foreach($categories as $category) {
 
        $fullCatData = getCategory($db, $config, strval($category['category_id']));

        $catName = mb_convert_encoding($fullCatData['path'] . ' &gt; ' . $fullCatData['name'], "HTML-ENTITIES", "UTF-8");

        $IDStr = "";
        if(SHOW_IDS)
            $IDStr = "[".$category['category_id']."] ";
        // print_r($category);
        print "  - $IDStr $catName "; //$category['category_name']; 
        // print_r($catName);

        // print_r($category);
        // print "  - [".$category['category_id']."] " . $catName; //$category['category_name'];
        br();
        // print "  - Category Id: ". $category['category_id'];
        // br();
    }
}


// foreach ($pcats as $product => $categories) {
//     br();
//     print "<b>$product</b>";
//     br();
//     print "  <b>Categories</b>";
//     uasort($categories, 'cmp');
//     // print_r ($categories);
//     br();
//     if(!$categories) { continue; }

//     foreach($categories as $category) {
 
//         // uasort($category, 'cmp');
//         $IDStr = "";
//         if(SHOW_IDS)
//             $IDStr = "[".$category['category_id']."] ";
//         // print_r($category);
//         print "  - $IDStr" . $category['category_name']; 
//         br();
//         // print "  - Category Id: ". $category['category_id'];
//         // br();
//     }
// }

    br();
// }

print "</pre>";

function getCategory($db, $config, $category_id) {

    // print "<br/> Cat ID: |$category_id|";
    // $queryStr = "SELECT DISTINCT *, ( SELECT GROUP_CONCAT(cd1.name ORDER BY level separator ' &gt; ') FROM oc_category_path cp LEFT JOIN oc_category_description cd1 ON ( cp.path_id = cd1.category_id AND cp.category_id != cp.path_id ) WHERE cp.category_id = c.category_id GROUP BY cp.category_id ) AS path, ( SELECT keyword FROM oc_url_alias WHERE query = 'category_id=$category_id' ) AS keyword FROM oc_category c LEFT JOIN oc_category_description cd2 ON ( c.category_id = cd2.category_id ) WHERE c.category_id = '$category_id'";
    $queryStr = "SELECT DISTINCT *, ( SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR ' &gt; ') FROM oc_category_path cp LEFT JOIN oc_category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id GROUP BY cp.category_id) AS path FROM oc_category c LEFT JOIN oc_category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '$category_id'";
    $query = $db->query($queryStr);
 
    return $query->row;
}

function intcmp($aa,$bb) {
    print "intcmp a = $aa";
    print "intcmp b = $bb";
    $a = $aa['category_id'];
    $b = $bb['category_id'];
    return ($a-$b) ? ($a-$b)/abs($a-$b) : 0;
}

function compareName($a, $b) {
    return strnatcmp(strtolower($a['name']), strtolower($b['name']));
}

function cmp($a, $b) {
    // print_r($a);br();
    // print_r($b);br();
    return (int)$b['category_id'] - (int)$a['category_id'];
}
 
function br($num = 1) {
    // Default is 1 if no input;
    $numlines = (int)$num;
    for( $i=0; $i<=$numlines; $i++) {
        print "<br/>";
    }
}
 

?>