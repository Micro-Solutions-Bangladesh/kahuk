<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUKPATH_LIBS . 'smartyvariables.php');

check_referrer();

// require user to log in
force_authentication();

/* Roles and permissions and Groups fixes */
// Check if the user didn't exceed the max number of creating groups.
$numGr = $db->get_var("SELECT count(*) FROM " . table_groups . " WHERE `group_creator` = " . $current_user->user_id);
$max_user_groups_allowed = $main_smarty->get_template_vars('max_user_groups_allowed');

if ($numGr >= $max_user_groups_allowed) {
	$error_max = $main_smarty->get_config_vars('KAHUK_Visual_Submit_A_New_Group_Error');
	$main_smarty->assign('error_max', $error_max);
}

// restrict access to admins and moderators
$amIadmin = 0;
$amIadmin = $amIadmin + checklevel('admin');
$main_smarty->assign('amIadmin', $amIadmin);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
$canIhaveAccess = $canIhaveAccess + checklevel('moderator');

$is_moderator = checklevel('moderator'); // Moderators have a value of '1' for the variable $is_moderator

if ($canIhaveAccess == 0) {
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

// misc smarty
$main_smarty->assign('isAdmin', $canIhaveAccess);


// pagename
define('pagename', 'admin_group');
$main_smarty->assign('pagename', pagename);

// read the mysql database to get the kahuk version
/* Redwine: kahuk version query removed and added to /libs/smartyvriables.php */
global $db;

if (isset($_REQUEST['mode'])) {
	$mode = $_REQUEST['mode'];
	$group_id = $_REQUEST['group_id'];
	if ($mode == 'delete' && is_numeric($group_id)) {
		$db->query("DELETE FROM " . table_groups . " WHERE group_id=" . $group_id);
		$db->query("DELETE FROM " . table_group_member . " WHERE member_group_id=" . $group_id);
		$db->query("DELETE FROM " . table_group_shared . " WHERE share_group_id=" . $group_id);

		header("Location: " . my_kahuk_base . "/admin/admin_group.php");
		die();
	} elseif ($mode == 'approve' && is_numeric($group_id)) {
		$db->query("UPDATE " . table_groups . " SET group_status='enable' WHERE group_id=$group_id");

		header("Location: " . my_kahuk_base . "/admin/admin_group.php");
		die();
	}
}

$sql = "SELECT * FROM " . table_groups . " LEFT JOIN " . table_users . " ON user_id=group_creator ORDER BY group_name";
$main_smarty->assign('groups', $db->get_results($sql, ARRAY_A));

// show the template
$main_smarty->assign('tpl_center', '/admin/groups');

if ($is_moderator == '1') {
	$main_smarty->display('/admin/moderator.tpl');
} else {
	$main_smarty->display('/admin/admin.tpl');
}
