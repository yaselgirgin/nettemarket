<?php
namespace Opencart\Admin\Model\Setting;
/**
 * Class Store
 *
 * @package Opencart\Admin\Model\Setting
 */
class Store extends \Opencart\System\Engine\Model {
	/**
	 * Add Store
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return int
	 */
	public function addStore(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "store` SET `name` = '" . $this->db->escape((string)$data['config_name']) . "', `url` = '" . $this->db->escape((string)$data['config_url']) . "'");

		$store_id = $this->db->getLastId();

		// SEO URL
		$this->load->model('design/seo_url');

		$results = $this->model_design_seo_url->getSeoUrlsByStoreId(0);

		foreach ($results as $result) {
			$this->model_design_seo_url->addSeoUrl($result['key'], $result['value'], $result['keyword'], $store_id, $result['language_id'], $result['sort_order']);
		}

		$this->cache->delete('store');

		return $store_id;
	}

	/**
	 * Edit Store
	 *
	 * @param int                  $store_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editStore(int $store_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "store` SET `name` = '" . $this->db->escape((string)$data['config_name']) . "', `url` = '" . $this->db->escape((string)$data['config_url']) . "' WHERE `store_id` = '" . (int)$store_id . "'");

		$this->cache->delete('store');
	}

	/**
	 * Delete Store
	 *
	 * @param int $store_id
	 *
	 * @return void
	 */
	public function deleteStore(int $store_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "store` WHERE `store_id` = '" . (int)$store_id . "'");

		$this->load->model('setting/setting');

		$this->model_setting_setting->deleteSettingsByStoreId($store_id);

		$this->cache->delete('store');
	}

	/**
	 * Get Store
	 *
	 * @param int $store_id
	 *
	 * @return array<string, mixed>
	 */
	public function getStore(int $store_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "store` WHERE `store_id` = '" . (int)$store_id . "'");

		return $query->row;
	}

	/**
	 * Get Stores
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getStores(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "store` ORDER BY `url`";

		$key = md5($sql);

		$store_data = $this->cache->get('store.' . $key);

		if (!$store_data) {
			$query = $this->db->query($sql);

			$store_data = $query->rows;

			$this->cache->set('store.' . $key, $store_data);
		}

		return $store_data;
	}

	/**
	 * Create Store Instance
	 *
	 * @param int    $store_id
	 * @param string $language
	 * @param string $session_id
	 *
	 * @throws \Exception
	 *
	 * @return \Opencart\System\Engine\Registry
	 */
	public function createStoreInstance(int $store_id = 0, string $language = '', string $session_id = ''): \Opencart\System\Engine\Registry {
		// Autoloader
		$this->autoloader->register('Opencart\Admin', DIR_APPLICATION);

		// Registry
		$registry = new \Opencart\System\Engine\Registry();
		$registry->set('autoloader', $this->autoloader);

		$config = new \Opencart\System\Engine\Config();
		$registry->set('config', $config);

		// Load the default config
		$config->addPath(DIR_CONFIG);
		$config->load('default');
		$config->load('admin');
		$config->set('application', 'Admin');

		// Store
		$config->set('config_store_id', $store_id);

		// Logging
		$registry->set('log', $this->log);

		// Event
		$event = new \Opencart\System\Engine\Event($registry);
		$registry->set('event', $event);

		// Event Register
		if ($config->has('action_event')) {
			foreach ($config->get('action_event') as $key => $value) {
				foreach ($value as $priority => $action) {
					$event->register($key, new \Opencart\System\Engine\Action($action), $priority);
				}
			}
		}

		// Factory
		$registry->set('factory', new \Opencart\System\Engine\Factory($registry));

		// Loader
		$loader = new \Opencart\System\Engine\Loader($registry);
		$registry->set('load', $loader);

		// Create a dummy request class, so we can feed the data to the order editor
		$request = new \stdClass();
		$request->get = [];
		$request->post = [];
		$request->server = $this->request->server;
		$request->cookie = [];

		// Request
		$registry->set('request', $request);

		// Response
		$response = new \Opencart\System\Library\Response();
		$registry->set('response', $response);

		// Database
		$registry->set('db', $this->db);

		// Cache
		$registry->set('cache', $this->cache);

		// Session
		$session = new \Opencart\System\Library\Session($config->get('session_engine'), $registry);
		$registry->set('session', $session);

		// Start session
		$session->start($session_id);

		// Template
		$template = new \Opencart\System\Library\Template($config->get('template_engine'));
		$template->addPath(DIR_APPLICATION . 'view/template/');
		$registry->set('template', $template);

		// Adding language var to the GET variable so there is a default language
		if ($language) {
			$request->get['language'] = $language;
		} else {
			$request->get['language'] = $config->get('language_code');
		}

		// Language
		$language = new \Opencart\System\Library\Language($request->get['language']);
		$language->addPath(DIR_APPLICATION . 'language/');
		$language->load('default');
		$registry->set('language', $language);

		// Url
		$registry->set('url', new \Opencart\System\Library\Url($config->get('site_url')));

		// Document
		$registry->set('document', new \Opencart\System\Library\Document());

		// Run pre actions to load key settings and classes.
		$pre_actions = [
			'startup/setting',
			'startup/language',
			'startup/extension',
			'startup/tax',
			'startup/currency',
			'startup/application',
			'startup/startup',
			'startup/event'
		];

		// Pre Actions
		foreach ($pre_actions as $pre_action) {
			$loader->controller($pre_action);
		}

		return $registry;
	}

	/**
	 * Get Total Stores
	 *
	 * @return int
	 */
	public function getTotalStores(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "store`");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Stores By Language
	 *
	 * @param string $language
	 *
	 * @return int
	 */
	public function getTotalStoresByLanguage(string $language): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND `store_id` != '0'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Stores By Currency
	 *
	 * @param string $currency
	 *
	 * @return int
	 */
	public function getTotalStoresByCurrency(string $currency): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND `store_id` != '0'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Stores By Country ID
	 *
	 * @param int $country_id
	 *
	 * @return int
	 */
	public function getTotalStoresByCountryId(int $country_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND `store_id` != '0'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Stores By Zone ID
	 *
	 * @param int $zone_id
	 *
	 * @return int
	 */
	public function getTotalStoresByZoneId(int $zone_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND `store_id` != '0'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Stores By Process Status ID
	 *
	 * @param int $process_status_id
	 *
	 * @return int
	 */
	public function getTotalStoresByProcessStatusId(int $process_status_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_process_status_id' AND `value` = '" . (int)$process_status_id . "' AND `store_id` != '0'");

		return (int)$query->row['total'];
	}
}
