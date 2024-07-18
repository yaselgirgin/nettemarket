<?php
/**
 * DB Create
 *
 * @param string $db_driver
 * @param string $db_hostname
 * @param string $db_username
 * @param string $db_password
 * @param string $db_database
 * @param string $db_port
 * @param string $db_prefix
 * @param string $db_ssl_key
 * @param string $db_ssl_cert
 * @param string $db_ssl_ca
 *
 * @return bool
 */
function oc_db_create(string $db_driver, string $db_hostname, string $db_username, string $db_password, string $db_database, string $db_port, string $db_prefix, string $db_ssl_key, string $db_ssl_cert, string $db_ssl_ca): bool {
	try {
		// Database
		$db = new \Opencart\System\Library\DB($db_driver, $db_hostname, $db_username, $db_password, $db_database, $db_port, $db_ssl_key, $db_ssl_cert, $db_ssl_ca);
	} catch (\Exception $e) {
		return false;
	}

	// Set up Database structure
	$tables = oc_db_schema();

	foreach ($tables as $table) {
		$table_query = $db->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = '" . $db_database . "' AND TABLE_NAME = '" . $db_prefix . $table['name'] . "'");

		if ($table_query->num_rows) {
			$db->query("DROP TABLE `" . $db_prefix . $table['name'] . "`");
		}

		$sql = "CREATE TABLE `" . $db_prefix . $table['name'] . "` (" . "\n";

		foreach ($table['field'] as $field) {
			$sql .= "  `" . $field['name'] . "` " . $field['type'] . (!empty($field['not_null']) ? " NOT NULL" : "") . (isset($field['default']) ? " DEFAULT '" . $db->escape($field['default']) . "'" : "") . (!empty($field['auto_increment']) ? " AUTO_INCREMENT" : "") . ",\n";
		}

		if (isset($table['primary'])) {
			$primary_data = [];

			foreach ($table['primary'] as $primary) {
				$primary_data[] = "`" . $primary . "`";
			}

			$sql .= "  PRIMARY KEY (" . implode(",", $primary_data) . "),\n";
		}

		if (isset($table['index'])) {
			foreach ($table['index'] as $index) {
				$index_data = [];

				foreach ($index['key'] as $key) {
					$index_data[] = "`" . $key . "`";
				}

				$sql .= "  KEY `" . $index['name'] . "` (" . implode(",", $index_data) . "),\n";
			}
		}

		$sql = rtrim($sql, ",\n") . "\n";
		$sql .= ") ENGINE=" . $table['engine'] . " CHARSET=" . $table['charset'] . " COLLATE=" . $table['collate'] . ";\n";

		$db->query($sql);
	}

	return true;
}

/**
 * DB Schema
 *
 * @return array<int, array<string, mixed>>
 */
