<?php
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'csrf.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

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

if ($canIhaveAccess == 0) {
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

// misc smarty
$main_smarty->assign('isAdmin', $canIhaveAccess);

$randkey = rand(1000000, 100000000);
$main_smarty->assign('randkey', $randkey);

// pagename	
define('pagename', 'page_submit');
$main_smarty->assign('pagename', pagename);

if (isset($_REQUEST['process']) && $_REQUEST['process'] == 'new_page') {
	global $current_user, $db;

	if (!$_REQUEST['page_url']) {
		$_REQUEST['page_url'] = $_REQUEST['page_title'];
	}

	$page_url = makeUrlFriendly($db->escape(trim($_REQUEST['page_url'])), true);
	$page_title = $db->escape(trim($_REQUEST['page_title']));
	$page_content = $db->escape(trim($_REQUEST['page_content']));
	$page_keywords = $db->escape(trim($_REQUEST['page_keywords']));
	$page_description = $db->escape(trim($_REQUEST['page_description']));
	$page_randkey = $db->escape(trim($_REQUEST['randkey']));
	$sql = "INSERT IGNORE INTO " . table_links . " (link_author, link_status, link_randkey, link_category, link_date, link_published_date, link_votes, link_karma, link_title, link_title_url, link_content, link_field1, link_field2) 
				VALUES (" . $db->escape($current_user->user_id) . ", 'page', $page_randkey, '0', NOW( ), '', 0, 0, '$page_title', '$page_url', '$page_content', '$page_keywords', '$page_description')";
	$result = $db->query($sql);

	if ($result == 1) {
		header('Location: ' . getmyurl("page", $page_url));
		die();
	}
}

// show the template
$main_smarty->assign('tpl_center', '/admin/page_submit');
$main_smarty->display('/admin/admin.tpl');
