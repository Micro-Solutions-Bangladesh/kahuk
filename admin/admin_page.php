<?php
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'link.php');
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

if ($canIhaveAccess == 0) {
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

// misc smarty
$main_smarty->assign('isAdmin', $canIhaveAccess);

// pagename
define('pagename', 'admin_page');
$main_smarty->assign('pagename', pagename);

global $db;

if (isset($_REQUEST['mode'])) {
	$mode = $_REQUEST['mode'];

	if (is_numeric($_REQUEST['link_id'])) {
		$link_id = $_REQUEST['link_id'];

		if ($mode == 'delete' && is_numeric($link_id)) {
			$db->query(" delete from " . table_links . " where link_id=" . $link_id);

			// module system hook
			$vars = array('link_id' => $link_id);
			check_actions('admin_story_delete', $vars);

			header("Location: " . my_kahuk_base . "/admin/admin_page.php");
			die();
		}
	}
}

$sql = (" SELECT * from " . table_links . " where link_status='page'");
$page_id = $db->get_results($sql);

if (!empty($page_id)) {
	$page_title = '';

	foreach ($page_id as $page_results) {
		$page_title .= '<tr>
						<td class="page_td_title">
							<a href="' . getmyurl("page", $page_results->link_title_url) . '" title="' . $page_results->link_title . '" target="_blank" rel="noopener noreferrer">' . $page_results->link_title . '</a>
						</td>
						<td class="page_td_edit">
							<a class="btn btn-default" href="' . KAHUK_BASE_URL . '/admin/edit_page.php?link_id=' . $page_results->link_id . '"><i class="fa fa-edit" title="' . $main_smarty->get_config_vars("KAHUK_Visual_AdminPanel_Page_Edit") . '"></i></a>
						</td>
						<td class="page_td_delete">
							<a class="btn btn-danger" onclick="return confirm(\'' . $main_smarty->get_config_vars('KAHUK_Visual_Page_Delete_Confirm') . '\');" href="' . KAHUK_BASE_URL . '/admin/admin_page.php?link_id=' . $page_results->link_id . '&mode=delete"><i class="fa fa-trash-o" title="' . $main_smarty->get_config_vars("KAHUK_Visual_AdminPanel_Page_Delete") . '"></i></a>
						</td>
					</tr>';
	}

	if (!empty($page_title)) {
		$main_smarty->assign('page_title', $page_title);
	}

	if (!empty($page_text)) {
		$main_smarty->assign('page_text', $page_text);
	}
}

// show the template
$main_smarty->assign('tpl_center', '/admin/pages');
$main_smarty->display('/admin/admin.tpl');
