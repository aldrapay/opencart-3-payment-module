<?php
class ControllerExtensionPaymentAldrapay extends Controller {
  
  const APPROVED = 1;
  const DECLINED = 2;
  const FAILED = 3;
  const REDIRECT = 4;
  const CANCELLED = 5;
  const PENDING_APPROVAL = 6;
  const PENDING_REFUND = 7;
  const PENDING_PROCESSOR = 8;
  const AUTHORIZED = 10;
  const REFUNDED = 40;
  const PENDING= 80;
  
  private static $responseMessages = array(
    '1' => 'Approved',
    '2' => 'Declined',
    '3' => 'Failed',
    '4' => 'Redirect',
    '5' => 'Canceled',
    '6' => 'Customer Pending Approval',
    '8' => 'Pending Processor Response',
    '10' => 'Authorized',
  );
  

  public function index() {
  	
    $this->language->load('extension/payment/aldrapay');
    $this->load->model('checkout/order');
    
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

    $initRequestResponse = $this->generateToken();
    $token = isset($initRequestResponse['transaction']) && isset($initRequestResponse['transaction']['transactionID']) 
    	? $initRequestResponse['transaction']['transactionID'] : false;
    
    $orderId = isset($initRequestResponse['transaction']) && isset($initRequestResponse['transaction']['orderID']) 
    	? $initRequestResponse['transaction']['orderID'] : $this->session->data['order_id'];
    
    $orderAmount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
    $orderAmount = (float)$orderAmount * pow(10,(int)$this->currency->getDecimalPlace($order_info['currency_code']));
    $orderAmount = intval(strval($orderAmount));
    if ($orderAmount > 0)
    	$orderAmount = $orderAmount / 100;
    
    $callback_url = $this->url->link('extension/payment/aldrapay/callback', '', 'SSL');
    $return_url = $this->url->link('extension/payment/aldrapay/customer_return', '', 'SSL');
    
    $data['action'] = 'https://' . $this->config->get('payment_aldrapay_domain_payment_page') . '/transaction/customer';
    $data['button_confirm'] = $this->language->get('button_confirm');
    $data['token'] = $token;
    $data['token_error'] = $this->language->get('token_error');
    $data['order_id'] = $this->session->data['order_id'];
    
    $redirectRequest = array(
	    array('paramName' => 'merchantID' , 'paramValue' => $this->config->get('payment_aldrapay_companyid')),
	    array('paramName' => 'amount' , 'paramValue' => $orderAmount),
	    array('paramName' => 'currency' , 'paramValue' => $order_info['currency_code']),
	    array('paramName' => 'orderID' , 'paramValue' => $orderId),
	    array('paramName' => 'returnURL' , 'paramValue' => $return_url),
	    array('paramName' => 'notifyURL' , 'paramValue' => $callback_url),
	    array('paramName' => 'transactionID' , 'paramValue' => $token),
	);
    
    $pSign = $this->config->get('payment_aldrapay_encyptionkey');
    foreach ($redirectRequest as $param){
    	$pSign .= $param['paramValue'];
    }
    $redirectRequest[] = array('paramName' => 'pSign', 'paramValue' => hash($this->config->get('payment_aldrapay_encyptionalgo'), $pSign));
    
    $data['redirectRequest'] = $redirectRequest;
    
    if ($token != false)
    $this->model_checkout_order->addOrderHistory($orderId, 1, 'Pending transaction on gateway', true);
    
    return $this->load->view('extension/payment/aldrapay', $data);
  }
  
  

