<?php
require_once dirname(__FILE__) . "/kahuk-loader.php";

require_once KAHUKPATH . "kahuk-configs.php";
require_once KAHUKPATH_INC . "kahuk-includes.php";

/**
 * Check maintenance mode
 */
$maintenance_mode = is_maintenance_mode();

if ($maintenance_mode) {
	if (!kahuk_session_user_level("admin")) {
		include_once(KAHUKPATH_PAGES . "maintenance.php");
		exit;
	}
}

// Temporary fix for lowercase category // Deleteable after 2022-12-31
if (isset($_REQUEST["category"])) {
	$_REQUEST["category"] = strtolower($_REQUEST["category"]);
}

/**
 * Sanitize GET variables used in templates
 */
if ($main_smarty) {
	$get = array();

	foreach ($_GET as $k => $v) {
		$get[$k] = stripslashes(htmlentities(strip_tags($v), ENT_QUOTES, "UTF-8"));
	}

	if (isset($get["return"])) {
		$get["return"] = addslashes($get["return"]);
	}

	$main_smarty->assign("get", $get);
}

// CSFR/XSFR protection (function check_referrer in /libs/html1.php)
if (!isset($_SESSION)) {
	@session_start();
}

if (!empty($_SESSION["xsfr"])) {
	$xsfr_first_page = 0;
} else {
	$xsfr_first_page = 1;
	$_SESSION["xsfr"] = 1;
}

//
function sanit($var)
{
	return addslashes(htmlentities(strip_tags($var), ENT_QUOTES, "UTF-8"));
}


/**
 * Check if we are loading Admin
 */
if (kahuk_is_admin()) {
    // Initialize Admin Menu
    kahuk_intial_admin_menu_items();

	$kahuk_admin_menu = kahuk_admin_menu_markup();
	$main_smarty->assign("kahuk_admin_menu", $kahuk_admin_menu);
}


/**
 * Make sure the template folder exist
 */
$the_template_path = KAHUKPATH_TPL . $thetemp . "/kahuk.tpl";

if (!is_readable($the_template_path)) {
	echo "<pre>Template {$thetemp} not found!</pre>";
	exit;
}

define("THE_TEMPLATE", $thetemp);

// Don't touch behind this
//Redwine: this means if there's a settings local.php file, read and include everything in that file now.
$local_configuration = $_SERVER["SERVER_NAME"] . "-local.php";
@include($local_configuration);


// start summarization and caching of mysql data
if (caching == 1) {
	$db->cache_dir = KAHUKPATH . "cache";
	$db->use_disk_cache = true;
	$db->cache_queries = true;
}

// if this query changes, be sure to change the 'clear the cache' code in admin_categories.php
$the_cats = loadCategoriesForCache();
$cached_categories = $the_cats;
$db->cache_queries = false;

// a simple cache type system for the users table
// used in the read() function of /libs/user.php
$cached_users = array();

// a simple cache type system for the totals table
// functions related to this are in /libs/html1.php
$cached_totals = array();
$cached_votes = array();
$cached_links = array();
$cached_comments = array();
$cached_saved_links = array();

// end summarization and caching of mysql data

ob_start();



/************************************
 * Redwine: this code is obsolete??? include_login does not exist in other than the below 2 lines.
 * However, we need to include the /libs/login.php file, otherwise, no user can login because they 
 * won't be authenticated and errors will be generated.
 * So, removing the if statement, because either way the file will be included???
 ******************* TO INVESTIGATE ***********************
 ************************************/

/*************************************
 * if no matter what, the language file does not exist, the we throw an error and kill the script.
 * Otherwise, we define the language as KAHUK_LANG.
 *************************************/
if (!file_exists(dirname(__FILE__) . "/languages/lang_" . KAHUK_LANG . ".conf")) {
	$output .= '<div id="wrapper" style="width:800px;padding:10px;background-color:#FFC6C6;border:1px solid #000;color:#000;display:block;margin-left:auto;margin-right:auto;">';
	$output .= 'The language file /languages/lang_' . KAHUK_LANG . '.conf does not exist. Either this file is missing or the server does not have permission to read it. Make sure that you renamed the file /languages/lang_' . KAHUK_LANG . '.conf.default to /languages/lang_' . KAHUK_LANG . '.conf.</div>';

	die($output);
}


include KAHUKPATH_LIBS . "utf8/utf8.php";

/** */
function loadCategoriesForCache($clear_cache = false)
{
	global $db;
	$sql = "select * from " . table_categories . ";";

	//Redwine: if clear_cache i true, then we call un_cache function in /libs/ez_sql_core.php
	if ($clear_cache) {
		$db->un_cache($sql);
	}

	return $db->get_results($sql);
}

include_once(KAHUKPATH_INC . "kahuk-variables.php");

