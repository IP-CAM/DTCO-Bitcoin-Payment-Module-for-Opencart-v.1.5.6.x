<?php

class ControllerPaymentDtcopay extends Controller {

    // below is the url that can take you do the order information
    // http://127.0.0.1/~spair/store/index.php?route=account/order/info&order_id=35

    private $payment_module_name  = 'dtcopay';
	protected function index() {
        $this->language->load('payment/'.$this->payment_module_name);
    	$this->data['button_dtcopay_confirm'] = $this->language->get('button_dtcopay_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/dtcopay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/dtcopay.tpl';
		} else {
			$this->template = 'default/template/payment/dtcopay.tpl';
		}	
		
		$this->render();
	}
	
	function log($contents){
		$file = 'dtcopay/log.txt';
		file_put_contents($file, date('m-d H:i:s').": \n", FILE_APPEND);
		if (is_array($contents))
			foreach($contents as $k => $v)
				file_put_contents($file, $k.': '.$v."\n", FILE_APPEND);
		else
			file_put_contents($file, $contents."\n", FILE_APPEND);
	}

	/*lead to payment page.*/
    public function send() { 
		require 'dtcopay/dt_lib.php';
		
        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->model_checkout_order->confirm($this->session->data['order_id'], 2);
        $this->model_checkout_order->update($this->session->data['order_id'], 2, 'Ready For Payment', true); //change the order status, not use here.
        $price = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false);
        $posData = $order['order_id'];

        $options = array(
			'apiKey' => $this->config->get($this->payment_module_name.'_api_key'),
            'notificationURL' => $this->url->link('payment/dtcopay/callback'),
            //'redirectURL' => $this->url->link('account/order/info&order_id=' . $order['order_id']),
            'redirectURL' => $this->url->link('checkout/success'),
            'currency' => $order['currency_code'],
            'physical' => 'true',
            'fullNotifications' => 'true',
            'transactionSpeed' => $this->config->get($this->payment_module_name.'_transaction_speed')
        );
        $response = bpCreateInvoice($order['order_id'], $price, $posData, $options);
		
        if(array_key_exists('error', $response)) {
            $this->log("communication error");
			$this->log($response['error']);
            echo "{\"error\": \"Error: Problem communicating with payment provider.\\nPlease try again later.\"}";
        } else {
            echo "{\"url\": \"" . $response["url"] . "\"}";
        }
    }
	
    /*Callback URL*/
    public function callback() {
		require 'dtcopay/dt_lib.php';
		
		//$apiKey = $this->config->get($this->payment_module_name.'_api_key');
		//$response = bpVerifyNotification($apiKey);
        
        $order_id = $_GET['KeyNo']; //Order NO.
        $order_status = $_GET['Status'];
        $order_fiatcuy = $_GET['FiatCurrency']; 
        $order_fiatamount = $_GET['FiatMoney']; 
        
        
		if ($order_id == '') {
			$this->log("API response error:".$order_id.$order_status.$order_fiatcuy.$order_fiatamount);            
        } 
		else
        {
            switch($order_status) {
				case 'Finished':
                    $this->load->model('checkout/order');
                    $order_id = $_GET['KeyNo'];
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->update($order_id, $this->config->get('dtcopay_confirmed_status_id'), 'Payment Received', true);
					break;
                case 'Finished+PayExpired':
                    $this->load->model('checkout/order');
                    $order_id = $_GET['KeyNo'];
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->update($order_id, $this->config->get('dtcopay_confirmed_status_id'), 'Payment Received (Delayed)', true);
					break;
				case 'NotEnough':
                    $this->load->model('checkout/order');
                    $order_id = $_GET['KeyNo'];
                    $order = $this->model_checkout_order->getOrder($order_id);
                    $this->model_checkout_order->update($order_id, $this->config->get('dtcopay_invalid_status_id'), 'NotEnough Payment (Contact Needed)', true);
					break;
			}
        }
    }
}
?>