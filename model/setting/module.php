<?php
namespace Opencart\Admin\Model\Setting;
/**
 * Class Module
 *
 * @package Opencart\Admin\Model\Setting
 */
class Module extends \Opencart\System\Engine\Model {
	/**
	 * Add Module
	 *
	 * @param string               $code
	 * @param array<string, mixed> $data
	 *
	 * @return int
	 */
	public function addModule(string $code, array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");

		$module_id = $this->db->getLastId();

		return (int)$module_id;
	}

	/**
	 * Edit Module
	 *
	 * @param int                  $module_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editModule(int $module_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "' WHERE `module_id` = '" . (int)$module_id . "'");
	}

	/**
	 * Delete Module
	 *
	 * @param int $module_id
	 *
	 * @return void
	 */
	public function deleteModule(int $module_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");
	}

	/**
	 * Delete Modules By Code
	 *
	 * @param string $code
	 *
	 * @return void
	 */
	public function deleteModulesByCode(string $code): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "'");
	}

	/**
	 * Get Module
	 *
	 * @param int $module_id
	 *
	 * @return array<mixed>
	 */
	public function getModule(int $module_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");

		if ($query->row) {
			return json_decode($query->row['setting'], true);
		} else {
			return [];
		}
	}

	/**
	 * Get Modules
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getModules(): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` ORDER BY `code`");

		return $query->rows;
	}

	/**
	 * Get Modules By Code
	 *
	 * @param string $code
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getModulesByCode(string $code): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `name`");

		return $query->rows;
	}
}