function oc_db_schema() {
	$tables = [];

	$tables[] = [
		'name'  => 'address_format',
		'field' => [
			[
				'name'           => 'address_format_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'address_format',
				'type' => 'text'
			]
		],
		'primary' => [
			'address_format_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'api',
		'field' => [
			[
				'name'           => 'api_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'username',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'key',
				'type' => 'text'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'api_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'api_ip',
		'field' => [
			[
				'name'           => 'api_ip_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'api_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			]
		],
		'primary' => [
			'api_ip_id'
		],
		'foreign' => [
			[
				'key'   => 'api_id',
				'table' => 'api',
				'field' => 'api_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'api_session',
		'field' => [
			[
				'name'           => 'api_session_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'api_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'session_id',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'api_session_id'
		],
		'foreign' => [
			[
				'key'   => 'api_id',
				'table' => 'api',
				'field' => 'api_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'cart',
		'field' => [
			[
				'name'           => 'cart_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => 0
			],
			[
				'name'    => 'api_id',
				'type'    => 'int(11)',
				'default' => 0
			],
			[
				'name'    => 'customer_id',
				'type'    => 'int(11)',
				'default' => 0
			],
			[
				'name' => 'session_id',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'product_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'option',
				'type' => 'text'
			],
			[
				'name' => 'quantity',
				'type' => 'int(5)'
			],
			[
				'name' => 'override',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'price',
				'type' => 'decimal(15,4)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'cart_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			],
			[
				'key'   => 'api_id',
				'table' => 'api',
				'field' => 'api_id'
			],
			[
				'key'   => 'customer_id',
				'table' => 'customer',
				'field' => 'customer_id'
			],
			[
				'key'   => 'session_id',
				'table' => 'session',
				'field' => 'session_id'
			],
			[
				'key'   => 'product_id',
				'table' => 'product',
				'field' => 'product_id'
			]
		],
		'index' => [
			[
				'name' => 'cart_id',
				'key'  => [
					'api_id',
					'customer_id',
					'session_id',
					'product_id'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'country',
		'field' => [
			[
				'name'           => 'country_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'iso_code_2',
				'type' => 'varchar(2)'
			],
			[
				'name' => 'iso_code_3',
				'type' => 'varchar(3)'
			],
			[
				'name' => 'address_format_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'postcode_required',
				'type' => 'tinyint(1)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '1'
			]
		],
		'primary' => [
			'country_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'cron',
		'field' => [
			[
				'name'           => 'cron_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'cycle',
				'type' => 'varchar(12)'
			],
			[
				'name' => 'action',
				'type' => 'text'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'cron_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'currency',
		'field' => [
			[
				'name'           => 'currency_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(3)'
			],
			[
				'name' => 'symbol_left',
				'type' => 'varchar(12)'
			],
			[
				'name' => 'symbol_right',
				'type' => 'varchar(12)'
			],
			[
				'name' => 'decimal_place',
				'type' => 'int(1)'
			],
			[
				'name' => 'value',
				'type' => 'double(15,8)'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_modified',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'currency_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'event',
		'field' => [
			[
				'name'           => 'event_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'trigger',
				'type' => 'text'
			],
			[
				'name' => 'action',
				'type' => 'text'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '0'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '1'
			]
		],
		'primary' => [
			'event_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'extension',
		'field' => [
			[
				'name'           => 'extension_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'extension',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'type',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			]
		],
		'primary' => [
			'extension_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'extension_install',
		'field' => [
			[
				'name'           => 'extension_install_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'extension_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'extension_download_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'code',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'version',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'author',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'link',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'extension_install_id'
		],
		'foreign' => [
			[
				'key'   => 'extension_id',
				'table' => 'extension',
				'field' => 'extension_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'extension_path',
		'field' => [
			[
				'name'           => 'extension_path_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'extension_install_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'path',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'extension_path_id'
		],
		'foreign' => [
			[
				'key'   => 'extension_install_id',
				'table' => 'extension_install',
				'field' => 'extension_install_id'
			]
		],
		'index' => [
			[
				'name' => 'path',
				'key'  => [
					'path'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'geo_zone',
		'field' => [
			[
				'name'           => 'geo_zone_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'description',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'geo_zone_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'language',
		'field' => [
			[
				'name'           => 'language_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(5)'
			],
			[
				'name' => 'locale',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'extension',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'sort_order',
				'type'    => 'int(3)',
				'default' => '0'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			]
		],
		'primary' => [
			'language_id'
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'length_class',
		'field' => [
			[
				'name'           => 'length_class_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'value',
				'type' => 'decimal(15,8)'
			]
		],
		'primary' => [
			'length_class_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'length_class_description',
		'field' => [
			[
				'name' => 'length_class_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'unit',
				'type' => 'varchar(4)'
			]
		],
		'primary' => [
			'length_class_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'length_class_id',
				'table' => 'length_class',
				'field' => 'length_class_id'
			],
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'location',
		'field' => [
			[
				'name'           => 'location_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'address',
				'type' => 'text'
			],
			[
				'name' => 'telephone',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'geocode',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'image',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'open',
				'type' => 'text'
			]
		],
		'primary' => [
			'location_id'
		],
		'index' => [
			[
				'name' => 'name',
				'key'  => [
					'name'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'modification',
		'field' => [
			[
				'name'           => 'modification_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'     => 'extension_install_id',
				'type'     => 'int(11)',
				'not_null' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'author',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'version',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'link',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'xml',
				'type' => 'mediumtext'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'modification_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'module',
		'field' => [
			[
				'name'           => 'module_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'setting',
				'type' => 'text'
			]
		],
		'primary' => [
			'module_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'notification',
		'field' => [
			[
				'name'           => 'notification_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'title',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'text',
				'type' => 'text'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(11)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'notification_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'process_status',
		'field' => [
			[
				'name'           => 'process_status_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			]
		],
		'primary' => [
			'process_status_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'order_total',
		'field' => [
			[
				'name'           => 'order_total_id',
				'type'           => 'int(10)',
				'auto_increment' => true
			],
			[
				'name' => 'order_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'extension',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(255)'
			],
			[
				'name'    => 'value',
				'type'    => 'decimal(15,4)',
				'default' => '0.0000'
			],
			[
				'name' => 'sort_order',
				'type' => 'int(3)'
			]
		],
		'primary' => [
			'order_total_id'
		],
		'foreign' => [
			[
				'key'   => 'order_id',
				'table' => 'order',
				'field' => 'order_id'
			]
		],
		'index' => [
			[
				'name' => 'order_id',
				'key'  => [
					'order_id'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'startup',
		'field' => [
			[
				'name'           => 'startup_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'description',
				'type' => 'text'
			],
			[
				'name' => 'code',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'action',
				'type' => 'text'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'sort_order',
				'type' => 'int(3)'
			]
		],
		'primary' => [
			'startup_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'session',
		'field' => [
			[
				'name' => 'session_id',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'data',
				'type' => 'text'
			],
			[
				'name' => 'expire',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'session_id'
		],
		'index' => [
			[
				'name' => 'expire',
				'key'  => [
					'expire'
				]
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'setting',
		'field' => [
			[
				'name'           => 'setting_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'store_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'code',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'key',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'value',
				'type' => 'text'
			],
			[
				'name'    => 'serialized',
				'type'    => 'tinyint(1)',
				'default' => 0
			]
		],
		'primary' => [
			'setting_id'
		],
		'foreign' => [
			[
				'key'   => 'store_id',
				'table' => 'store',
				'field' => 'store_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'store',
		'field' => [
			[
				'name'           => 'store_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'url',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'store_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'tax_class',
		'field' => [
			[
				'name'           => 'tax_class_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'description',
				'type' => 'varchar(255)'
			]
		],
		'primary' => [
			'tax_class_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'tax_rate',
		'field' => [
			[
				'name'           => 'tax_rate_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'geo_zone_id',
				'type'    => 'int(11)',
				'default' => '0'
			],
			[
				'name' => 'name',
				'type' => 'varchar(32)'
			],
			[
				'name'    => 'rate',
				'type'    => 'decimal(15,4)',
				'default' => '0.0000'
			],
			[
				'name' => 'type',
				'type' => 'char(1)'
			]
		],
		'primary' => [
			'tax_rate_id'
		],
		'foreign' => [
			[
				'key'   => 'geo_zone_id',
				'table' => 'geo_zone',
				'field' => 'geo_zone_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'tax_rule',
		'field' => [
			[
				'name'           => 'tax_rule_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'tax_class_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'tax_rate_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'priority',
				'type'    => 'int(5)',
				'default' => '1'
			]
		],
		'primary' => [
			'tax_rule_id'
		],
		'foreign' => [
			[
				'key'   => 'tax_class_id',
				'table' => 'tax_class',
				'field' => 'tax_class_id'
			],
			[
				'key'   => 'tax_rate_id',
				'table' => 'tax_rate',
				'field' => 'tax_rate_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'upload',
		'field' => [
			[
				'name'           => 'upload_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'filename',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'upload_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'user',
		'field' => [
			[
				'name'           => 'user_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'user_group_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'username',
				'type' => 'varchar(20)'
			],
			[
				'name' => 'password',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'firstname',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'lastname',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'email',
				'type' => 'varchar(96)'
			],
			[
				'name'    => 'image',
				'type'    => 'varchar(255)',
				'default' => ''
			],
			[
				'name'    => 'code',
				'type'    => 'varchar(40)',
				'default' => ''
			],
			[
				'name'    => 'ip',
				'type'    => 'varchar(40)',
				'default' => ''
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_id'
		],
		'foreign' => [
			[
				'key'   => 'user_group_id',
				'table' => 'user_group',
				'field' => 'user_group_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'user_authorize',
		'field' => [
			[
				'name'           => 'user_authorize_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'user_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'token',
				'type' => 'varchar(96)'
			],
			[
				'name' => 'total',
				'type' => 'int(1)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'user_agent',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'status',
				'type' => 'tinyint(1)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_authorize_id'
		],
		'foreign' => [
			[
				'key'   => 'user_id',
				'table' => 'user',
				'field' => 'user_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'user_group',
		'field' => [
			[
				'name'           => 'user_group_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'name',
				'type' => 'varchar(64)'
			],
			[
				'name' => 'permission',
				'type' => 'text'
			]
		],
		'primary' => [
			'user_group_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'user_login',
		'field' => [
			[
				'name'           => 'user_login_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'user_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'ip',
				'type' => 'varchar(40)'
			],
			[
				'name' => 'user_agent',
				'type' => 'varchar(255)'
			],
			[
				'name' => 'date_added',
				'type' => 'datetime'
			]
		],
		'primary' => [
			'user_login_id'
		],
		'foreign' => [
			[
				'key'   => 'user_id',
				'table' => 'user',
				'field' => 'user_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'weight_class',
		'field' => [
			[
				'name'           => 'weight_class_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name'    => 'value',
				'type'    => 'decimal(15,8)',
				'default' => '0.00000000'
			]
		],
		'primary' => [
			'weight_class_id'
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'weight_class_description',
		'field' => [
			[
				'name' => 'weight_class_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'language_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'title',
				'type' => 'varchar(32)'
			],
			[
				'name' => 'unit',
				'type' => 'varchar(4)'
			]
		],
		'primary' => [
			'weight_class_id',
			'language_id'
		],
		'foreign' => [
			[
				'key'   => 'language_id',
				'table' => 'language',
				'field' => 'language_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'zone',
		'field' => [
			[
				'name'           => 'zone_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'country_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'name',
				'type' => 'varchar(128)'
			],
			[
				'name' => 'code',
				'type' => 'varchar(32)'
			],
			[
				'name'    => 'status',
				'type'    => 'tinyint(1)',
				'default' => '1'
			]
		],
		'primary' => [
			'zone_id'
		],
		'foreign' => [
			[
				'key'   => 'country_id',
				'table' => 'country',
				'field' => 'country_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	$tables[] = [
		'name'  => 'zone_to_geo_zone',
		'field' => [
			[
				'name'           => 'zone_to_geo_zone_id',
				'type'           => 'int(11)',
				'auto_increment' => true
			],
			[
				'name' => 'geo_zone_id',
				'type' => 'int(11)'
			],
			[
				'name' => 'country_id',
				'type' => 'int(11)'
			],
			[
				'name'    => 'zone_id',
				'type'    => 'int(11)',
				'default' => '0'
			]
		],
		'primary' => [
			'zone_to_geo_zone_id'
		],
		'foreign' => [
			[
				'key'   => 'geo_zone_id',
				'table' => 'geo_zone',
				'field' => 'geo_zone_id'
			],
			[
				'key'   => 'country_id',
				'table' => 'country',
				'field' => 'country_id'
			],
			[
				'key'   => 'zone_id',
				'table' => 'zone',
				'field' => 'zone_id'
			]
		],
		'engine'  => 'InnoDB',
		'charset' => 'utf8mb4',
		'collate' => 'utf8mb4_general_ci'
	];

	return $tables;
}
