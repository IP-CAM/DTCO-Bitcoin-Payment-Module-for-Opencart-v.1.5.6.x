<?php 
class ModelPaymentDtcopay extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/dtcopay');
		
		if ($this->config->get('dtcopay_status')) {
        	$status = TRUE;
		} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         	=> 'dtcopay',
        		'title'      	=> $this->language->get('text_title'),
				'sort_order' 	=> $this->config->get('dtcopay_sort_order'),
      		);
    	}
   
    	return $method_data;
  	}
}
?>
