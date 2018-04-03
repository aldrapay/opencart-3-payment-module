<?php
class ControllerExtensionPaymentAldrapay extends Controller {
  private $error = array();

  public function index() {
    $this->load->language('extension/payment/aldrapay');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('setting/setting');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
      $this->model_setting_setting->editSetting('payment_aldrapay', $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
    }

    $data['heading_title'] = $this->language->get('heading_title');
    $data['text_edit'] = $this->language->get('text_edit');

    $data['text_live_mode'] = $this->language->get('text_live_mode');
    $data['text_test_mode'] = $this->language->get('text_test_mode');
    $data['text_enabled'] = $this->language->get('text_enabled');
    $data['text_disabled'] = $this->language->get('text_disabled');
    $data['text_all_zones'] = $this->language->get('text_all_zones');

    $data['entry_email'] = $this->language->get('entry_email');
    $data['entry_order_status'] = $this->language->get('entry_order_status');
    $data['entry_order_status_completed_text'] = $this->language->get('entry_order_status_completed_text');
    $data['entry_order_status_pending'] = $this->language->get('entry_order_status_pending');
    $data['entry_order_status_canceled'] = $this->language->get('entry_order_status_canceled');
    $data['entry_order_status_failed'] = $this->language->get('entry_order_status_failed');
    $data['entry_order_status_failed_text'] = $this->language->get('entry_order_status_failed_text');
    $data['entry_order_status_processing'] = $this->language->get('entry_order_status_processing');
    $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
    $data['entry_status'] = $this->language->get('entry_status');
    $data['entry_sort_order'] = $this->language->get('entry_sort_order');
    $data['entry_companyid'] = $this->language->get('entry_companyid');
    $data['entry_companyid_help'] = $this->language->get('entry_companyid_help');
    $data['entry_encyptionkey'] = $this->language->get('entry_encyptionkey');
    $data['entry_encyptionkey_help'] = $this->language->get('entry_encyptionkey_help');
    $data['entry_encyptionalgo'] = $this->language->get('entry_encyptionalgo');
    $data['entry_encyptionalgo_help'] = $this->language->get('entry_encyptionalgo_help');
    $data['entry_domain_payment_page'] = $this->language->get('entry_domain_payment_page');
    $data['entry_domain_payment_page_help'] = $this->language->get('entry_domain_payment_page_help');
    $data['entry_payment_type'] = $this->language->get('entry_payment_type');
    $data['entry_payment_type_sale'] = $this->language->get('entry_payment_type_sale');
    $data['entry_payment_type_authorization'] = $this->language->get('entry_payment_type_authorization');
    $data['button_save'] = $this->language->get('button_save');
    $data['button_cancel'] = $this->language->get('button_cancel');
    $data['tab_general'] = $this->language->get('tab_general');

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['companyid'])) {
      $data['error_companyid'] = $this->error['companyid'];
    } else {
      $data['error_companyid'] = '';
    }

    if (isset($this->error['encyptionkey'])) {
      $data['error_encyptionkey'] = $this->error['encyptionkey'];
    } else {
      $data['error_encyptionkey'] = '';
    }

    if (isset($this->error['encyptionalgo'])) {
      $data['error_encyptionalgo'] = $this->error['encyptionalgo'];
    } else {
      $data['error_encyptionalgo'] = '';
    }

    if (isset($this->error['domain_payment_page'])) {
      $data['error_domain_payment_page'] = $this->error['domain_payment_page'];
    } else {
      $data['error_domain_payment_page'] = '';
    }

    $data['breadcrumbs'] = array();

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_home'),
      'href'      =>  $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      'separator' => false
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('text_extension'),
      'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
    );

    $data['breadcrumbs'][] = array(
      'text'      => $this->language->get('heading_title'),
      'href'      => $this->url->link('extension/payment/aldrapay', 'user_token=' . $this->session->data['user_token'], true),
      'separator' => ' :: '
    );

    $data['action'] = $this->url->link('extension/payment/aldrapay', 'user_token=' . $this->session->data['user_token'], true);

    $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']  . '&type=payment', true);


    if (isset($this->request->post['payment_aldrapay_companyid'])) {
      $data['payment_aldrapay_companyid'] = $this->request->post['payment_aldrapay_companyid'];
    } else {
      $data['payment_aldrapay_companyid'] = $this->config->get('payment_aldrapay_companyid');
    }

    if (isset($this->request->post['payment_aldrapay_encyptionkey'])) {
      $data['payment_aldrapay_encyptionkey'] = $this->request->post['payment_aldrapay_encyptionkey'];
    } else {
      $data['payment_aldrapay_encyptionkey'] = $this->config->get('payment_aldrapay_encyptionkey');
    }
    
    if (isset($this->request->post['payment_aldrapay_encyptionalgo'])) {
      $data['payment_aldrapay_encyptionalgo'] = $this->request->post['payment_aldrapay_encyptionalgo'];
    } else {
      $data['payment_aldrapay_encyptionalgo'] = $this->config->get('payment_aldrapay_encyptionalgo');
    }

    if (isset($this->request->post['payment_aldrapay_domain_payment_page'])) {
      $data['payment_aldrapay_domain_payment_page'] = $this->request->post['payment_aldrapay_domain_payment_page'];
    } else {
      $data['payment_aldrapay_domain_payment_page'] = $this->config->get('payment_aldrapay_domain_payment_page');
    }

	if (isset($this->request->post['payment_aldrapay_payment_type'])) {
	  $data['payment_aldrapay_payment_type'] = $this->request->post['payment_aldrapay_payment_type'];
	} else {
	  $data['payment_aldrapay_payment_type'] = $this->config->get('payment_aldrapay_payment_type');
	}

    if (isset($this->request->post['payment_aldrapay_completed_status_id'])) {
      $data['payment_aldrapay_completed_status_id'] = $this->request->post['payment_aldrapay_completed_status_id'];
    } else {
      $data['payment_aldrapay_completed_status_id'] = $this->config->get('payment_aldrapay_completed_status_id');
    }

    if (isset($this->request->post['payment_aldrapay_failed_status_id'])) {
      $data['payment_aldrapay_failed_status_id'] = $this->request->post['payment_aldrapay_failed_status_id'];
    } else {
      $data['payment_aldrapay_failed_status_id'] = $this->config->get('payment_aldrapay_failed_status_id');
    }

    $this->load->model('localisation/order_status');

    $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    
    $data['payment_types'] = array(
    		array('payment_type_id' => 'sale', 'name' => $this->language->get('entry_payment_type_sale')),
    		array('payment_type_id' => 'authorization', 'name' => $this->language->get('entry_payment_type_authorization')),
    );
    
    $data['algo_types'] = array(
    		array('algo_type_id' => 'sha1', 'name' => 'SHA-1 (160 bits)'),
    		array('algo_type_id' => 'sha224', 'name' => 'SHA-2 (224 bits)'),
    		array('algo_type_id' => 'sha256', 'name' => 'SHA-2 (256 bits)'),
    		array('algo_type_id' => 'sha384', 'name' => 'SHA-2 (384 bits)'),
    		array('algo_type_id' => 'sha512', 'name' => 'SHA-2 (512 bits)'),
    );

    if (isset($this->request->post['payment_aldrapay_status'])) {
      $data['payment_aldrapay_status'] = $this->request->post['payment_aldrapay_status'];
    } else {
      $data['payment_aldrapay_status'] = $this->config->get('payment_aldrapay_status');
    }

    if (isset($this->request->post['payment_aldrapay_geo_zone_id'])) {
      $data['payment_aldrapay_geo_zone_id'] = $this->request->post['payment_aldrapay_geo_zone_id'];
    } else {
      $data['payment_aldrapay_geo_zone_id'] = $this->config->get('payment_aldrapay_geo_zone_id');
    }

    $this->load->model('localisation/geo_zone');

    $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

    if (isset($this->request->post['payment_aldrapay_sort_order'])) {
      $data['payment_aldrapay_sort_order'] = $this->request->post['payment_aldrapay_sort_order'];
    } else {
      $data['payment_aldrapay_sort_order'] = $this->config->get('payment_aldrapay_sort_order');
    }

    $data['user_token'] = $this->session->data['user_token'];

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    
    $this->response->setOutput($this->load->view('extension/payment/aldrapay', $data));
  }

  private function validate() {
    if (!$this->user->hasPermission('modify', 'extension/payment/aldrapay')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!$this->request->post['payment_aldrapay_companyid']) {
      $this->error['companyid'] = $this->language->get('error_companyid');
    }

    if (!$this->request->post['payment_aldrapay_encyptionkey']) {
      $this->error['encyptionkey'] = $this->language->get('error_encyptionkey');
    }
    
    if (!$this->request->post['payment_aldrapay_encyptionalgo']) {
      $this->error['encyptionalgo'] = $this->language->get('error_encyptionalgo');
    }

    if (!$this->request->post['payment_aldrapay_domain_payment_page']) {
      $this->error['domain_payment_page'] = $this->language->get('error_domain_payment_page');
    }

    return !$this->error;
  }
}
