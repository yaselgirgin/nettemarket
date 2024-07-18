<?php
namespace Opencart\Admin\Controller\Agenda;
/**
 * Class Note
 *
 * @package Opencart\Admin\Controller\Agenda
 */
class Note extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('agenda/note');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('agenda/note', $data));
	}
}