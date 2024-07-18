<?php
namespace Opencart\Admin\Controller\Common;
/**
 * Class Header
 *
 * @package Opencart\Admin\Controller\Common
 */
class Header extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['title'] = $this->document->getTitle();
		$data['base'] = HTTP_SERVER;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();

		// Hard coding css so they can be replaced via the event's system.
		$data['bootstrap'] = 'view/stylesheet/bootstrap.css';
		$data['icons'] = 'view/stylesheet/fonts/fontawesome/css/all.min.css';
		$data['stylesheet'] = 'view/stylesheet/stylesheet.css';

		// Hard coding scripts so they can be replaced via the event's system.
		$data['jquery'] = 'view/javascript/jquery/jquery-3.7.1.min.js';

		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();

		$this->load->language('common/header');

		if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
			$data['logged'] = false;

			$data['home'] = $this->url->link('common/login');
		} else {
			$data['logged'] = true;

			$data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);

			$data['language'] = $this->load->controller('common/language');

			// Notifications
			$filter_data = [
				'start' => 0,
				'limit' => 5
			];

			$data['notifications'] = [];

			$this->load->model('tool/notification');

			$results = $this->model_tool_notification->getNotifications($filter_data);

			foreach ($results as $result) {
				$data['notifications'][] = [
					'title' => $result['title'],
					'href'  => $this->url->link('tool/notification.info', 'user_token=' . $this->session->data['user_token'] . '&notification_id=' . $result['notification_id'])
				];
			}

			$data['notification_all'] = $this->url->link('tool/notification', 'user_token=' . $this->session->data['user_token']);
			$data['notification_total'] = $this->model_tool_notification->getTotalNotifications(['filter_status' => 0]);

			$data['logout'] = $this->url->link('common/logout', 'user_token=' . $this->session->data['user_token']);
		}

		return $this->load->view('common/header', $data);
	}
}
