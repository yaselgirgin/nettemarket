<?php
/**
 * @package   OpenCart
 *
 * @author    Daniel Kerr
 * @copyright Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license   https://opensource.org/licenses/GPL-3.0
 * @author    Daniel Kerr
 *
 * @see       https://www.opencart.com
 */
namespace Opencart\System\Library;
/**
 * Class URL
 */
class Url {
	/**
	 * @var string
	 */
	private string $url;
	/**
	 * @var array<int, object>
	 */
	private array $rewrite = [];

	/**
	 * Constructor
	 *
	 * @param string $url
	 */
	public function __construct(string $url) {
		$this->url = $url;
	}

	/**
	 * Add Rewrite
	 *
	 * Add a rewrite method to the URL system
	 *
	 * @param \Opencart\System\Engine\Controller $rewrite
	 *
	 * @return void
	 */
	public function addRewrite(object $rewrite): void {
		if (is_callable([$rewrite, 'rewrite'])) {
			$this->rewrite[] = $rewrite;
		}
	}

	/**
	 * Link
	 *
	 * Generates a URL
	 *
	 * @param string $route
	 * @param mixed  $args
	 * @param bool   $js
	 *
	 * @return string
	 */
	public function link(string $route, $args = '', bool $js = false): string {
		$url = $this->url . 'index.php?route=' . $route;

		if ($args) {
			if (is_array($args)) {
				$url .= '&' . http_build_query($args);
			} else {
				$url .= '&' . trim($args, '&');
			}
		}

		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}

		if (!$js) {
			return str_replace('&', '&amp;', $url);
		} else {
			return $url;
		}
	}
}
