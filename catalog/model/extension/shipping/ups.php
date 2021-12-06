<?php
define( 'UPS_SHIPPER_NUMBER', "429744" );
define( 'UPS_SUREPOST_SHIPPER_NUMBER', "3YF222" );

/*
 * DEBUG WITH ChromePHP Logger 
 *
// $root = "/home/b16aa05/oc3.throttlejockey.com/";
$root = "/home/b16aa05/public_html/";    
if (file_exists($root . 'system/library/ChromePHP.php')) {
    require_once($root . 'system/library/ChromePHP.php');
}  
/******/



class ModelExtensionShippingUps extends Model {




    // ++++++ RM   
    function joinXML($parent, $child, $tag = null)
    {
        $DOMParent = new DOMDocument;
        $DOMParent->formatOutput = true;
        $DOMParent->loadXML($parent);

        if(empty($child)) {
            return $DOMParent;
        }        

        $DOMChild = new DOMDocument;
        $DOMChild->loadXML($child);
        $node = $DOMChild->documentElement;

        $node = $DOMParent->importNode($node, true);

        if ($tag !== null) {
            $tag = $DOMParent->getElementsByTagName($tag)->item(0);
            $tag->appendChild($node);
        } else {
            $DOMParent->documentElement->appendChild($node);
        }

        return $DOMParent;//
    }

