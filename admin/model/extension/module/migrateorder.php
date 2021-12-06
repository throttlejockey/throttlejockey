<?php 

$db_user1     = $this->session->data['user1'];
$db_password1 = $this->session->data['password1']; 
$db_host1     = $this->session->data['host1'];
$db_port1     = $this->session->data['port1'];

$db_name1     = $this->session->data['name1'];
$db_prefix1   = $this->session->data['prefix1'];

$con1=mysqli_connect($db_host1,$db_user1,$db_password1,$db_name1);
// Check connection 1
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL 1: " . mysqli_connect_error();
    exit;
}

$db_user2     = $this->session->data['user2'];
$db_password2 = $this->session->data['password2']; 
$db_host2     = $this->session->data['host2'];
$db_port2     = $this->session->data['port2'];

$db_name2     = $this->session->data['name2'];
$db_prefix2   = $this->session->data['prefix2'];

$con2=mysqli_connect($db_host2,$db_user2,$db_password2,$db_name2);
// Check connection 2
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL 2: " . mysqli_connect_error();
    exit;
}

$sql = "SET NAMES 'utf8'";
$query = mysqli_query($con1,$sql);
$sql = "SET CHARACTER SET 'utf8'";
$query = mysqli_query($con1,$sql);

// Empty Table and reset Auto-Increment
$this->load->model('extension/module/migratetov3');

$tables = array();

$tables[] = "order";
if(isset($this->request->post['input_o_history']))
{   $tables[] = "order_history";    }
if(isset($this->request->post['input_o_option']))
{   $tables[] = "order_option";    }
if(isset($this->request->post['input_o_product']))
{   $tables[] = "order_product";    }
if(isset($this->request->post['input_o_recurring']))
{   $tables[] = "order_recurring";    }
if(isset($this->request->post['input_o_recurring_txn']))
{   $tables[] = "order_recurring_transaction";    }
if(isset($this->request->post['input_o_shipment']))
{   $tables[] = "order_shipment";    }
if(isset($this->request->post['input_o_status']))
{   $tables[] = "order_status";    }
if(isset($this->request->post['input_o_total']))
{   $tables[] = "order_total";    }
if(isset($this->request->post['input_o_voucher']))
{   $tables[] = "order_voucher";    }
if(isset($this->request->post['input_lo_return']))
{   $tables[] = "return"; $tables[] = "return_action"; $tables[] = "return_history"; $tables[] = "return_reason"; $tables[] = "return_status"; }
if(isset($this->request->post['input_lo_recurring']))
{   $tables[] = "recurring"; $tables[] = "recurring_description"; }

// if(!$this->model_extension_module_migratetov3->emptyTable($con2,$db_prefix2,$tables))
// {
//     echo "Error in ORDERS DATABASE TABLES<br />";
// 	print_r($tables);
// 	echo "<br />";
// 	$this->error['order'] = $this->language->get('error_order');
// 	exit;
// } 

// $OrderIDS = "105730, 105729, 105727, 105726, 105724, 105723, 105722, 105721, 105720, 105718, 105716, 105715, 105714, 105713, 105711, 105709, 105707, 105706, 105704, 105703, 105702, 105700, 105698, 105693, 105692, 105690, 105687, 105686, 105683, 105681, 105679, 105677, 105671, 105669, 105667, 105631, 105465, 104821, 104270, 104262, 104260, 104259, 104258, 104255, 104252, 104248, 104243, 104240, 104231, 104158, 104088, 104070, 103785, 103525, 103205, 103128, 103101, 103095, 103073, 102429, 101936, 101875, 101870, 101789, 101780, 101701, 101692, 101642, 101520, 101285, 101063, 101060, 101041, 100550";
$OrderIDS = "105730"; //,105701";
$ADDITIONAL_SQL = "WHERE `order_id` IN ($OrderIDS)";
// $ADDITIONAL_SQL = "WHERE `order_status_id` = 2"; // <-- 2 is pending

// Get Order Records
$o = 0;
$sql = "SELECT * FROM `" . $db_prefix1 . "order` $ADDITIONAL_SQL";
$query = mysqli_query($con1,$sql);

