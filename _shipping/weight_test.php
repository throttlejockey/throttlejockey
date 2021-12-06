<?
    header('Content-Type: text/xml');
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

/*

  First-Class™ packages cannot exceed 22" x 18" x 15". 
  Contents for Priority Mail Express, Priority Mail, or Media Mail® must weigh less than 70 lbs. 
  First-Class™ packages must weigh less than 16 oz. Maximum size is 130" in combined length and girth (distance around the thickest part).

    Yes, that is correct, USPS has limitations on length+girth. 
    Girth = 2*width+2*height, assuming that length is the longest side. 
    Girth+length = length+2*width+2*height. Girth itself does not include length in it.


*/

  $ZipDestination = 72022;

//   $Width = 15.00000000;
//   $Height= 9.00000000;
//   $Length = 1.00000000;

  $Width = 18.00000000;
  $Height= 1.00000000;
  $Length = 22.00000000;


  $Pounds = 0;
  $Ounces = 8; // 8; // ( 0.5 lbs )

//   print ($Pounds * 16) + $Ounces;

  $weight = 1.5;
  $weight = ($weight < 0.1 ? 0.1 : $weight);
  $pounds = floor($weight);
  $ounces = round(16 * ($pounds - $ounces), 2); // max 5 digits


//   print "ounces: $ounces <br>";
  // print $ounces;
  // $ComputedOunces = (16*$Pounds) + $Ounces;
//   print "ComputedOunces: $ComputedOunces";
  $DimensionsArr = array($Width, $Height, $Length);

  // REGULAR: Package dimensions are 12’’ or less;
  // LARGE: Any package dimension is larger than 12’’.
  $PackageSize = "REGULAR"; // ( (int)(max($DimensionsArr)) <= 12 ) ? "REGULAR" : "LARGE";
//   print max($DimensionsArr);
  
  // print "<br>$PackageSize<br>";

  // Sorted least to greatest
  sort($DimensionsArr);
    // print_r($DimensionsArr);
  // Girth is smallest 2 lengths * 2.
  $Girth = ($DimensionsArr[0] + $DimensionsArr[1]) * 2;
  // Some systems require combined girth.. Not USPS though.
  $combined_girth = $Girth + $DimensionsArr[2];

    // print "\r\n";
    // print "Girth: $Girth \r\n";
    // print "\r\n";
    // print "Combined Girth: $combined_girth \r\n";
    // print "\r\n";

  $reqxml = '<RateV4Request USERID="881MOUNT1457">';
  $reqxml .= '  <Package ID="1">';
  $reqxml .= '    <Service>Online</Service>';
//   $reqxml .= '    <Service>All</Service>';
  $reqxml .= '    <ZipOrigination>46902</ZipOrigination>';
  $reqxml .= '    <ZipDestination>'.$ZipDestination.'</ZipDestination>';
  $reqxml .= '    <Pounds>' . $Pounds . '</Pounds>';
  $reqxml .= '    <Ounces>' . $Ounces . '</Ounces>';
  $reqxml .= '    <Container>VARIABLE</Container>';
  $reqxml .= '    <Size> ' . $PackageSize . '</Size>';
  $reqxml .= '    <Width>' . $Width . '</Width>';
  $reqxml .= '    <Length>' . $Length . '</Length>';
  $reqxml .= '    <Height>' . $Height . '</Height>';
//   $reqxml .= '    <Girth> ' . $Girth . '</Girth>';
  $reqxml .= '    <Girth> ' . $combined_girth . '</Girth>';
  $reqxml .= '    <Machinable>false</Machinable>';
  $reqxml .= '  </Package>';
  $reqxml .= '</RateV4Request>';

  $cleanxmltmp = tidy_repair_string($reqxml, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);
//   print $cleanxmltmp;
  
$xml = $xml = <<<XML
<RateV4Request USERID="881MOUNT1457">   <Package ID="1">       <Service>ONLINE</Service>       <ZipOrigination>46902</ZipOrigination>       <ZipDestination>72022</ZipDestination>       <Pounds>0</Pounds>       <Ounces>5</Ounces>       <Container>VARIABLE</Container>       <Size>LARGE</Size>       <Width>18</Width>       <Length>24</Length>       <Height>1</Height>       <Girth>0</Girth>       <Machinable>false</Machinable>   </Package></RateV4Request>
XML;
  // print $reqxml;

//   $reqxml = $xml;
  $curl = curl_init();

  $request = 'API=RateV4&XML=' . urlencode($reqxml);
  curl_setopt($curl, CURLOPT_URL, 'production.shippingapis.com/ShippingAPI.dll?' . $request);
  curl_setopt($curl, CURLOPT_HEADER, 0);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);

  curl_close($curl);

  $av_dom = new DOMDocument('1.0', 'UTF-8');
  $av_dom->loadXml($result);

  $rate_response = $av_dom->getElementsByTagName('RateV4Response')->item(0);
  $intl_rate_response = $av_dom->getElementsByTagName('IntlRateV2Response')->item(0);
  $error = $av_dom->getElementsByTagName('Error')->item(0);


  $cleanxml = tidy_repair_string($result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);


  print $cleanxml;


// />
?>