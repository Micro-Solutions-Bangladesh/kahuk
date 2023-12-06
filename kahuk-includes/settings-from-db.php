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
        $this->get_db_config();
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
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
		$sql = 'SELECT var_name, var_value, var_method, var_enclosein FROM ' . TABLE_PREFIX . 'config';
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
function kahuk_get_config($var_name, $default_val = '') {
	global $globalDBConfs;

	$option = isset($globalDBConfs->configs[$var_name]) ? $globalDBConfs->configs[$var_name] : [];

	if ($option) {
		return $option['var_value'];
	} else {
		return $default_val;
	}
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
			$sqlInsert .= ", var_enclosein  = ''";

			$id = $db->query_insert($sqlInsert);

			if (!$id) {
				kahuk_log_unexpected( "BOTH Query FAILED:\n---------\n{$sql}\n---------\n{$sqlInsert}");
        		return false;
			}

			return true;
		}        
    }
}

/**
 * No Idea, what is the use of these conditional block
 */
if (caching == 1) {
	$db->cache_dir = KAHUKPATH . 'cache';
	$db->use_disk_cache = true;
	$db->cache_queries = true;
}

/**
 * 
 */
foreach ($globalDBConfs->configs as $row) {
	$value = $row['var_value'];

	if ($row['var_method'] == "option") {
		// these configs are available using the kahuk_get_config() function
	} elseif ($row['var_method'] == "normal") {
		/* the var_method normal are only assigned a smarty varibale. SEE NOTE AT THE BOTTOM OF THE FILE. ACCESSIBLE FROM EVERY PAGE / FILE.*/
		$kahuk_vars[$row['var_name']] = $value;

		if ($main_smarty) {
			$main_smarty->assign(str_replace("$", "", $row['var_name']), $value);
		}
	} elseif ($row['var_method'] == "define") {
		/* all the var_method define are assigned a smarty variable and defined as well. ACCESSIBLE FROM EVERY PAGE / FILE.*/
		if ($row['var_name'] == 'TABLE_PREFIX') {
			continue;
		}

		$thenewval = $value;

		if ($row['var_enclosein'] == "") {
			if ($value == "true") {
				$thenewval = true;
			} elseif ($value == "false") {
				$thenewval = false;
			} else {
				$thenewval = $value;
			}
		} else {
			$thenewval = $value;
		}

		define($row['var_name'], $thenewval);

		if ($main_smarty) {
			$main_smarty->assign($row['var_name'], $thenewval);
		}
	} else {
		if ($main_smarty) {
			$main_smarty->assign($row['var_name'], $value);
		}
	}
}

$db->cache_queries = false;


/**
 * Set a few global variable from Admin Options
 */
global $thetemp, $page_size;

//
$thetemp = kahuk_get_config('the_template', 'aowal');

/**
 * Determine Item limit From Admin config
 * Must have a number to display items, we hard coded it 20 when nothing found
 * 
 * @return int
 */
function kahuk_page_size() {
	global $hooks;

	$page_size = kahuk_get_config('page_size', 20);

	return $hooks->apply_filters('kahuk_page_size', $page_size);
}

$page_size = kahuk_page_size();
