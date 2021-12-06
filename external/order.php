<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// $root = $_SERVER['DOCUMENT_ROOT'] . '/oc3.throttlejockey.com/';
// $root = "/home1/throttle/oc3.throttlejockey.com/";
$root = "/home/b16aa05/public_html/";


$prod_id = 0;
if ( $_GET && $_GET['prod_id'] ) {
    $prod_id = $_GET['prod_id'];
} else {
    print "Add ?prod_id=#####";
    return;
}



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

// print "<pre>";
// print "Order info". "<br>";
// print "---". "<br>";


// header('Content-Type: text/csv; charset=utf-8');
// header('Content-Disposition: attachment; filename=users.csv');

// $output = fopen('php://output', 'w');
// $tmp = getOrder(2, $db, $customer);
// fputcsv($output, $tmp); // ['First name', 'Last name', 'Occupation']);

// $f = fopen('users.csv', 'r');

// while (!feof($f)) {

//     $rows[] = fgetcsv($f);
// }

// foreach ($rows as $row) {
//     fputcsv($output, $row);
// }

// fclose($f);
function arrayToCSV($inputArray)
{
    $csvFieldRow = array();
    foreach ($inputArray as $CSBRow) {
        $csvFieldRow[] = str_putcsv($CSBRow);
    }
    $csvData = implode("\n", $csvFieldRow);
    return $csvData;
}

function str_putcsv($input, $delimiter = ',', $enclosure = '"')
{
    // Open a memory "file" for read/write
    $fp = fopen('php://temp', 'r+');
    // Write the array to the target file using fputcsv()
    fputcsv($fp, $input, $delimiter, $enclosure);
    // Rewind the file
    rewind($fp);
    // File Read
    $data = fread($fp, 1048576);
    fclose($fp);
    // Ad line break and return the data
    return rtrim($data, "\n");
}

$inputArray = array(
    array("First Name", "Last Name", "Identification Number"),
    array("Kim","Thomas","8001"),
    array("Jeffery","Robert","8021"),
    array("Helan","Albert","8705")
);
print "<PRE>";
// print $CSVData = arrayToCSV($inputArray);


$order_id = 0;
if ( $_GET['order_id'] ) {
    $order_id = $_GET['order_id'];
}

// $XML_RequestFile = file_get_contents( __DIR__.'/request-test.xml' );
// $XML_RequestFile = file_get_contents( __DIR__."/$order_id.xml" );

$heading = [];
$values = [];
$headingStr = "";
$valueStr = "";

$tmp = getOrder(2, $db, $customer);
print "<pre>";

print_r ($tmp);

foreach ($tmp as $key => $value) {

    array_push($heading, $key);
    array_push($values, $value); 

    // $headingStr .= $key . ', ';
    // $valueStr .= $value . ', ';
 
}

    // print "\r\n";
    // print_r ($heading);
    // print "\r\n";

    // print "\r\n";
    // print_r ($values);
    // print "\r\n";
    $outputArr = array( $heading, $values );

    print_r ($inputArray);
    print "\r\n";
    print_r ($outputArr);
    print "\r\n";
    print "\r\n";


    // print $headingStr;
    // print "\r\n";
    // print "\r\n";
    // print $valueStr;
    // print "\r\n";

    $CSVData = arrayToCSV($outputArr);
    print $CSVData;
//     // print_r($tmp);
// print "</pre>";
$fp = fopen('farts.csv', 'w');
fputs($fp, $CSVData);
fclose($fp);

    // Create an array of elements
    $list = array(
        $heading, $values
    );

// //    $list = $tmp;

    // Open a file in write mode ('w')
    $fp = fopen('persons.csv', 'w');
    
    // Loop through file pointer and a line
    foreach ($list as $fields) {
        fputcsv($fp, $fields);
    }
    
    fclose($fp);
    


function getCustomer($customer_id) {
    $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

    return $query->row;
}

