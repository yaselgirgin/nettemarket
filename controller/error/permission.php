<?php
namespace Opencart\Admin\Controller\Error;
/**
 * Class Permission
 *
 * @package Opencart\Admin\Controller\Error
 */
class Permission extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('error/permission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->response->addheader($this->request->server['SERVER_PROTOCOL'] . ' 401 Unauthorized');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('error/permission', $data));
	}
}
