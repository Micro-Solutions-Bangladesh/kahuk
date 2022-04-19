<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'group.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

check_referrer();

// sidebar
//$main_smarty = do_sidebar($main_smarty);
// require user to log in
force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
if ($current_user->user_id != get_group_creator($_REQUEST['id']) && $canIhaveAccess == 0) {
	header("Location: " . getmyurl('login', $_SERVER['REQUEST_URI']));
	die();
}

// pagename
define('pagename', 'delete');
$main_smarty->assign('pagename', pagename);
if (isset($_REQUEST['id'])) {
	global $db;
	$group_id = $_REQUEST['id'];

	if (!is_numeric($group_id)) {
		die();
	}
	$group_delete = $db->query(" Delete from " . table_groups . " where group_id =" . $group_id);
	$member_delete = $db->query(" Delete from " . table_group_member . " where member_group_id =" . $group_id);
	$db->query(" Delete from " . table_group_shared . " where share_group_id =" . $group_id);
	
	header('Location: ' . KAHUK_BASE_URL);
}
