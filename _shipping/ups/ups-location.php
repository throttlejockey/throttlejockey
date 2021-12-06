<?php
    header('Content-Type: text/xml');
/*
  $ups_key = '0CFF620989E39008';
  $ups_username = 'mattdavis.qvm';
  $ups_password = 'golT5E0!';

  $av_xml  = '<?xml version="1.0"?>';
  $av_xml .= '<AccessRequest xml:lang="en-US">';
  $av_xml .= '    <AccessLicenseNumber>' . $ups_key . '</AccessLicenseNumber>';
  $av_xml .= '    <UserId>' . $ups_username . '</UserId>';
  $av_xml .= '    <Password>' . $ups_password . '</Password>';
  $av_xml .= '</AccessRequest>';
  $av_xml .= '<?xml version="1.0"?>';
  $av_xml .= '<AddressValidationRequest xml:lang="en-US">';
  $av_xml .= '    <Request>';
  $av_xml .= '        <TransactionReference>';
  $av_xml .= '            <CustomerContext>Street Level Address Validation Request</CustomerContext>';
  $av_xml .= '            <XpciVersion>1.0</XpciVersion>';
  $av_xml .= '        </TransactionReference>';
  $av_xml .= '        <RequestAction>XAV</RequestAction>';
  $av_xml .= '        <RequestOption>3</RequestOption>';
  $av_xml .= '    </Request>';
  $av_xml .= '<AddressKeyFormat>';
  $av_xml .= '  <ConsigneeName>Chris Roe - Michigan</ConsigneeName>';
  $av_xml .= '  <AddressLine>3145 Kassab Ln</AddressLine>';
  $av_xml .= '  <PoliticalDivision2>Commerce</PoliticalDivision2>';
  $av_xml .= '  <PoliticalDivision1>MI</PoliticalDivision1>';
  $av_xml .= '  <PostcodePrimaryLow>48382</PostcodePrimaryLow>';
  $av_xml .= '  <CountryCode>US</CountryCode>';
  $av_xml .= '</AddressKeyFormat>';
  $av_xml .= '</AddressValidationRequest>';
*/

  $XML_Request=file_get_contents(__DIR__ . "/request-location.xml");
  // print "<pre>";
  // var_dump($XML_Request);
  // print "</pre>";
  // print $XML_Request;
  // print __DIR__;

  // $av_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/Rate');
  $av_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/XAV');

  curl_setopt($av_curl, CURLOPT_HEADER, 0);
  curl_setopt($av_curl, CURLOPT_POST, 1);
  curl_setopt($av_curl, CURLOPT_TIMEOUT, 120);
  curl_setopt($av_curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($av_curl, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($av_curl, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($av_curl, CURLOPT_POSTFIELDS, $XML_Request);

  $av_result = curl_exec($av_curl);

  curl_close($av_curl);

  // $av_dom = new DOMDocument('1.0', 'UTF-8');
  // $av_dom->loadXml($av_result);


  $av_dom = new DOMDocument('1.0', 'UTF-8');
  $av_dom->loadXml($av_result);

  $cleanxml = tidy_repair_string($av_result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);

  // if (!empty($av_dom->getElementsByTagName('Description')->item(0)->nodeValue)) {
  //     $address_type = strtolower($av_dom->getElementsByTagName('Description')->item(0)->nodeValue);

  //     if($address_type=="commercial") {
  //         print("Commercial Address\r\n");
  //     } else {
  //         print("Residential Address\r\n");
  //     }

  // }


//  print_r($av_result);
  // print "<br><br>";
   // print "<pre><code>";
//   print_r ($av_result);
// echo htmlentities($av_result, ENT_COMPAT, 'UTF-8');
   // var_dump($av_result);
   print $cleanxml;
   // print "</code></pre>";

?>