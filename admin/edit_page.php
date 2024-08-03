<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUKPATH_LIBS . 'csrf.php');
include(KAHUKPATH_LIBS . 'smartyvariables.php');

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
define('pagename', 'edit_page');
$main_smarty->assign('pagename', pagename);

$page_url = '';

if (isset($_REQUEST['link_id'])) {
	if (is_numeric($_REQUEST['link_id'])) {
		$link_id = sanitize_number($_REQUEST['link_id']);

		if ($link_id) {
			global $db;

			$sql = (" SELECT * from " . table_links . " where link_id='$link_id'");
			$page_id = $db->get_results($sql);

			foreach ($page_id as $page_results) {
				$page_url = $page_results->link_title_url;

				$main_smarty->assign('page_title', $page_results->link_title);
				$main_smarty->assign('page_url', $page_results->link_title_url);
				$main_smarty->assign('page_content', $page_results->link_content);
			}

			$main_smarty->assign('link_id', $link_id);
		}
	}
}

if (isset($_POST['process']) && $_POST['process'] == 'edit_page') {
	global $current_user, $db;

	$link_id = sanitize_number($_POST['link_id']);
	$page_url = $db->escape(kahuk_create_slug_story($_POST['page_url']));
	$page_title = $db->escape(trim($_REQUEST['page_title']));

	if (empty($page_url) || empty($link_id)) {
		die("Page slug should not be empty.");
	}

	// $link_content = $db->escape(trim($_REQUEST['page_content']));
	$link_content = $db->escape(
		kahuk_kses($_POST['page_content'],
		'<div><p><a><legend><strong><b><i><h1><h2><h3><h4><h5><h6><ul><ol><li><hr>')
	);

	$page_randkey = $db->escape(trim($_POST['randkey']));
	$page_keywords = $db->escape(trim($_POST['page_keywords']));
	$page_description = $db->escape(trim($_POST['page_description']));

	// Save old SEO URL if changed
	$old_url = $db->get_var("SELECT link_title_url FROM " . table_links . " WHERE link_id=$link_id");

	if ($old_url && $old_url != $page_url) {
		$db->query("INSERT INTO " . table_old_urls . " SET old_link_id=$link_id, old_title_url='$old_url'");
	}

	$sql = " UPDATE " . table_links . " SET `link_modified` = NOW(), `link_title` = '$page_title', `link_title_url` = '$page_url', `link_content` = '$link_content' WHERE `link_id` = {$link_id} LIMIT 1 ";
	$result = $db->query($sql);

	// echo "<pre>SQL: {$sql}</pre>";
	// exit;

	if ($result) {
		$permalink = kahuk_create_url("admin/edit_page.php", ['link_id' => $link_id]);
		// header('Location: ' . getmyurl("page", $page_url));
		kahuk_redirect($permalink);
		die();
	}
}


$page_dynamic_page_url = kahuk_create_url("page/{$page_url}");
$main_smarty->assign('page_dynamic_page_url', $page_dynamic_page_url);


// show the template
$main_smarty->assign('tpl_center', '/admin/page_edit');
$main_smarty->display('/admin/admin.tpl');