    function getAddressType( $address ) {

        // ChromePhp::log("-- UPS Res|Commercial getAddressType ADDRESS --");
        // ChromePhp::log( print_r($address, 1 ) );
        // $this->log-write( print_r($address, 1) );

        $retType = "residential";
        // Clear Thinking: ups_residential_commercial_validation.xml
        $av_xml = '<?xml version="1.0"?>';
        $av_xml .= '<AccessRequest xml:lang="en-US">';
        $av_xml .= '    <AccessLicenseNumber>' . $this->config->get('shipping_ups_key') . '</AccessLicenseNumber>';
        $av_xml .= '    <UserId>' . $this->config->get('shipping_ups_username') . '</UserId>';
        $av_xml .= '    <Password>' . $this->config->get('shipping_ups_password'). '</Password>';
        $av_xml .= '</AccessRequest>';
        // ChromePhp::log("-- UPS Res|Commercial test 1 --");
        // ChromePhp::log( print_r($av_xml, 1) );
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
        $av_xml .= '    <AddressKeyFormat>';
        $av_xml .= '     <ConsigneeName>' . (isset($address['firstname']) ? $address['firstname'] : '') . ' ' . (isset($address['lastname']) ? $address['lastname'] : '') . '</ConsigneeName>';
        $av_xml .= '        <AddressLine>' . (isset($address['address_1']) ? $address['address_1'] : '') . '</AddressLine>';
        $av_xml .= '        <PoliticalDivision2>' . (isset($address['city']) ? $address['city'] : '') . '</PoliticalDivision2>';
        $av_xml .= '     <PoliticalDivision1>' . (isset($address['zone_code']) ? $address['zone_code'] : '') . '</PoliticalDivision1>';
        $av_xml .= '        <PostcodePrimaryLow>' . (isset($address['postcode']) ? $address['postcode'] : '') . '</PostcodePrimaryLow>';
        $av_xml .= '        <CountryCode>' . (isset($address['iso_code_2']) ? $address['iso_code_2'] : '') . '</CountryCode>';
        $av_xml .= '    </AddressKeyFormat>';
        $av_xml .= '</AddressValidationRequest>';
 
        // ChromePhp::log("-- UPS Res|Commercial test --");
        // $tmpOut = htmlspecialchars($av_xml, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        // ChromePhp::log( $tmpOut );


        $av_curl = curl_init('https://onlinetools.ups.com/ups.app/xml/XAV');

        curl_setopt($av_curl, CURLOPT_HEADER, 0);
        curl_setopt($av_curl, CURLOPT_POST, 1);
        curl_setopt($av_curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($av_curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($av_curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($av_curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($av_curl, CURLOPT_POSTFIELDS, $av_xml);

        $av_result = curl_exec($av_curl);

        curl_close($av_curl);

        // $this->log->write("-- UPS Res|Commercial test --");
        // $this->log->write($av_xml);
        // ChromePhp::log("-- UPS Res|Commercial test --");
        // ChromePhp::log($av_result);
        // ChromePhp::log( print_r($address, 1 ) );

        $av_dom = new DOMDocument('1.0', 'UTF-8');
        $av_dom->loadXml($av_result);

        if (!empty($av_dom->getElementsByTagName('Description')->item(0)->nodeValue)) {
            // $this->address_type = strtolower($av_dom->getElementsByTagName('Description')->item(0)->nodeValue);
            $addressType = $av_dom->getElementsByTagName('Description')->item(0)->nodeValue; 
            // if the address type is resdiential or unknown but the dfault is residential then use that, otherwise assume commercial?
            if ($addressType == 'residential' || ($addressType == 'unknown' && $this->config->get('shipping_ups_quote_type') == 'residential')) {
                $retType = "residential";
            } else {
            // if ($addressType == 'commercial') {
                $retType = "commercial";
            }

            // $retType = strtolower($av_dom->getElementsByTagName('Description')->item(0)->nodeValue);
        }

        // if ($this->address_type == 'residential' || ($this->address_type == 'unknown' && $this->config->get('ups_quote_type') == 'residential')) {
        //     // end: ups_residential_commercial_validation.xml
        // }


        // ChromePhp::log("-- UPS Res|Commercial test address type --");
        // ChromePhp::log($retType);
 
        return $retType;
        // return $this->address_type;
    }

    function getRequestXML($data, $ServiceCode=null){

        $address = $data['address']; 
        $length_code = $data['length_code'];
        $length = $data['length'];
        $width = $data['width'];
        $height = $data['height']; 
        $weight = $data['weight'];
        $weight_code = $data['weight_code'];
        
        $address_type = $this->getAddressType($address);
        // $address_type = "";
        // ChromePhp::log( var_export($address, 1 ) );
        // $this->log->write( print_r($address, 1 ) );

        /** MOST OPTIONS */
        $xml  = '<?xml version="1.0"?>';
        $xml .= '<AccessRequest xml:lang="en-US">';
        $xml .= '	<AccessLicenseNumber>' . $this->config->get('shipping_ups_key') . '</AccessLicenseNumber>';
        $xml .= '	<UserId>' . $this->config->get('shipping_ups_username') . '</UserId>';
        $xml .= '	<Password>' . $this->config->get('shipping_ups_password') . '</Password>';
        $xml .= '</AccessRequest>';
        $xml .= '<?xml version="1.0"?>';
        $xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
        $xml .= '	<Request>';
        $xml .= '		<TransactionReference>';
        $xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
        $xml .= '			<XpciVersion>1.0001</XpciVersion>';
        $xml .= '		</TransactionReference>';
        $xml .= '		<RequestAction>Rate</RequestAction>';

        /**  SURE POST uses rate..  **/
        if( $ServiceCode ) {
            $xml .= '		<RequestOption>Rate</RequestOption>';
        } else {
            $xml .= '		<RequestOption>shop</RequestOption>';
        }
        /**  SURE POST  **/

        $xml .= '	</Request>';
        $xml .= '   <PickupType>';
        $xml .= '       <Code>' . $this->config->get('shipping_ups_pickup') . '</Code>';
        $xml .= '   </PickupType>';

        if ($this->config->get('shipping_ups_country') == 'US' && $this->config->get('shipping_ups_pickup') == '11') {
            $xml .= '   <CustomerClassification>';
            $xml .= '       <Code>' . $this->config->get('shipping_ups_classification') . '</Code>';
            $xml .= '   </CustomerClassification>';
        }

        $xml .= '	<Shipment>';
        $xml .= '		<Shipper>';

        if( $ServiceCode ) { // If we're using ServiceCode, we use the different Shipper Number.
            $xml .= '            <ShipperNumber>'. UPS_SUREPOST_SHIPPER_NUMBER . '</ShipperNumber>'; 
        } else { // otherwise use the standard account.
            $xml .= '            <ShipperNumber>'. UPS_SHIPPER_NUMBER . '</ShipperNumber>';

        }

        $xml .= '			<Address>';
        $xml .= '				<City>' . $this->config->get('shipping_ups_city') . '</City>';
        $xml .= '				<StateProvinceCode>' . $this->config->get('shipping_ups_state') . '</StateProvinceCode>';
        $xml .= '				<CountryCode>' . $this->config->get('shipping_ups_country') . '</CountryCode>';
        $xml .= '				<PostalCode>' . $this->config->get('shipping_ups_postcode') . '</PostalCode>';
        $xml .= '			</Address>';
        $xml .= '		</Shipper>';
        $xml .= '		<ShipTo>';
        $xml .= '			<Address>';
        $xml .= ' 				<City>' . $address['city'] . '</City>';
        $xml .= '				<StateProvinceCode>' . $address['zone_code'] . '</StateProvinceCode>';
        $xml .= '				<CountryCode>' . $address['iso_code_2'] . '</CountryCode>';
        $xml .= '				<PostalCode>' . $address['postcode'] . '</PostalCode>';

        // if ($this->config->get('shipping_ups_quote_type') == 'residential') {
        if ($address_type  == 'residential') {
            $xml .= '				<ResidentialAddressIndicator />';
        }

        $xml .= '			</Address>';
        $xml .= '		</ShipTo>';
        $xml .= '		<ShipFrom>';
        $xml .= '			<Address>';
        $xml .= '				<City>' . $this->config->get('shipping_ups_city') . '</City>';
        $xml .= '				<StateProvinceCode>' . $this->config->get('shipping_ups_state') . '</StateProvinceCode>';
        $xml .= '				<CountryCode>' . $this->config->get('shipping_ups_country') . '</CountryCode>';
        $xml .= '				<PostalCode>' . $this->config->get('shipping_ups_postcode') . '</PostalCode>';
        $xml .= '			</Address>';
        $xml .= '		</ShipFrom>';



        /**  SURE POST  **/
        // We want a specific service code. 
        if( $ServiceCode ) {
            $xml .= '        <Service>';
            $xml .= '            <Code>' . $ServiceCode . '</Code>';               
            $xml .= '        </Service>';
        }            
        /**  SURE POST  **/

        
        $xml .= '		<Package>';
        $xml .= '			<PackagingType>';
        $xml .= '				<Code>' . $this->config->get('shipping_ups_packaging') . '</Code>';
        $xml .= '			</PackagingType>';

        $xml .= '		    <Dimensions>';
        $xml .= '				<UnitOfMeasurement>';
        $xml .= '					<Code>' . $length_code . '</Code>';
        $xml .= '				</UnitOfMeasurement>';
        $xml .= '				<Length>' . $length . '</Length>';
        $xml .= '				<Width>' . $width . '</Width>';
        $xml .= '				<Height>' . $height . '</Height>';
        $xml .= '			</Dimensions>';

        $xml .= '			<PackageWeight>';
        $xml .= '				<UnitOfMeasurement>';
        $xml .= '					<Code>' . $weight_code . '</Code>';
        $xml .= '				</UnitOfMeasurement>';
        $xml .= '				<Weight>' . $weight . '</Weight>';
        $xml .= '			</PackageWeight>';

        if ($this->config->get('shipping_ups_insurance')) {
            $xml .= '           <PackageServiceOptions>';
            $xml .= '               <InsuredValue>';
            $xml .= '                   <CurrencyCode>' . $this->session->data['currency'] . '</CurrencyCode>';
            $xml .= '                   <MonetaryValue>' . $this->currency->format($this->cart->getSubTotal(), $this->session->data['currency'], false, false) . '</MonetaryValue>';
            $xml .= '               </InsuredValue>';
            $xml .= '           </PackageServiceOptions>';
        }

        $xml .= '		</Package>';


        // We want Negotiated Rates.
        $xml .= '       <RateInformation>';
        $xml .= '           <NegotiatedRatesIndicator/>';
        $xml .= '       </RateInformation>';


        $xml .= '	</Shipment>';
        $xml .= '</RatingServiceSelectionRequest>';

        // ChromePhp::log( "Request XML: " );
        // ChromePhp::log( $xml );

        return $xml;
    }


    public function get_largest_package( $packages ) {
        $largestSize = 0;
        $weightTotal = 0;
        $retHeight="";
        $retWidth = "";
        $retLength="";
        if ( $packages ) {        
            foreach ( $packages as $key => $parcel ) {
                if ( !empty( $parcel['height'] ) && !empty( $parcel['width'] ) && !empty( $parcel['length'] ) ) {
                    $Height = $parcel['height'];
                    $Length = $parcel['length'];
                    $Width  = $parcel['width'];
                    $Weight = $parcel['weight'];

                    // Convert all weights to LBS
                    $WeightClass = $parcel['weight_class_id'];
                    $WeightCode = $this->getWeightUnit($WeightClass);

                    // convert OZ to LB.. just divide by 16.
                    if( $WeightCode == "oz" ) {
                        // $this->log->write( "Weight is an OZ as : $Weight");
                        $Weight = (int)$Weight/16;
                        // $this->log->write( "Weight converted to LBS is $Weight");
                    }

                    $this->log->write("$ Weight Code: " . $WeightCode);
                    $currentSize = $Width * $Height * $Length;
                    $weightTotal += (float)$Weight;
    
                    if ( $currentSize > $largestSize ) {
                        $largestSize = $currentSize;
                        $retHeight  = $Height;
                        $retWidth   = $Width;
                        $retLength  = $Length;
                    }
                }
            }
    
            $package_total_weight = $weightTotal;
    
            return array( 'width'  => $retWidth, 
                         'height'  => $retHeight, 
                         'length'  => $retLength, 
                         'weight'  => $package_total_weight );
        }
    }

 
	public function getLanguageId() {
		if ($this->language_id === null) {
			$this->language_id = (int)$this->config->get('config_language_id');
		}

		return $this->language_id;
	}    

	public function getWeightUnit($class_id) {
		static $weights;
				
		if (empty($weights[$class_id])) {
            $qryString = "SELECT * FROM " . DB_PREFIX . "weight_class_description
                WHERE weight_class_id = '$class_id' 
                    AND language_id = '" . $this->getLanguageId() . "'";

            // $this->log->write("Querystring: $qryString");
            $qry = $this->db->query( $qryString );

            // $this->log->write( print_r($qry, 1) );
						
			if (!empty($qry->row)) {
				$weights[$class_id] = $qry->row;
			} else {
				$weights[$class_id] = array(
					'unit' => ''
				);
			}
		}

		return	$weights[$class_id]['unit'];
	}



    function isSurePostEligible( $package ) {


        define("MAX_TOTAL_DIMENSION", 130);
        // No single dimension > 60"
        define("MAX_SINGLE_DIMENSION", 60);

        // [93] SurePost 1 lb. or greater: 
        // - weight: 1 lb. – 70 lbs. 
        define("SUREPOST_ONE_POUND_OR_GREATER_MIN_WEIGHT_OZS", $this->LbsToOzs( 1 ));  // 16oz
        define("SUREPOST_ONE_POUND_OR_GREATER_MAX_WEIGHT_OZS", $this->LbsToOzs( 70 )); // 1120oz

        // [92] SurePost less than 1 lb: 
        // - weight: 1 ounce – 15.9 ounces. 
        define("SUREPOST_LESS_THAN_ONE_POUND_MIN_WEIGHT_OZS",     0); // Pretty sure this *should* be .. 1);    // ounces
        define("SUREPOST_LESS_THAN_ONE_POUND_MAX_WEIGHT_OZS",  15.9); // ounces
        define("SUREPOST_LESS_THAN_ONE_POUND_MAX_CUBIC_INCHES", 864); // ounces


        // print "isSurePostEligible(): \r\n";
        // ChromePhp::log("Cart Package for UPS: ");
        // ChromePhp::log($package);
        // return true;
        // print_r($package);
        // Minimum dimension: 4" x 6" x 0.75"  
        // define("MIN_TOTAL_DIMENSION", 18); // getLengthPlusGirth( 6, 4, 0.75);
        // print "\r\n".getLengthPlusGirth( 6, 4, 0.75)."\r\n";
        // - Package dimensions cannot exceed 130″.

        // This is actually handled on UPS side.. 
        return true;


        // Sort dimensions length first. GTL
        $packageDimensions = $this->getSortedDimensions($package);
      
        if( $packageDimensions[0] > MAX_SINGLE_DIMENSION ) { 
            // print "\r\nNOT SurePost eligible. \r\n - Reason: a single dimension exceeds max single dimension. \r\n - Length: " . $packageDimensions[0] . "\" \r\n - Max Weight: " . MAX_SINGLE_DIMENSION . "\"\r\n";
            return false;
        }
    
        // Minimum dimension: 6" x 4" x 0.75"  
        if( $packageDimensions[0] < 6 ||
            $packageDimensions[1] < 4 ||
            $packageDimensions[2] < 0.75 ) {
                $msg  = "Minimum Dimension: 6 x 4 x 0.75 \r\n";
                $msg .= "Package Dimension: " . $packageDimensions[0] . ' x ' . $packageDimensions[1] . ' x ' . $packageDimensions[2] . "\r\n";
                // ChromePhp::log( "\r\nNOT SurePost eligible. \r\n - Reason: Minimum Dimension not met. \r\n $msg \r\n" );
                return false;            
            }
    
    
        // print "before convert to OZs $weight \r\n";
        $weight = $this->LbsToOzs($package['weight']);  
        // print "after convert to OZs $weight \r\n";
    
        $totalDimension   = $this->getLengthPlusGirth($packageDimensions[0], $packageDimensions[1], $packageDimensions[2]); // L + ( WIDTH * 2 ) * ( HEIGHT * 2)
        $totalCubicInches = $this->getCubicInches($packageDimensions[0], $packageDimensions[1], $packageDimensions[2]);     // L * W * H
    
        // print "totalDimension: $totalDimension\r\n";
        // print "totalCubicInches: $totalCubicInches\r\n";
    
        // Minimum dimension: 4" x 6" x 0.75", Max Total dimension: 130"
        if( $totalDimension >= MAX_TOTAL_DIMENSION ) {
            // ChromePhp::log( "\r\nNOT eligible for SurePost service. \r\nReason: Package Dimension not supported.\r\nPackage Size: $totalDimension\" \r\n - Max Dimension: " . MAX_TOTAL_DIMENSION . " \r\n - Minimum Dimension: " . MIN_TOTAL_DIMENSION . "\"\r\n");
            return false;
        }
        
        
        // [92] SurePost < 1 lb: 
        // Min Weight: 1 ounce – 15.9 ounces.
        if( $weight <= SUREPOST_LESS_THAN_ONE_POUND_MAX_WEIGHT_OZS && $weight >= SUREPOST_LESS_THAN_ONE_POUND_MIN_WEIGHT_OZS ) {
            // print "So far we are SurePost less than 1lb eligible.\r\n";
            if( $totalCubicInches < SUREPOST_LESS_THAN_ONE_POUND_MAX_CUBIC_INCHES ) {
                // ChromePhp::log( "We are eligible for SurePost < 1 pound! \r\n");
                return true;
            }
        }
    
    
        // [93] SurePost 1 lb. or greater: 
        // - weight: 1 lb. – 70 lbs. 
        if( $weight <= SUREPOST_ONE_POUND_OR_GREATER_MAX_WEIGHT_OZS && $weight >= SUREPOST_ONE_POUND_OR_GREATER_MIN_WEIGHT_OZS ) { 
            // ChromePhp::log(  "We are eligible for: SurePost > 1LB .\r\n" );
            // print "\r\nNOT eligible for SurePost 1 lb or greater. \r\n - Reason: Package weight not supported.\r\n - Package Weight: $weight ounces \r\n - Max Weight: " . SUREPOST_ONE_POUND_OR_GREATER_MAX_WEIGHT_OZS . "\r\n - Min Weight: " . SUREPOST_ONE_POUND_OR_GREATER_MIN_WEIGHT_OZS . "\r\n";
            return true;
        }
     
    
        // print_r( $packageDimensions );
        
        // print "totalDimension: ";
        // print($totalDimension);
    
    }
    
    function LbsToOzs($n){ return $n * 16; }
    function OzsToLBS($n){ return $n / 16; }
    
    // Sort greatest to least. Length is the largest number.
    // returns an Array.
    function getSortedDimensions( $package ) {
        // print "getSortedDimensions() :: \r\n";
        $dims =  [$package['length'], $package['width'], $package['height'] ];
        rsort($dims, SORT_NUMERIC);
        return $dims;
    }
    
    function getLengthPlusGirth( $length, $width, $height ) {
        // print "getLengthPlusGirth() :: \r\n";
        // print "length: $length | width: $width | height: $height\r\n";
        return $length + (( $width*2 ) * ( $height*2 ));
    }
    
    
    function getCubicInches( $length, $width, $height ) {
        // print "getLengthPlusGirth() :: \r\n";
        // print "length: $length | width: $width | height: $height\r\n";
        return $length * $width * $height;
    }
    // RM ++++++ 


	function getQuote($address) {
    
		$this->load->language('extension/shipping/ups');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_ups_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_ups_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			// $weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('shipping_ups_weight_class_id'));
			$weight_code = strtoupper($this->weight->getUnit($this->config->get('shipping_ups_weight_class_id')));

            // ++++++ RM   
            $products = $this->cart->getProducts();
            $get_product = array();
			foreach($products as $sing_product)
			{
				if(isset($sing_product['shipping']) && $sing_product['shipping'] == 1)
				{
					$get_product[] = $sing_product;
				}
			}
            // $this->log->write(" $ weight code: $weight_code");

            $this->log->write(" Products: ");
            $this->log->write( var_export($get_product, 1) );
            
            $largestPackage = $this->get_largest_package( $get_product );
            $sure_post_eligible = $this->isSurePostEligible($largestPackage);

			if ($weight_code == 'KG') {
				$weight_code = 'KGS';
			} elseif ($weight_code == 'LB') {
				$weight_code = 'LBS';
			}
            $weight = $largestPackage['weight'];
            $this->log->write(" $ weight: $weight");
            // ++++++ RM  



			// $length = $this->length->convert($this->config->get('shipping_ups_length'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
			// $width = $this->length->convert($this->config->get('shipping_ups_width'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
			// $height = $this->length->convert($this->config->get('shipping_ups_height'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
            $length = round( $largestPackage['length'] );
            $width  = round( $largestPackage['width'] );
            $height = round( $largestPackage['height'] );
            
			$length_code = strtoupper($this->length->getUnit($this->config->get('shipping_ups_length_class_id')));



            // 2021-09-30 16:42:35 - PHP Notice:  Undefined variable: length_code in /home/b16aa05/oc3.throttlejockey.com/catalog/model/extension/shipping/ups.php on line 84
            // 2021-09-30 16:42:35 - PHP Notice:  Undefined variable: length in /home/b16aa05/oc3.throttlejockey.com/catalog/model/extension/shipping/ups.php on line 86
            // 2021-09-30 16:42:35 - PHP Notice:  Undefined variable: width in /home/b16aa05/oc3.throttlejockey.com/catalog/model/extension/shipping/ups.php on line 87
            // 2021-09-30 16:42:35 - PHP Notice:  Undefined variable: height in /home/b16aa05/oc3.throttlejockey.com/catalog/model/extension/shipping/ups.php on line 88
            // 2021-09-30 16:42:35 - PHP Notice:  Undefined variable: weight_code in /home/b16aa05/oc3.throttlejockey.com/catalog/model/extension/shipping/ups.php on line 93
            // 2021-09-30 16:42:35 - PHP Notice:  Undefined variable: weight in /home/b16aa05/oc3.throttlejockey.com/catalog/model/extension/shipping/ups.php on line 95
            
            
			$service_code = array(
				// US Origin
				'US' => array(
					'01' => $this->language->get('text_us_origin_01'),
					'02' => $this->language->get('text_us_origin_02'),
					'03' => $this->language->get('text_us_origin_03'),
					'07' => $this->language->get('text_us_origin_07'),
					'08' => $this->language->get('text_us_origin_08'),
					'11' => $this->language->get('text_us_origin_11'),
					'12' => $this->language->get('text_us_origin_12'),
					'13' => $this->language->get('text_us_origin_13'),
					'14' => $this->language->get('text_us_origin_14'),
					'54' => $this->language->get('text_us_origin_54'),
					'59' => $this->language->get('text_us_origin_59'),
                    '65' => $this->language->get('text_us_origin_65'),
                    '92' => $this->language->get('text_us_origin_92'),
                    '93' => $this->language->get('text_us_origin_93')				),
				// Canada Origin
				'CA' => array(
					'01' => $this->language->get('text_ca_origin_01'),
					'02' => $this->language->get('text_ca_origin_02'),
					'07' => $this->language->get('text_ca_origin_07'),
					'08' => $this->language->get('text_ca_origin_08'),
					'11' => $this->language->get('text_ca_origin_11'),
					'12' => $this->language->get('text_ca_origin_12'),
					'13' => $this->language->get('text_ca_origin_13'),
					'14' => $this->language->get('text_ca_origin_14'),
					'54' => $this->language->get('text_ca_origin_54'),
					'65' => $this->language->get('text_ca_origin_65')
				),
				// European Union Origin
				'EU' => array(
					'07' => $this->language->get('text_eu_origin_07'),
					'08' => $this->language->get('text_eu_origin_08'),
					'11' => $this->language->get('text_eu_origin_11'),
					'54' => $this->language->get('text_eu_origin_54'),
					'65' => $this->language->get('text_eu_origin_65'),
					// next five services Poland domestic only
					'82' => $this->language->get('text_eu_origin_82'),
					'83' => $this->language->get('text_eu_origin_83'),
					'84' => $this->language->get('text_eu_origin_84'),
					'85' => $this->language->get('text_eu_origin_85'),
					'86' => $this->language->get('text_eu_origin_86')
				),
				// Puerto Rico Origin
				'PR' => array(
					'01' => $this->language->get('text_pr_origin_01'),
					'02' => $this->language->get('text_pr_origin_02'),
					'03' => $this->language->get('text_pr_origin_03'),
					'07' => $this->language->get('text_pr_origin_07'),
					'08' => $this->language->get('text_pr_origin_08'),
					'14' => $this->language->get('text_pr_origin_14'),
					'54' => $this->language->get('text_pr_origin_54'),
					'65' => $this->language->get('text_pr_origin_65')
				),
				// Mexico Origin
				'MX' => array(
					'07' => $this->language->get('text_mx_origin_07'),
					'08' => $this->language->get('text_mx_origin_08'),
					'54' => $this->language->get('text_mx_origin_54'),
					'65' => $this->language->get('text_mx_origin_65')
				),
				// All other origins
				'other' => array(
					// service code 7 seems to be gone after January 2, 2007
					'07' => $this->language->get('text_other_origin_07'),
					'08' => $this->language->get('text_other_origin_08'),
					'11' => $this->language->get('text_other_origin_11'),
					'54' => $this->language->get('text_other_origin_54'),
					'65' => $this->language->get('text_other_origin_65')
				)
			);


            $requestData = array( "address" => $address,
                                  "length_code" => $length_code,
                                  "length" => $length,
                                  "width" => $width,
                                  "height" => $height,
                                  "weight" => $weight,
                                  "weight_code" => $weight_code 
            );

			if (!$this->config->get('shipping_ups_test')) {
				// $url = 'https://www.ups.com/ups.app/xml/Rate';
                // This URL was changed.
                $url = 'https://onlinetools.ups.com/ups.app/xml/Rate';
			} else {
				$url = 'https://wwwcie.ups.com/ups.app/xml/Rate';
			}

            /** MOST OPTIONS */
            $all_rates_xml = $this->getRequestXML($requestData); // add service code to request specific service rate.            

			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_TIMEOUT, 60);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $all_rates_xml);
			$all_rates_result = curl_exec($curl);
			curl_close($curl);
            /** MOST OPTIONS */

            /** SURE POST */ 
            // $sure_post_result = array();
            $sure_post_result = false;

            if( $sure_post_eligible ) {

                // By default do a "normal" sure post. 
                $sure_post_service_id = 93;
                $sure_post_weight_code = $weight_code;
                $sure_post_weight = $weight;

                // If total is less than 1 lb then request the UNDER 1 lbs sure post service.. 
                if( $weight < 1 ) {
                    $sure_post_service_id = 92;
                    $sure_post_weight_code = "OZS";
                    $sure_post_weight = $weight * 16; // Convert value to OZs... 
                }
                
                $surePostRequestData = array(   "address" => $address,
                                                "length_code" => $length_code,
                                                "length" => $length,
                                                "width" => $width,
                                                "height" => $height,
                                                "weight" => $sure_post_weight,
                                                "weight_code" => $sure_post_weight_code 
                                            );

                $sure_post_xml = $this->getRequestXML($surePostRequestData, $sure_post_service_id); // add service code to request specific service rate.            

                // $this->log->write("XML:");
                // $this->log->write( print_r($sure_post_xml, 1) );

                $curl2 = curl_init($url);
                curl_setopt($curl2, CURLOPT_HEADER, 0);
                curl_setopt($curl2, CURLOPT_POST, 1);
                curl_setopt($curl2, CURLOPT_TIMEOUT, 60);
                curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl2, CURLOPT_POSTFIELDS, $sure_post_xml);
                $sure_post_result = curl_exec($curl2);
                curl_close($curl2);
                /** SURE POST */
            }

            // if( $sure_post_result && $all_rates_result ) {
            //     $result = joinXML($sure_post_result, $all_rates_result, 'RatingServiceSelectionResponse');
            // }

            // $this->log->write( print_r($all_rates_result, 1) );
            // $this->log->write( print_r($sure_post_result, 1) );

            // result includes all rates, but if we have sure post rates, add them to the result.. 
            // $result = $all_rates_result;

            $sure_post_error = "";
            if( $sure_post_result ) {

                $DOMResponseX = new DOMDocument;
                $DOMResponseX->formatOutput = true;
                $DOMResponseX->loadXML($sure_post_result);

				$rating_service_selection_response = $DOMResponseX->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
				$response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);
				$response_status_code = $response->getElementsByTagName('ResponseStatusCode');

                // if SurePost didn't return an error, merge it.. 
				if ($response_status_code->item(0)->nodeValue == '1') {          
                    $result = $this->joinXML($sure_post_result, $all_rates_result, 'RatingServiceSelectionResponse');
                } else {
                    $error = $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorCode')->item(0)->nodeValue . ': ' . $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;                
                    $sure_post_error = $error;
                    $result = $this->joinXML($all_rates_result, '', 'RatingServiceSelectionResponse');

                    $this->log->write("UPS SurePost error: ");
                    $this->log->write(print_r($error, 1));

                    // ChromePhp::log("UPS SurePost error: ");
                    // ChromePhp::log( print_r($error, 1) );
                    
                }

                // $result = $this->joinXML($sure_post_result, $all_rates_result, 'RatingServiceSelectionResponse');
                if ($this->config->get('shipping_ups_debug')) {
                    $this->log->write("UPS DATA SENT (SurePost): " . $sure_post_xml);
					$this->log->write("UPS DATA RECV (SurePost): " . print_r($sure_post_result, 1) );
                }

            } else {
                $result = $this->joinXML($all_rates_result, '', 'RatingServiceSelectionResponse');
            }

            // ChromePhp::log("UPS XML Result: ");
            // ChromePhp::log( print_r($result, 1) );

           
            // PARSE THE RESULT
			$error = '';
			$quote_data = array();

			if ($result) {
				if ($this->config->get('shipping_ups_debug')) {
					$this->log->write("UPS DATA SENT (ALL): " . $all_rates_xml);
					$this->log->write("UPS DATA RECV (ALL): " . print_r($all_rates_result, 1) );
				}
				
				$previous_value = libxml_use_internal_errors(true);

                $dom = $result;
				// $dom = new DOMDocument('1.0', 'UTF-8');
				// $dom->loadXml($result);

				libxml_use_internal_errors($previous_value);
				
				if (libxml_get_errors()) {
					return false;
				}
				
				$rating_service_selection_response = $dom->getElementsByTagName('RatingServiceSelectionResponse')->item(0);
				$response = $rating_service_selection_response->getElementsByTagName('Response')->item(0);
				$response_status_code = $response->getElementsByTagName('ResponseStatusCode');

				if ($response_status_code->item(0)->nodeValue != '1') {
					$error = $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorCode')->item(0)->nodeValue . ': ' . $response->getElementsByTagName('Error')->item(0)->getElementsByTagName('ErrorDescription')->item(0)->nodeValue;

                    $this->log->write("UPS error: ");
                    $this->log->write(print_r($error, 1));

                    // ChromePhp::log("UPS error: ");
                    // ChromePhp::log( print_r($error, 1) );

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


                        $this->log->write('ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code); 


                        if ($this->config->get('shipping_ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code)) {
                            // $this->log->write('shipping_ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code); 
							$quote_data[ strVal($code) ] = array(
								'code'         => 'ups.' . $code,
								'title'        => $service_code[$this->config->get('shipping_ups_origin')][$code],
								'cost'         => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
								'tax_class_id' => $this->config->get('shipping_ups_tax_class_id'),
								'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->session->data['currency']), $this->config->get('shipping_ups_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000)
							);
                            /** 
                             * 
                             * Should Use below instead? 
                             * 
                             */
                            // $quote_data[$code] = array(
                            //     'type'         => $this->address_type,
                            //     'code'         => 'ups.' . $code,
                            //     'title'        => $service_code[$this->config->get('ups_origin')][$code],
                            //     'cost'         => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
                            //     'tax_class_id' => $this->config->get('ups_tax_class_id'),
                            //     'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->currency->getCode()), $this->config->get('ups_tax_class_id'), $this->config->get('config_tax')), $this->currency->getCode(), 1.0000000)
                            // );
                        }
					}
				}
			}

			$title = $this->language->get('text_title');

			if ($this->config->get('shipping_ups_display_weight')) {
				$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('shipping_ups_weight_class_id')) . ')';
			}

