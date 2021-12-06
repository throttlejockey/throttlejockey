<?php


$nl = "<br>";
// $nl = "\n";

$tab = "&emsp;";
// $tab = "$tab";

// $emdash = "--";
$emdash = "&mdash;";

$b = "<b>";
$be = "</b>";

// $b = $be = "";

include('config.php');
$link = mysql_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD);
if (!$link) {
  die('Could not connect: ' . mysql_error());
}
// mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $link);

if (!mysql_select_db(DB_DATABASE)) {
  echo "Unable to select mydbname: " . mysql_error();
  exit;
}


$option_value_description_query = <<<EOL
SELECT * FROM `option_value_description` WHERE `otooltip` <> ''
ORDER BY `option_value_description`.`option_id`  ASC
EOL;

$products_with_sub_options = <<<EOL
SELECT * FROM `product_option` WHERE `parent_product_option_value_id` <> 0 ORDER BY `product_option`.`product_id` ASC
EOL;

# $sql = "SELECT pd.name FROM " . DB_PREFIX . "product p," . DB_PREFIX . "product_description pd WHERE p.status = 1 AND p.product_id = pd.product_id AND language_id = '".$id."' AND UPPER(pd.name) like UPPER('%$q%') GROUP BY pd.product_id ORDER BY pd.name ASC";
$res = mysql_query($products_with_sub_options);

// echo 'option_id  |  option_name' ."$nl";
// echo '--------------------------------' . "$nl";


$array_option_value_ids = array();
$arrChildOptions = array();


$step=0;
$currId=0;

if(mysql_num_rows($res)>0){
    while($ro = mysql_fetch_assoc($res)){
        $id = $ro['product_id'];

        $cv = intval($currId);
        $iv = intval($id);
        // Start counter over if this is a new id ...
        if(intval($id) !== intval($currId)) {
             $step = 0;
        }

        array_push($array_option_value_ids, $id);


        $step++;
        $currId = $id;
   }
}


// var_dump($array_option_value_ids);
$ProductIds = array_values( array_unique($array_option_value_ids) );
$uidsList = implode(', ', $ProductIds);
print "<pre>";
// print_r($ProductIds);
// print_r($uidsList);


$optionsArr = array();

$OptionIDs = array();

foreach ($ProductIds as $pId) {

    // print $optid;
$product_query = <<<EOL
SELECT * FROM `product_description` WHERE `product_id` = $pId
EOL;



    $res = mysql_query($product_query);

    if(mysql_num_rows($res)>0){
        while($ro = mysql_fetch_assoc($res)){
        $name = $ro['name'];

        print "$pId ]  $name \n";
        // $optionsArr[$id] = $ro;
            // print_r($ro);

        }
    }
}


// $option_query = <<<EOL
// SELECT * FROM `product_option` WHERE `product_id` = $pId ORDER BY `product_id` ASC
// EOL;
//         $res = mysql_query($option_query);
//         // print_r($res);
//     if(mysql_num_rows($res)>0){
//         while($ro = mysql_fetch_assoc($res)){
//             $name = $ro['name'];

//             print "$pId ]  $name \n";
//             // $optionsArr[$id] = $ro;
//             print_r($ro);
//         }
//     }

// }


print "</pre>";
// print_r( $optionsArr );


// foreach ($optionsArr as $option_id => $value) {
//     $optName = $value['name'];
//     $optTip  = $value['otooltip'];

//     $optTipX = str_replace("http://throttlejockey.com/image/data/", "/image/data/", $optTip);
//     // print "option_id: $option_id $nl";
//     print "$b $optName $be $nl";
//     print "TOOLTIP:$nl $optTipX $nl $nl";

//     $childOptArr = $arrChildOptions[$option_id];
//     // print "$emdash $b SUB OPTIONS $be $nl $nl";
//     foreach ($childOptArr as $key => $valuex) {

//         $cOptName = $valuex['name'];
//         $cOptTip  = $valuex['otooltip'];
//         $cOptTipX = str_replace("http://throttlejockey.com/image/data/", "/image/data/", $cOptTip);

//         if( !empty($cOptTip) || $cOptTip !== "") {

//             // print "Key: $key | Value: $valuex $nl";
//             // print_r($valuex);
//             print "$tab $tab $emdash $b $cOptName $be $nl";
//             print " $nl $tab $cOptTipX $nl";
//             print "$nl";
//             # code...
//         }

//     }
//     print "<hr>";
// }
// }
?>