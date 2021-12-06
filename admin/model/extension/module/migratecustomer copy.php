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

// Empty Tables and reset Auto-Increment
$this->load->model('extension/module/migratetov3');

// Empty Tables and reset Auto-Increment
$tables = array();

$tables[] = "customer"; $tables[] = "address";
if(isset($this->request->post['input_c_activity']) && $this->model_extension_module_migratetov3->checkBoth($con1, $con2, $db_prefix1, $db_prefix2, 'customer_activity'))
{   $exist_customer_activity=true; $tables[] = "customer_activity";    }
if(isset($this->request->post['input_c_affiliate']) && $this->model_extension_module_migratetov3->checkBoth($con1, $con2, $db_prefix1, $db_prefix2, 'customer_affiliate'))
{   $exist_customer_affiliate=true; $tables[] = "customer_affiliate";    }
if(isset($this->request->post['input_c_approval']) && $this->model_extension_module_migratetov3->checkBoth($con1, $con2, $db_prefix1, $db_prefix2, 'customer_approval'))
{   $exist_customer_approval=true; $tables[] = "customer_approval";    }
if(isset($this->request->post['input_c_history']))
{   $tables[] = "customer_history";    }
if(isset($this->request->post['input_c_ip']))
{   $tables[] = "customer_ip";    }
if(isset($this->request->post['input_c_online']))
{   $tables[] = "customer_online";    }
if(isset($this->request->post['input_c_reward']))
{   $tables[] = "customer_reward";    }
if(isset($this->request->post['input_c_search']) && $this->model_extension_module_migratetov3->checkBoth($con1, $con2, $db_prefix1, $db_prefix2, 'customer_search'))
{   $exist_customer_search=true; $tables[] = "customer_search";    }
if(isset($this->request->post['input_c_transaction']))
{   $tables[] = "customer_transaction";    }
if(isset($this->request->post['input_c_wishlist']) && $this->model_extension_module_migratetov3->checkBoth($con1, $con2, $db_prefix1, $db_prefix2, 'customer_wishlist'))
{   $exist_customer_wishlist=true; $tables[] = "customer_wishlist";    }
if(isset($this->request->post['input_c_group']))
{   $tables[] = "customer_group"; $tables[] = "customer_group_description";    }
if(isset($this->request->post['input_c_login']))
{   $tables[] = "customer_login";    }
if(isset($this->request->post['input_c_select']) && $this->model_extension_module_migratetov3->checkBoth($con1, $con2, $db_prefix1, $db_prefix2, 'customer_select'))
{   $exist_customer_select=true; $tables[] = "customer_select";    }

if(!$this->model_extension_module_migratetov3->emptyTable($con2,$db_prefix2,$tables))
{
    echo "Error in CUSTOMER TABLES<br />";
	print_r($tables);
	echo "<br />";
	$this->error['customer'] = $this->language->get('error_customer');
	exit;
} 


// Get Customer Records
if($this->model_extension_module_migratetov3->rowExists($con1, $db_prefix1,'customer','language_id'))
{   
    $lang1_id = true;
} else {
    $lang1_id = false;
}
if($this->model_extension_module_migratetov3->rowExists($con2, $db_prefix2,'customer','language_id'))
{   
    $lang2_id = true;
} else {
    $lang2_id = false;
}

$cc = 0;
$sql = "SELECT * FROM `" . $db_prefix1 . "customer`";
$query = mysqli_query($con1,$sql);

