<?php
namespace Opencart\Admin\Model\Localisation;
/**
 * Class Process Status
 *
 * @package Opencart\Admin\Model\Localisation
 */
class ProcessStatus extends \Opencart\System\Engine\Model {
	/**
	 * Add Process Status
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return ?int
	 */
	public function addProcessStatus(array $data): ?int {
		$process_status_id = 0;

		foreach ($data['process_status'] as $language_id => $process_status) {
			if (!$process_status_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "process_status` SET `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($process_status['name']) . "'");

				$process_status_id = $this->db->getLastId();
			} else {
				$this->model_localisation_process_status->addDescription($process_status_id, $language_id, $process_status);
			}
		}

		$this->cache->delete('process_status');

		return $process_status_id;
	}

	/**
	 * Edit Process Status
	 *
	 * @param int                  $process_status_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editProcessStatus(int $process_status_id, array $data): void {
		$this->deleteProcessStatus($process_status_id);

		foreach ($data['process_status'] as $language_id => $value) {
			$this->model_localisation_process_status->addDescription($process_status_id, $language_id, $value);
		}

		$this->cache->delete('process_status');
	}

	/**
	 * Delete Process Status
	 *
	 * @param int $process_status_id
	 *
	 * @return void
	 */
	public function deleteProcessStatus(int $process_status_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "process_status` WHERE `process_status_id` = '" . (int)$process_status_id . "'");

		$this->cache->delete('process_status');
	}

	/**
	 * Delete Process Statuses By Language ID
	 *
	 * @param int $language_id
	 *
	 * @return void
	 */
	public function deleteProcessStatusesByLanguageId(int $language_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "process_status` WHERE `language_id` = '" . (int)$language_id . "'");

		$this->cache->delete('process_status');
	}

	/**
	 * Get Process Status
	 *
	 * @param int $process_status_id
	 *
	 * @return array<string, mixed>
	 */
	public function getProcessStatus(int $process_status_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "process_status` WHERE `process_status_id` = '" . (int)$process_status_id . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Get Process Statuses
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getProcessStatuses(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "process_status` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `name`";

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

		$key = md5($sql);

		$process_status_data = $this->cache->get('process_status.' . $key);

		if (!$process_status_data) {
			$query = $this->db->query($sql);

			$process_status_data = $query->rows;

			$this->cache->set('process_status.' . $key, $process_status_data);
		}

		return $process_status_data;
	}

	/**
	 * Add Description
	 *
	 * @param int                  $process_status_id
	 * @param int                  $language_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function addDescription(int $process_status_id, int $language_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "process_status` SET `process_status_id` = '" . (int)$process_status_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($data['name']) . "'");
	}

	/**
	 * Get Descriptions
	 *
	 * @param int $process_status_id
	 *
	 * @return array<int, array<string, string>>
	 */
	public function getDescriptions(int $process_status_id): array {
		$process_status_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "process_status` WHERE `process_status_id` = '" . (int)$process_status_id . "'");

		foreach ($query->rows as $result) {
			$process_status_data[$result['language_id']] = ['name' => $result['name']];
		}

		return $process_status_data;
	}

	/**
	 * Get Descriptions By Language ID
	 *
	 * @param int $language_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getDescriptionsByLanguageId(int $language_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "process_status` WHERE `language_id` = '" . (int)$language_id . "'");

		return $query->rows;
	}

	/**
	 * Get Total Process Statuses
	 *
	 * @return int
	 */
	public function getTotalProcessStatuses(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "process_status` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return (int)$query->row['total'];
	}
}
