<?php

class ControllerExtensionPaymentZibal extends Controller {
	public function index() {
		$this->load->language('extension/payment/zibal');
		
		$data['text_connect'] = $this->language->get('text_connect');

		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_wait'] = $this->language->get('text_wait');
		
    	$data['button_confirm'] = $this->language->get('button_confirm');

		return $this->load->view('extension/payment/zibal', $data);
	}

	public function confirm() {
		$this->load->language('extension/payment/zibal');
		
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$amount = $this->correctAmount($order_info);
		
		$data['return'] = $this->url->link('checkout/success', '', true);
		$data['cancel_return'] = $this->url->link('checkout/payment', '', true);
		$data['back'] = $this->url->link('checkout/payment', '', true);
		
		$MerchantID = $this->config->get('payment_zibal_pin');  	//Required
		$Amount = $amount;
		$Description = $this->language->get('text_order_no') . $order_info['order_id']; // Required
		$Mobile = isset($order_info['fax']) ? $order_info['fax'] : $order_info['telephone']; 	// Optional
		$data['order_id'] = $this->encryption->encrypt($this->config->get('config_encryption'), $this->session->data['order_id']);
		$CallbackURL = $this->url->link('extension/payment/zibal/callback', 'order_id=' . $data['order_id'], true);  // Required

		$parameters = array(
			'merchant' 	=> $MerchantID,
			'amount' 		=> $Amount,
			'description' 	=> $Description,
			'mobile' 		=> $Mobile,
			'callbackUrl' 	=> $CallbackURL
		);

		$requestResult = $this->postToZibal('request',$parameters);
		
		if(!$requestResult) {
			$json = array();
			$json['error']= $this->language->get('error_cant_connect');				
		} elseif($requestResult->result == 100) {
            if ($this->config->get('payment_zibal_direct')=='1') {
			$data['action'] = "https://gateway.zibal.ir/start/" . $requestResult->trackId.'/direct';
			$json['success']= $data['action'];}
			else{
                $data['action'] = "https://gateway.zibal.ir/start/" . $requestResult->trackId;
                $json['success']= $data['action'];
			}
		} else {
			$json = $requestResult->message;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


    /**
     * connects to zibal's rest api
     * @param $path
     * @param $parameters
     * @return stdClass
     */
    private function postToZibal($path, $parameters)
    {
        $url = 'https://gateway.zibal.ir/v1/'.$path;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

	public function callback() {
		if ($this->session->data['payment_method']['code'] == 'zibal') {
			$this->load->language('extension/payment/zibal');

			$this->document->setTitle($this->language->get('text_title'));
			
			$data['heading_title'] = $this->language->get('text_title');
			$data['results'] = "";
			
			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'), 
				'href' => $this->url->link('common/home', '', true)
			);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_title'), 
				'href' => $this->url->link('extension/payment/zibal/callback', '', true)
			);

			try {
				if($this->request->get['success'] != '1')
					throw new Exception($this->language->get('error_verify'));

				if (isset($this->session->data['order_id'])) {
					$order_id = $this->session->data['order_id'];
				} else {
					$order_id = 0;
				}
	
				$this->load->model('checkout/order');
				$order_info = $this->model_checkout_order->getOrder($order_id);

				if (!$order_info)
					throw new Exception($this->language->get('error_order_id'));

				$trackId = $this->request->get['trackId'];
				$amount = $this->correctAmount($order_info);
                $MerchantID = $this->config->get('payment_zibal_pin');

				$verifyResult = $this->postToZibal('verify',array(
					"merchant"=>$MerchantID,
					"trackId"=>$trackId
				));
                if (!$verifyResult)
                    throw new Exception($this->language->get('error_connect_verify'));


				if($verifyResult->result==100 && $verifyResult->amount==$amount)
				{
                    $comment = $this->language->get('text_results') . $trackId;
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_zibal_order_status_id'), $comment, true);

                    $data['error_warning'] = NULL;
                    $data['results'] = $trackId;
                    $data['button_continue'] = $this->language->get('button_complete');
                    $data['continue'] = $this->url->link('checkout/success');

				}else{
                    throw new Exception($verifyResult->message);
				}


			} catch (Exception $e) {
				$data['error_warning'] = $e->getMessage();
				$data['button_continue'] = $this->language->get('button_view_cart');
				$data['continue'] = $this->url->link('checkout/cart');
			}

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/payment/zibal_confirm', $data));
		}
	}

	private function correctAmount($order_info) {
		$amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$amount = round($amount);
		$amount = $this->currency->convert($amount, $order_info['currency_code'], "TOM");
		return (int)$amount*10;
	}

}
?>
