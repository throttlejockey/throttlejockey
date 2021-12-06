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

$tables[] = "category";
if(isset($this->request->post['input_cat_description']))
{   $tables[] = "category_description";    }
if(isset($this->request->post['input_cat_filter']))
{   $tables[] = "category_filter";    }
if(isset($this->request->post['input_cat_path']))
{   $tables[] = "category_path";    }
if(isset($this->request->post['input_cat_to_layout']))
{   $tables[] = "category_to_layout";    }
if(isset($this->request->post['input_cat_to_store']))
{   $tables[] = "category_to_store";    }

if(!$this->model_extension_module_migratetov3->emptyTable($con2,$db_prefix2,$tables))
{
    echo "Error in CATEGORY DATABASE TABLES<br />";
	print_r($tables);
	echo "<br />";
	$this->error['category'] = $this->language->get('error_category');
	exit;
} 

// Get category Records
$p = 0;
$sql = "SELECT * FROM `" . $db_prefix1 . "category`";
$query = mysqli_query($con1,$sql);

while($result = $query->fetch_assoc())
{
	$category_id       = $result['category_id'];
	$image             = $result['image'];
	$parent_id         = $result['parent_id'];
	$top               = $result['top'];
	$column            = $result['column'];
	$sort_order        = $result['sort_order'];
	$status            = $result['status'];
	$date_added        = $result['date_added'];
	$date_modified     = $result['date_modified'];

	$sql2 = "INSERT INTO `" . $db_prefix2 . "category` (`category_id`,`image`,`parent_id`,`top`,`column`,`sort_order`,`status`,`date_added`,`date_modified`) 
	         VALUES ('$category_id','$image','$parent_id','$top','$column','$sort_order','$status','$date_added','$date_modified')";
	
	if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	{
	    $p++;
	} else {
        echo "Error in category" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		echo "SQL2: " . $sql2 . "<br />";
		$this->error['category'] = $this->language->get('error_category');
		exit;
    }
}

// Get category DESCRIPTION records
if(isset($this->request->post['input_cat_description']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "category_description`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $category_id       = $result['category_id'];
		$language_id       = $result['language_id'];
		$name              = addslashes($result['name']);
		$description       = addslashes($result['description']);
        // doesn't exist in 1.5, maybe should have done sanity checks?
        // $meta_title        = addslashes($result['meta_title']);
		$meta_description  = addslashes($result['meta_description']);
		$meta_keyword      = addslashes($result['meta_keyword']);
	
	    // $sql2 = "INSERT INTO `" . $db_prefix2 . "category_description` (`category_id`,`language_id`,`name`,`description`,`meta_title`,`meta_description`,`meta_keyword`) 
	    //          VALUES ('$category_id','$language_id','$name','$description','$meta_title','$meta_description','$meta_keyword')";

        // Removing meta_title... not in 1.5
        $sql2 = "INSERT INTO `" . $db_prefix2 . "category_description` (`category_id`,`language_id`,`name`,`description`,`meta_description`,`meta_keyword`) 
                VALUES ('$category_id','$language_id','$name','$description','$meta_description','$meta_keyword')";

	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in category DESCRIPTION" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['category'] = $this->language->get('error_category');
		    exit;
		}
    }
}

// Get category FILTER records
if(isset($this->request->post['input_cat_filter']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "category_filter`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $category_id        = $result['category_id'];
		$filter_id          = $result['filter_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "category_filter` (`category_id`,`filter_id`) 
	             VALUES ('$category_id','$filter_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in category FILTER" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['category'] = $this->language->get('error_category');
		    exit;
		}
    }
}

// Get category PATH records
if(isset($this->request->post['input_cat_path']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "category_path`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $category_id        = $result['category_id'];
		$path_id            = $result['path_id'];
		$level              = $result['level'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "category_path` (`category_id`,`path_id`,`level`) 
	             VALUES ('$category_id','$path_id','$level')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in category PATH" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['category'] = $this->language->get('error_category');
		    exit;
		}
    }
}

// Get category CATEGORY_TO_LAYOUT records
if(isset($this->request->post['input_cat_to_layout']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "category_to_layout`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $category_id        = $result['category_id'];
		$store_id           = $result['store_id'];
		$layout_id          = $result['layout_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "category_to_layout` (`category_id`,`store_id`,`layout_id`) 
	             VALUES ('$category_id','$store_id','$layout_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in category CATEGORY_TO_LAYOUT" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['category'] = $this->language->get('error_category');
		    exit;
		}
    }
}

// Get category CATEGORY_TO_STORE records
if(isset($this->request->post['input_cat_to_store']))
{ 
    $pa = 0;
    $sql = "SELECT * FROM `" . $db_prefix1 . "category_to_store`";
    $query = mysqli_query($con1,$sql);

    while($result = $query->fetch_assoc()) {
        $category_id        = $result['category_id'];
		$store_id            = $result['store_id'];
	
	    $sql2 = "INSERT INTO `" . $db_prefix2 . "category_to_store` (`category_id`,`store_id`) 
	             VALUES ('$category_id','$store_id')";
	
	    if ($query2 = mysqli_query($con2,$sql2) === TRUE)
	    {
	        $pa++;
	    } else {
            echo "Error in category CATEGORY_TO_STORE" . $query2 . "<br />" . mysqli_error($con2) . "<br />";
		    echo "SQL2: " . $sql2 . "<br />";
		    $this->error['category'] = $this->language->get('error_category');
		    exit;
		}
    }
}