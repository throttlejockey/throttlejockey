<?php
    // header('Content-Type: text/xml');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once "supertidy.php";
    include_once "ups_codes.php";
    // print "hey.";
    $WEIGHT = 1;

    $LENGTH = 9;  // in
    $WIDTH  = 6;  // in
    $HEIGHT = 1;  // in

    define( 'NINEBYSIX', false );
    if( NINEBYSIX ) {
        $outTitle = '9" x 6" x 1"   |   1 lbs';
        $XML_SurePost   = file_get_contents(__DIR__."/request-surepost-9x6x1.xml");
        $XML_OtherRates = file_get_contents(__DIR__."/request-rate-9x6x1.xml");
    } else {
        $outTitle = '24" x 19" x 1"  |  1 lbs';
        $XML_SurePost   = file_get_contents(__DIR__."/request-surepost.xml");
        $XML_OtherRates = file_get_contents(__DIR__."/request-rate.xml");
    }


    $search  = array("{LENGTH}","{WIDTH}","{HEIGHT}","{WEIGHT}");
    $replace = array( $LENGTH, $WIDTH, $HEIGHT, $WEIGHT );

    $XML_SurePostRequest = str_replace($search, $replace, $XML_SurePost);
    $XML_RatesRequest    = str_replace($search, $replace, $XML_OtherRates);


    /** SURE POST  */
    $surepost_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/Rate');
   
    curl_setopt($surepost_curl, CURLOPT_HEADER, 0);
    curl_setopt($surepost_curl, CURLOPT_POST, 1);
    curl_setopt($surepost_curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($surepost_curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($surepost_curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($surepost_curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($surepost_curl, CURLOPT_POSTFIELDS, $XML_SurePostRequest);

    $surepost_result = curl_exec($surepost_curl);
    curl_close($surepost_curl);
    // // ----------------------

    $surepost_dom = new DOMDocument('1.0', 'UTF-8');
    // Initial block (must before load xml string)
    $surepost_dom->preserveWhiteSpace = false;
    $surepost_dom->formatOutput = true;
    // End initial block
    $surepost_dom->loadXml($surepost_result);
    // $x = new DOMDocument;
    // $x->loadHTML($dirty);
    $surepost_rates_xml = $surepost_dom->saveXML();

   /** SURE POST  */


    /** OTHER RATES  */
    $other_rates_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/Rate');

    curl_setopt($other_rates_curl, CURLOPT_HEADER, 0);
    curl_setopt($other_rates_curl, CURLOPT_POST, 1);
    curl_setopt($other_rates_curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($other_rates_curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($other_rates_curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($other_rates_curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($other_rates_curl, CURLOPT_POSTFIELDS, $XML_RatesRequest);

    $other_rates_result = curl_exec($other_rates_curl);
    curl_close($other_rates_curl);
    // // ----------------------

    $other_rates_dom = new DOMDocument('1.0', 'UTF-8');
    // Initial block (must before load xml string)
    $other_rates_dom->preserveWhiteSpace = false;
    $other_rates_dom->formatOutput = true;
    // End initial block
    $other_rates_dom->loadXml($other_rates_result);
    $other_rates_xml = $other_rates_dom->saveXML();

    

 



    /** OTHER RATES  */

    // print "#### SURE POST \n\r\n\r\n\r";
    // print_r ( $surepost_rates_xml );
    // print "\n\r\n\r\n\r";
    // print "#### OTHER RATES \n\r\n\r\n\r";
    // print_r ( $other_rates_xml );



    $surepost_dom_path = new DOMXPath($surepost_dom);
 
    $dom = joinXML($surepost_result, $other_rates_result, 'RatingServiceSelectionResponse');


    // print_r($dom);

    function joinXML($parent, $child, $tag = null)
    {
        $DOMChild = new DOMDocument;
        $DOMChild->loadXML($child);
        $node = $DOMChild->documentElement;
       
        $DOMParent = new DOMDocument;
        $DOMParent->formatOutput = true;
        $DOMParent->loadXML($parent);

        $node = $DOMParent->importNode($node, true);

        if ($tag !== null) {
            $tag = $DOMParent->getElementsByTagName($tag)->item(0);
            $tag->appendChild($node);
        } else {
            $DOMParent->documentElement->appendChild($node);
        }

        // return $DOMParent->saveXML();
        return $DOMParent;//
    }




            // PARSE THE RESULT
			$error = '';
			$quote_data = array();
    $rating_service_selection_response = $dom->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
    $response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);
    $response_status_code = $response->getElementsByTagName('ResponseStatusCode');

    if ($response_status_code->item(0)->nodeValue != '1') {
        $error = $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorCode')->item(0)->nodeValue . ': ' . $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;
    } else {
        $rated_shipments = $rating_service_selection_response->getElementsByTagName('RatedShipment');

        foreach ($rated_shipments as $rated_shipment) {
            $service = $rated_shipment->getElementsByTagName('Service')->item(0);

            $code = $service->getElementsByTagName('Code')->item(0)->nodeValue;

            $total_charges = $rated_shipment->getElementsByTagName('TotalCharges')->item(0);


                        /** Negotiated Rates */
                        // Returned XML Structure:
                        // RatedShipment
                        //   -> NegotiatedRates
                        //     -> NetSummaryCharges
                        //       -> GrandTotal
                        //         -> MonetaryValue
                        // Ensure sure we actually have a negotiated rate to use.
                        $has_negotiated_rates=$rated_shipment->getElementsByTagName('NegotiatedRates') ;
                        if ($has_negotiated_rates->length==0) {
                            continue;
                        }

                        $negotiated_rate = $rated_shipment->getElementsByTagName('NegotiatedRates')->item(0);


                        $net_summary = $negotiated_rate->getElementsByTagName('NetSummaryCharges')->item(0);
                        $grand_total = $net_summary->getElementsByTagName('GrandTotal')->item(0);
                        $cost = $grand_total->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;

                        /* Generic Estimate, non-negotiated **/
                        // $cost = $total_charges->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;

						$currency = $total_charges->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;


            if (!($code && $cost)) {
                continue;
            }

            // if ($this->config->get('ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code)) {
                $quote_data[$code] = array(
                    'code'         => 'ups.' . $code,
                    'title'        => $service_code[$code],
                    'cost'         => $cost, //$this->currency->convert($cost, $currency, $this->config->get('config_currency')),
                    'tax_class_id' => 0, //$this->config->get('shipping_ups_tax_class_id'),
                    'text'         => $cost, //$this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->session->data['currency']), $this->config->get('shipping_ups_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000)
                );
            // }


        }
    }
 
    print "<pre>";
    print $outTitle;
    print "<br><br>";
    print_r($quote_data);
    print "</pre>";
 


?>

