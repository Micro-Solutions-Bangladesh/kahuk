<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

include(KAHUK_LIBS_DIR . 'csrf.php');
include(KAHUK_LIBS_DIR . 'document_class.php');

$KahukDoc->add_js(my_base_url . my_kahuk_base . "/templates/admin/js/move.js");

$KahukDoc->get_js();

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
if ($session_user_level != "admin") {
    die(".");
}


$main_smarty->assign('globalCategoryStatuses', $globalCategoryStatuses);

//
$CSRF = new csrf();

// pagename
define('pagename', 'admin_categories');
$main_smarty->assign('pagename', pagename);

$action = sanitize_text_field(_request('action', 'view'));

if ($action == "save") {
	// if TOKEN is empty, no need to continue, just display the invalid token error.
	if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}

	// if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'category_manager') == 2) {
		$id = sanitize_number(_post("category_id", 0));

		$data = [
			"category_parent" => sanitize_number(_post("parent", 0)),
			"category_name" => _post("name"),
			"category_safe_name" => _post("category_safe_name"),
			"category_status" => _post("category_status"),
			"category_order" => _post("category_order", 100),
			"category_desc" => _post("category_desc"),
			"category_keywords" => _post("category_keywords"),
		];

		$output = kahuk_save_category($data, $id);

		if ($output["status"] == false) {
			kahuk_set_session_message($output["message"], "warning");
			// die($output["message"]);
		} else {
			kahuk_set_session_message($output["message"], "success");
		}
	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}

	kahuk_redirect(kahuk_create_url("admin/admin_categories.php"));
	exit;

} elseif ($action == "view") {
	$CSRF->create('category_manager', true, true);

	$args = [
		'is_hierarchical' => false,
		'category_status' => $globalCategoryStatuses,
		// 'do_not_cache' => true,
		// 'order_by' => 'category_name ASC',
	];
	$catArray = $globalCategoriesObj->get_categories($args);

	$args = [
		'is_hierarchical' => true,
		'category_status' => $globalCategoryStatuses,
		// 'do_not_cache' => true,
		// 'order_by' => 'category_name ASC',
	];
	$hierarchicalCategories = $globalCategoriesObj->get_categories($args);

	// print_r($hierarchicalCategories);

	$main_smarty->assign('cat_count', count($catArray));
	$main_smarty->assign('cat_array', $catArray);
	$main_smarty->assign('hierarchical_categories', $hierarchicalCategories);
	$main_smarty->assign('tpl_center', '/admin/categories');
	$main_smarty->display('/admin/admin.tpl');
}



function move_delete_stories($id)
{
	global $db;

	if ($_REQUEST['sub'] == 'delete') {
		if (Multiple_Categories) {
			$sql = "SELECT link_id FROM " . table_links . " 
				LEFT JOIN " . table_additional_categories . " ON ac_link_id=link_id
				WHERE link_category=$id OR ac_cat_id=$id
				GROUP BY link_id";
		} else {
			$sql = "SELECT link_id FROM " . table_links . " WHERE link_category='$id'";
		}

		$links = $db->get_results($sql);

		foreach ($links as $link) {
			$db->query('UPDATE ' . table_comments . " SET comment_status='discard' WHERE comment_link_id={$link->link_id}");

			$vars = array('link_id' => $link->link_id);

			$db->query('UPDATE ' . table_links . " SET link_status='discard' WHERE link_id={$link->link_id}");
		}

		$db->query('DELETE FROM ' . table_additional_categories . " WHERE ac_cat_id='$id'");
	} elseif ($_REQUEST['move'] && is_numeric($_REQUEST['move'])) {
		$db->query('UPDATE ' . table_links . " SET link_category='{$_REQUEST['move']}' WHERE link_category='$id'");

		// Update additional categories
		$db->query('UPDATE IGNORE ' . table_additional_categories . " SET ac_cat_id='{$_REQUEST['move']}' WHERE ac_cat_id='$id'");

		// Delete duplicates that were not updated
		$db->query('DELETE FROM ' . table_additional_categories . " WHERE ac_cat_id='$id'");
	} else {
		die();
	}
}
