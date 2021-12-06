<?php
// ini_set ('display_errors', 'true');
// error_reporting(E_ALL);

/*
Valid domestic values: 
“14” = Next Day Air Early 
AM  
“01” = Next Day Air  
“13” = Next Day Air Saver 
“59” = 2nd Day Air AM  
“02” = 2nd Day Air 
“12” = 3 Day Select 
“03” = Ground 


Valid international values: 
“11”= Standard  
“07” = Worldwide Express  
“54” = Worldwide Express Plus
“08” = Worldwide Expedited
“65” = Saver 
 
Required for Rating and 
Ignored for Shopping 
 
Valid Poland to Poland 
Same Day values:
“82” = UPS Today Standard
“83” = UPS Today Dedicated Courier
“84” = UPS Today Intercity
“85” = UPS Today Express
“86” = UPS Today Express Saver   

Packaging Type Codes
“00” = “UNKNOWN”
“01” = UPS Letter
“02” = Package
“03” = Tube
“04” = Pak
“21” = Express Box
“24” = 25KG Box
“25” = 10KG Box
“30” = Pallet
“2a” = Small Express Box
“2b” = Medium Express Box
“2c” = Large Express Box 
*/


if(isset($_REQUEST['fromzip'])){
	$fromzip = $_REQUEST['fromzip'];
}else{
	$fromzip = "90210";
}
if(isset($_REQUEST['tozip'])){
	$tozip = $_REQUEST['tozip'];
}else{
	$tozip = "10001";
}
if(isset($_REQUEST['width'])){
	$width = $_REQUEST['width'];
}else{
	$width = 6;
}
if(isset($_REQUEST['height'])){
	$height = $_REQUEST['height'];
}else{
	$height = 6;
}
if(isset($_REQUEST['length'])){
	$length = $_REQUEST['length'];
}else{
	$length = 6;
}
if(isset($_REQUEST['weight'])){
	$weight = $_REQUEST['weight'];
}else{
	$weight = 5;
}
?>
<form name="upsRate" action="upsRateForm.php" method="post" enctype="application/x-www-form-urlencoded">
<input type="hidden" name="a" value="step1" />
<div class="formline">
Shipping from Zip: <input type="text" name="fromzip" value="<?php echo $fromzip; ?>" />
</div>
<div class="formline">
Shipping to Zip: <input type="text" name="tozip" value="<?php echo $tozip; ?>" />
</div>
<div class="formline">
<strong>Package Dimensions (inches)</strong>
Width: <input type="text" class="short" name="width" value="<?php echo $width; ?>" /> 
Height: <input type="text" class="short" name="height" value="<?php echo $height; ?>" /> 
Length: <input type="text" class="short" name="length" value="<?php echo $length; ?>" />
</div>
<div class="formline">
Weight (LBS): <input type="text" name="weight" class="short" value="<?php echo $weight; ?>" />
</div>
<div class="formline">
<input type="submit" />
</div>
</form>
<?php

if(isset($_REQUEST['a']) && $_REQUEST['a'] == "step1"){

	$errors = false;
	$errmessage = "";
	if(is_numeric($_REQUEST['fromzip'])){
		$fromzip = $_REQUEST['fromzip'];
	}else{
		$errors = true;
		$errmessage = "Please enter a valid Shipping from Zip.<br />\n";
	}
	if(is_numeric($_REQUEST['tozip'])){
		$tozip = $_REQUEST['tozip'];
	}else{
		$errors = true;
		$errmessage = "Please enter a valid Shipping from Zip.<br />\n";
	}
	if(is_numeric($_REQUEST['length'])){
		$length = $_REQUEST['length'];
	}else{
		$errors = true;
		$errmessage = "Please enter a valid length.<br />\n";
	}
	if(is_numeric($_REQUEST['width'])){
		$width = $_REQUEST['width'];
	}else{
		$errors = true;
		$errmessage = "Please enter a valid width.<br />\n";
	}
	if(is_numeric($_REQUEST['height'])){
		$height=$_REQUEST['height'];
	}else{
		$errors = true;
		$errmessage = "Please enter a valid height.<br />\n";
	}
	if(is_numeric($_REQUEST['weight'])){
		$weight = $_REQUEST['weight'];
	}else{
		$errors = true;
		$errmessage = "Please enter a valid weight.<br />\n";
	}
		
	if(!$errors){
		
		require("upsRate.php");

		/*************************************
		Get your own credentials from ups.com
		*************************************/
		$ups_accessnumber = "";
		$ups_username = "";
		$ups_password = "";
		$ups_shippernumber = "";
	
		// just doing domestic for demonstration purposes
		$services = array(
						"Next Day Air (early AM)"=>"14", 
						"Next Day Air"=>"01", 
						"Next Day Air Saver"=>"13", 
						"2nd Day Air AM"=>"59", 
						"2nd Day Air"=>"02", 
						"3 Day Select"=>"12", 
						"Ground"=>"03"
					);
		
		$myRate = new upsRate;
		$myRate->setCredentials($ups_accessnumber, $ups_username, $ups_password, $ups_shippernumber);
		echo "<h1>UPS Rates</h1>\n";
		echo "<ul>\n";
		foreach($services as $name=>$value){
			$service = $value;
			$rate = $myRate->getRate($fromzip, $tozip, $service, $length, $width, $height, $weight);
			echo "<li>$name - $rate</li>\n";
		}
		echo "</ul>";
	
	}else{
		
		echo "<h2>Form Errors:</h2><p>$errmessage</p>\n";
	
	}
	

}
?>
