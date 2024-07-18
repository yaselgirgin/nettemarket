<?php
namespace Opencart\Admin\Model\Localisation;
/**
 * Class Language
 *
 * @package Opencart\Admin\Model\Localisation
 */
class Language extends \Opencart\System\Engine\Model {
	/**
	 * Add Language
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return int
	 */
	public function addLanguage(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "language` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `code` = '" . $this->db->escape((string)$data['code']) . "', `locale` = '" . $this->db->escape((string)$data['locale']) . "', `extension` = '" . $this->db->escape((string)$data['extension']) . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (bool)($data['status'] ?? 0) . "'");

		$this->cache->delete('language');

		$language_id = $this->db->getLastId();

		// Length
		$this->load->model('localisation/length_class');

		$results = $this->model_localisation_length_class->getDescriptionsByLanguageId($this->config->get('config_language_id'));

		foreach ($results as $length) {
			$this->model_localisation_length_class->addDescription($length['length_class_id'], $language_id, $length);
		}

		// Process Status
		$this->load->model('localisation/process_status');

		$results = $this->model_localisation_process_status->getDescriptionsByLanguageId($this->config->get('config_language_id'));

		foreach ($results as $process_status) {
			$this->model_localisation_process_status->addDescription($process_status['process_status_id'], $language_id, $process_status);
		}

		// Weight Class
		$this->load->model('localisation/weight_class');

		$results = $this->model_localisation_weight_class->getDescriptionsByLanguageId($this->config->get('config_language_id'));

		foreach ($results as $weight_class) {
			$this->model_localisation_weight_class->addDescription($weight_class['weight_class_id'], $language_id, $weight_class);
		}

		return $language_id;
	}

	/**
	 * Edit Language
	 *
	 * @param int                  $language_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editLanguage(int $language_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "language` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `code` = '" . $this->db->escape((string)$data['code']) . "', `locale` = '" . $this->db->escape((string)$data['locale']) . "', `extension` = '" . $this->db->escape((string)$data['extension']) . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (bool)($data['status'] ?? 0) . "' WHERE `language_id` = '" . (int)$language_id . "'");

		$this->cache->delete('language');
	}

	/**
	 * Delete Language
	 *
	 * @param int $language_id
	 *
	 * @return void
	 */
	public function deleteLanguage(int $language_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "language` WHERE `language_id` = '" . (int)$language_id . "'");

		$this->cache->delete('language');

		// Length
		$this->load->model('localisation/length_class');

		$this->model_localisation_length_class->deleteDescriptionsByLanguageId($language_id);

		// Process Status
		$this->load->model('localisation/process_status');

		$this->model_localisation_process_status->deleteProcessStatusesByLanguageId($language_id);

		// Weight Class
		$this->load->model('localisation/weight_class');

		$this->model_localisation_weight_class->deleteDescriptionsByLanguageId($language_id);
	}

	/**
	 * Get Language
	 *
	 * @param int $language_id
	 *
	 * @return array<string, mixed>
	 */
	public function getLanguage(int $language_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "language` WHERE `language_id` = '" . (int)$language_id . "'");

		$language = $query->row;

		if ($language) {
			$language['image'] = HTTP_SERVER;

			if (!$language['extension']) {
				$language['image'] .= '';
			} else {
				$language['image'] .= 'extension/' . $language['extension'] . '/';
			}

			$language['image'] .= 'language/' . $language['code'] . '/' . $language['code'] . '.png';
		}

		return $language;
	}

	/**
	 * Get Language By Code
	 *
	 * @param string $code
	 *
	 * @return array<string, mixed>
	 */
	public function getLanguageByCode(string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE `code` = '" . $this->db->escape($code) . "'");

		$language = $query->row;

		if ($language) {
			$language['image'] = HTTP_SERVER;

			if (!$language['extension']) {
				$language['image'] .= '';
			} else {
				$language['image'] .= 'extension/' . $language['extension'] . '/';
			}

			$language['image'] .= 'language/' . $language['code'] . '/' . $language['code'] . '.png';
		}

		return $language;
	}

	/**
	 * Get Languages
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function getLanguages(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "language`";

		$sort_data = [
			'name',
			'code',
			'sort_order'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `sort_order`, `name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$results = $this->cache->get('language.' . md5($sql));

		if (!$results) {
			$query = $this->db->query($sql);

			$results = $query->rows;

			$this->cache->set('language.' . md5($sql), $results);
		}

		$language_data = [];

		foreach ($results as $result) {
			$image = HTTP_SERVER;

			if (!$result['extension']) {
				$image .= '';
			} else {
				$image .= 'extension/' . $result['extension'] . '/';
			}

			$language_data[$result['code']] = $result + ['image' => $image . 'language/' . $result['code'] . '/' . $result['code'] . '.png'];
		}

		return $language_data;
	}

	/**
	 * Get Languages By Extension
	 *
	 * @param string $extension
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getLanguagesByExtension(string $extension): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE `extension` = '" . $this->db->escape($extension) . "'");

		return $query->rows;
	}

	/**
	 * Get Total Languages
	 *
	 * @return int
	 */
	public function getTotalLanguages(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "language`");

		return (int)$query->row['total'];
	}
}
