<?php
namespace Opencart\Admin\Model\Localisation;
/**
 * Class ReturnReason
 *
 * @package Opencart\Admin\Model\Localisation
 */
class ReturnReason extends \Opencart\System\Engine\Model {
	/**
	 * Add Return Reason
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return ?int
	 */
	public function addReturnReason(array $data): ?int {
		$return_reason_id = 0;

		foreach ($data['return_reason'] as $language_id => $return_reason) {
			if (!$return_reason_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "return_reason` SET `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($return_reason['name']) . "'");

				$return_reason_id = $this->db->getLastId();
			} else {
				$this->model_localisation_return_reason->addDescription($return_reason_id, $language_id, $return_reason);
			}
		}

		$this->cache->delete('return_reason');

		return $return_reason_id;
	}

	/**
	 * Edit Return Reason
	 *
	 * @param int                  $return_reason_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editReturnReason(int $return_reason_id, array $data): void {
		$this->deleteReturnReason($return_reason_id);

		foreach ($data['return_reason'] as $language_id => $return_reason) {
			$this->model_localisation_return_reason->addDescription($return_reason_id, $language_id, $return_reason);
		}

		$this->cache->delete('return_reason');
	}

	/**
	 * Delete Return Reason
	 *
	 * @param int $return_reason_id
	 *
	 * @return void
	 */
	public function deleteReturnReason(int $return_reason_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return_reason` WHERE `return_reason_id` = '" . (int)$return_reason_id . "'");

		$this->cache->delete('return_reason');
	}

	/**
	 * Delete Return Reasons By Language ID
	 *
	 * @param int $language_id
	 *
	 * @return void
	 */
	public function deleteReturnReasonsByLanguageId(int $language_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return_reason` WHERE `language_id` = '" . (int)$language_id . "'");

		$this->cache->delete('return_reason');
	}

	/**
	 * Get Return Reason
	 *
	 * @param int $return_reason_id
	 *
	 * @return array<string, mixed>
	 */
	public function getReturnReason(int $return_reason_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "return_reason` WHERE `return_reason_id` = '" . (int)$return_reason_id . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Get Return Reasons
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getReturnReasons(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "return_reason` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `name`";

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

		$return_reason_data = $this->cache->get('return_reason.' . $key);

		if (!$return_reason_data) {
			$query = $this->db->query($sql);

			$return_reason_data = $query->rows;

			$this->cache->set('return_reason.' . $key, $return_reason_data);
		}

		return $return_reason_data;
	}

	/**
	 * Add Description
	 *
	 * @param int                  $return_reason_id
	 * @param int                  $language_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function addDescription(int $return_reason_id, int $language_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "return_reason` SET `return_reason_id` = '" . (int)$return_reason_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($data['name']) . "'");
	}

	/**
	 * Get Descriptions
	 *
	 * @param int $return_reason_id
	 *
	 * @return array<int, array<string, string>>
	 */
	public function getDescriptions(int $return_reason_id): array {
		$return_reason_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "return_reason` WHERE `return_reason_id` = '" . (int)$return_reason_id . "'");

		foreach ($query->rows as $result) {
			$return_reason_data[$result['language_id']] = ['name' => $result['name']];
		}

		return $return_reason_data;
	}

	/**
	 * Get Descriptions By Language ID
	 *
	 * @param int $language_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getDescriptionsByLanguageId(int $language_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "return_reason` WHERE `language_id` = '" . (int)$language_id . "'");

		return $query->rows;
	}

	/**
	 * Get Total Return Reasons
	 *
	 * @return int
	 */
	public function getTotalReturnReasons(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "return_reason` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return (int)$query->row['total'];
	}
}