while($result = $query->fetch_assoc())
{
	$order_id                 = $result['order_id'];
	$invoice_no               = $result['invoice_no'];
	$invoice_prefix           = $result['invoice_prefix'];
	$store_id                 = $result['store_id'];
	$store_name               = addslashes($result['store_name']);
	$store_url                = $result['store_url'];
	$customer_id              = $result['customer_id'];
	$customer_group_id        = $result['customer_group_id'];
	$firstname                = addslashes($result['firstname']);
	$lastname                 = addslashes($result['lastname']);
	$email                    = $result['email'];
	$telephone                = addslashes($result['telephone']);
	$fax                      = $result['fax'];
	// $custom_field             = addslashes($result['custom_field']);
    $custom_field     = "";
	$payment_firstname        = addslashes($result['payment_firstname']);
	$payment_lastname         = addslashes($result['payment_lastname']);
	$payment_company          = addslashes($result['payment_company']);
	$payment_address_1        = addslashes($result['payment_address_1']);
	$payment_address_2        = addslashes($result['payment_address_2']);
	$payment_city             = addslashes($result['payment_city']);
	$payment_postcode         = $result['payment_postcode'];
	$payment_country          = $result['payment_country'];
	$payment_country_id       = $result['payment_country_id'];
	$payment_zone             = addslashes($result['payment_zone']);
	$payment_zone_id          = $result['payment_zone_id'];
	$payment_address_format   = $result['payment_address_format'];
	$payment_custom_field     = ""; // $result['payment_custom_field'];
	$payment_method           = $result['payment_method'];
	$payment_code             = $result['payment_code'];
	$shipping_firstname       = addslashes($result['shipping_firstname']);
	$shipping_lastname        = addslashes($result['shipping_lastname']);
	$shipping_company         = addslashes($result['shipping_company']);
	$shipping_address_1       = addslashes($result['shipping_address_1']);
	$shipping_address_2       = addslashes($result['shipping_address_2']);
	$shipping_city            = addslashes($result['shipping_city']);
	$shipping_postcode        = $result['shipping_postcode'];
	$shipping_country         = $result['shipping_country'];
	$shipping_country_id      = $result['shipping_country_id'];
	$shipping_zone            = addslashes($result['shipping_zone']);
	$shipping_zone_id         = $result['shipping_zone_id'];
	$shipping_address_format  = $result['shipping_address_format'];
	$shipping_custom_field    = ""; // addslashes($result['shipping_custom_field']);
	$shipping_method          = $result['shipping_method'];
	$shipping_code            = $result['shipping_code'];
	$comment                  = addslashes($result['comment']);
	$total                    = $result['total'];
	$order_status_id          = $result['order_status_id'];
	$affiliate_id             = $result['affiliate_id'];
	$commission               = $result['commission'];
	$marketing_id             = ""; // $result['marketing_id'];
	$tracking                 = ""; // $result['tracking'];
	$language_id              = $result['language_id'];
	$currency_id              = $result['currency_id'];
	$currency_code            = $result['currency_code'];
	$currency_value           = $result['currency_value'];
	$ip                       = $result['ip'];
	$forwarded_ip             = $result['forwarded_ip'];
	$user_agent               = $result['user_agent'];
	$accept_language          = $result['accept_language'];
	$date_added               = $result['date_added'];
	$date_modified            = $result['date_modified'];

	$sql2 = "INSERT INTO `" . $db_prefix2 . "order` (`order_id`,`invoice_no`,`invoice_prefix`,`store_id`,`store_name`,`store_url`,`customer_id`,`customer_group_id`,`firstname`,`lastname`,`email`,`telephone`,`fax`,`custom_field`,`payment_firstname`,`payment_lastname`,`payment_company`,`payment_address_1`,`payment_address_2`,`payment_city`,`payment_postcode`,`payment_country`,`payment_country_id`,`payment_zone`,`payment_zone_id`,`payment_address_format`,`payment_custom_field`,`payment_method`,`payment_code`,`shipping_firstname`,`shipping_lastname`,`shipping_company`,`shipping_address_1`,`shipping_address_2`,`shipping_city`,`shipping_postcode`,`shipping_country`,`shipping_country_id`,`shipping_zone`,`shipping_zone_id`,`shipping_address_format`,`shipping_custom_field`,`shipping_method`,`shipping_code`,`comment`,`total`,`order_status_id`,`affiliate_id`,`commission`,`marketing_id`,`tracking`,`language_id`,`currency_id`,`currency_code`,`currency_value`,`ip`,`forwarded_ip`,`user_agent`,`accept_language`,`date_added`,`date_modified`) 
	         VALUES ('$order_id','$invoice_no','$invoice_prefix','$store_id','$store_name','$store_url','$customer_id','$customer_group_id','$firstname','$lastname','$email','$telephone','$fax','$custom_field','$payment_firstname','$payment_lastname','$payment_company','$payment_address_1','$payment_address_2','$payment_city','$payment_postcode','$payment_country','$payment_country_id','$payment_zone','$payment_zone_id','$payment_address_format','$payment_custom_field','$payment_method','$payment_code','$shipping_firstname','$shipping_lastname','$shipping_company','$shipping_address_1','$shipping_address_2','$shipping_city','$shipping_postcode','$shipping_country','$shipping_country_id','$shipping_zone','$shipping_zone_id','$shipping_address_format','$shipping_custom_field','$shipping_method','$shipping_code','$comment','$total','$order_status_id','$affiliate_id','$commission','$marketing_id','$tracking','$language_id','$currency_id','$currency_code','$currency_value','$ip','$forwarded_ip','$user_agent','$accept_language','$date_added','$date_modified')";
	
	if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	{
	    $o++;
	} else { 
        echo "Error in ORDER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		echo "SQL2: " . $sql2 . "<br />";
		$this->error['order'] = $this->language->get('error_order');
		exit;
   }
       

// End of ORDER --------------------------------------------
}
// -----------------------------------------------------------

