<?php

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
$siteEmail = $main_smarty->get_config_vars('KAHUK_PassEmail_From');

if (strpos($siteEmail, 'ENTER_THE_LOCALPART_HERE') !== false) {
    $main_smarty->assign('siteEmail', 'Not configured');
} else {
    $main_smarty->assign('siteEmail', $siteEmail);
}

//Check if the site's email is entered.
$allowSMTP = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'allow_smtp_testing';");
$main_smarty->assign('allowSMTP', $allowSMTP);

$hostSMTP = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'smtp_host';");
$main_smarty->assign('hostSMTP', $hostSMTP);

$portSMTP = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'smtp_port';");
$main_smarty->assign('portSMTP', $portSMTP);

$passSMTP = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'smtp_pass';");
$main_smarty->assign('passSMTP', $passSMTP);

$fakeSMTP = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'smtp_fake_email';");
$main_smarty->assign('fakeSMTP', $fakeSMTP);

//Logo, openGraph and Twitter Card
$useLogo = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Default_Site_Logo';");
$main_smarty->assign('useLogo', $useLogo);

$useopenGraph = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Default_Site_OG_Image';");
$main_smarty->assign('useopenGraph', $useopenGraph);

$useTwittercard = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Default_Site_Twitter_Image';");
$main_smarty->assign('useTwittercard', $useTwittercard);

//Misc settings
$validateuserEmail = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'misc_validate';");
$main_smarty->assign('validateuserEmail', $validateuserEmail);

$validateuserPass = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'validate_password';");
$main_smarty->assign('validateuserPass', $validateuserPass);

$SEOMethod = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = '\$URLMethod';");
$main_smarty->assign('SEOMethod', $SEOMethod);

$Anti_spam = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'CHECK_SPAM';");
$main_smarty->assign('Anti_spam', $Anti_spam);

$submitDraft = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Allow_Draft';");
$main_smarty->assign('submitDraft', $submitDraft);

$submitScheduled = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Allow_Scheduled';");
$main_smarty->assign('submitScheduled', $submitScheduled);

$submitMultiCat = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'Multiple_Categories';");
$main_smarty->assign('submitMultiCat', $submitMultiCat);

$submitNoURLName = $db->get_var("SELECT `var_value` FROM " . table_config ." WHERE `var_name` = 'No_URL_Name';");
$main_smarty->assign('submitNoURLName', $submitNoURLName);

// pagename
define('pagename', 'admin_settings'); 
$main_smarty->assign('pagename', pagename);

// show the template
$main_smarty->assign('tpl_center', '/admin/settings_help');
$main_smarty->display('/admin/admin.tpl');
