<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if($canIhaveAccess == 0){	
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

//Check if the site's email is entered.
$siteEmail = kahuk_get_config("_site_email_contact");

if (strpos($siteEmail, 'ENTER_THE_LOCALPART_HERE') !== false) {
    $main_smarty->assign('siteEmail', 'Not configured');
} else {
    $main_smarty->assign('siteEmail', $siteEmail);
}

//Check if the site's email is entered.
$emailDebug = kahuk_get_config("_email_debug");
$main_smarty->assign('emailDebug', $emailDebug);

$hostSMTP = kahuk_get_config("_smtp_host");
$main_smarty->assign('hostSMTP', $hostSMTP);

$portSMTP = kahuk_get_config("_smtp_port");
$main_smarty->assign('portSMTP', $portSMTP);

$passSMTP = kahuk_get_config("_smtp_pass");
$main_smarty->assign('passSMTP', $passSMTP);



$useopenGraph = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Default_Site_OG_Image';");
$main_smarty->assign('useopenGraph', $useopenGraph);

$useTwittercard = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Default_Site_Twitter_Image';");
$main_smarty->assign('useTwittercard', $useTwittercard);

$validateuserPass = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'validate_password';");
$main_smarty->assign('validateuserPass', $validateuserPass);

$submitMultiCat = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Multiple_Categories';");
$main_smarty->assign('submitMultiCat', $submitMultiCat);

// pagename
define('pagename', 'admin_settings'); 
$main_smarty->assign('pagename', pagename);

// show the template
$main_smarty->assign('tpl_center', '/admin/settings_help');
$main_smarty->display('/admin/admin.tpl');
