<?php

if (file_exists($root . 'oc_bootstrap.php')) {
    require_once($root . 'oc_bootstrap.php');
    // print "config.php Found!";
} else {
    print ('oc_bootstrap.php not found :(');
}

if (file_exists($root . 'external/_opts_required.php')) {
    require_once($root . 'external/_opts_required.php');
    // print "config.php Found!";
} else {
    print ('external/_opts_required.php not found :(');
}

// $testQ = $db->query("SELECT * FROM `oc_product_option` WHERE product_id = '252' AND product_option_id = '2307'");

/*
    [row] => Array
        (
            [product_option_id] => 2300
            [product_id] => 251
            [option_id] => 295
            [value] => 
            [required] => 0
            [prodoptsort] => 7
            [type] => select
            [sort_order] => 0
            [language_id] => 1
            [name] => ADD SEAT COVER RIPPLES(5)
        )
*/

// $setIt = setOptionRequired($db, '251', '2300');

if(1) {
    print "<pre>";
    foreach ($options_required as $product_name => $option) {

        $pId = getProductIdByName($product_name, $db);
        
        print_r($pId);
        print "<pre>";
        print "####### <br/>";
        print "/external/get_required_opts.php is run on OC15, output is /logs/lists/15-req-opts.txt, copy that array into _opts_required.php.<br/>";
        // print_r($pId);
        // print_r($testQ);
        print "####### <br/>";
        print "</pre>";

        // Apparently there have been new / updated Products since last sync. Great.
        if(!$pId->num_rows) continue;

        $pId = $pId->row['product_id'];

        if(!$option) {
            continue; 
        }
        
        // print_r($option);


        // print "$product_name => $option";
        print "$pId ]  $product_name";
        print "<br/>";
        // print "pId: $pId";

        foreach($option as $option_name) {

            $optTest = getProductOptionsByIdAndOptionName($pId, $option_name, $db);  
    
            if(!$optTest) continue;

            $prodId = $optTest->row['product_id'];
            $prodOptId = $optTest->row['product_option_id'];


            print "I'd run: setOptionRequired(db, $prodId, $prodOptId);"; //;($product_name, $db);"
            $setIt = setOptionRequired($db, $prodId, $prodOptId);

            print "<br/>";
            print "optTest: " . print_r($optTest);
            print "<br/>";
        }


    }

    print "</pre>";
}

    // SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
    // $query = $db->query("SELECT * FROM " . DB_PREFIX . "oc_product" WHERE product_id = '" . (int)$product_id . "'");
    // $rows = $query->rows;

    $query = $db->query("
        SELECT * FROM `oc_product` ORDER BY `oc_product`.`product_id` ASC
        ");


    function setOptionRequired($db, $product_id, $product_option_id) {
        //
       $qUpdate = "UPDATE `oc_product_option` po SET po.required = 1 WHERE product_id = '$product_id' AND product_option_id = '$product_option_id'";
       $q = "SELECT * FROM `oc_product_option` WHERE product_id = '" . $product_id . "' AND product_option_id = '" . $product_option_id . "'";
    //    $qUpdate = "UPDATE `oc_product_option` SET"
       $query = $db->query($qUpdate);

       print_r($query);
    }


    function getProductIdByName($name, $db){
        $retId = 0;
        $name = str_replace("'", "\'", $name);

        $query = $db->query("SELECT `product_id` FROM `oc_product_description` WHERE `name` = '$name'");
        // print "<br/>";
        // print "Name: $name <br/>";
        // print_r($query);
        // print "<br/>";

        if($query) {
            // print "getProductIdByName: $name found!";
            // print_r($query);
            return $query;
        } else {
            
            return [];
        //    print "getProductIdByName: $name NOT found!";
        }
    }

    function getProductOptionsByIdAndOptionName($product_id, $name, $db){
        $retId = 0;
        $name = str_replace("'", "\'", $name);
        $xquery = "SELECT * FROM `oc_product_option` po LEFT JOIN `oc_option` o ON (po.option_id = o.option_id) LEFT JOIN `oc_option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = $product_id AND od.name = '$name'";

        $query = $db->query($xquery);

        if($query) {
            // print "getProductOptionsByIdAndOptionName: $name found! <br/>";
            // print_r($query);
            return $query;
        } else {
            return false;
        //    print "getProductOptionsByIdAndOptionName: $product_id,  $name NOT found! <br/>";
           return "Not Found :(";
        }
    }

    function getProduct($product_id, $db){ //, $customer, $config) {

        // if ($customer->isLogged()) {
        //     $customer_group_id = $customer->getCustomerGroupId();
        // } else {
        //     $customer_group_id = $config->get('config_customer_group_id');
        // }

        // print "PID: $product_id";

        // $query = $db->query("SELECT `name` FROM `oc_product_description` WHERE product_id = $product_id");
        $query = $db->query("SELECT * FROM `oc_product_description` WHERE product_id = $product_id");
        
        // print_r($query);

        if ($query->num_rows) {
            // return array('')
            // return ($query->row['name']);
            print "<br/>$product_id] " . $query->row['name'] . "<br>";
            
            // return array(
            //     'product_id'       => $query->row['product_id'],
            //     'name'             => $query->row['name'],
            //     'description'      => $query->row['description'],
            //     'meta_description' => $query->row['meta_description'],
            //     'meta_keyword'     => $query->row['meta_keyword'],
            //     'tag'              => $query->row['tag'],
            //     'model'            => $query->row['model'],
            //     'sku'              => $query->row['sku'],
            //     'upc'              => $query->row['upc'],
            //     'ean'              => $query->row['ean'],
            //     'jan'              => $query->row['jan'],
            //     'isbn'             => $query->row['isbn'],
            //     'mpn'              => $query->row['mpn'],
            //     'location'         => $query->row['location'],
            //     'quantity'         => $query->row['quantity'],
            //     'stock_status'     => $query->row['stock_status'],
            //     'image'            => $query->row['image'],
            //     'manufacturer_id'  => $query->row['manufacturer_id'],
            //     'manufacturer'     => $query->row['manufacturer'],
            //     'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
            //     'special'          => $query->row['special'],
            //     'reward'           => $query->row['reward'],
            //     'points'           => $query->row['points'],
            //     'tax_class_id'     => $query->row['tax_class_id'],
            //     'date_available'   => $query->row['date_available'],
            //     'weight'           => $query->row['weight'],
            //     'weight_class_id'  => $query->row['weight_class_id'],
            //     'length'           => $query->row['length'],
            //     'width'            => $query->row['width'],
            //     'height'           => $query->row['height'],
            //     'length_class_id'  => $query->row['length_class_id'],
            //     'subtract'         => $query->row['subtract'],
            //     'rating'           => round($query->row['rating']),
            //     'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
            //     'minimum'          => $query->row['minimum'],
            //     'sort_order'       => $query->row['sort_order'],
            //     'status'           => $query->row['status'],
            //     'date_added'       => $query->row['date_added'],
            //     'date_modified'    => $query->row['date_modified'],
            //     'viewed'           => $query->row['viewed']
            // );
        } else {
            return false;
        }
    }

?>