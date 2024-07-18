<?php
namespace Opencart\Admin\Controller\Common;
/**
 * Class Column Left
 *
 * @package Opencart\Admin\Controller\Common
 */
class ColumnLeft extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		if (isset($this->request->get['user_token']) && isset($this->session->data['user_token']) && ((string)$this->request->get['user_token'] == $this->session->data['user_token'])) {
			$this->load->language('common/column_left');
			
			$data['profile'] = $this->url->link('user/profile', 'user_token=' . $this->session->data['user_token']);

			$this->load->model('user/user');

			$user_info = $this->model_user_user->getUser($this->user->getId());

			if ($user_info) {
				$data['firstname'] = $user_info['firstname'];
				$data['lastname'] = $user_info['lastname'];
				$data['user_group'] = $user_info['user_group'];
			} else {
				$data['firstname'] = '';
				$data['lastname'] = '';
				$data['user_group'] = '';
			}

			$this->load->model('tool/image');

			if ($user_info['image'] && is_file(DIR_IMAGE . html_entity_decode($user_info['image'], ENT_QUOTES, 'UTF-8'))) {
				$data['image'] = $this->model_tool_image->resize($user_info['image'], 45, 45);
			} else {
				$data['image'] = $this->model_tool_image->resize('profile.png', 45, 45);
			}

			// Create a 3 level menu array
			// Level 2 cannot have children

			// Menu
			$data['menus'][] = [
				'id'       => 'menu-dashboard',
				'icon'     => 'fas fa-home',
				'name'     => $this->language->get('text_dashboard'),
				'href'     => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']),
				'children' => []
			];

			// Agenda
			$agenda = [];

