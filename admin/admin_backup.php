<?php

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins and moderators
$amIadmin = 0;
$amIadmin = $amIadmin + checklevel('admin');
$main_smarty->assign('amIadmin', $amIadmin);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
$canIhaveAccess = $canIhaveAccess + checklevel('moderator');

if($canIhaveAccess == 0){	
//	$main_smarty->assign('tpl_center', '/admin/access_denied');
//	$main_smarty->display('/admin/admin.tpl');
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

$main_smarty->assign('isAdmin', $canIhaveAccess);

// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel');
$navwhere['link1'] = getmyurl('admin', '');
$navwhere['text2'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel_4');
$navwhere['link2'] = my_kahuk_base . "/admin/admin_backup.php";
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel'));

// sidebar
//$main_smarty = do_sidebar($main_smarty);

// pagename
define('pagename', 'admin_backup'); 
$main_smarty->assign('pagename', pagename);

// read the mysql database to get the kahuk version
/* Redwine: kahuk version query removed and added to /libs/smartyvriables.php */

// show the template
$main_smarty->assign('tpl_center', '/admin/backup');
$main_smarty->display('/admin/admin.tpl');
