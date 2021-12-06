<?php

/*
 * DEBUG WITH ChromePHP Logger 
 * 
// $root = "/home/b16aa05/oc3.throttlejockey.com/";
$root = "/home/b16aa05/public_html/";    
if (file_exists($root . 'system/library/ChromePHP.php')) {
    require_once($root . 'system/library/ChromePHP.php');
}
/******/



class ControllerCheckoutShippingMethod extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');

		if (isset($this->session->data['shipping_address'])) {
			// Shipping Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);
                    // ChromePhp::log("Quote: ", $quote);
					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);
             
			$this->session->data['shipping_methods'] = $method_data;
		}

		if (empty($this->session->data['shipping_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['shipping_methods'])) {
			$data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$data['shipping_methods'] = array();
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($this->session->data['comment'])) {
			$data['comment'] = $this->session->data['comment'];
		} else {
			$data['comment'] = '';
		}
		
        // $data['error_warning'] = print_r($method_data, 1); // 
        $data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));

		$this->response->setOutput($this->load->view('checkout/shipping_method', $data));
	}

    public function var_dump_ret($mixed = null) {
        ob_start();
        var_dump($mixed);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
      }
      
	public function save() {
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate if shipping address has been set.
		if (!isset($this->session->data['shipping_address'])) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', true);
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!isset($this->request->post['shipping_method'])) {
			$json['error']['warning'] = $this->language->get('error_shipping');
		} else {
			$shipping = explode('.', $this->request->post['shipping_method']);
             
            $this->log->write( '$shipping: ' . print_r($shipping, 1) );
            $this->log->write( '$this->session->data[\'shipping_methods\']: ' . print_r( $this->session->data['shipping_methods'],  1 ) );
            $this->log->write( '$shipping='. var_export( $shipping, 1));
            $this->log->write( '$this_session_data_shipping_methods='.var_export( $this->session->data['shipping_methods'], 1) );
			if (!isset($shipping[0]) || 
                !isset($shipping[1]) || 
                !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                    // $this->log->write( '$this->session->data[\'shipping_methods\']: ' . print_r( $this->session->data['shipping_methods'],  1 ) );


				$json['error']['warning'] = $this->language->get('error_shipping');
			}
		}

		if (!$json) {
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}