<?php
class ControllerPaymentSn extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/sn');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sn', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['entry_spin'] = $this->language->get('entry_spin');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_canceled_reversal_status'] = $this->language->get('entry_canceled_reversal_status');
		$data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_webservice'] = $this->language->get('entry_webservice');
        $data['entry_webservice_desc'] = $this->language->get('entry_webservice_desc');
		$data['help_debug'] = $this->language->get('help_debug');
		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_order_status'] = $this->language->get('tab_order_status');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['spin'])) {
			$data['error_spin'] = $this->error['spin'];
		} else {
			$data['error_spin'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/sn', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/sn', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['sn_spin'])) {
			$data['sn_spin'] = $this->request->post['sn_spin'];
		} else {
			$data['sn_spin'] = $this->config->get('sn_spin');
		}

		if (isset($this->request->post['sn_debug'])) {
			$data['sn_debug'] = $this->request->post['sn_debug'];
		} else {
			$data['sn_debug'] = $this->config->get('sn_debug');
		}

		if (isset($this->request->post['sn_webservice'])) {
			$data['sn_webservice'] = $this->request->post['sn_webservice'];
		} else {
			$data['sn_webservice'] = $this->config->get('sn_webservice');
		}

		if (isset($this->request->post['sn_total'])) {
			$data['sn_total'] = $this->request->post['sn_total'];
		} else {
			$data['sn_total'] = $this->config->get('sn_total');
		}

		if (isset($this->request->post['sn_canceled_reversal_status_id'])) {
			$data['sn_canceled_reversal_status_id'] = $this->request->post['sn_canceled_reversal_status_id'];
		} else {
			$data['sn_canceled_reversal_status_id'] = $this->config->get('sn_canceled_reversal_status_id');
		}

		if (isset($this->request->post['sn_completed_status_id'])) {
			$data['sn_completed_status_id'] = $this->request->post['sn_completed_status_id'];
		} else {
			$data['sn_completed_status_id'] = $this->config->get('sn_completed_status_id');
		}

		if (isset($this->request->post['sn_failed_status_id'])) {
			$data['sn_failed_status_id'] = $this->request->post['sn_failed_status_id'];
		} else {
			$data['sn_failed_status_id'] = $this->config->get('sn_failed_status_id');
		}

		if (isset($this->request->post['sn_pending_status_id'])) {
			$data['sn_pending_status_id'] = $this->request->post['sn_pending_status_id'];
		} else {
			$data['sn_pending_status_id'] = $this->config->get('sn_pending_status_id');
		}

		if (isset($this->request->post['sn_processed_status_id'])) {
			$data['sn_processed_status_id'] = $this->request->post['sn_processed_status_id'];
		} else {
			$data['sn_processed_status_id'] = $this->config->get('sn_processed_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['sn_geo_zone_id'])) {
			$data['sn_geo_zone_id'] = $this->request->post['sn_geo_zone_id'];
		} else {
			$data['sn_geo_zone_id'] = $this->config->get('sn_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['sn_status'])) {
			$data['sn_status'] = $this->request->post['sn_status'];
		} else {
			$data['sn_status'] = $this->config->get('sn_status');
		}

		if (isset($this->request->post['sn_sort_order'])) {
			$data['sn_sort_order'] = $this->request->post['sn_sort_order'];
		} else {
			$data['sn_sort_order'] = $this->config->get('sn_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/sn.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/sn')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['sn_spin']) {
			$this->error['spin'] = $this->language->get('error_spin');
		}

		return !$this->error;
	}
}