<?php
class ModelExtensionShippingUps extends Model {


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

    function getRequestXML($data, $ServiceCode=null){

        $address = $data['address'];//->address;
        $length_code = $data['length_code'];
        $length = $data['length'];
        $width = $data['width'];
        $height = $data['height']; 
        $weight = $data['weight'];
        $weight_code = $data['weight_code'];
        

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

        /**  SURE POST  **/
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

        if( $ServiceCode ) {
            $xml .= '            <ShipperNumber>429744</ShipperNumber>'; 
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

        if ($this->config->get('shipping_ups_quote_type') == 'residential') {
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

        $xml .= '	</Shipment>';
        $xml .= '</RatingServiceSelectionRequest>';

        return $xml;
    }

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
			$weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('shipping_ups_weight_class_id'));
			$weight_code = strtoupper($this->weight->getUnit($this->config->get('shipping_ups_weight_class_id')));

			if ($weight_code == 'KG') {
				$weight_code = 'KGS';
			} elseif ($weight_code == 'LB') {
				$weight_code = 'LBS';
			}

			$weight = ($weight < 0.1 ? 0.1 : $weight);

			$length = $this->length->convert($this->config->get('shipping_ups_length'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
			$width = $this->length->convert($this->config->get('shipping_ups_width'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));
			$height = $this->length->convert($this->config->get('shipping_ups_height'), $this->config->get('config_length_class_id'), $this->config->get('shipping_ups_length_class_id'));

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
				$url = 'https://www.ups.com/ups.app/xml/Rate';
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
            $sure_post_xml = $this->getRequestXML($requestData, '93'); // add service code to request specific service rate.            
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

            // if( $sure_post_result && $all_rates_result ) {
            //     $result = joinXML($sure_post_result, $all_rates_result, 'RatingServiceSelectionResponse');
            // }

            // $this->log->write( print_r($all_rates_result, 1) );
            // $this->log->write( print_r($sure_post_result, 1) );


            // $result = $sure_post_result;
            $result = $this->joinXML($sure_post_result, $all_rates_result, 'RatingServiceSelectionResponse');

            // PARSE THE RESULT
			$error = '';
			$quote_data = array();

			if ($result) {
				if ($this->config->get('shipping_ups_debug')) {
					$this->log->write("UPS DATA SENT (ALL): " . $all_rates_xml);
					$this->log->write("UPS DATA RECV (ALL): " . print_r($all_rates_result, 1) );

                    $this->log->write("UPS DATA SENT (SurePost): " . $sure_post_xml);
					$this->log->write("UPS DATA RECV (SurePost): " . print_r($sure_post_result, 1) );
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
				} else {
					$rated_shipments = $rating_service_selection_response->getElementsByTagName('RatedShipment');

					foreach ($rated_shipments as $rated_shipment) {
						$service = $rated_shipment->getElementsByTagName('Service')->item(0);

						$code = $service->getElementsByTagName('Code')->item(0)->nodeValue;

						$total_charges = $rated_shipment->getElementsByTagName('TotalCharges')->item(0);

						$cost = $total_charges->getElementsByTagName('MonetaryValue')->item(0)->nodeValue;

						$currency = $total_charges->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;

						if (!($code && $cost)) {
							continue;
						}


                        $this->log->write('ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code); 


                        if ($this->config->get('shipping_ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code)) {
                            // $this->log->write('shipping_ups_' . strtolower($this->config->get('shipping_ups_origin')) . '_' . $code); 
							$quote_data[$code] = array(
								'code'         => 'ups.' . $code,
								'title'        => $service_code[$this->config->get('shipping_ups_origin')][$code],
								'cost'         => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
								'tax_class_id' => $this->config->get('shipping_ups_tax_class_id'),
								'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->session->data['currency']), $this->config->get('shipping_ups_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000)
							);
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

        // $this->log->write('method_data:');
        // $this->log->write( print_r($method_data, 1));         
        // $this->log->write('');

		return $method_data;
	}
}
