<?php
require_once dirname( __FILE__ ) . '/kahuk-loader.php';

require_once KAHUKPATH . 'kahuk-configs.php';

//
require_once KAHUK_LIBS_DIR . 'class-kahuk-error.php';

//
require_once KAHUK_LIBS_DIR . 'db.php';
require_once KAHUK_LIBS_DIR . 'utils.php';

//
require_once KAHUK_LIBS_DIR . 'functions.php';

require_once( KAHUK_LIBS_DIR . 'check_behind_proxy.php' );


// Load early Kahuk files.
require_once KAHUK_LIBS_DIR . 'wp/formatting.php';

require_once KAHUK_LIBS_DIR . 'wp/functions.php';


require_once KAHUK_LIBS_DIR . 'functions-table-user.php';
require_once KAHUK_LIBS_DIR . 'functions-table-link.php';
require_once KAHUK_LIBS_DIR . 'functions-table-vote.php';

require_once KAHUK_LIBS_DIR . 'html1.php';


// Load the L10n library.
require_once KAHUK_LIBS_DIR . 'wp/l10n.php';


// Load most of Kahuk library.
require_once KAHUK_LIBS_DIR . 'wp/kses.php';



// Lately included functions and class files
require_once KAHUK_LIBS_DIR . 'user.php';

require_once KAHUK_LIBS_DIR . 'kahuk-categories.php';






/**
 * Set PHP error reporting based on Kahuk debug settings.
 * 
 * @since 5.0.0
 * @access private
 */
function kahuk_debug_mode() {
	if ( KAHUK_DEBUG ) {
		error_reporting( E_ALL );

		if ( KAHUK_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 1 );
		} elseif ( null !== KAHUK_DEBUG_DISPLAY ) {
			ini_set( 'display_errors', 0 );
		}
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}

	if ( ( defined( 'KAHUK_INSTALLING' ) && KAHUK_INSTALLING ) ) {
		ini_set( 'display_errors', 0 );
	}
}

kahuk_debug_mode();





//Redwine: function to remove the effect of magic quotes if it is set to ON.
function stripslashes_deep($value) {
	$value = is_array($value) ?
	array_map('stripslashes_deep', $value) :
	stripslashes($value);
	return $value;
}

//Redwine: if magic quotes is set to ON, the below code will stripslashes from $_POST, $_GET, $_REQUEST, $_COOKIE  and any string.
if (get_magic_quotes_gpc()) {
	$_POST		= stripslashes_deep($_POST);
	$_GET		= stripslashes_deep($_GET);
	$_COOKIE	= stripslashes_deep($_COOKIE);
	$_REQUEST	= stripslashes_deep($_REQUEST);
}

// Sanitize GET variables used in templates
if ($main_smarty) {
    $get = array();
    foreach ($_GET as $k => $v) {
		$get[$k] = stripslashes(htmlentities(strip_tags($v),ENT_QUOTES,'UTF-8'));
	}
	
	if (isset($get['return'])) { 
		$get['return'] = addslashes($get['return']);
	}
    $main_smarty->assign('get',$get);
}

// CSFR/XSFR protection (function check_referrer in /libs/html1.php)
if (!isset($_SESSION)) {
	@session_start();
}
if (!empty($_SESSION['xsfr'])) {
    $xsfr_first_page = 0;
} else {
    $xsfr_first_page = 1;
    $_SESSION['xsfr'] = 1;
}

// DO NOT EDIT THIS FILE. USE THE ADMIN PANEL (logged in as "admin") TO MAKE CHANGES
// IF YOU MUST MAKE CHANGES MANUALLY, EDIT SETTINGS.PHP

include_once KAHUK_LIBS_DIR . 'pre_install_check.php';
include_once 'settings.php';

function sanit($var)
{
	return addslashes(htmlentities(strip_tags($var),ENT_QUOTES,'UTF-8'));
}



//Redwine: URLMethod is set in /libs/settings_from_db.php
define('urlmethod', $URLMethod);

//Redwine: We want to unset the template cookie if the Allow_User_Change_Templates is set to false, otherwise, the cookie will remain.
if (!Allow_User_Change_Templates) {
    unset($_COOKIE['template']);
}

//Redwine: If Admins allow users to change the default template from their profile settings, this below statement will be executed if the config value of Allow_User_Change_Templates is set to true, only then the template name is set in a cookie.
if (isset($_COOKIE['template'])) {
	$thetemp = str_replace('..','',sanit($_COOKIE['template']));
}

//Redwine: First check if $thetemp (value from the cookie, if user can change templates, or the config table) exists.
$file = dirname(__FILE__) . '/templates/' . $thetemp . "/kahuk.tpl";
unset($errors);
if (!file_exists($file)) {
	$errors[]='You may have typed the template name wrong or "'. $thetemp . '" does not exist.';
}