while($result = $query->fetch_assoc())
{
	$customer_id      = $result['customer_id'];
	$group            = $result['customer_group_id'];
	$store            = $result['store_id'];
	
	if ($lang1_id) {
	    $language_id  = $result['language_id'];
	} else {
	    $language_id      = '1';
	}
	$firstname        = addslashes($result['firstname']);
	$lastname         = addslashes($result['lastname']);
	$email            = addslashes($result['email']);
	$telephone        = addslashes($result['telephone']);
	$fax              = $result['fax'];
	$password         = $result['password'];
	$salt             = $result['salt'];
	$cart             = $result['cart'];
	$wishlist         = $result['wishlist'];
	$newsletter       = $result['newsletter'];
	$address_id       = $result['address_id'];
	$custom_field     = addslashes($result['custom_field']);
	$ip               = $result['ip'];
	$status           = $result['status'];
	$safe             = addslashes($result['safe']);
	$token            = $result['token'];
	$code             = '';
	$date_added       = $result['date_added'];
	
	if ($lang2_id) {
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "customer` (`customer_id`,`customer_group_id`, `store_id`, `language_id`, `firstname`, `lastname`, `email`, `telephone`, `fax`, `password`, `salt`, `cart`, `wishlist`, `newsletter`, `address_id`, `custom_field`, `ip`, `status`, `safe`, `token`, `code`, `date_added`) 
	         VALUES ('$customer_id','$group','$store','$language_id','$firstname','$lastname','$email','$telephone','$fax','$password','$salt','$cart','$wishlist','$newsletter','$address_id','$custom_field','$ip','$status','$safe','$token','$code','$date_added')";
	} else {
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "customer` (`customer_id`,`customer_group_id`, `store_id`, `firstname`, `lastname`, `email`, `telephone`, `fax`, `password`, `salt`, `cart`, `wishlist`, `newsletter`, `address_id`, `custom_field`, `ip`, `status`, `safe`, `token`, `code`, `date_added`) 
	         VALUES ('$customer_id','$group','$store','$firstname','$lastname','$email','$telephone','$fax','$password','$salt','$cart','$wishlist','$newsletter','$address_id','$custom_field','$ip','$status','$safe','$token','$code','$date_added')";
	}
	
	if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	{
	    $cc++;
	} else {
        echo "Error in CUSTOMER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		echo "SQL2: " . $sql2 . "<br />";
		$this->error['customer'] = $this->language->get('error_customer');
		exit;
    }

//Get ADDRESS Records
    $aa = 0;
    $sql3 = "SELECT * FROM `" . $db_prefix1 . "address` WHERE `customer_id` = '" . $customer_id . "'";
    $query3 = mysqli_query($con1,$sql3);
    while($result3 = $query3->fetch_assoc())
    {
        $address_id     = $result3['address_id'];
	    $firstname      = addslashes($result3['firstname']);
	    $lastname       = addslashes($result3['lastname']);
	    $company        = addslashes($result3['company']);
	    $address_1      = addslashes($result3['address_1']);
	    $address_2      = addslashes($result3['address_2']);
	    $city           = addslashes($result3['city']);
	    $postcode       = $result3['postcode'];
	    $country_id     = $result3['country_id'];
	    $zone_id        = $result3['zone_id'];
	    $custom_field   = addslashes($result3['custom_field']);
	
	    $sql4 = "INSERT INTO `" . $db_prefix2 . "address` (`address_id`,`customer_id`,`firstname`,`lastname`,`company`,`address_1`,`address_2`,`city`,`postcode`,`country_id`,`zone_id`,`custom_field`)
	             VALUES ('$address_id','$customer_id','$firstname','$lastname','$company','$address_1','$address_2','$city','$postcode','$country_id','$zone_id','$custom_field')";

    	if ($query4 = mysqli_query($con2,$sql4) === TRUE)
		{
	        $aa++;
	    } else {
	        echo "Error in CUSTOMER ADDRESS" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL4: " . $sql4 . "<br />";
	        $this->error['address'] = $this->language->get('error_address');
		    exit;
	    }
    }

//Get CUSTOMER ACTIVITY Records
if(isset($exist_customer_activity))
{
        if($this->model_extension_module_migratetov3->rowExists($con1, $db_prefix1,'customer_activity','activity_id'))
        {   
            $activity1 = true;
        } else {
            $activity1 = false;
        }
        if($this->model_extension_module_migratetov3->rowExists($con2, $db_prefix2,'customer_activity','activity_id'))
        {   
            $activity2 = true;
        } else {
            $activity2 = false;
        }
        
		$ca = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_activity` WHERE `customer_id` = '" . $customer_id . "'";
		$query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    if($activity1)
			{
			    $activity_id       = $result3['activity_id'];
			} else {
			    $activity_id       = $result3['customer_activity_id'];
			}
			$key               = $result3['key'];
	        $data              = addslashes($result3['data']);
	        $ip                = $result3['ip'];
	        $date_added        = $result3['date_added'];

            if($activity2)
			{
			    $id            = 'activity_id';
			} else {
			    $id            = 'customer_activity_id';
			}	        
			$sql4 = "INSERT INTO `" . $db_prefix2 . "customer_activity` (`$id`,`customer_id`,`key`,`data`,`ip`,`date_added`)
			         VALUES ('$activity_id','$customer_id','$key','$data','$ip','$date_added')";
			
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $ca++;
			} else {
			    echo "Error in CUSTOMER ACTIVITY" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	
}

//Get CUSTOMER AFFILIATE Records
if(isset($exist_customer_affiliate))
{
    
        $caf = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_affiliate` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
			$company             = addslashes($result3['company']);
	        $website             = $result3['website'];
	        $tracking            = $result3['tracking'];
	        $commission          = $result3['commission'];
			$tax                 = $result3['tax'];
			$payment             = $result3['payment'];
			$cheque              = $result3['cheque'];
			$paypal              = $result3['paypal'];
			$bank_name           = $result3['bank_name'];
			$bank_branch_number  = $result3['bank_branch_number'];
			$bank_swift_code     = $result3['bank_swift_code'];
			$bank_account_name   = $result3['bank_account_name'];
			$bank_account_number = $result3['bank_account_number'];
			$custom_field        = addslashes($result3['custom_field']);
			$status              = $result3['status'];
			$date_added          = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_affiliate` (`customer_id`,`company`,`website`,`tracking`,`commission`,`tax`,`payment`,`cheque`,`paypal`,`bank_name`,`bank_branch_number`,`bank_swift_code`,`bank_account_name`,`bank_account_number`,`custom_field`,`status`,`date_added`)
			         VALUES ('$customer_id','$company','$website','$tracking','$commission','$tax','$payment','$cheque','$paypal','$bank_name','$bank_branch_number','$bank_swift_code','$bank_account_name','$bank_account_number','$custom_field','status','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $caf++;
			} else {
			    echo "Error in CUSTOMER AFFILIATE" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	
}

//Get CUSTOMER APPROVAL Records
if(isset($exist_customer_approval))
{
    
		$cap = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_approval` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $customer_approval_id = $result3['customer_approval_id'];
			$type                 = $result3['type'];
			$date_added           = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_approval` (`customer_approval_id`,`customer_id`,`type`,`date_added`)
			         VALUES ('$customer_approval_id','$customer_id','$type','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $cap++;
			} else {
			    echo "Error in CUSTOMER APPROVAL" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	
}

//Get CUSTOMER HISTORY Records
if(isset($this->request->post['input_c_history']))
{
	$sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "customer_history'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
	{
		$ch = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_history` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $customer_history_id = $result3['customer_history_id'];
			$comment             = addslashes($result3['comment']);
			$date_added          = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_history` (`customer_history_id`,`customer_id`,`comment`,`date_added`)
			         VALUES ('$customer_history_id','$customer_id','$comment','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $ch++;
			} else {
			    echo "Error in CUSTOMER HISTORY" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	}
}
	
//Get CUSTOMER IP Records
if(isset($this->request->post['input_c_ip']))
{
	$sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "customer_ip'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
	{
		$ip = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_ip` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
			$customer_ip_id      = $result3['customer_ip_id'];
			$ip                  = $result3['ip'];
			$date_added          = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_ip` (`customer_ip_id`,`customer_id`,`ip`,`date_added`)
			         VALUES ('$customer_ip_id','$customer_id','$ip','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $ip++;
			} else {
			    echo "Error in CUSTOMER IP" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	}
}
		
//Get CUSTOMER ONLINE Records
if(isset($this->request->post['input_c_online']))
{
	$sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "customer_online'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
	{
		$ol = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_online` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $ip                  = $result3['ip'];
			$url                 = $result3['url'];
			$referer             = addslashes($result3['referer']);
			$date_added          = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_online` (`ip`,`customer_id`,`url`,`referer`,`date_added`)
			         VALUES ('$ip','$customer_id','$url','$referer','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $ol++;
			} else {
			    echo "Error in CUSTOMER ONLINE" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	}
}

//Get CUSTOMER REWARD Records
if(isset($this->request->post['input_c_reward']))
{
    $sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "customer_reward'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
	{
		$cr = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_reward` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $customer_reward_id   = $result3['customer_reward_id'];
			$order_id             = $result3['order_id'];
			$description          = addslashes($result3['description']);
			$points               = $result3['points'];
			$date_added           = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_reward` (`customer_reward_id`,`customer_id`,`order_id`,`description`,`points`,`date_added`)
			         VALUES ('$customer_reward_id','$customer_id','$order_id','$description','$points','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $cr++;
			} else {
			    echo "Error in CUSTOMER REWARD" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	}
}
	
//Get CUSTOMER SEARCH Records
if(isset($exist_customer_search))
{
	
		$cs = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_search` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $customer_search_id   = $result3['customer_search_id'];
			$store_id             = $result3['store_id'];
			$language_id          = $result3['language_id'];
			$keyword              = addslashes($result3['keyword']);
			$category_id          = $result3['category_id'];
			$sub_category         = $result3['sub_category'];
			$description          = addslashes($result3['description']);
			$products             = $result3['products'];
			$ip                   = $result3['ip'];
			$date_added           = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_search` (`customer_search_id`,`store_id`,`language_id`,`customer_id`,`keyword`,`category_id`,`sub_category`,`description`,`products`,`ip`,`date_added`)
			         VALUES ('$customer_search_id','$store_id','$language_id','$customer_id','$keyword','$category_id','$sub_category','$description','$products','$ip','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $cs++;
			} else {
			    echo "Error in CUSTOMER SEARCH" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	
}
	
//Get CUSTOMER TRANSACTION Records
if(isset($this->request->post['input_c_transaction']))
{
	$sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "customer_transaction'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
	{
		$ct = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_transaction` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $customer_transaction_id = $result3['customer_transaction_id'];
			$order_id                = $result3['order_id'];
			$description             = addslashes($result3['description']);
			$amount                  = $result3['amount'];
			$date_added              = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_transaction` (`customer_transaction_id`,`customer_id`,`order_id`,`description`,`amount`,`date_added`)
			         VALUES ('$customer_transaction_id','$customer_id','$order_id','$description','$amount','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $ct++;
			} else {
			    echo "Error in CUSTOMER TRANSACTION" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	}
}
	
//Get CUSTOMER WISHLIST Records
if(isset($exist_customer_wishlist))
{
	
		$cw = 0;
	    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_wishlist` WHERE `customer_id` = '" . $customer_id . "'";
        $query3 = mysqli_query($con1,$sql3);
		while($result3 = $query3->fetch_assoc())
        {
		    $product_id           = $result3['product_id'];
			$date_added           = $result3['date_added'];

	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_wishlist` (`customer_id`,`product_id`,`date_added`)
			         VALUES ('$customer_id','$product_id','$date_added')";
			if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
			    $cw++;
			} else {
			    echo "Error in CUSTOMER WISHLIST" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
			    $this->error['customer'] = $this->language->get('error_customer');
				exit;
			}
        }
	
}

// End of Customer ------------------------------------------------------------------
}
// ----------------------------------------------------------------------------------

//Get CUSTOMER GROUP Records
if(isset($this->request->post['input_c_group']))
{
    $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_group`";
    $query3 = mysqli_query($con1,$sql3);
    $cg = 0;
    while($result3 = $query3->fetch_assoc())
    {
		$customer_group_id   = $result3['customer_group_id'];
        $approval            = $result3['approval'];
	    $sort_order          = $result3['sort_order'];

        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_group` (`customer_group_id`,`approval`,`sort_order`)
	    	     VALUES ('$customer_group_id','$approval','$sort_order')";
        if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
		    $cg++;
//Get CUSTOMER GROUP DESCRIPTION Records
            $cgd = 0;
	        $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_group_description` WHERE `customer_group_id` = '" . $customer_group_id . "'";
            $query3 = mysqli_query($con1,$sql3);
		    while($result3 = $query3->fetch_assoc())
            {
		        $language_id      = $result3['language_id'];
	            $name             = addslashes($result3['name']);
			    $description      = addslashes($result3['description']);

    	        $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_group_description` (customer_group_id,language_id,name,description)
	    		         VALUES ('$customer_group_id','$language_id','$name','$description')";
 
	    		if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
		    	    $cgd++;
			    } else {
			        echo "Error in CUSTOMER GROUP DESCRIPTION" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
			        echo "SQL4: " . $sql4 . "<br />";
	                $this->error['customer'] = $this->language->get('error_customer');
	                exit;
			    }
            }
	    } else {
	        echo "Error in CUSTOMER GROUP" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
			echo "SQL4: " . $sql4 . "<br />";
	        $this->error['customer'] = $this->language->get('error_customer');
	        exit;
	    }
    }
}

//Get CUSTOMER LOGIN Records
if(isset($this->request->post['input_c_login']))
{
    $sql0 = "SHOW TABLES LIKE '" . $db_prefix1 . "customer_login'";
    $exists=mysqli_query($con1, $sql0);

    if($exists->num_rows == 1)
    {
        $cl = 0;
        $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_login`";
        $query3 = mysqli_query($con1,$sql3);
        while($result3 = $query3->fetch_assoc())
        {
            $customer_login_id   = $result3['customer_login_id'];
	        $email               = addslashes($result3['email']);
            $ip                  = $result3['ip'];
	        $total               = $result3['total'];
	        $date_added          = $result3['date_added'];
	        $date_modified       = $result3['date_modified'];

            $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_login` (`customer_login_id`,`email`,`ip`,`total`,`date_added`,`date_modified`)
	    	         VALUES ('$customer_login_id','$email','$ip','$total','$date_added','$date_modified')";
            if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
		        $cl++;
            } else {
	            echo "Error in CUSTOMER LOGIN" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
	            $this->error['customer'] = $this->language->get('error_customer');
	            exit;
	        }
        }
    }
}

//Get CUSTOMER SELECT Records
if(isset($exist_customer_search))
{
        $sel = 0;
        $sql3 = "SELECT * FROM `" . $db_prefix1 . "customer_select`";
        $query3 = mysqli_query($con1,$sql3);
        while($result3 = $query3->fetch_assoc())
        {
            $cust_field          = addslashes($result3['cust_field']);
	        $field_order         = $result3['field_order'];
            $field_header        = $result3['field_header'];

            $sql4 = "INSERT INTO `" . $db_prefix2 . "customer_select` (`cust_field`,`field_order`,`field_header`)
	        	     VALUES ('$cust_field','$field_order','$field_header')";
            if ($query4 = mysqli_query($con2,$sql4) === TRUE) {
		        $sel++;
            } else {
	            echo "Error in CUSTOMER SELECT" . $query4 . "<br />" . mysqli_error($con2) . "<br />";
				echo "SQL4: " . $sql4 . "<br />";
	            $this->error['customer'] = $this->language->get('error_customer');
	            exit;
	        }
        }
}

