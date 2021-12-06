<?
    // header('Content-Type: text/xml');
  /*
    Measure the three parcel dimensions in inches.
      Example: 30″ (Length) x 10″ (Width) x 15″ (Height)

    Add the measurements of the two smallest dimensions together and multiply by two. This is the girth of your package.
      Example: [10″ (Width) + 15″  (Height) ] x 2 = 50″ (Girth)

    Take the longest dimension and add this to the package girth. The result is the combined length and girth of your parcel.
      Example: 30″ (Length) + 50″  (Girth) = 80″  (Combined Length and Girth)

I could go up to 30”x21”x6” and 6lbs and still be at $8.79 postal.
1 more inch at 7” and/or lb to 7lbs and it would go up to like $19.06.
Matt Davis
*/

  // 30x21x6 @ 6 lbs
  // Cost:  <Rate>11.30</Rate>
  //        <CommercialRate>8.79</CommercialRate>
  // $Width = 30.00000000;
  // $Height= 21.00000000;
  // $Length = 6.00000000;
  // $Pounds = 6;


  // 30x21x7 @ 6 lbs
  // Cost:  <Rate>25.35</Rate>
  //        <CommercialRate>19.02</CommercialRate>
  // $Width = 30.00000000;
  // $Height= 21.00000000;
  // $Length = 7.00000000;
  // $Pounds = 6;


  // 25”X19”X3” @ 4 lbs
  // Cost:  <Rate>9.90</Rate>
  //        <CommercialRate>8.05</CommercialRate>


  $ZipDestination = 92058;
  $Width = 15.00000000;
  $Height= 9.00000000;
  $Length = 1.00000000;
  $Pounds = 0;
  $Ounces = 5;

  $weight = 1.5;
  $weight = ($weight < 0.1 ? 0.1 : $weight);
  $pounds = floor($weight);
  $ounces = round(16 * ($pounds - $Ounces), 2); // max 5 digits
  // print "ounces: $ounces <br>";
  // print $ounces;
  // $ComputedOunces = (16*$Pounds) + $Ounces;
  // print "ComputedOunces: $ComputedOunces";
  $DimensionsArr = array($Width, $Height, $Length);

  // REGULAR: Package dimensions are 12’’ or less;
  // LARGE: Any package dimension is larger than 12’’.
  $PackageSize = max($DimensionsArr) <= 12 ? "REGULAR" : "LARGE";
  // print "<br>$PackageSize<br>";
  // Sorted least to greatest
  sort($DimensionsArr);

  // Girth is smallest 2 lengths * 2.
  $Girth = ($DimensionsArr[0] + $DimensionsArr[1]) * 2;
  // Some systems require combined girth.. Not USPS though.
  // $combined_girth = $girth + $DimensionsArr[2];

  $reqxml = '<RateV4Request USERID="881MOUNT1457">';
  $reqxml .= '  <Package ID="1">';
  $reqxml .= '    <Service>Online</Service>';
  $reqxml .= '    <ZipOrigination>46902</ZipOrigination>';
  $reqxml .= '    <ZipDestination>'.$ZipDestination.'</ZipDestination>';
  $reqxml .= '    <Pounds>' . $Pounds . '</Pounds>';
  $reqxml .= '    <Ounces>' . $Ounces . '</Ounces>';
  $reqxml .= '    <Container>VARIABLE</Container>';
  $reqxml .= '    <Size> ' . $PackageSize . '</Size>';
  $reqxml .= '    <Width>' . $Width . '</Width>';
  $reqxml .= '    <Length>' . $Length . '</Length>';
  $reqxml .= '    <Height>' . $Height . '</Height>';
  $reqxml .= '    <Girth> ' . $Girth . '</Girth>';
  $reqxml .= '    <Machinable>false</Machinable>';
  $reqxml .= '  </Package>';
  $reqxml .= '</RateV4Request>';

// $xml = $xml = <<<XML
// <RateV4Request USERID="881MOUNT1457">   <Package ID="1">       <Service>ONLINE</Service>       <ZipOrigination>46902</ZipOrigination>       <ZipDestination>72022</ZipDestination>       <Pounds>0</Pounds>       <Ounces>5</Ounces>       <Container>VARIABLE</Container>       <Size>LARGE</Size>       <Width>18</Width>       <Length>24</Length>       <Height>1</Height>       <Girth>0</Girth>       <Machinable>false</Machinable>   </Package></RateV4Request>
// XML;

$XML_RequestFile=file_get_contents(__DIR__."/usps-request-rate.xml");

  // print $reqxml;

  $reqxml = $XML_RequestFile;
  $curl = curl_init();

  $request = 'API=RateV4&XML=' . urlencode($reqxml);
  curl_setopt($curl, CURLOPT_URL, 'production.shippingapis.com/ShippingAPI.dll?' . $request);
  curl_setopt($curl, CURLOPT_HEADER, 0);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);

  curl_close($curl);


  $av_dom = new DOMDocument('1.0', 'UTF-8');
  $av_dom->preserveWhiteSpace = false;
  $av_dom->formatOutput = true;
  $av_dom->loadXml($result);
 
  $cleanxml = $av_dom->saveXML();
    
  // s$cleanxml = tidy_repair_string($av_result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);
 
  print $cleanxml;

//   print($result);


// />
?>