//Redwine: If an error was genrated from the above statement, we check using the default Kahuk template, bootstrap.
$thetemp = "bootstrap";
$file = dirname(__FILE__) . '/templates/' . $thetemp . "/kahuk.tpl";
if (!file_exists($file)) {
    $errors[] = 'The default template "bootstrap" does not exist anymore. Please fix this by re-uploading the bootstrap template!';
}   

//Redwine: if errors, then we output them.    
if (isset($errors)) {
    $output .= '<div id="wrapper" style="width:800px;padding:10px;background-color:#FFC6C6;border:1px solid #000;color:#000;display:block;margin-left:auto;margin-right:auto;">';
	foreach ($errors as $error) {
		$output.= "<p><b>Error:</b> $error</p>";
	}
    $output .= '</div>';
    echo $output;
    
    //Redwine: If the user is logged in and is Admin and is on the page specified below, then they can still have access to the Admin section to check the template name. Otherwise, all other user levels will not be able.
	if (strpos($_SERVER['SCRIPT_NAME'], "admin_config.php") == 0) {
 		die();
	}

}
//Redwine: the defined The_Template is then assigned as a variable The_Template and assigned to Smarty as well, in /libs/smartyvariables.php 
define('The_Template', $thetemp);

if (Enable_Extra_Fields) {
	include KAHUK_LIBS_DIR.'extra_fields.php';
}

// Don't touch behind this
//Redwine: this means if there's a settings local.php file, read and include everything in that file now.
$local_configuration = $_SERVER['SERVER_NAME'].'-local.php';
@include($local_configuration);

include_once KAHUK_LIBS_DIR.'define_tables.php';

/*Redwine: a preventive measure to make sure that the smtp_fake_email, which allows printing the email on the page during SMTP testing, is turned off if the base URL does not contain localhost.*/
if (strpos($my_base_url, 'localhost') === false && smtp_fake_email == 1) {
    global $db;
    $db->query("update ". table_config ." set `var_value` = 'false' where `var_name` = 'smtp_fake_email';");
}

// start summarization and caching of mysql data

/***************************
    * added to replace 55 redundant queries with 1
    * used with the following functions in /lib/link.php
    *	function category_name() {
    *	function category_safe_name() {
    * cache the data if caching is enabled
***************************/

if (caching == 1) {
    $db->cache_dir = KAHUKPATH.'cache';
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

if (!isset($include_login) || $include_login !== false) {
	// if $include_login is set to false (like in jspath.php and xmlhttp.php), then we don't
	// include login, because login will run a query right away to check user credentials
	// and these two files don't require that.
	include_once KAHUK_LIBS_DIR.'login.php';
}

// Defining the settings.php language as it's own variable
// $settings_language = $language;

/*************************************
    * If Admin allowed users to use a different language file, other than the site's default,
    * and for a reason, the user language file does not exist anymore,
    * then we attempt to use the site's default language that is set in /settings.php
*************************************/
// if ( ! file_exists( dirname( __FILE__ ) . '/languages/lang_'.$language.'.conf' ) ) {
// 	$language = $settings_language; // Back where we started. The settings.php file value.
// }

// // If all else fails, we default to the english language file
// if ( ! file_exists( dirname( __FILE__ ) . '/languages/lang_'.$language.'.conf' ) ) {
// 	$language = 'english';
// }

/*************************************
    * Redwine: if no matter what, the language file does not exist, the we throw an error and kill the script.
    * Otherwise, we define the language as KAHUK_LANG.
*************************************/
if ( !file_exists( dirname( __FILE__ ) . '/languages/lang_'.KAHUK_LANG.'.conf' ) ) {
    $output .= '<div id="wrapper" style="width:800px;padding:10px;background-color:#FFC6C6;border:1px solid #000;color:#000;display:block;margin-left:auto;margin-right:auto;">';
    $output .= 'The language file /languages/lang_' . KAHUK_LANG . '.conf does not exist. Either this file is missing or the server does not have permission to read it. Make sure that you renamed the file /languages/lang_' . $language . '.conf.default to /languages/lang_' . $language . '.conf.</div>';
	
	die( $output );
}

//var_dump(get_defined_constants(true));
if ( ! defined( 'page' ) || ( defined( 'page' ) && page != 'terms' ) ) {
    include_once( KAHUK_MODULES_DIR . 'modules_init.php' );
}

include KAHUK_LIBS_DIR.'utf8/utf8.php';
include_once( KAHUK_LIBS_DIR.'dbtree.php' );


function loadCategoriesForCache( $clear_cache = false ) {
	global $db;
	$sql = "select * from " . table_categories . " ORDER BY lft ASC;";

    //Redwine: if clear_cache i true, then we call un_cache function in /libs/ez_sql_core.php
	if ( $clear_cache ) {
		$db->un_cache($sql);
	}

	return $db->get_results($sql);
}
