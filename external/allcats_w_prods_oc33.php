<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SHOW_IDS', false);
// define('SHOW_IDS', false);
// ftp://ftp.throttlejockey.com//oc3.throttlejockey.com
// $root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';
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


// $query = $db->query("SELECT * FROM oc_category_description ocd");
// $query = $db->query("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c.parent_id, c.sort_order FROM oc_category_path cp LEFT JOIN oc_category c ON (cp.path_id = c.category_id) LEFT JOIN oc_category_description cd1 ON (c.category_id = cd1.category_id) LEFT JOIN oc_category_description cd2 ON (cp.category_id = cd2.category_id) GROUP BY cp.category_id ORDER BY name");

// $query = $db->query("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c.parent_id, c.sort_order, c.image FROM oc_category_path cp LEFT JOIN oc_category c ON (cp.path_id = c.category_id) LEFT JOIN oc_category_description cd1 ON (c.category_id = cd1.category_id) LEFT JOIN oc_category_description cd2 ON (cp.category_id = cd2.category_id) GROUP BY cp.category_id ORDER BY name;");
// $queryStr = ("SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' &gt; ') AS name, c.parent_id, c.sort_order, c.image FROM oc_category_path cp LEFT JOIN oc_category c ON (cp.path_id = c.category_id) LEFT JOIN oc_category_description cd1 ON (c.category_id = cd1.category_id) LEFT JOIN oc_category_description cd2 ON (cp.category_id = cd2.category_id) GROUP BY cp.category_id ORDER BY name;");
$queryStr =  ("SELECT * FROM oc_category");

$query = $db->query( $queryStr );

// print "Query String: <br>";
// print $queryStr;
// print "<br>";

$categories = $query->rows;
uasort($categories, 'compareName');
 
$rIdx = 0;

foreach ($categories as $result) {
    // print_r($result);
    $catName = mb_convert_encoding($result['name'], "HTML-ENTITIES", "UTF-8");
    print "<br>";
    $category_id  = $result['category_id'];
    // $catName = $result['name'];


        $IDStr = "";
        if(SHOW_IDS)
            $IDStr = "[".$result['category_id']."] ";
        // print_r($category);

        // print "<span><b>";
        print "  - $IDStr $catName "; //$category['category_name'];
        // print "</b></span>";

        if(empty($result['image']) ) { continue; }
        print "<br>image: ";
        print $result['image'] ?  "<img src='".HTTPS_SERVER . "image/" . $result['image'] . "'>" : "";    
    
        
        // $products = getProductsByCategoryId( $result['category_id'], $db );    
        // // print_r($products);    
        // foreach ($products as $product) {
 
        //     // product_id, name
        //     $prodName = $product['name'];
        //     print "<br>";
        //     // print "$prodName!";
        //     // print "<br>";
        //     $IDStr = "";
        //     // if(SHOW_IDS)
        //         // $IDStr = "[".$product['product_id']."] ";            
            
        //     print "      $IDStr $prodName "; //$category['category_name'];

        //     print "<br>";
        //     // print "image: " . $product['image'] ? $product['image'] : "";

        // }
        
        print "<br>";
        // br();

    // print "hi";

}


function getProductsByCategoryId($category_id, $db) {
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

    return $query->rows;
}


// foreach ($categories as $result) {
//     $output = "";
//     $category_id  = $result['category_id'];
//     $catName = $result['name'];
//     $catImage = $result['image'];
 

//     if( $catImage === ' ' || empty($catImage)) {
//     // if(empty($catImage)) { 
//         // br();
//         // print "no cat image, continue";
//         // br();
//         // $rIdx--;
//         continue;
//     }
    
//     $rIdx++;
//     $IDStr = "";
//     if(SHOW_IDS) $IDStr = "[".$category['category_id']."] | ";

//         // print_r($category);
//         $output .= "$IDStr $catName"; //$category['category_name'];
//         $output .= "<br/>"; //br();
//         $errorImg = "<span style='color: red;'> $catImage </span>";
//         // print "<br>catImage:  |$catImage| <br>";
//         if( !is_image( $catImage ) ) {
//             print $output;
//             print "Image: $errorImg";
//             br();
//         } else {
//             // print "Image: $catImage";
//         }
//         // $poo = isImage($catImage);
//         // print "$catImage valid? : $poo";

//         // $ImgValid = is_image( $catImage );
//         // print "$catImage valid? : $poo";
//         // br();
//         // print "hi";

// }

print "</pre>";

function compareName($a, $b) {
    return strnatcmp(strtolower($a['name']), strtolower($b['name']));
}

function br($num = 1) {
    // Default is 1 if no input;
    $numlines = (int)$num;
    for( $i=0; $i<=$numlines; $i++) {
        print "<br/>";
    }
}

function isImage($pathToFile) {
  if( false === exif_imagetype($pathToFile) ) {
      print "[ valid ]";
       return FALSE;
  }
  print "[ invalid ]";
   return TRUE;
}

function is_image($path)
{
    $rootPath = "/home1/throttle/oc3.throttlejockey.com/image/";
    $a = getimagesize($rootPath.$path);
    $image_type = $a[2];

    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
    {
        // print "[ valid ]";
        return true;
    }
    // print "[ invalid ]";
    return false;
}
