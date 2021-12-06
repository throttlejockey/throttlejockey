<?php
    header('Content-Type: text/xml');

    $LENGTH = 1;  // in
    $WIDTH  = 18;  // in
    $HEIGHT = 24;  // in
    $WEIGHT = 3;  // lbs

    $XML_RequestFile=file_get_contents(__DIR__."/request-rate.xml");

    $search  = array("{LENGTH}","{WIDTH}","{HEIGHT}","{WEIGHT}");
    $replace = array( $LENGTH, $WIDTH, $HEIGHT, $WEIGHT );

    $XML_Request = str_replace($search, $replace, $XML_RequestFile);


    $av_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/Rate');

    curl_setopt($av_curl, CURLOPT_HEADER, 0);
    curl_setopt($av_curl, CURLOPT_POST, 1);
    curl_setopt($av_curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($av_curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($av_curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($av_curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($av_curl, CURLOPT_POSTFIELDS, $XML_Request);

    $av_result = curl_exec($av_curl);

    curl_close($av_curl);

    // ----------------------

    $av_dom = new DOMDocument('1.0', 'UTF-8');
    $av_dom->loadXml($av_result);

    $cleanxml = tidy_repair_string($av_result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);


    print $cleanxml;
    // print "</code></pre>";

?>

