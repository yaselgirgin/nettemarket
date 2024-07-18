<?php
namespace Opencart\Admin\Controller\Extension\Opencart\Payment;
/**
 * Class Free Checkout
 *
 * @package Opencart\Admin\Controller\Extension\Opencart\Payment
 */
class FreeCheckout extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/opencart/payment/free_checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['save'] = $this->url->link('extension/opencart/payment/free_checkout.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_free_checkout_process_status_id'] = $this->config->get('payment_free_checkout_process_status_id');

		$this->load->model('localisation/process_status');

		$data['process_statuses'] = $this->model_localisation_process_status->getProcessStatuses();

		$data['payment_free_checkout_status'] = $this->config->get('payment_free_checkout_status');
		$data['payment_free_checkout_sort_order'] = $this->config->get('payment_free_checkout_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/opencart/payment/free_checkout', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/opencart/payment/free_checkout');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/opencart/payment/free_checkout')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_free_checkout', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
