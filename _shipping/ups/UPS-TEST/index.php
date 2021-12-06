<html>
<head>
<title>UPS API Sample - websitedesignby.com - naples, fl</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
 <div class="container">   
<p><a href="http://www.websitedesignby.com">websitedesignby.com</a></p>
<h1>UPS Rates API Example Using PHP</h1>

<h2>Enter Shipping Details</h2>
<form name="upsRate" class="form-horizontal" action="index.php" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="a" value="step1" />
    <div class="form-group">
        <label for="fromzip" class="col-sm-2 control-label">Shipping from Zip:</label>
        <div class="col-sm-10">
            <input type="text" name="fromzip" value="60608" />
        </div>
    </div>
    <div class="form-group">
        <label for="tozip" class="col-sm-2 control-label">Shipping to Zip:</label>
        <div class="col-sm-10">
            <input type="text" name="tozip" value="10001" />
        </div>
    </div>
    <div class="form-group">
        <label for="residential" class="col-sm-2 control-label">Residential:</label>
        <div class="col-sm-10">
            <input type="checkbox" name="residential" checked="checked" />
        </div>
    </div>
<div class="formline">
Package Dimensions (inches)<br />
    <div class="form-group">
        <label for="width" class="col-sm-2 control-label">Width:</label>
        <div class="col-sm-10">
            <input type="text" class="short" name="width" value="5" />
        </div>
    </div>
    <div class="form-group">
        <label for="height" class="col-sm-2 control-label">Height:</label>
        <div class="col-sm-10">
            <input type="text" class="short" name="height" value="5" />
        </div>
    </div>
    <div class="form-group">
        <label for="length" class="col-sm-2 control-label">Length:</label>
        <div class="col-sm-10">
            <input type="text" class="short" name="length" value="5" />
        </div>
    </div>
</div>
    <div class="form-group">
        <label for="weight" class="col-sm-2 control-label">Weight (LBS):</label> 
        <div class="col-sm-10">
            <input type="text" name="weight" class="short" value="0.01" />
        </div>
    </div>
    <div class="formline">
        Insurance:<br />
        <div class="form-group">
            <label for="insurance" class="col-sm-2 control-label">Insure package:</label>
            <div class="col-sm-10">
                <input type="checkbox" name="insurance"  />
            </div>
        </div>
        <div class="form-group">
            <label for="insurance_amount" class="col-sm-2 control-label">Insurance amount:</label>
            <div class="col-sm-10">
                <input type="text" name="insurance_amount" value="0"/>
            </div>
        </div>
    </div>
<div class="formline">
    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            <button type="submit" class="btn btn-primary">Get Rates</button>
        </div>
    </div>
</div>
</form>

</div>
</body>
</html>

