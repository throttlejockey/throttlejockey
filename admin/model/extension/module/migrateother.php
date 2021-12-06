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

if(isset($this->request->post['input_x_coupon']))
{   $tables[] = "coupon"; $tables[] = "coupon_category"; $tables[] = "coupon_history"; $tables[] = "coupon_product";    }
if(isset($this->request->post['input_x_voucher']))
{   $tables[] = "voucher"; $tables[] = "voucher_history"; $tables[] = "voucher_theme"; $tables[] = "voucher_theme_description";    }

if(!$this->model_extension_module_migratetov3->emptyTable($con2,$db_prefix2,$tables))
{
    echo "Error in OTHERS DATABASE TABLES<br />";
	print_r($tables);
	echo "<br />";
	$this->error['other'] = $this->language->get('error_other');
	exit;
} 

	
// Get COUPON records
if(isset($this->request->post['input_x_coupon']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "coupon`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $coupon_id               = $result['coupon_id'];
		$name                    = addslashes($result['name']);
		$code                    = addslashes($result['code']);
		$type                    = $result['type'];
		$discount                = $result['discount'];
		$logged                  = $result['logged'];
		$shipping                = $result['shipping'];
		$total                   = $result['total'];
		$date_start              = $result['date_start'];
		$date_end                = $result['date_end'];
        $uses_total              = $result['uses_total'];
		$uses_customer           = $result['uses_customer'];
		$status                  = $result['status'];
		$date_added              = $result['date_added'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "coupon` (`coupon_id`,`name`,`code`,`type`,`discount`,`logged`,`shipping`,`total`,`date_start`,`date_end`,`uses_total`,`uses_customer`,`status`,`date_added`) 
	             VALUES ('$coupon_id','$name','$code','$type','$discount','$logged','$shipping','$total','$date_start','$date_end','$uses_total','$uses_customer','$status','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in COUPON" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }


// Get COUPON CATEGORY records
	$oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "coupon_category`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $coupon_id            = $result['coupon_id'];
		$category_id          = $result['category_id'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "coupon_category` (`coupon_id`,`category_id`) 
	             VALUES ('$coupon_id','$category_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in COUPON CATEGORY" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }

// Get COUPON HISTORY records
	$oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "coupon_history`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $coupon_history_id      = $result['coupon_history_id'];
		$coupon_id              = $result['coupon_id'];
		$order_id               = $result['order_id'];
		$customer_id            = $result['customer_id'];
		$amount                 = $result['amount'];
		$date_added              = $result['date_added'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "coupon_history` (`coupon_history_id`,`coupon_id`,`order_id`,`customer_id`,`amount`,`date_added`) 
	             VALUES ('$coupon_history_id','$coupon_id','$order_id','$customer_id','$amount','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in COUPON HISTORY" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }
	
// Get COUPON PRODUCT records
	$oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "coupon_product`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $coupon_product_id        = $result['coupon_product_id'];
		$coupon_id                = $result['coupon_id'];
		$product_id               = $result['product_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "coupon_product` (`coupon_product_id`,`coupon_id`,`product_id`) 
	             VALUES ('$coupon_product_id','$coupon_id','$product_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in COUPON PRODUCT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }
}

// Get VOUCHER records
if(isset($this->request->post['input_x_voucher']))
{ 
    $oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "voucher`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $voucher_id             = $result['voucher_id'];
		$order_id               = $result['order_id'];
		$code                   = addslashes($result['code']);
		$from_name              = addslashes($result['from_name']);
		$from_email             = addslashes($result['from_email']);
		$to_name                = addslashes($result['to_name']);
		$to_email               = addslashes($result['to_email']);
		$voucher_theme_id       = $result['vouchertheme__id'];
		$message                = addslashes($result['message']);
		$amount                 = $result['amount'];
		$status                 = $result['status'];
		$date_added             = $result['date_added'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "voucher` (`voucher_id`,`order_id`,`code`,`from_name`,`from_email`,`to_name`,`to_email`,`voucher_theme_id`,`message`,`amount`,`status`,`date_added`) 
	             VALUES ('$voucher_id','$order_id','$code','$from_name','$from_email','$to_name','$to_email','$voucher_theme_id','$message','$amount','$status','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in VOUCHER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }

// Get VOUCHER HISTORY records
	$oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "voucher_history`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $voucher_history_id      = $result['voucher_history_id'];
		$voucher_id              = $result['voucher_id'];
		$order_id                = $result['order_id'];
		$amount                  = $result['amount'];
		$date_added              = $result['date_added'];
		

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "voucher_history` (`voucher_history_id`,`voucher_id`,`order_id`,`amount`,`date_added`) 
	             VALUES ('$voucher_history_id','$voucher_id','$order_id','$amount','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in VOUCHER HISTORY" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }

// Get VOUCHER THEME records
	$oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "voucher_theme`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $voucher_theme_id      = $result['voucher_theme_id'];
		$image                 = addslashes($result['image']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "voucher_theme` (`voucher_theme_id`,`image`) 
	             VALUES ('$voucher_theme_id','$image')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in VOUCHER THEME" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }
	
// Get VOUCHER THEME DESCRIPTION records
	$oh = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "voucher_theme_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $voucher_theme_id        = $result['voucher_theme_id'];
		$language_id             = $result['language_id'];
		$name                    = addslashes($result['name']);
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "voucher_theme_description` (`voucher_theme_id`,`language_id`,`name`) 
	             VALUES ('$voucher_theme_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $oh++;
	    } else {
            echo "Error in VOUCHER THEME DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['other'] = $this->language->get('error_other');
		    exit;
		}
    }
}