  public function generateToken(){

    $this->load->model('checkout/order');
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
    $orderAmount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
    $orderAmount = (float)$orderAmount * pow(10,(int)$this->currency->getDecimalPlace($order_info['currency_code']));
    $orderAmount = intval(strval($orderAmount));
    if ($orderAmount > 0)
    	$orderAmount = $orderAmount / 100;
    
    $callback_url = $this->url->link('extension/payment/aldrapay/callback', '', 'SSL');
    $return_url = $this->url->link('extension/payment/aldrapay/customer_return', '', 'SSL');
    
    $request = array(
    		'merchantID' => $this->config->get('payment_aldrapay_companyid'),
    		'amount' => $orderAmount,
    		'currency' => $order_info['currency_code'],
    		'orderID' => $order_info['order_id'],
    		'returnURL' => $return_url, //$this->url->link('checkout/checkout', '', 'SSL'),
    		'notifyURL' => $callback_url,
    		'customerIP' => $this->request->server['REMOTE_ADDR'],
    		'customerEmail' => strlen($order_info['email']) > 0 ? $order_info['email'] : null,
    		'customerPhone' => strlen($order_info['telephone']) > 0 ? $order_info['telephone'] : null,
    		'customerFirstName' => strlen($order_info['payment_firstname']) > 0 ? $order_info['payment_firstname'] : null,
    		'customerLastName' => strlen($order_info['payment_lastname']) > 0 ? $order_info['payment_lastname'] : null,
    		'customerAddress1' => strlen($order_info['payment_address_1']) > 0 ? $order_info['payment_address_1'] : null,
    		//'customerAddress2' => strlen($order_info['payment_address_2']) > 0 ? $order_info['payment_address_2'] : null,,
    		'customerCity' => strlen($order_info['payment_city']) > 0 ? $order_info['payment_city'] : null,
    		'customerZipCode' => strlen($order_info['payment_postcode']) > 0 ? $order_info['payment_postcode'] : null,
    		'customerStateProvince' => $order_info['payment_zone_code'],
    		'customerCountry' => strlen($order_info['payment_iso_code_2']) > 0 ? $order_info['payment_iso_code_2'] : null,
    		'description' => $this->language->get('text_order'). ' ' .$order_info['order_id'],
    );
    
    $pSign = $this->config->get('payment_aldrapay_encyptionkey');
    foreach ($request as $k=>$v){
    	$pSign .= $v;
    }
    
    $request['pSign'] = hash($this->config->get('payment_aldrapay_encyptionalgo'), $pSign);
    
     
    $this->load->model('checkout/order');

    $post_string = http_build_query($request);

    $ctp_url = 'https://' . $this->config->get('payment_aldrapay_domain_payment_page') . '/transaction/execute';

    $curl = curl_init($ctp_url);
    curl_setopt($curl, CURLOPT_PORT, 443);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);

    $httpResponse = curl_exec($curl);
    curl_close($curl);

    if (!$httpResponse) {
      $this->log->write('Payment init request failed: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
      return false;
    }

    $response = json_decode($httpResponse,true);

    if ($response == NULL) {
      $this->log->write("Payment init response parse error: $httpResponse");
      return false;
    }

    if (isset($response['errorInfo'])) {
      $this->log->write("Payment init request validation errors: $httpResponse");
      return false;
    }

    if (isset($response['responseCode']) && $response['responseCode'] == 3) {
      $this->log->write("Payment init request error: $httpResponse");
      return false;
    }

    if (isset($response['responseCode']) && isset($response['redirectURL']) && $response['responseCode'] == 4) {
      return $response;
    } else {
      $this->log->write("Unexpected/failed init response: $httpResponse");
      return false;
    }
  }

  
  
  public function customer_return() {

  	if (isset($this->session->data['order_id'])) {
      $order_id = $this->session->data['order_id'];
    } else {
      $order_id = 0;
    }

    $response = $_POST;
    if (count($response) < 1)
    	$response = $_GET;
    
    if ($this->processOrder($response) == true)
    	$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
    else
    	$this->response->redirect($this->url->link('checkout/failure', '', 'SSL'));
  }

  
  
  public function callback() {

    $responseRaw = (string)file_get_contents("php://input");
    
    if (trim($responseRaw) == '')
	    $response = json_decode($responseRaw, true);
    else{
	    $response = $_POST;
    	$responseRaw = print_r($_POST, true);
    }
    
    if (empty($response) ||  count($response) < 2){
	    $response = $_GET;
    	$responseRaw = print_r($_GET, true);
    }
    
    $this->log->write("Webhook received: $responseRaw");

    $this->processOrder($response);
  }
  
  
  
  private function processOrder($response){
  	
  	$order_id = isset($response['orderID']) ? $response['orderID'] : null;
  	$status = isset($response['responseCode']) ? $response['responseCode'] : null;
  	$transaction_id = isset($response['transactionID']) ? $response['transactionID'] : null;
  	
  	$transaction_message = isset(self::$responseMessages[$status]) ? self::$responseMessages[$status] : 'N/A';
  	
  	$this->load->model('checkout/order');
  	$order_info = $this->model_checkout_order->getOrder($order_id);
  	
  	if ($order_info && $status != null) {
  		
  		if($status == self::APPROVED){
  			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_aldrapay_completed_status_id'), "UID: {$transaction_id}. Message: {$transaction_message}", true);
  			return true;
  		}
  		else if(in_array($status, array(self::FAILED, self::DECLINED))){
  			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_aldrapay_failed_status_id'), "UID: {$transaction_id}. Message: {$transaction_message}", true);
  		}
  		
  	}
  		
  	return false;
  }

  
  private function _language($lang_id) {
    $lang = substr($lang_id, 0, 2);
    $lang = strtolower($lang);
    return $lang;
  }
}
