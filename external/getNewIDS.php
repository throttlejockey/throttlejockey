<?php

// // $categories15 =
// require_once("./data/cats15.php"); 

// // $categories3 =
// require_once("./data/cats3.php"); 

// // $products15 = 
// require_once("./data/prods15.php"); 

// // $products3 = 
// require_once("./data/prods3.php"); 



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
$categories15 = fetchArray('./data/cats15.php');
$products15 = fetchArray('./data/prods15.php');
$categories3 = fetchArray('./data/cats3.php');
$products3  = fetchArray('./data/prods3.php');

// print_r($categories15);

function fetchArray($in) {
  if(is_file($in)) 
       return include $in;
  return false;
}
 
print "<pre>";

$CATEGORIES = [];
$PRODUCTS = [];
// $INSERTS = [];
foreach ($categories15 as $name15 => $value15) {
    
    $catMatch = array_key_exists($name15, $categories3);
    $oldID = $value15; 
    if( $catMatch ) {
        $newID = $categories3[ $name15 ];
        // print "Was $oldID :: Now $newID <br>";
        $CATEGORIES[$oldID] = $newID; // ["old" => $oldID, "new" => $newID];
    } else {
        print "$name15 not found in categories3..<br>";
    }
}

// print "CATEGORIES!@! <br>";
// print_r($CATEGORIES);
// print "-------------<br><br>";
// NEW PRODUCT IDs. Products15 > Products3
foreach ($products15 as $name15 => $value15) {
    $catMatch = array_key_exists($name15, $products3);
    $oldID = $value15; 

    if( $catMatch ) {
        $newID = $products3[ $name15 ];
        // print "$oldID :: $newID <br>";
        $PRODUCTS[$oldID] = $newID; // ["old" => $oldID, "new" => $newID];
    } else {
        print "$name15 not found in products3..<br>";
    }
}

// print "PRODUCTS!@! <br>";
// print_r($PRODUCTS);
// print "-------------<br><br>";




print '$CATEGORIES = ' . var_export($CATEGORIES, 1) . "<br>"; 
print '$PRODUCTS = ' . var_export($PRODUCTS, 1) . "<br>"; 
// foreach ($prods15 as $prods15 => $value15) {
    
//     $catMatch = array_key_exists($prods15, $prods3);
//     $oldID = $value15; 

//     if( $catMatch ) {
//         $newID = $prods3[ $prods15 ];
//         print "$oldID :: $newID <br>";
//     } else {
//         print "$name15 not found in cats15..";
//     }

// }
print "</pre>";

?>