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

$tables[] = "product";
if(isset($this->request->post['input_p_attribute']))
{   $tables[] = "product_attribute";    }
if(isset($this->request->post['input_p_description']))
{   $tables[] = "product_description";    }
if(isset($this->request->post['input_p_discount']))
{   $tables[] = "product_discount";    }
if(isset($this->request->post['input_p_filter']))
{   $tables[] = "product_filter";    }
if(isset($this->request->post['input_p_image']))
{   $tables[] = "product_image";    }
if(isset($this->request->post['input_p_option']))
{   $tables[] = "product_option";    }
if(isset($this->request->post['input_p_option_value']))
{   $tables[] = "product_option_value";    }
if(isset($this->request->post['input_p_recurring']))
{   $tables[] = "product_recurring";    }
if(isset($this->request->post['input_p_related']))
{   $tables[] = "product_related";    }
if(isset($this->request->post['input_p_reward']))
{   $tables[] = "product_reward";    }
if(isset($this->request->post['input_p_special']))
{   $tables[] = "product_special";    }
if(isset($this->request->post['input_p_to_category']))
{   $tables[] = "product_to_category";    }
if(isset($this->request->post['input_p_to_download']))
{   $tables[] = "product_to_download";    }
if(isset($this->request->post['input_p_to_layout']))
{   $tables[] = "product_to_layout";    }
if(isset($this->request->post['input_p_to_store']))
{   $tables[] = "product_to_store";    }
if(isset($this->request->post['input_p_review']))
{   $tables[] = "review";    }
if(isset($this->request->post['input_p_attributes']))
{   $tables[] = "attribute"; $tables[] = "attribute_description"; $tables[] = "attribute_group"; $tables[] = "attribute_group_description";   }
if(isset($this->request->post['input_p_options']))
{   $tables[] = "option"; $tables[] = "option_description"; $tables[] = "option_value"; $tables[] = "option_value_description";   }
if(isset($this->request->post['input_p_manufacturers']))
{   $tables[] = "manufacturer"; $tables[] = "manufacturer_to_store";   }
if(isset($this->request->post['input_p_downloads']))
{   $tables[] = "download"; $tables[] = "download_description";   }

if(!$this->model_extension_module_migratetov3->emptyTable($con2,$db_prefix2,$tables))
{
    echo "Error in PRODUCT DATABASE TABLES<br />";
	print_r($tables);
	echo "<br />";
	$this->error['product'] = $this->language->get('error_product');
	exit;
} 


// Get Product Records
$p = 0;
$sql = "SELECT * FROM `" . $db_prefix1 . "product`";
$query = mysqli_query($con1,$sql);


while($result = $query->fetch_assoc())
{
	$product_id        = $result['product_id'];
	// $model             = $result['model'];
	$model             = addslashes($result['model']);
	// $model             = string_replace("'", "\'", $result['model']);    
	$sku               = $result['sku'];
	$upc               = $result['upc'];
	$ean               = $result['ean'];
	$jan               = $result['jan'];
	$isbn              = $result['isbn'];
	$mpn               = $result['mpn'];
	$location          = $result['location'];
	$quantity          = $result['quantity'];
	$stock_status_id   = $result['stock_status_id'];
	$image             = $result['image'];
	$manufacturer_id   = $result['manufacturer_id'];
	$shipping          = $result['shipping'];
	$price             = $result['price'];
	$points            = $result['points'];
	$tax_class_id      = $result['tax_class_id'];
	$date_available    = $result['date_available'];
	$weight            = $result['weight'];
	$weight_class_id   = $result['weight_class_id'];
	$length            = $result['length'];
	$width             = $result['width'];
	$height            = $result['height'];
	$length_class_id   = $result['length_class_id'];
	$subtract          = $result['subtract'];
	$minimum           = $result['minimum'];
	$sort_order        = $result['sort_order'];
	$status            = $result['status'];
	$viewed            = $result['viewed'];
	$date_added        = $result['date_added'];
	$date_modified     = $result['date_modified'];

	$sql2 = "INSERT INTO `" . $db_prefix2 . "product` (`product_id`,`model`,`sku`,`upc`,`ean`,`jan`,`isbn`,`mpn`,`location`,`quantity`,`stock_status_id`,`image`,`manufacturer_id`,`shipping`,`price`,`points`,`tax_class_id`,`date_available`,`weight`,`weight_class_id`,`length`,`width`,`height`,`length_class_id`,`subtract`,`minimum`,`sort_order`,`status`,`viewed`,`date_added`,`date_modified`) 
	         VALUES ('$product_id','$model','$sku','$upc','$ean','$jan','$isbn','$mpn','$location','$quantity','$stock_status_id','$image','$manufacturer_id','$shipping','$price','$points','$tax_class_id','$date_available','$weight','$weight_class_id','$length','$width','$height','$length_class_id','$subtract','$minimum','$sort_order','$status','$viewed','$date_added','$date_modified')";
	
	if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	{
	    $p++;
	} else {
        echo "Error in PRODUCT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		echo "SQL2: " . $sql2 . "<br />";
		$this->error['product'] = $this->language->get('error_product');
		exit;
    }
}

