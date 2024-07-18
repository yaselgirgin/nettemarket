<?php
/**
 * @package        OpenCart
 *
 * @author         Daniel Kerr
 * @copyright      Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 *
 * @see           https://www.opencart.com
 */
namespace Opencart\System\Library;
/**
 * Class Request
 */
class Request {
	/**
	 * @var array<string, mixed>
	 */
	public array $get = [];
	/**
	 * @var array<string, mixed>
	 */
	public array $post = [];
	/**
	 * @var array<string, mixed>
	 */
	public array $cookie = [];
	/**
	 * @var array<string, mixed>
	 */
	public array $files = [];
	/**
	 * @var array<string, mixed>
	 */
	public array $server = [];

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->get = $this->clean($_GET);
		$this->post = $this->clean($_POST);
		$this->cookie = $this->clean($_COOKIE);
		$this->files = $this->clean($_FILES);
		$this->server = $this->clean($_SERVER);
	}

	/**
	 * Clean
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	public function clean($data) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$data[$this->clean($key)] = $this->clean($value);
			}
		} else {
			$data = trim(htmlspecialchars($data, ENT_COMPAT, 'UTF-8'));
		}

		return $data;
	}
}
