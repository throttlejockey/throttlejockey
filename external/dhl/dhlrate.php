<?php


/*** 
 * 
 *  Calculate Volumetric Weight.. 
 * http://wap.dhl.com/serv/volweight.html  
 * 
 */


define( 'DHL_TEST', false );

    // header( 'Content-Type: text/html' );
    $order_id = 0;
    if($_GET['order_id']) {
        $order_id = $_GET['order_id'];
    }


    // private $stagingUrl    = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';
    // private $productionUrl = 'https://xmlpi-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';


    // $XML_RequestFile = file_get_contents( __DIR__.'/request-test.xml' );
    $XML_RequestFile = file_get_contents( __DIR__."/$order_id.xml" );
    $CURL_RESULT = "";
    if ( !DHL_TEST ) {
        // $url = 'https://xmlpi-ea.dhl.com/XMLShippingServlet';
        $url = 'https://xmlpi-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';
    } else {
        $url = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet';
    }

    $curl = curl_init();
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $XML_RequestFile );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt_array( $curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_HEADER         => false,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
    ) );

    $result = utf8_encode( curl_exec( $curl ) );

    $xml = '';
    libxml_use_internal_errors( true );
    if ( !empty( $result ) ) {
        $xml = simplexml_load_string( utf8_encode( $result ) );
    }

    $result = $xml;
    $CURL_RESULT = $result;
    print '<pre>';
 
    if ( $result && !empty( $result->GetQuoteResponse->BkgDetails->QtdShp ) ) {

        foreach ( $result->GetQuoteResponse->BkgDetails->QtdShp as $quote ) {

            $rate_code     = ((string) $quote->GlobalProductCode );
            $rate_title    = ((string) $quote->ProductShortName );
  
            $rate_cost     = (float)((string) $quote->ShippingCharge );
            $rate_charge   = (float)((string) $quote->WeightCharge );
            $rate_taxes    = (float)((string) $quote->TotalTaxAmount );
            $discount      = (float)((string) $quote->TotalDiscount);

            $rate_cost_all = $rate_cost;

            if( $rate_code !== "P") continue;

            // print "<br><br>";

            print "<br><br>";
            print "Rate: [$rate_code] $rate_title <br>";
            print "<br>";

            print "Transportation Charges: $rate_charge";
            print "<br>";
            print "Discount Applied: -$discount";
            print "\r\n";
            print "<br><br>";

            if ( !empty( $quote->QtdShpExChrg ) ) {

                print_r( 'Extra Charges: ' );
                print "<br>";
                print_r( "---" );
                print "\r\n";

                foreach ( $quote->QtdShpExChrg as $ExtraCharge ) {
                    $exName = ( ( string ) $ExtraCharge->LocalServiceTypeName );
                    $exCost = ( float )( ( string ) $ExtraCharge->ChargeValue ) ;

                    if (strpos($exName, 'REMOTE') !== false) {
                        $rate_title .= " [remote fee]";
                    }
                    
                    $rate_cost_all = ( float )$rate_cost_all + ( float )$exCost;

                    print_r( $exName );
                    print ": "; 
                    print_r( $exCost );
                    print "\r\n";

                    print "New rate_cost_all: " . $rate_cost_all . "<br>";
                    // print_r( ( float )( ( string )$ExtraCharge->ChargeValue ) );
                }

            }

            print "<br><br>";
            print "Rate: [$rate_code] $rate_title <br>";
            print "<br><br>";
            
            print_r( "---" );
            print "<br><br>";
            print_r( 'Total Cost: ' );
            print (float)$rate_cost_all;
            print "\r\n";

            // $shcharge = $rate_cost
            print "<br>";
            print "Shipping Charge: $rate_cost";
            print "\r\n";
        }
    }

            // $selected_services_aaray = $this->config->get( 'shipping_hitshippo_dhlexpress_service' );
            print '<pre>';
            print "<br><br>";
            print "<br><br>";
            print "<br><br>";
            print "<br><br>";
            print "<br><br>";

            print "Request Sent: <br>";
            
            // print "<pre>$XML_RequestFile</pre>";
            // $xml = new SimpleXMLElement($XML_RequestFile);
            // echo $xml->asXML();
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->load ( __DIR__."/$order_id.xml" );
            $dom->formatOutput = true;
            printf ("<pre>%s</pre>", htmlentities ($dom->saveXML()));
        
            // $dom2 = new DOMDocument();
            // $dom2->preserveWhiteSpace = false;
            // $dom2->loadXML ( $CURL_RESULT );
            // $dom2->formatOutput = true;
            // printf ("<pre>%s</pre>", htmlentities ($dom2->saveXML()));
            print "Full Response: <br>";
            print_r( $result );
            print "\r\n";
        
?>