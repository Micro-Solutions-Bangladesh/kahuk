<?php
/**
 * this file pulls settings directly from the DB
 */
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * 
 */
class KahukDBOptions
{
	private $db;

    public $configs = [];

    /**
     * Class construcotr
     */
    private function __construct() {
		global $db;

		$this->db = $db;

        if (defined("KAHUK_INSTALLING") && KAHUK_INSTALLING) {
            // todo if require in the setup process.
        } else {
            $this->get_db_config();
        }
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init() {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Get configs from configs table
     * 
     * @since 5.0.7
     * 
     * @return void
     */
    private function get_db_config() {
		$sql = 'SELECT var_name, var_value, var_method FROM ' . TABLE_PREFIX . 'config';
        $rs = $this->db->get_results($sql, ARRAY_A);

		if (!$rs) {
			die('Error. The ' . TABLE_PREFIX . 'config table is empty or does not exist');
		}

		foreach ($rs as $row) {
			$this->configs[$row['var_name']] = $row;
		}
    }
}


/**
 * Initializes KahukDBOptions Class
 *
 * @return KahukDBOptions
 */
function kahuk_db_options_init() {
    return KahukDBOptions::init();
}

// Set the global variables to URL
$globalDBConfs = kahuk_db_options_init();

// echo "<pre class=\"debug\">";
// print_r($globalDBConfs->configs);
// echo "</pre>";


/**
 * Get the provided config value from the static $globalDBConfs object and return
 * 
 * 
 */
function kahuk_get_config($var_name, $default_val = "", $type_cast = "") {
	global $globalDBConfs;

	$option = isset($globalDBConfs->configs[$var_name]) ? $globalDBConfs->configs[$var_name] : [];

	$output = $default_val;

	if ($option) {
		$output = $option["var_value"];
	}

	if ($type_cast) {
		if ($type_cast == "boolean") {
			$output = (($output == "true") || ($output == "yes") || ($output == "1"));
		}
	}

	return $output;
}

/**
 * Update db config option
 * 
 * @return boolean
 */
function kahuk_update_config($var_name, $var_val) {
	global $db;

	$sql = "UPDATE `" . TABLE_PREFIX . "config` SET";
	$sql .= " var_value  = '" . $db->escape($var_val) . "'";
	$sql .= " WHERE var_name = '" . sanitize_text_field($var_name) . "'";

	// Execute Query and return
	$rs = $db->query( $sql );

	if ( 1 == $rs ) {
        return true;
    } else {
		// Take a look if the `var_name` does not exist, create it.
		$sqlSelect  = "SELECT count(var_name) FROM " . TABLE_PREFIX . "config";
		$sqlSelect .= " WHERE var_name = '" . $db->escape($var_name) . "'";

		$rs = $db->get_var($sqlSelect);

		if (!$rs) {
			$sqlInsert  = "INSERT INTO `" . TABLE_PREFIX . "config` SET";
			$sqlInsert .= " var_name  = '" . $db->escape(sanitize_text_field($var_name)) . "'";
			$sqlInsert .= ", var_value  = '" . $db->escape(sanitize_text_field($var_val)) . "'";
			$sqlInsert .= ", var_page  = 'Plugins'";
			$sqlInsert .= ", var_defaultvalue  = ''";
			$sqlInsert .= ", var_optiontext  = 'text'";
			$sqlInsert .= ", var_title  = 'Plugin Settings'";
			$sqlInsert .= ", var_desc   = 'Plugin desc'";
			$sqlInsert .= ", var_method  = 'option'";

			$id = $db->query_insert($sqlInsert);

			if (!$id) {
				kahuk_log_unexpected( "BOTH Query FAILED:\n---------\n{$sql}\n---------\n{$sqlInsert}");
        		return false;
			}

			return true;
		}        
    }
}
