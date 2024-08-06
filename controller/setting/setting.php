<?php
namespace Opencart\Admin\Controller\Setting;
/**
 * Class Setting
 *
 * @package Opencart\Admin\Controller\Setting
 */
class Setting extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['save'] = $this->url->link('setting/setting.save', 'user_token=' . $this->session->data['user_token']);

		if ($this->user->hasPermission('access', 'common/developer')) {
			$data['developer_status'] = true;
		} else {
			$data['developer_status'] = false;
		}		

		// General
		$data['company_url'] = HTTP_SERVER;

		// Company Details
		$data['config_name'] = $this->config->get('config_name');
		$data['config_owner'] = $this->config->get('config_owner');
		$data['config_address'] = $this->config->get('config_address');
		$data['config_geocode'] = $this->config->get('config_geocode');
		$data['config_email'] = $this->config->get('config_email');
		$data['config_telephone'] = $this->config->get('config_telephone');
		$data['config_gsm'] = $this->config->get('config_gsm');
		$data['config_open_hours'] = (array)$this->config->get('config_open_hours');
		$data['config_calendar_view'] = $this->config->get('config_calendar_view');
		$data['config_public_holidays'] = (array)$this->config->get('config_public_holidays');
		$data['config_religious_holiday_qurban'] = $this->config->get('config_religious_holiday_qurban');
		$data['config_religious_holiday_ramadan'] = $this->config->get('config_religious_holiday_ramadan');
		
		$this->load->model('localisation/location');

		$data['locations'] = $this->model_localisation_location->getLocations();

		$data['config_location'] = (array)$this->config->get('config_location');

		// Localisation
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['config_country_id'] = $this->config->get('config_country_id');
		$data['config_zone_id'] = $this->config->get('config_zone_id');
		$data['config_timezone'] = $this->config->get('config_timezone');

		$data['timezones'] = [];

		$timestamp = date_create('now');

		$timezones = timezone_identifiers_list();

		foreach ($timezones as $timezone) {
			date_timezone_set($timestamp, timezone_open($timezone));

			$hour = ' (' . date_format($timestamp, 'P') . ')';

			$data['timezones'][] = [
				'text'  => $timezone . $hour,
				'value' => $timezone
			];
		}

		// Language
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['config_language_admin'] = $this->config->get('config_language_admin');

		// Currency
		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['config_currency'] = $this->config->get('config_currency');

		$data['currency_engines'] = [];

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getExtensionsByType('currency');

		foreach ($extensions as $extension) {
			if ($this->config->get('currency_' . $extension['code'] . '_status')) {
				$this->load->language('extension/' . $extension['extension'] . '/currency/' . $extension['code'], 'extension');

				$data['currency_engines'][] = [
					'text'  => $this->language->get('extension_heading_title'),
					'value' => $extension['code']
				];
			}
		}

		$data['config_currency_engine'] = $this->config->get('config_currency_engine');
		$data['config_currency_auto'] = $this->config->get('config_currency_auto');

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		$data['config_length_class_id'] = $this->config->get('config_length_class_id');

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		$data['config_weight_class_id'] = $this->config->get('config_weight_class_id');

		// Options
		$data['config_product_description_length'] = $this->config->get('config_product_description_length');
		$data['config_pagination_admin'] = $this->config->get('config_pagination_admin');
		$data['config_autocomplete_limit'] = $this->config->get('config_autocomplete_limit');

		// Legal
		$data['config_cookie_id'] = $this->config->get('config_cookie_id');
		
		// Tax
		$data['config_tax'] = $this->config->get('config_tax');
		$data['config_tax_default'] = $this->config->get('config_tax_default');

		// Checkout

		if ($this->config->get('config_invoice_prefix')) {
			$data['config_invoice_prefix'] = $this->config->get('config_invoice_prefix');
		} else {
			$data['config_invoice_prefix'] = 'INV-' . date('Y') . '-00';
		}

		$this->load->model('localisation/process_status');

		$data['process_statuses'] = $this->model_localisation_process_status->getProcessStatuses();

		$data['config_process_status_id'] = $this->config->get('config_process_status_id');
		$data['config_processing_status'] = (array)$this->config->get('config_processing_status');
		$data['config_complete_status'] = (array)$this->config->get('config_complete_status');

		// Images
		$data['config_logo'] = $this->config->get('config_logo');

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('placeholder.svg', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));

		if ($data['config_logo'] && is_file(DIR_IMAGE . html_entity_decode($data['config_logo'], ENT_QUOTES, 'UTF-8'))) {
			$data['logo'] = $this->model_tool_image->resize($data['config_logo'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
		} else {
			$data['logo'] = $data['placeholder'];
		}

		// Image
		$data['config_image_default_width'] = $this->config->get('config_image_default_width');
		$data['config_image_default_height'] = $this->config->get('config_image_default_height');
		$data['config_image_thumb_width'] = $this->config->get('config_image_thumb_width');
		$data['config_image_thumb_height'] = $this->config->get('config_image_thumb_height');
		$data['config_image_popup_width'] = $this->config->get('config_image_popup_width');
		$data['config_image_popup_height'] = $this->config->get('config_image_popup_height');
		$data['config_image_product_width'] = $this->config->get('config_image_product_width');
		$data['config_image_product_height'] = $this->config->get('config_image_product_height');
		$data['config_image_additional_width'] = $this->config->get('config_image_additional_width');
		$data['config_image_additional_height'] = $this->config->get('config_image_additional_height');
		$data['config_image_cart_width'] = $this->config->get('config_image_cart_width');
		$data['config_image_cart_height'] = $this->config->get('config_image_cart_height');

		// Mail
		$data['config_mail_engine'] = $this->config->get('config_mail_engine');
		$data['config_mail_parameter'] = $this->config->get('config_mail_parameter');
		$data['config_mail_smtp_hostname'] = $this->config->get('config_mail_smtp_hostname');
		$data['config_mail_smtp_username'] = $this->config->get('config_mail_smtp_username');
		$data['config_mail_smtp_password'] = $this->config->get('config_mail_smtp_password');
		$data['config_mail_smtp_port'] = $this->config->get('config_mail_smtp_port');
		$data['config_mail_smtp_timeout'] = $this->config->get('config_mail_smtp_timeout');
		$data['config_mail_alert'] = (array)$this->config->get('config_mail_alert');

		$data['mail_alerts'] = [];

		$data['mail_alerts'][] = [
			'text'  => $this->language->get('text_mail_order'),
			'value' => 'order'
		];

		$data['config_mail_alert_email'] = $this->config->get('config_mail_alert_email');

		// Server
		$data['config_session_expire'] = $this->config->get('config_session_expire');
		$data['config_session_samesite'] = $this->config->get('config_session_samesite');
		$data['config_compression'] = $this->config->get('config_compression');

		// Security
		if (DIR_STORAGE == DIR_SYSTEM . 'storage/') {
			$data['storage_moved'] = 0;
		}else{
			$data['storage_moved'] = 1;
		}
		$data['config_storage'] = $this->config->get('config_storage');
		$data['config_user_2fa'] = $this->config->get('config_user_2fa');

		// Uploads
		$data['config_file_max_size'] = $this->config->get('config_file_max_size');
		$data['config_file_ext_allowed'] = $this->config->get('config_file_ext_allowed');
		$data['config_file_mime_allowed'] = $this->config->get('config_file_mime_allowed');

		// Errors
		$data['config_error_display'] = $this->config->get('config_error_display');
		$data['config_error_log'] = $this->config->get('config_error_log');
		$data['config_error_filename'] = $this->config->get('config_error_filename');

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('setting/setting', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('setting/setting');

		$json = [];

		if (!$this->user->hasPermission('modify', 'setting/setting')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['config_name']) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if (!oc_validate_length($this->request->post['config_owner'], 3, 64)) {
			$json['error']['owner'] = $this->language->get('error_owner');
		}

		if (!oc_validate_length($this->request->post['config_address'], 3, 256)) {
			$json['error']['address'] = $this->language->get('error_address');
		}

		if ((oc_strlen($this->request->post['config_email']) > 96) || !filter_var($this->request->post['config_email'], FILTER_VALIDATE_EMAIL)) {
			$json['error']['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['config_pagination_admin']) {
			$json['error']['pagination_admin'] = $this->language->get('error_pagination');
		}

		if (!$this->request->post['config_autocomplete_limit']) {
			$json['error']['autocomplete_limit'] = $this->language->get('error_autocomplete_limit');
		}

		if (!isset($this->request->post['config_processing_status'])) {
			$json['error']['processing_status'] = $this->language->get('error_processing_status');
		}

		if (!isset($this->request->post['config_complete_status'])) {
			$json['error']['complete_status'] = $this->language->get('error_complete_status');
		}

		if (!$this->request->post['config_image_default_width'] || !$this->request->post['config_image_default_height']) {
			$json['error']['image_default'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
			$json['error']['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
			$json['error']['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
			$json['error']['image_product'] = $this->language->get('error_image_product');
		}

		if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
			$json['error']['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
			$json['error']['image_cart'] = $this->language->get('error_image_cart');
		}

		if ($this->request->post['config_user_2fa'] && !$this->request->post['config_mail_engine']) {
			$json['error']['warning'] = $this->language->get('error_user_2fa');
		}

		if (!$this->request->post['config_file_max_size']) {
			$json['error']['file_max_size'] = $this->language->get('error_file_max_size');
		}

		$disallowed = [
			'php',
			'php4',
			'php3'
		];

		$extensions = explode("\n", $this->request->post['config_file_ext_allowed']);

		foreach ($extensions as $extension) {
			if (in_array(trim($extension), $disallowed)) {
				$json['error']['file_ext_allowed'] = $this->language->get('error_extension');

				break;
			}
		}

		$disallowed = [
			'php',
			'php4',
			'php3'
		];

		$mimes = explode("\n", $this->request->post['config_file_mime_allowed']);

		foreach ($mimes as $mime) {
			if (in_array(trim($mime), $disallowed)) {
				$json['error']['file_mime_allowed'] = $this->language->get('error_mime');

				break;
			}
		}

		if (!$this->request->post['config_error_filename']) {
			$json['error']['error_filename'] = $this->language->get('error_log_required');
		} elseif (preg_match('/\.\.[\/\\\]?/', $this->request->post['config_error_filename'])) {
			$json['error']['error_filename'] = $this->language->get('error_log_invalid');
		} elseif (substr($this->request->post['config_error_filename'], strrpos($this->request->post['config_error_filename'], '.')) != '.log') {
			$json['error']['error_filename'] = $this->language->get('error_log_extension');
		}

		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('config', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
