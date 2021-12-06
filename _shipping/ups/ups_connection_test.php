<?php
    // header('Content-Type: text/xml');
    header('Content-Type: text/html');
    $LENGTH = 1;  // in
    $WIDTH  = 18;  // in
    $HEIGHT = 24;  // in
    $WEIGHT = 3;  // lbs

    // $XML_RequestFile=file_get_contents(__DIR__."/request-test.xml");
    $XML_RequestFile=file_get_contents(__DIR__."/request-rate.xml");    

    $av_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/Rate');
    // $av_curl = curl_init('https://wwwcie.ups.com/ups.app/xml/Rate');

    $search  = array("{LENGTH}","{WIDTH}","{HEIGHT}","{WEIGHT}");
    $replace = array( $LENGTH, $WIDTH, $HEIGHT, $WEIGHT );

    $XML_Request = str_replace($search, $replace, $XML_RequestFile);



    

    curl_setopt($av_curl, CURLOPT_HEADER, 0);
    curl_setopt($av_curl, CURLOPT_POST, 1);
    curl_setopt($av_curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($av_curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($av_curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($av_curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($av_curl, CURLOPT_POSTFIELDS, $XML_Request);

    $av_result = curl_exec($av_curl);

    curl_close($av_curl);

    print "<br/>" . "response: <br/>";

    $cleanxml = tidy_repair_string($av_result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);

    print "<pre>";
    print_r($cleanxml);
    print "</pre>";


    if($av_result){


        // ----------------------
        // print 'UPS XML <br/>';
        // var_dump($XML_RequestFile);

        // print "<br/>" . "response: <br/>";
        // var_dump($av_result);

        // ----------------------

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXml($av_result);

        $cleanxml = tidy_repair_string($av_result, ['input-xml'=> 1, 'indent' => 1, 'wrap' => 0]);


        print $rating_service_selection_response;
        print "<br/>";

        // print $cleanxml;
        // print "</code></pre>";



    }

    
?>