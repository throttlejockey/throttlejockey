<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    // header('Content-Type: text/xml');
 



    $UserID = "881MOUNT1457";
    $AddressID = "0";
    $Address1 = "2312 Lakeshore Dr.";
    $Address2 = "";
    $City = "Bryant";
    $State = "AR";
    $Zip5 = "72022";
    $Zip4 = "";


    $reqxml  = "<AddressValidateRequest USERID=\"$UserID\">";
    $reqxml .= "    <Address ID=\"$AddressID\">";
    $reqxml .= "        <Address1>\"$Address1\"</Address1>";
    $reqxml .= "        <Address2>\"$Address2\"</Address2>";
    $reqxml .= "        <City>\"$City\"</City>";
    $reqxml .= "        <State>\"$State\"</State>";
    $reqxml .= "        <Zip5>\"$Zip5\"</Zip5>";
    $reqxml .= "        <Zip4>\"$Zip4\"</Zip4>";
    $reqxml .= "    </Address>";
    $reqxml .= "</AddressValidateRequest>";
 

// $xml = $xml = <<<XML
// <RateV4Request USERID="881MOUNT1457">   <Package ID="1">       <Service>ONLINE</Service>       <ZipOrigination>46902</ZipOrigination>       <ZipDestination>72022</ZipDestination>       <Pounds>0</Pounds>       <Ounces>5</Ounces>       <Container>VARIABLE</Container>       <Size>LARGE</Size>       <Width>18</Width>       <Length>24</Length>       <Height>1</Height>       <Girth>0</Girth>       <Machinable>false</Machinable>   </Package></RateV4Request>
// XML;

//   $XML_RequestFile=file_get_contents(__DIR__."/usps-request-rate.xml");

  // print $reqxml;

//   $reqxml = $XML_RequestFile;

/*
  $curl = curl_init();

  $request = 'API=Verify&XML=' . urlencode($reqxml);

  curl_setopt($curl, CURLOPT_URL, 'production.shippingapis.com/ShippingAPI.dll?' . $request);
  curl_setopt($curl, CURLOPT_HEADER, 0);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);

  curl_close($curl);


  $av_dom = new DOMDocument('1.0', 'UTF-8');
  $av_dom->loadXml($av_result);
  $cleanxml = tidy_repair_string($result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);


  print $cleanxml;
*/
//   print($result);


// />
?>