			if ($quote_data || $error) {
				$method_data = array(
					'code'       => 'ups',
					'title'      => $title,
					'quote'      => $quote_data,
					'sort_order' => $this->config->get('shipping_ups_sort_order'),
					'error'      => $error
				);
			}
		}

        // ChromePhp::log("UPS method_data: ");
        // ChromePhp::log( print_r($method_data['quote'], 1) );
        // $method_count = sizeof( $method_data['quote'] );
        // if(count($method_count) == 0) {
            // ChromePhp::log( "method_count: $method_count" );
        // }

        // Prevent the store from displaying empty selection on the checkout page if no quotes 
        // were returned. 
        if( sizeof( $method_data['quote'] ) == 0 ) {
            return array();
        }


        // $this->log->write('method_data:');
        // $this->log->write( print_r($method_data, 1));   
        // $this->log->write( '$method_data = ' . var_export($method_data, 1));   
                     
        // SORT LOWEST TO HIGHEST
        $quotes = $method_data['quote'];
        uasort($quotes, function ($a, $b) {
            return ($a["cost"] < $b["cost"]) ? -1 : (($a["cost"] > $b["cost"]) ? 1 : 0);  
        });
        // $method_data['quote'] = $quotes;

        $x = "";
        foreach ($quotes as $key => $value) {
            $x .= "$key, ";
        }

        $new_method_data['code'] = $method_data['code'];
        // $new_method_data['title'] = $method_data['title'] . ' ' . $x;
        $new_method_data['title'] = $method_data['title'];
        $new_method_data['quote'] = $quotes;
        $new_method_data['sort_order'] = $method_data['sort_order'];
        $new_method_data['error'] = $method_data['error'];
        $new_method_data['hamburgs'] = "Test.";

        // ChromePhp::log("UPS new_method_data: ");
        // ChromePhp::log( print_r($new_method_data['quote'], 1) );

        // $this->log->write('new_method_data:');
        // $this->log->write( print_r($new_method_data, 1) );   
        // $this->log->write( '$new_method_data = ' . var_export($new_method_data, 1));   
        // $this->log->write('method_data sorted:');
        // $this->log->write( print_r($method_data, 1) );   
        // $this->log->write( '$arrayTest = ' . var_export($method_data, 1));   
		return $new_method_data;
		// return $method_data;
	}
}