// Get Order HISTORY records
if(isset($this->request->post['input_o_history']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_history` $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_history_id        = $result['order_history_id'];
		$order_id                = $result['order_id'];
		$order_status_id         = $result['order_status_id'];
		$notify                  = $result['notify'];
		$comment                 = addslashes($result['comment']);
		$date_added              = $result['date_added'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_history` (`order_history_id`,`order_id`,`order_status_id`,`notify`,`comment`,`date_added`) 
	             VALUES ('$order_history_id','$order_id','$order_status_id','$notify','$comment','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER HISTORY" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Order OPTION records
if(isset($this->request->post['input_o_option']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_option` $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_option_id         = $result['order_option_id'];
		$order_id                = $result['order_id'];
		$order_product_id        = $result['order_product_id'];
		$product_option_id       = $result['product_option_id'];
		$product_option_value_id = $result['product_option_value_id'];
		$name                    = addslashes($result['name']);
		$value                   = addslashes($result['value']);
		$type                    = $result['type'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_option` (`order_option_id`,`order_id`,`order_product_id`,`product_option_id`,`product_option_value_id`,`name`,`value`,`type`) 
	             VALUES ('$order_option_id','$order_id','$order_product_id','$product_option_id','$product_option_value_id','$name','$value','$type')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER OPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Order PRODUCT records
if(isset($this->request->post['input_o_product']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_product` $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_product_id       = $result['order_product_id'];
		$order_id               = $result['order_id'];
		$product_id             = $result['product_id'];
		$name                   = addslashes($result['name']);
		$model                  = addslashes($result['model']);
		$quantity               = $result['quantity'];
		$price                  = $result['price'];
		$total                  = $result['total'];
		$tax                    = $result['tax'];
		$reward                 = $result['reward'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_product` (`order_product_id`,`order_id`,`product_id`,`name`,`model`,`quantity`,`price`,`total`,`tax`,`reward`) 
	             VALUES ('$order_product_id','$order_id','$product_id','$name','$model','$quantity','$price','$total','$tax','$reward')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER PRODUCT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Order RECURRING records
if(isset($this->request->post['input_o_recurring']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_recurring` $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_recurring_id     = $result['order_recurring_id'];
		$order_id               = $result['order_id'];
		$reference              = $result['reference'];
		$product_id             = $result['product_id'];
		$product_name           = addslashes($result['product_name']);
		$product_quantity       = $result['product_quantity'];
		$recurring_id           = $result['recurring_id'];
		$recurring_name         = addslashes($result['recurring_name']);
		$recurring_description  = addslashes($result['recurring_description']);
		$recurring_frequency    = addslashes($result['recurring_frequency']);
		$recurring_cycle        = $result['recurring_cycle'];
		$recurring_duration     = $result['recurring_duration'];
		$recurring_price        = $result['recurring_price'];
		$trial                  = $result['trial'];
		$trial_frequency        = addslashes($result['trial_frequency']);
		$trial_cycle            = $result['trial_cycle'];
		$trial_duration         = $result['trial_duration'];
		$trial_price            = $result['trial_price'];
		$status                 = $result['status'];
		$date_added             = $result['date_added'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_recurring` (`order_recurring_id`,`order_id`,`reference`,`product_id`,`product_name`,`product_quantity`,`recurring_id`,`recurring_name`,`recurring_description`,`recurring_frequency`,`recurring_cycle`,`recurring_duration`,`recurring_price`,`trial`,`trial_frequency`,`trial_cycle`,`trial_duration`,`trial_price`,`status`,`date_added`) 
	             VALUES ('$order_recurring_id','$order_id','$reference','$product_id','$product_name','$product_quantity','$recurring_id','$recurring_name','$recurring_description','$recurring_frequency','$recurring_cycle','$recurring_duration','$recurring_price','$trial','$trial_frequency','$trial_cycle','$trial_duration','$trial_price','$status','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER RECURRING" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['recurring'] = $this->language->get('error_recurring');
		    exit;
		}
    }
}

// Get Order RECURRING TRANSACTION records
if(isset($this->request->post['input_o_recurring_txn']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_recurring_transaction`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_recurring_transaction_id = $result['order_recurring_transaction_id'];
		$order_recurring_id             = $result['order_recurring_id'];
		$reference                      = addslashes($result['reference']);
		$type                           = addslashes($result['type']);
		$amount                         = $result['amount'];
		$date_added                     = $result['date_added'];

	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_recurring_transaction` (`order_recurring_transaction_id`,`order_recurring_id`,`reference`,`type`,`amount`,`date_added`) 
	             VALUES ('$order_recurring_transaction_id','$order_recurring_id','$reference','$type','$amount','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER RECURRING TRANSACTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Order SHIPMENT records
if(isset($this->request->post['input_o_shipment']))
{ 
    $sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "order_shipment'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
    {
	    $oh = 0;
        $sql = "SELECT * FROM `" . $db_prefix1 . "order_shipment` $ADDITIONAL_SQL";
        $query = mysqli_query($con1,$sql);

        while($result = $query->fetch_assoc())
        {
            $order_shipment_id    = $result['order_shipment_id'];
		    $order_id             = $result['order_id'];
		    $date_added           = $result['date_added'];
		    $shipping_courier_id  = $result['shipping_courier_id'];
		    $tracking_number      = $result['tracking_number'];
	
	        $sql2 = "INSERT INTO `" . $db_prefix2 . "order_shipment` (`order_shipment_id`,`order_id`,`date_added`,`shipping_courier_id`,`tracking_number`) 
	                 VALUES ('$order_shipment_id','$order_id','$date_added','$shipping_courier_id','$tracking_number')";
	
	        if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	        {
	            $oh++;
	        } else {
                echo "Error in ORDER SHIPMENT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		        echo "SQL2: " . $sql2 . "<br />";
		        $this->error['product'] = $this->language->get('error_product');
		        exit;
	    	}
		}
    }
}

// Get Order STATUS records
if(isset($this->request->post['input_o_status']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_status`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_status_id         = $result['order_status_id'];
		$language_id             = $result['language_id'];
		$name                    = $result['name'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_status` (`order_status_id`,`language_id`,`name`) 
	             VALUES ('$order_status_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER STATUS" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Order TOTAL records
if(isset($this->request->post['input_o_total']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_total`  $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_total_id         = $result['order_total_id'];
		$order_id               = $result['order_id'];
		$code                   = $result['code'];
		$title                  = addslashes($result['title']);
		$value                  = addslashes($result['value']);
		$sort_order             = $result['sort_order'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_total` (`order_total_id`,`order_id`,`code`,`title`,`value`,`sort_order`) 
	             VALUES ('$order_total_id','$order_id','$code','$title','$value','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER TOTAL" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Order VOUCHER records
if(isset($this->request->post['input_o_voucher']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "order_voucher`  $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $order_voucher_id         = $result['order_voucher_id'];
		$order_id                 = $result['order_id'];
		$voucher_id               = $result['voucher_id'];
		$description              = addslashes($result['description']);
		$code                     = addslashes($result['code']);
		$from_name                = addslashes($result['from_name']);
		$to_name                  = addslashes($result['to_name']);
		$to_email                 = $result['to_email'];
		$voucher_theme_id         = $result['voucher_theme_id'];
		$message                  = addslashes($result['message']);
		$amount                   = $result['amount'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "order_voucher` (`order_voucher_id`,`order_id`,`voucher_id`,`description`,`code`,`from_name`,`to_name`,`to_email`,`voucher_theme_id`,`message`,`amount`)
	             VALUES ('$order_voucher_id','$order_id','$voucher_id','$description','$code','$from_name','$to_name','$to_email','$voucher_theme_id','$message','$amount')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in ORDER VOUCHER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get ALL RETURN records
if(isset($this->request->post['input_lo_return']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "return`  $ADDITIONAL_SQL";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$return_id            = $result['return_id'];
		$order_id             = $result['order_id'];
		$product_id           = $result['product_id'];
		$customer_id          = $result['customer_id'];
		$firstname            = addslashes($result['firstname']);
	    $lastname             = addslashes($result['lastname']);
	    $email                = $result['email'];
	    $telephone            = addslashes($result['telephone']);
		$product              = addslashes($result['product'] );
		$model                = addslashes( $result['model']);
		$quantity             = $result['quantity'];
		$opened               = $result['opened'];
		$return_reason_id     = $result['return_reason_id'];
		$return_action_id     = $result['return_action_id'];
		$return_status_id     = $result['return_status_id'];
		$comment              = addslashes($result['comment']);
		$date_ordered         = $result['date_ordered'];
    	$date_added           = $result['date_added'];
	    $date_modified        = $result['date_modified'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "return` (`return_id`,`order_id`,`product_id`,`customer_id`,`firstname`,`lastname`,`email`,`telephone`,`product`,`model`,`quantity`,`opened`,`return_reason_id`,`return_action_id`,`return_status_id`,`comment`,`date_ordered`,`date_added`,`date_modified`) 
	             VALUES ('$return_id','$order_id','$product_id','$customer_id','$firstname','$lastname','$email','$telephone','$product','$model','$quantity','$opened','$return_reason_id','$return_action_id','$return_status_id','$comment','$date_ordered','$date_added','$date_modified')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RETURN" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }

// Get RETURN ACTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "return_action`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$return_action_id    = $result['return_action_id'];
		$language_id         = $result['language_id'];
		$name                = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "return_action` (`return_action_id`,`language_id`,`name`) 
	             VALUES ('$return_action_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RETURN ACTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get RETURN HISTORY records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "return_history`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$return_history_id   = $result['return_history_id'];
		$return_id           = $result['return_id'];
		$return_status_id    = $result['return_status_id'];
		$notify              = $result['notify'];
		$comment             = addslashes($result['comment']);
    	$date_added          = $result['date_added'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "return_history` (`return_history_id`,`return_id`,`return_status_id`,`notify`,`comment`,`date_added`) 
	             VALUES ('$return_history_id','$return_id','$return_status_id','$notify','$comment','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RETURN HISTORY" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get RETURN REASON records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "return_reason`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$return_reason_id    = $result['return_reason_id'];
		$language_id         = $result['language_id'];
		$name                = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "return_reason` (`return_reason_id`,`language_id`,`name`) 
	             VALUES ('$return_reason_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RETURN REASON" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get RETURN STATUS records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "return_status`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$return_status_id    = $result['return_status_id'];
		$language_id         = $result['language_id'];
		$name                = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "return_status` (`return_status_id`,`language_id`,`name`) 
	             VALUES ('$return_status_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RETURN STATUS" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get ALL RECURRING records
if(isset($this->request->post['input_lo_recurring']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "recurring`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$_id            = $result['_id'];
		
		$recurring_id         = $result['recurring_id'];
		$price                = $result['price'];
		$frequency            = $result['frequency'];
		$duration             = $result['duration'];
		$cycle                = $result['cycle'];
		$trial_status         = $result['trial_status'];
		$trial_price          = $result['trial_price'];
		$trial_frequency      = $result['trial_frequency'];
		$trial_duration       = $result['trial_duration'];
		$trial_cycle          = $result['trial_cycle'];
        $status               = $result['status'];
		$sort_order           = $result['sort_order'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "recurring` (`recurring_id`,`price`,`frequency`,`duration`,`cycle`,`trial_status`,`trial_price`,`trial_frequency`,`trial_duration`,`trial_cycle`,`status`,`sort_order`) 
	             VALUES ('$recurring_id','$price','$frequency','$duration','$cycle','$trial_status','$trial_price','$trial_frequency','$trial_duration','$trial_cycle','$status','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RECURRING" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }

// Get RECURRING DESCRIPTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "recurring_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$recurring_id        = $result['recurring_id_id'];
		$language_id         = $result['language_id'];
		$name                = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "recurring_description` (`recurring_id`,`language_id`,`name`) 
	             VALUES ('$recurring_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in RECURRING DESCRIPTIONN" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}


