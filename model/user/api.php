<?php
namespace Opencart\Admin\Model\User;
/**
 * Class Api
 *
 * @package Opencart\Admin\Model\User
 */
class Api extends \Opencart\System\Engine\Model {
	/**
	 * Add Api
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return int
	 */
	public function addApi(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api` SET `username` = '" . $this->db->escape((string)$data['username']) . "', `key` = '" . $this->db->escape((string)$data['key']) . "', `status` = '" . (bool)($data['status'] ?? 0) . "', `date_added` = NOW(), `date_modified` = NOW()");

		$api_id = $this->db->getLastId();

		if (isset($data['api_ip'])) {
			foreach ($data['api_ip'] as $ip) {
				if ($ip) {
					$this->addIp($api_id, $ip);
				}
			}
		}

		return $api_id;
	}

	/**
	 * Edit Api
	 *
	 * @param int                  $api_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editApi(int $api_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "api` SET `username` = '" . $this->db->escape((string)$data['username']) . "', `key` = '" . $this->db->escape((string)$data['key']) . "', `status` = '" . (bool)($data['status'] ?? 0) . "', `date_modified` = NOW() WHERE `api_id` = '" . (int)$api_id . "'");

		$this->deleteIps($api_id);

		if (isset($data['api_ip'])) {
			foreach ($data['api_ip'] as $ip) {
				if ($ip) {
					$this->addIp($api_id, $ip);
				}
			}
		}
	}

	/**
	 * Delete Api
	 *
	 * @param int $api_id
	 *
	 * @return void
	 */
	public function deleteApi(int $api_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api` WHERE `api_id` = '" . (int)$api_id . "'");

		$this->deleteIps($api_id);
	}

	/**
	 * Get Api
	 *
	 * @param int $api_id
	 *
	 * @return array<string, mixed>
	 */
	public function getApi(int $api_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `api_id` = '" . (int)$api_id . "'");

		return $query->row;
	}

	/**
	 * Get Apis
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getApis(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "api`";

		$sort_data = [
			'username',
			'status',
			'date_added',
			'date_modified'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `username`";
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Total Apis
	 *
	 * @return int
	 */
	public function getTotalApis(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "api`");

		return (int)$query->row['total'];
	}

	/**
	 * Add Ip
	 *
	 * @param int    $api_id
	 * @param string $ip
	 *
	 * @return void
	 */
	public function addIp(int $api_id, string $ip): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_ip` SET `api_id` = '" . (int)$api_id . "', `ip` = '" . $this->db->escape($ip) . "'");
	}

	/**
	 * Delete Ips
	 *
	 * @param int $api_id
	 *
	 * @return void
	 */
	public function deleteIps(int $api_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_ip` WHERE `api_id` = '" . (int)$api_id . "'");
	}

	/**
	 * Get Ips
	 *
	 * @param int $api_id
	 *
	 * @return array<int, string>
	 */
	public function getIps(int $api_id): array {
		$ip_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_ip` WHERE `api_id` = '" . (int)$api_id . "'");

		foreach ($query->rows as $result) {
			$ip_data[] = $result['ip'];
		}

		return $ip_data;
	}

	/**
	 * Add Session
	 *
	 * @param int    $api_id
	 * @param string $session_id
	 * @param string $ip
	 *
	 * @return int
	 */
	public function addSession(int $api_id, string $session_id, string $ip): int {
		$api_ip_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_ip` WHERE `ip` = '" . $this->db->escape($ip) . "'");

		if (!$api_ip_query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "api_ip` SET `api_id` = '" . (int)$api_id . "', `ip` = '" . $this->db->escape($ip) . "'");
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_session` SET `api_id` = '" . (int)$api_id . "', `session_id` = '" . $this->db->escape($session_id) . "', `ip` = '" . $this->db->escape($ip) . "', `date_added` = NOW(), `date_modified` = NOW()");

		return $this->db->getLastId();
	}

	/**
	 * Get Sessions
	 *
	 * @param int $api_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getSessions(int $api_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_session` WHERE `api_id` = '" . (int)$api_id . "'");

		return $query->rows;
	}

	/**
	 * Delete Session
	 *
	 * @param int $api_session_id
	 *
	 * @return void
	 */
	public function deleteSession(int $api_session_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE `api_session_id` = '" . (int)$api_session_id . "'");
	}

	/**
	 * Delete Session By Session ID
	 *
	 * @param string $session_id
	 *
	 * @return void
	 */
	public function deleteSessionsBySessionId(string $session_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE `session_id` = '" . $this->db->escape($session_id) . "'");
	}
}
