<?php
/**
 * this file pulls settings directly from the DB
 */
if (!defined('KAHUKPATH')) {
	die();
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

		if ($value == "true") {
			$thenewval = true;
		} elseif ($value == "false") {
			$thenewval = false;
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
 * Get the site logo from Admin config
 * 
 * @return int
 */
function kahuk_site_logo() {
	global $hooks;

	$site_logo = kahuk_get_config("_site_logo");

	return $hooks->apply_filters("site_logo", $site_logo);
}

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

/**
 * Check Admin config: _maintenance_mode
 */
function is_maintenance_mode() {
    global $hooks;

    $maintenance_mode = kahuk_get_config("_maintenance_mode");

    return $hooks->apply_filters("is_maintenance", ($maintenance_mode == "true"));
}