function getOrder($order_id, $db, $customer) {
    $order_query = $db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '1') AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

    if ($order_query->num_rows) {
        $country_query = $db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

        if ($country_query->num_rows) {
            $payment_iso_code_2 = $country_query->row['iso_code_2'];
            $payment_iso_code_3 = $country_query->row['iso_code_3'];
        } else {
            $payment_iso_code_2 = '';
            $payment_iso_code_3 = '';
        }

        $zone_query = $db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

        if ($zone_query->num_rows) {
            $payment_zone_code = $zone_query->row['code'];
        } else {
            $payment_zone_code = '';
        }

        $country_query = $db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

        if ($country_query->num_rows) {
            $shipping_iso_code_2 = $country_query->row['iso_code_2'];
            $shipping_iso_code_3 = $country_query->row['iso_code_3'];
        } else {
            $shipping_iso_code_2 = '';
            $shipping_iso_code_3 = '';
        }

        $zone_query = $db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

        if ($zone_query->num_rows) {
            $shipping_zone_code = $zone_query->row['code'];
        } else {
            $shipping_zone_code = '';
        }

        $reward = 0;

        $order_product_query = $db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

        foreach ($order_product_query->rows as $product) {
            $reward += $product['reward'];
        }
        
        // // $this->load->model('customer/customer');

        // $affiliate_info = $customer->getCustomer($order_query->row['affiliate_id']);

        // if ($affiliate_info) {
        //     $affiliate_firstname = $affiliate_info['firstname'];
        //     $affiliate_lastname = $affiliate_info['lastname'];
        // } else {
        //     $affiliate_firstname = '';
        //     $affiliate_lastname = '';
        // }

        // $this->load->model('localisation/language');

        // $language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

        // if ($language_info) {
        //     $language_code = $language_info['code'];
        // } else {
        //     $language_code = $config->get('config_language');
        // }

        return array(
            // 'order_id'                => $order_query->row['order_id'],
            // 'invoice_no'              => $order_query->row['invoice_no'],
            // 'invoice_prefix'          => $order_query->row['invoice_prefix'],
            // 'store_id'                => $order_query->row['store_id'],
            // 'store_name'              => $order_query->row['store_name'],
            // 'store_url'               => $order_query->row['store_url'],
            // 'customer_id'             => $order_query->row['customer_id'],
            // 'customer'                => $order_query->row['customer'],
            // 'customer_group_id'       => $order_query->row['customer_group_id'],
            // 'firstname'               => $order_query->row['firstname'],
            // 'lastname'                => $order_query->row['lastname'],
            // 'email'                   => $order_query->row['email'],

            // 'custom_field'            => json_decode($order_query->row['custom_field'], true),
            // 'payment_firstname'       => $order_query->row['payment_firstname'],
            // 'payment_lastname'        => $order_query->row['payment_lastname'],
            // 'payment_company'         => $order_query->row['payment_company'],
            // 'payment_address_1'       => $order_query->row['payment_address_1'],
            // 'payment_address_2'       => $order_query->row['payment_address_2'],
            // 'payment_postcode'        => $order_query->row['payment_postcode'],
            // 'payment_city'            => $order_query->row['payment_city'],
            // 'payment_zone_id'         => $order_query->row['payment_zone_id'],
            // 'payment_zone'            => $order_query->row['payment_zone'],
            // 'payment_zone_code'       => $payment_zone_code,
            // 'payment_country_id'      => $order_query->row['payment_country_id'],
            // 'payment_country'         => $order_query->row['payment_country'],
            // 'payment_iso_code_2'      => $payment_iso_code_2,
            // 'payment_iso_code_3'      => $payment_iso_code_3,
            // 'payment_address_format'  => $order_query->row['payment_address_format'],
            // 'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
            // 'payment_method'          => $order_query->row['payment_method'],
            // 'payment_code'            => $order_query->row['payment_code'],
            'shipping_name' => $order_query->row['shipping_firstname'] . ' ' . $order_query->row['shipping_lastname'],
            // 'shipping_firstname'      => $order_query->row['shipping_firstname'],
            // 'shipping_lastname'       => $order_query->row['shipping_lastname'],
            'shipping_company'        => $order_query->row['shipping_company'],
            'shipping_address_1'      => $order_query->row['shipping_address_1'],
            'shipping_address_2'      => $order_query->row['shipping_address_2'],
            'shipping_city'           => $order_query->row['shipping_city'],
            'shipping_postcode'       => $order_query->row['shipping_postcode'],
            'telephone'               => $order_query->row['telephone'],
            
            
            'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
            'shipping_zone'           => $order_query->row['shipping_zone'],
            'shipping_zone_code'      => $shipping_zone_code,
            'shipping_country_id'     => $order_query->row['shipping_country_id'],
            'shipping_country'        => $order_query->row['shipping_country'],
            'shipping_iso_code_2'     => $shipping_iso_code_2,
            'shipping_iso_code_3'     => $shipping_iso_code_3,
            'shipping_address_format' => $order_query->row['shipping_address_format'],
            // 'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
            'shipping_method'         => $order_query->row['shipping_method'],
            'shipping_code'           => $order_query->row['shipping_code'],
            'comment'                 => $order_query->row['comment'],
            'total'                   => $order_query->row['total'],
            'reward'                  => $reward,
            'order_status_id'         => $order_query->row['order_status_id'],
            'order_status'            => $order_query->row['order_status'],
            // 'affiliate_id'            => $order_query->row['affiliate_id'],
            // 'affiliate_firstname'     => $affiliate_firstname,
            // 'affiliate_lastname'      => $affiliate_lastname,
            // 'commission'              => $order_query->row['commission'],
            // 'language_id'             => $order_query->row['language_id'],
            // 'language_code'           => $language_code,
            // 'currency_id'             => $order_query->row['currency_id'],
            // 'currency_code'           => $order_query->row['currency_code'],
            // 'currency_value'          => $order_query->row['currency_value'],
            // 'ip'                      => $order_query->row['ip'],
            // 'forwarded_ip'            => $order_query->row['forwarded_ip'],
            // 'user_agent'              => $order_query->row['user_agent'],
            // 'accept_language'         => $order_query->row['accept_language'],
            // 'date_added'              => $order_query->row['date_added'],
            // 'date_modified'           => $order_query->row['date_modified']
        );
    } else {
        return;
    }
}