// Get Product ATTRIBUTE records
if(isset($this->request->post['input_p_attribute']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_attribute`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc())
    {
        $product_id        = $result['product_id'];
		$attribute_id      = $result['attribute_id'];
		$language_id       = $result['language_id'];
		$text              = $result['text'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_attribute` (`product_id`,`attribute_id`,`language_id`,`text`) 
	             VALUES ('$product_id','$attribute_id','$language_id','$text')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT ATTRIBUTE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}
	
// Get Product DESCRIPTION records
if(isset($this->request->post['input_p_description']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $product_id        = $result['product_id'];
		$language_id       = $result['language_id'];
		$name              = addslashes($result['name']);
		$description       = addslashes($result['description']);
		$tag               = addslashes($result['tag']);
		$meta_title        = $name; // addslashes($result['meta_title']);
        // print "meta tile " . $result['meta_title'] . "<br/>"; 
		$meta_description  = addslashes($result['meta_description']);
		$meta_keyword      = addslashes($result['meta_keyword']);
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_description` (`product_id`,`language_id`,`name`,`description`,`tag`,`meta_title`,`meta_description`,`meta_keyword`) 
	             VALUES ('$product_id','$language_id','$name','$description','$tag','$meta_title','$meta_description','$meta_keyword')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product DISCOUNT records
if(isset($this->request->post['input_p_discount']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_discount`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $product_discount_id = $result['product_discount_id'];
		$product_id          = $result['product_id'];
		$customer_group_id   = $result['customer_group_id'];
		$quantity            = $result['quantity'];
		$priority            = $result['priority'];
		$price               = $result['price'];
		$date_start          = $result['date_start'];
		$date_end            = $result['date_end'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_discount` (`product_discount_id`,`product_id`,`customer_group_id`,`quantity`,`priority`,`price`,`date_start`,`date_end`) 
	             VALUES ('$product_discount_id','$product_id','$customer_group_id','$quantity','$priority','$price','$date_start','$date_end')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT DISCOUNT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product FILTER records
if(isset($this->request->post['input_p_filter']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_filter`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $product_id         = $result['product_id'];
		$filter_id          = $result['filter_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_filter` (`product_id`,`filter_id`) 
	             VALUES ('$product_id','$filter_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT FILTER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product IMAGE records
if(isset($this->request->post['input_p_image']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_image`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $product_image_id  = $result['product_image_id'];
		$product_id        = $result['product_id'];
		$image             = $result['image'];
		$sort_order        = $result['sort_order'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_image` (`product_image_id`,`product_id`,`image`,`sort_order`) 
	             VALUES ('$product_image_id','$product_id','$image','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT IMAGE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product OPTION records
if(isset($this->request->post['input_p_option']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_option`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $product_option_id  = $result['product_option_id'];
		$product_id         = $result['product_id'];
		$option_id          = $result['option_id'];
		// $value              = $result['value'];
		$required           = $result['required'];
	
	    // $sql2 = "INSERT INTO `" . $db_prefix2 . "product_option` (`product_option_id`,`product_id`,`option_id`,`value`,`required`) 
	    //          VALUES ('$product_option_id','$product_id','$option_id','$value','$required')";

        // OC3 doesn't hae a value property
        $sql2 = "INSERT INTO `" . $db_prefix2 . "product_option` (`product_option_id`,`product_id`,`option_id`, `required`) 
        VALUES ('$product_option_id','$product_id','$option_id','$required')";                 
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT OPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product OPTION VALUE records
if(isset($this->request->post['input_p_option_value']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_option_value`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $product_option_value_id = $result['product_option_value_id'];
		$product_option_id       = $result['product_option_id'];
		$product_id              = $result['product_id'];
		$option_id               = $result['option_id'];
		$option_value_id         = $result['option_value_id'];
		$quantity                = $result['quantity'];
		$subtract                = $result['subtract'];
		$price                   = $result['price'];
		$price_prefix            = $result['price_prefix'];
		$points                  = $result['points'];
		$points_prefix           = $result['points_prefix'];
		$weight                  = $result['weight'];
		$weight_prefix           = $result['weight_prefix'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_option_value` (`product_option_value_id`,`product_option_id`,`product_id`,`option_id`,`option_value_id`,`quantity`,`subtract`,`price`,`price_prefix`,`points`,`points_prefix`,`weight`,`weight_prefix`) 
	             VALUES ('$product_option_value_id','$product_option_id','$product_id','$option_id','$option_value_id','$quantity','$subtract','$price','$price_prefix','$points','$points_prefix','$weight','$weight_prefix')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT OPTION VALUE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product RECURRING records
if(isset($this->request->post['input_p_recurring']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_recurring`";
    $query = mysqli_query($con1,$sql);

    if( count($query->rows) >= 1 ) 
    {

        while($result = $query->fetch_assoc()) {
            $product_id              = $result['product_id'];
            $recurring_id            = $result['recurring_id'];
            $customer_group_id       = $result['customer_group_id'];

        
            $sql2 = "INSERT INTO `" . $db_prefix2 . "product_recurring` (`product_id`,`recurring_id`,`customer_group_id`) 
                    VALUES ('$product_id','$recurring_id','$customer_group_id')";
        
            if ($query2 = mysqli_query($con2,$sql2) === TRUE)
            {
                $pa++;
            } else {
                echo "Error in PRODUCT RECURRING" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
                echo "SQL2: " . $sql2 . "<br />";
                $this->error['product'] = $this->language->get('error_product');
                exit;
            }
        }
    }

}

// Get Product RELATED records
if(isset($this->request->post['input_p_related']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_related`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_id            = $result['product_id'];
		$related_id            = $result['related_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_related` (`product_id`,`related_id`) 
	             VALUES ('$product_id','$related_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT RECURRING" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product REWARD records
if(isset($this->request->post['input_p_reward']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_reward`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_reward_id    = $result['product_reward_id'];
		$product_id           = $result['product_id'];
		$customer_group_id    = $result['customer_group_id'];
		$points               = $result['points'];

	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_reward` (`product_reward_id`,`product_id`,`customer_group_id`,`points`) 
	             VALUES ('$product_reward_id','$product_id','$customer_group_id','$points')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT REWARD" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product SPECIAL records
if(isset($this->request->post['input_p_special']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_special`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_special_id   = $result['product_special_id'];
		$product_id           = $result['product_id'];
		$customer_group_id    = $result['customer_group_id'];
		$priority             = $result['priority'];
		$price                = $result['price'];
		$date_start           = $result['date_start'];
		$date_end             = $result['date_end'];

	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_special` (`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`) 
	             VALUES ('$product_special_id','$product_id','$customer_group_id','$priority','$price','$date_start','$date_end')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT SPECIAL" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product TO CATEGORY records
if(isset($this->request->post['input_p_to_category']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_to_category`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_id           = $result['product_id'];
		$category_id          = $result['category_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_to_category` (`product_id`,`category_id`) 
	             VALUES ('$product_id','$category_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT TO CATEGORY" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product TO DOWNLOAD records
if(isset($this->request->post['input_p_to_download']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_to_download`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_id           = $result['product_id'];
		$download_id          = $result['download_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_to_download` (`product_id`,`download_id`) 
	             VALUES ('$product_id','$download_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT TO DOWNLOAD" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product TO LAYOUT records
if(isset($this->request->post['input_p_to_layout']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_to_layout`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_id        = $result['product_id'];
		$store_id          = $result['store_id'];
		$layout_id         = $result['layout_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_to_layout` (`product_id`,`store_id`,`layout_id`) 
	             VALUES ('$product_id','$store_id','$layout_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT TO LAYOUT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product TO STORE records
if(isset($this->request->post['input_p_to_store']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "product_to_store`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$product_id         = $result['product_id'];
		$store_id           = $result['store_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "product_to_store` (`product_id`,`store_id`) 
	             VALUES ('$product_id','$store_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT TO STORE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get ALL ATTRIBUTE records
if(isset($this->request->post['input_p_attributes']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "attribute`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$attribute_id         = $result['attribute_id'];
		$attribute_group_id   = $result['attribute_group_id'];
		$sort_order           = $result['sort_order'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "attribute` (`attribute_id`,`attribute_group_id`,`sort_order`) 
	             VALUES ('$attribute_id','$attribute_group_id','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in ATTRIBUTE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }

// Get ATTRIBUTE DESCRIPTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "attribute_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$attribute_id     = $result['attribute_id'];
		$language_id      = $result['language_id'];
		$name             = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "attribute_description` (`attribute_id`,`language_id`,`name`) 
	             VALUES ('$attribute_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in ATTRIBUTE DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get ATTRIBUTE GROUP records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "attribute_group`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$attribute_group_id   = $result['attribute_group_id'];
		$sort_order           = $result['sort_order'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "attribute_group` (`attribute_group_id`,`sort_order`) 
	             VALUES ('$attribute_group_id','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in ATTRIBUTE GROUP" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get ATTRIBUTE GROUP DESCRIPTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "attribute_group_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$attribute_group_id = $result['attribute_group_id'];
		$language_id        = $result['language_id'];
		$name               = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "attribute_group_description` (`attribute_group_id`,`language_id`,`name`) 
	             VALUES ('$attribute_group_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in ATTRIBUTE GROUP DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get ALL OPTION records
if(isset($this->request->post['input_p_options']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "option`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$option_id            = $result['option_id'];
		$type                 = $result['type'];
		$sort_order           = $result['sort_order'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "option` (`option_id`,`type`,`sort_order`) 
	             VALUES ('$option_id','$type','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in OPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }

// Get OPTION DESCRIPTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "option_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$option_id        = $result['option_id'];
		$language_id      = $result['language_id'];
		$name             = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "option_description` (`option_id`,`language_id`,`name`) 
	             VALUES ('$option_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in OPTION DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get OPTION VALUE records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "option_value`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$option_value_id    = $result['option_value_id'];
		$option_id          = $result['option_id'];
		$image              = $result['image'];
		$sort_order         = $result['sort_order'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "option_value` (`option_value_id`,`option_id`,`image`,`sort_order`) 
	             VALUES ('$option_value_id','$option_id','$image','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in OPTION VALUE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
	
// Get OPTION VALUE DESCRIPTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "option_value_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$option_value_id    = $result['option_value_id'];
		$language_id        = $result['language_id'];
		$option_id          = $result['option_id'];
		$name               = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "option_value_description` (`option_value_id`,`language_id`,`option_id`,`name`) 
	             VALUES ('$option_value_id','$language_id','$option_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in OPTION VALUE DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get ALL MANUFACTURER records
if(isset($this->request->post['input_p_manufacturers']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "manufacturer`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$manufacturer_id    = $result['manufacturer_id'];
		$name               = addslashes($result['name']);
		$image              = $result['image'];
		$sort_order         = $result['sort_order'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "manufacturer` (`manufacturer_id`,`name`,`image`,`sort_order`) 
	             VALUES ('$manufacturer_id','$name','$image','$sort_order')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in MANUFACTURER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }

// Get MANUFACTURER TO STORE records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "manufacturer_to_store`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$manufacturer_id        = $result['manufacturer_id'];
		$store_id               = $result['store_id'];

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "manufacturer_to_store` (`manufacturer_id`,`store_id`) 
	             VALUES ('$manufacturer_id','$store_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in MANUFACTURER TO STORE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get ALL DOWNLOAD records
if(isset($this->request->post['input_p_downloads']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "download`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$download_id          = $result['download_id'];
		$filename             = $result['filename'];
		$mask                 = $result['mask'];
		$date_added           = $result['date_added'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "download` (`download_id`,`filename`,`mask`,`date_added`) 
	             VALUES ('$download_id','$filename','$mask','$date_added')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in DOWNLOAD" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }

// Get DOWNLOAD DESCRIPTION records
	$pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "download_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$download_id        = $result['download_id'];
		$language_id        = $result['language_id'];
		$name               = addslashes($result['name']);

	    $sql2 = "INSERT INTO `" . $db_prefix2 . "download_description` (`download_id`,`language_id`,`name`) 
	             VALUES ('$download_id','$language_id','$name')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in DOWNLOAD DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}

// Get Product REVIEW records
if(isset($this->request->post['input_p_review']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "review`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
		$review_id            = $result['review_id'];
		$customer_id          = $result['customer_id'];
		$product_id           = $result['product_id'];
		$author               = addslashes($result['author']);
		$text                 = addslashes($result['text']);
		$rating               = $result['rating'];
		$status               = $result['status'];
		$date_added           = $result['date_added'];
		$date_modified        = $result['date_modified'];


	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "review` (`review_id`,`customer_id`,`product_id`,`author`,`text`,`rating`,`status`,`date_added`,`date_modified`) 
	             VALUES ('$review_id','$customer_id','$product_id','$author','$text','$rating','$status','$date_added','$date_modified')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in PRODUCT REVIEW" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['product'] = $this->language->get('error_product');
		    exit;
		}
    }
}