			if ($this->user->hasPermission('access', 'agenda/calendar')) {
				$agenda[] = [
					'name'     => $this->language->get('text_calendar'),
					'href'     => $this->url->link('agenda/calendar', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			
			if ($this->user->hasPermission('access', 'agenda/task')) {
				$agenda[] = [
					'name'     => $this->language->get('text_task'),
					'href'     => $this->url->link('agenda/task', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'agenda/note')) {
				$agenda[] = [
					'name'     => $this->language->get('text_note'),
					'href'     => $this->url->link('agenda/note', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($agenda) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'far fa-calendar-check',
					'name'     => $this->language->get('text_agenda'),
					'href'     => '',
					'children' => $agenda
				];
			}	

			// Stock
			$stock = [];

			if ($this->user->hasPermission('access', 'stock/product')) {
				$stock[] = [
					'name'     => $this->language->get('text_product'),
					'href'     => $this->url->link('stock/product.a', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($stock) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-tags',
					'name'     => $this->language->get('text_stock'),
					'href'     => '',
					'children' => $stock
				];
			}					

			// Contact
			$contact = [];

			if ($this->user->hasPermission('access', 'contact/contact')) {
				$contact[] = [
					'name'     => $this->language->get('text_contacts'),
					'href'     => $this->url->link('contact/contact', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($contact) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'far fa-address-book',
					'name'     => $this->language->get('text_contact'),
					'href'     => '',
					'children' => $contact
				];
			}				

			// Transaction Sale
			$transaction_sale = [];

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$transaction_sale[] = [
					'name'     => $this->language->get('text_invoice'),
					'href'     => $this->url->link('transaction/invoice.sale', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($transaction_sale) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'far fa-money-bill-1',
					'name'     => $this->language->get('text_sale'),
					'href'     => '',
					'children' => $transaction_sale
				];
			}				
			
			// Transaction Purchase
			$transaction_purchase = [];

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$transaction_purchase[] = [
					'name'     => $this->language->get('text_invoice'),
					'href'     => $this->url->link('transaction/invoice.purchase', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($transaction_purchase) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-cart-shopping',
					'name'     => $this->language->get('text_purchase'),
					'href'     => '',
					'children' => $transaction_purchase
				];
			}				

			// Finance
			$finance = [];

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$finance[] = [
					'name'     => $this->language->get('text_bank_cash'),
					'href'     => $this->url->link('finance/account', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$finance[] = [
					'name'     => $this->language->get('text_cheques_bonds'),
					'href'     => $this->url->link('finance/future', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}			

			if ($finance) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-calculator',
					'name'     => $this->language->get('text_finance'),
					'href'     => '',
					'children' => $finance
				];
			}	
			

			// Employee
			$employee = [];

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$employee[] = [
					'name'     => $this->language->get('text_employees'),
					'href'     => $this->url->link('employee/employee', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$employee[] = [
					'name'     => $this->language->get('text_salary'),
					'href'     => $this->url->link('employee/salary', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}	

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$employee[] = [
					'name'     => $this->language->get('text_annual_leave'),
					'href'     => $this->url->link('employee/leave.annual', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}			

			if ($employee) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-people-group',
					'name'     => $this->language->get('text_employee'),
					'href'     => '',
					'children' => $employee
				];
			}	

			// Research and Development
			$research_development = [];

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$research_development[] = [
					'name'     => $this->language->get('text_project'),
					'href'     => $this->url->link('arda/project', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$research_development[] = [
					'name'     => $this->language->get('text_meeting'),
					'href'     => $this->url->link('arda/meeting', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($research_development) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'far fa-lightbulb',
					'name'     => $this->language->get('text_research_development'),
					'href'     => '',
					'children' => $research_development
				];
			}	

			// Report
			$report = [];

			if ($this->user->hasPermission('access', 'transaction/invoice')) {
				$report[] = [
					'name'     => $this->language->get('text_summary'),
					'href'     => $this->url->link('report/summary', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($report) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-chart-simple',
					'name'     => $this->language->get('text_report'),
					'href'     => '',
					'children' => $report
				];
			}	

			// System
			$system = [];

			if ($this->user->hasPermission('access', 'setting/setting')) {
				$system[] = [
					'name'     => $this->language->get('text_setting'),
					'href'     => $this->url->link('setting/setting', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			// Extension
			$marketplace = [];

			if ($this->user->hasPermission('access', 'marketplace/marketplace')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_marketplace'),
					'href'     => $this->url->link('marketplace/marketplace', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/installer')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_installer'),
					'href'     => $this->url->link('marketplace/installer', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/extension')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_extension'),
					'href'     => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/modification')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_modification'),
					'href'     => $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/startup')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_startup'),
					'href'     => $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/event')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_event'),
					'href'     => $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'marketplace/cron')) {
				$marketplace[] = [
					'name'     => $this->language->get('text_cron'),
					'href'     => $this->url->link('marketplace/cron', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($marketplace) {
				$system[] = [
					'name'     => $this->language->get('text_extension'),
					'href'     => '',
					'children' => $marketplace
				];
			}			

			// Users
			$user = [];

			if ($this->user->hasPermission('access', 'user/user')) {
				$user[] = [
					'name'     => $this->language->get('text_users'),
					'href'     => $this->url->link('user/user', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'user/user_permission')) {
				$user[] = [
					'name'     => $this->language->get('text_user_group'),
					'href'     => $this->url->link('user/user_permission', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'user/api')) {
				$user[] = [
					'name'     => $this->language->get('text_api'),
					'href'     => $this->url->link('user/api', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($user) {
				$system[] = [
					'name'     => $this->language->get('text_users'),
					'href'     => '',
					'children' => $user
				];
			}

			// Localisation
			$localisation = [];

			if ($this->user->hasPermission('access', 'localisation/location')) {
				$localisation[] = [
					'name'     => $this->language->get('text_location'),
					'href'     => $this->url->link('localisation/location', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/language')) {
				$localisation[] = [
					'name'     => $this->language->get('text_language'),
					'href'     => $this->url->link('localisation/language', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/currency')) {
				$localisation[] = [
					'name'     => $this->language->get('text_currency'),
					'href'     => $this->url->link('localisation/currency', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			// Statuses
			$statuses = [];

			if ($this->user->hasPermission('access', 'localisation/process_status')) {
				$statuses[] = [
					'name'     => $this->language->get('text_process_status'),
					'href'     => $this->url->link('localisation/process_status', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($statuses) {
				$localisation[] = [
					'name'     => $this->language->get('text_statuses'),
					'href'     => '',
					'children' => $statuses
				];
			}

			if ($this->user->hasPermission('access', 'localisation/country')) {
				$localisation[] = [
					'name'     => $this->language->get('text_country'),
					'href'     => $this->url->link('localisation/country', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/zone')) {
				$localisation[] = [
					'name'     => $this->language->get('text_zone'),
					'href'     => $this->url->link('localisation/zone', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/geo_zone')) {
				$localisation[] = [
					'name'     => $this->language->get('text_geo_zone'),
					'href'     => $this->url->link('localisation/geo_zone', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			// Tax
			$tax = [];

			if ($this->user->hasPermission('access', 'localisation/tax_class')) {
				$tax[] = [
					'name'     => $this->language->get('text_tax_class'),
					'href'     => $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/tax_rate')) {
				$tax[] = [
					'name'     => $this->language->get('text_tax_rate'),
					'href'     => $this->url->link('localisation/tax_rate', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($tax) {
				$localisation[] = [
					'name'     => $this->language->get('text_tax'),
					'href'     => '',
					'children' => $tax
				];
			}

			if ($this->user->hasPermission('access', 'localisation/length_class')) {
				$localisation[] = [
					'name'     => $this->language->get('text_length_class'),
					'href'     => $this->url->link('localisation/length_class', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/weight_class')) {
				$localisation[] = [
					'name'     => $this->language->get('text_weight_class'),
					'href'     => $this->url->link('localisation/weight_class', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'localisation/address_format')) {
				$localisation[] = [
					'name'     => $this->language->get('text_address_format'),
					'href'     => $this->url->link('localisation/address_format', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($localisation) {
				$system[] = [
					'name'     => $this->language->get('text_localisation'),
					'href'     => '',
					'children' => $localisation
				];
			}

			// Tools
			$maintenance = [];

			if ($this->user->hasPermission('access', 'tool/upgrade')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_upgrade'),
					'href'     => $this->url->link('tool/upgrade', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'tool/backup')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_backup'),
					'href'     => $this->url->link('tool/backup', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'tool/upload')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_upload'),
					'href'     => $this->url->link('tool/upload', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($this->user->hasPermission('access', 'tool/log')) {
				$maintenance[] = [
					'name'     => $this->language->get('text_log'),
					'href'     => $this->url->link('tool/log', 'user_token=' . $this->session->data['user_token']),
					'children' => []
				];
			}

			if ($maintenance) {
				$system[] = [
					'name'     => $this->language->get('text_maintenance'),
					'href'     => '',
					'children' => $maintenance
				];
			}

			if ($system) {
				$data['menus'][] = [
					'id'       => 'menu-system',
					'icon'     => 'fas fa-gear',
					'name'     => $this->language->get('text_system'),
					'href'     => '',
					'children' => $system
				];
			}

			return $this->load->view('common/column_left', $data);
		} else {
			return '';
		}
	}
}
