<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("ShowOptions", true);
define("ShowDuplicates", false);
define("ShowSubOptions", false);
define("ShowRequiredOnly", false);
define("SHOW_IDS", false);

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



$query = $db->query("
            SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
            ");

        $product_dupes = [];
        print "<pre>";
        print "OC 3.0.3.6". "<br>";
        print "---". "<br>";
        print "</pre>";

        foreach ($query->rows as $result) {
            $opts = getProductOptions($result['product_id'], $db, $customer, $config);
            ksort($opts[2]);
            // $product_option_datax[ $opts[0] ] = [$opts[2]];
            $product_option_data2[ $opts[0] ] = [$opts[3]];
            $product_option_data2[ $opts[0] ]['product_info'] = $opts[4]['product_info']; //[$opts[4]];
            // $product_information[$opts[0]] = $opts[4][0]['product_info'];
        }
 
        ksort($product_option_data2, SORT_NATURAL);

        print "<pre>";    
        foreach ($product_option_data2 as $product => $options) {
            // print "prod: $product <br/>";
            // Add this name to the dupe list
            print "<br><br>";
            print "============<br>";
            // print "<pre><p>===<br/>";
            // print_r($options['product_info']);
            // print "<br/>===</p></pre>";

            $isProdDuplicate = in_array($product, $product_dupes);
            $isRequired = $options['product_info']['required'];
            $productId =  $options['product_info']['product_id'];
            $requiredOptCount = (int)$options['product_info']['num_required']; 

            // print_r($options['product_info']);

            // print "<p>";
            // print  "requiredOptCount Value: ";
            // print $requiredOptCount;
            // print "</p>";

            $endchar = "";
            if($requiredOptCount >= 1) {
                $isRequired = true;
                $endchar = "*";
            } else { 
                $isRequired = false;
                $endchar = "x";
            }

            $productIdStr = "";
            if(SHOW_IDS)  $productIdStr = "$productId ] "; 
            $isRequired = true;
            // if this product isn't required, and we don't want to show not required prods. 
            if(!$isRequired ) {
                // print"<br/>$productIdStr $product -- <b>not required, skipping</b><br/>"; 
                // print "<br/>ID: $productId<br/>";
                continue;
            }

            $endchar = "";
            if ($isRequired) {
                // print "<p style=\"color:blue; font-weight:bold;\">$productId ] Product: $product</p>";
                print "$productIdStr $product $endchar </br>";
                // print "ID: $productId<br/>";
                // continue;
            } else if($isProdDuplicate){
                print "<p style=\"color:red\">$productId ] Product: $product $endchar -- <b>duplicate</b></p>";
                // print "ID: $productId<br/>";
                // continue;                        
            } else {
                print "$productIdStr $product -other</br>";
                print "ID: $productId<br/>";
            }

 
            if(!ShowOptions){ continue; }
 
            if($options) {
                $dupes = [];
                usort($options[0], 'compareName');
                // print "<pre>";
                // Sort Options by name.
                //  usort($options[0], function($a, $b) {
                //     return $a['name'] <=> $b['name'];
                // });
                // print_r($options[0][0]);

                // product_option_id
                $showProduct = false;
                $showProductTitle = true;

                // $hasRequiredOpts = true;
                // foreach ($options[0] as $option) { 
                //     if((int)$option['required']) {
                //         $hasRequiredOpts = false;
                //     } 
                // }
                
                // Iterate through all options of current product
                foreach ($options[0] as $option) {
                    // print_r($option);
                    //  = false;
                    $required = (int)$option['required'] ? "*" : "x"; // ? "* " : "";

                    $required = "";

                    $option_name = $option['name'];
                    $option_id = $option['option_id'];
                    $isDuplicate = in_array($option_name, $dupes);
                    // print "-Duplicate: $option_name<br/>";

                    // Skip duplicates if we've set ShowDuplicates to true...
                    if( !ShowDuplicates ) {
                        if(in_array($option_name, $dupes)) continue;
                    }
 
                    // Add this name to the dupe list
                    $dupes[] = $option_name;

                    // print_r($dupes);
                    $option_idStr = "";
                    if(SHOW_IDS)  $option_idStr = "$option_id ] ";                     

                    print "<br>";
                    // print "    #### <br>";
                    print "  - $option_idStr Option: $option_name $required </br>";
                     
                    $option_values = $option['product_option_value'];

                    // usort($options[0], 'cmp');
                    // print_r($options);
                    // print_r($options[0]['product_option_value']);
                    usort($option_values, 'compareName');
                    // print_r($option_values);                    
                    // usort($option_values, 'cmp');
                    // print_r($option_values);


                    if(!ShowSubOptions){ continue; }
   

                    foreach ($option_values as $option_value) {
                        print "<br/>";
                        // print_r($option_value);
                        print "     - Value: " . $option_value['name'] . " </br>"; 
                        if(SHOW_IDS) {
                            print "        - [product_option_value_id] => " . $option_value['product_option_value_id'] . " </br>";
                            print "        - [option_value_id] => " . $option_value['option_value_id'] . " </br>";
                        }

                        $imgnm = !empty($option_value['image'])  ?  $option_value['image'] : "no_image.jpg";


                        // print "        - [name] => " . $option_value['name'] . " </br>";
                        print "        - [image] => " . $imgnm . " </br>"; // $option_value['image'] . " </br>";
                        print "        - [quantity] => " . $option_value['quantity'] . " </br>";
                        print "        - [subtract] =>  " . $option_value['subtract'] . " </br>";
                        print "        - [price] => " . $option_value['price'] . " </br>";
                        print "        - [price_prefix] => " . $option_value['price_prefix'] . " </br>";
                        print "        - [weight] => " . $option_value['weight'] . " </br>";
                        print "        - [weight_prefix] => " . $option_value['weight_prefix'] . " </br>";
                        print "<br/>";

                    } // end of:  foreach ($option_values as $option_value)
                } // end of:  foreach ($options[0] as $option)

                print "</br>";

                $product_dupes[] = $product;
            } //  end of:  if($options)

        } // end of: foreach ($product_option_data2 as $product => $options)

        print "</pre>";




        function cmp($a, $b) {
            // print_r($a);br();
            // print_r($b);br();
            return (int)$b['product_option_value_id'] - (int)$a['product_option_value_id'];
        }

    function compareID($a, $b)  { // {   
        return ( intcmp($a['product_option_value_id'], $b['product_option_value_id'] ) ) ;
    }

    function intcmp($a,$b)
    {
    return ($a-$b) ? ($a-$b)/abs($a-$b) : 0;
    }

   function compareName($a, $b) {
        return strnatcmp(strtolower($a['name']), strtolower($b['name']));
    }
        // return ($a-$b) ? ($a-$b)/abs($a-$b) : 0;

    function compareOptionName($a, $b)
    {
        return strnatcmp(strtolower($a['name']), strtolower($b['name']));
    }



    function getProductOptions($product_id, $db, $customer, $config) {
        $product_option_data = array();
        $productNameQ = $db->query("SELECT `name` FROM `" . DB_PREFIX . "product_description` WHERE product_id = $product_id");

        $productName = $productNameQ->row['name']; //['row']['name'];
        $prodOptArr = [];
        $productArr[$productName] = array();
        $stringOut = "";

        $RequiredCount = 0;
 
        $stringOut .=  "<br/>$productName<br>";

        $poptQuery = "SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "'";
        // print $poptQuery;

        $product_option_query = $db->query($poptQuery);
 

        foreach ($product_option_query->rows as $product_option) {

            $product_option_value_data = array();
 

            $povQuery = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'"; //" AND ovd.language_id = '" . (int)$config->get('config_language_id') . "' ORDER BY ov.sort_order";
            $product_option_value_query = $db->query($povQuery);

            // $required = (int)$product_option['required'] ? "*" : "x"; //true : false;
            $required = (int)$product_option['required'] ? true : false; //true : false;
            // $required = $product_option['required'] ? "yes" : "no"; //true : false;

            if($required) $RequiredCount++;

            $stringOut .= "  + " .  $required . $product_option['name'] . " <br>";

            foreach ($product_option_value_query->rows as $product_option_value) {
 
                // $productArr[$product_option[$productName]]['numRequired'] = array('RequiredCount' => $RequiredCount); 

                $stringOut .= "    - ". $product_option_value['name'] . " <br>";

                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'option_value_id'         => $product_option_value['option_value_id'],
                    'name'                    => $product_option_value['name'],
                    'image'                   => $product_option_value['image'],
                    'quantity'                => $product_option_value['quantity'],
                    'subtract'                => $product_option_value['subtract'],
                    'price'                   => $product_option_value['price'],
                    'price_prefix'            => $product_option_value['price_prefix'],
                    'weight'                  => $product_option_value['weight'],
                    'weight_prefix'           => $product_option_value['weight_prefix']
                );
            }


            $stringOut .= "<br>";
            $product_option_data[] = array(
                'option_name'         => $product_option['name'],
                'product_option_id'    => $product_option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $product_option['option_id'],
                'name'                 => $product_option['name'],
                'type'                 => $product_option['type'],
                // 'value'                => $product_option['value'],
                'required'             => $required, //$product_option['required']
                'required_count'       => $RequiredCount
            );

            // print "<pre>";
            // // print("currArray: ");
            // // print_r($currArray);
 
            //     // print_r($currArray[]);
            // print "</pre>";
            // if($required) $currArray[]
            // $productArr[$productName]['count'] = $RequiredCount;
         }

        // print "<pre>";
        // // print("currArray: ");
        // print_r($productArr); //product_option_data);

        //     // print_r($currArray[]);
        // print "</pre>";

        // product has no required options by default
        $currRequired = false;


        // If required count is not 0 then product has required options.
        // print "<br/>Required Count: $RequiredCount <br/>";
        if($RequiredCount>0) {
            // print "<span>$productName</span>";
            // print "<br/>";
            $currRequired = "true";
        } else{
            // print "*Required: $productName ";
            // print "<span style='text-decoration: line-through;'>* $productName </span>";
            // print "<br/>";
            $currRequired = "false";
        }

        $product_nfo['product_info'] = array( "name"=>$productName, "num_required"=>$RequiredCount, "required"=>$currRequired, "product_id"=>$product_id );

        return $product_option_data = [$productName, $stringOut, $prodOptArr, $product_option_data, $product_nfo]; //, $product_option_data_tmp]; //['product_name'] = $productName;

        // $product_option_data_tmp = [$productName, $stringOut, $prodOptArr, $product_option_data, $RequiredCount]; //['product_name'] = $productName;

        // print "<pre>";
        // // print("currArray: ");
        // print_r($product_option_data_tmp); //product_option_data);

        //     // print_r($currArray[]);
        // print "</pre>";
 

        // return $product_option_data = ["name"=>$productName, $RequiredCount]
    }



 ?>