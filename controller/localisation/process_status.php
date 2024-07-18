<?php
namespace Opencart\Admin\Controller\Localisation;
/**
 * Class Process Status
 *
 * @package Opencart\Admin\Controller\Localisation
 */
class ProcessStatus extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('localisation/process_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['add'] = $this->url->link('localisation/process_status.form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('localisation/process_status.delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/process_status', $data));
	}

	/**
	 * List
	 *
	 * @return void
	 */
	public function list(): void {
		$this->load->language('localisation/process_status');

		$this->response->setOutput($this->getList());
	}

	/**
	 * Get List
	 *
	 * @return string
	 */
	protected function getList(): string {
		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['action'] = $this->url->link('localisation/process_status.list', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['process_statuses'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('localisation/process_status');

		$results = $this->model_localisation_process_status->getProcessStatuses($filter_data);

		foreach ($results as $result) {
			$data['process_statuses'][] = [
				'process_status_id' => $result['process_status_id'],
				'name'            => $result['name'] . (($result['process_status_id'] == $this->config->get('config_process_status_id')) ? $this->language->get('text_default') : ''),
				'edit'            => $this->url->link('localisation/process_status.form', 'user_token=' . $this->session->data['user_token'] . '&process_status_id=' . $result['process_status_id'] . $url)
			];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_name'] = $this->url->link('localisation/process_status.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$process_status_total = $this->model_localisation_process_status->getTotalProcessStatuses();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $process_status_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('localisation/process_status.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($process_status_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($process_status_total - $this->config->get('config_pagination_admin'))) ? $process_status_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $process_status_total, ceil($process_status_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('localisation/process_status_list', $data);
	}

	/**
	 * Form
	 *
	 * @return void
	 */
	public function form(): void {
		$this->load->language('localisation/process_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['process_status_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['save'] = $this->url->link('localisation/process_status.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('localisation/process_status', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['process_status_id'])) {
			$data['process_status_id'] = (int)$this->request->get['process_status_id'];
		} else {
			$data['process_status_id'] = 0;
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->get['process_status_id'])) {
			$this->load->model('localisation/process_status');

			$data['process_status'] = $this->model_localisation_process_status->getDescriptions($this->request->get['process_status_id']);
		} else {
			$data['process_status'] = [];
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/process_status_form', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('localisation/process_status');

		$json = [];

		if (!$this->user->hasPermission('modify', 'localisation/process_status')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['process_status'] as $language_id => $value) {
			if (!oc_validate_length($value['name'], 3, 32)) {
				$json['error']['name_' . $language_id] = $this->language->get('error_name');
			}
		}

		if (!$json) {
			$this->load->model('localisation/process_status');

			if (!$this->request->post['process_status_id']) {
				$json['process_status_id'] = $this->model_localisation_process_status->addProcessStatus($this->request->post);
			} else {
				$this->model_localisation_process_status->editProcessStatus($this->request->post['process_status_id'], $this->request->post);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Delete
	 *
	 * @return void
	 */
	public function delete(): void {
		$this->load->language('localisation/process_status');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'localisation/process_status')) {
			$json['error'] = $this->language->get('error_permission');
		}

		foreach ($selected as $process_status_id) {
			if ($this->config->get('config_process_status_id') == $process_status_id) {
				$json['error'] = $this->language->get('error_default');
			}
		}

		if (!$json) {
			$this->load->model('localisation/process_status');

			foreach ($selected as $process_status_id) {
				$this->model_localisation_process_status->deleteProcessStatus($process_status_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
