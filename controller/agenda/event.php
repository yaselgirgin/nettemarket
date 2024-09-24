<?php
namespace Opencart\Admin\Controller\Agenda;
/**
 * Class Event
 *
 * @package Opencart\Admin\Controller\Agenda
 */
class Event extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		$this->load->language('agenda/event');
		
		$data['event_form'] = $this->controller_agenda_event->addEvent();

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('agenda/event', $data);
	}

	/**
	 * Add Event
	 *
	 * @return void
	 */
	public function addEvent(): void {
		$this->load->language('agenda/event');

		$this->response->setOutput($this->controller_agenda_event->getForm());
	}

	/**
	 * getForm
	 *
	 * @return string
	 */
	public function getForm(): string {
		$data['user_id'] = $this->user->getId();
		
		// Guest Users
		$data['guest_users'] = [];

		// Guest User Groups
		$data['guest_user_groups'] = [];

		$data['upload_row'] = 0;
		$data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));
		$data['help_upload'] = sprintf($this->language->get('allowed_upload_size'), $this->config->get('config_file_max_size'));
		$data['config_file_max_size'] = ((int)$this->config->get('config_file_max_size') * 1024 * 1024);

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('agenda/event_form', $data);
	}


}
