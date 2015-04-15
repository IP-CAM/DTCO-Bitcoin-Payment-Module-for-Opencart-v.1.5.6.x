<?php

require_once 'dt_options.php';

function bpCurl($url, $apiKey, $post = false) {
	global $dtOptions;	
		
	$curl = curl_init($url);
	$length = 0;
	if ($post)
	{	
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$length = strlen($post);
	}
	
	$uname = base64_encode($apiKey);
	$header = array(
		'Content-Type: application/json',
		"Content-Length: $length",
		"Authorization: Basic $uname",
		);

	curl_setopt($curl, CURLOPT_PORT, 443);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		
	$responseString = curl_exec($curl);
	
	if($responseString == false) {
		$response = curl_error($curl);
	} else {
		$response = json_decode($responseString, true);
	}
	curl_close($curl);
	return $response;
}
// $orderId: Used to display an orderID to the buyer. In the account summary view, this value is used to 
// identify a ledger entry if present.
//
// $price: by default, $price is expressed in the currency you set in bp_options.php.  The currency can be 
// changed in $options.
//
// $posData: this field is included in status updates or requests to get an invoice.  It is intended to be used by
// the merchant to uniquely identify an order associated with an invoice in their system.  Aside from that, Bit-Pay does
// not use the data in this field.  The data in this field can be anything that is meaningful to the merchant.
//
// $options keys can include any of: 
// ('itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 'apiKey'
//		'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName', 
//		'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone')
// If a given option is not provided here, the value of that option will default to what is found in bp_options.php
// (see api documentation for information on these options).
function bpCreateInvoice($orderId, $price, $posData, $options = array()) {	
	global $dtOptions;	
	
	$options = array_merge($dtOptions, $options);	// $options override any options found in bp_options.php
	/*
	$options['posData'] = '{"posData": "' . $posData . '"';
	if ($dtOptions['verifyPos']) // if desired, a hash of the POS data is included to verify source in the callback
		$options['posData'].= ', "hash": "' . crypt($posData, $options['apiKey']).'"';
        
	$options['posData'].= '}';	
	*/
	$options['orderID'] = $orderId;
	$options['price'] = $price;
	
	$postOptions = array('orderID', 'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 
		'posData', 'price', 'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName', 
		'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone');
              
    /*DTCO API*/
	$toURL = 'https://merchant.dtco.co/payment_encode';
	
	$ch = curl_init();
	
	$params = array(
		'api_key' => $options['apiKey'],
		'fiat_money' => $options['price'],
		'fiat_currency' => $options['currency'],
		'order_id' => $orderId,
		'return_url' => $options['notificationURL'],
	);
	
	$curl_op = array(
		CURLOPT_URL => $toURL,
		CURLOPT_HEADER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query($params),
	);
	
	curl_setopt_array($ch,$curl_op);
	$invoice_id = curl_exec($ch);
	curl_close($ch);
	$invoice_id;
	$webURL = urlencode($options['redirectURL']);
	$payment_url = "https://merchant.dtco.co/payment?invoiceID=".$invoice_id."&weburl=".$webURL;
    /*DTCO API-END*/
    $response["url"] = $payment_url;
	return $response; 
    
}


?>