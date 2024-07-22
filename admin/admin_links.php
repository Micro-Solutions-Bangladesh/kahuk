<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');

include(KAHUKPATH_LIBS.'smartyvariables.php');
include(KAHUKPATH_LIBS.'csrf.php');

check_referrer();

force_authentication();

if (!in_array($session_user_level, ['admin','moderator'])) {
    die(".");
}

$CSRF = new csrf();

// pagename
// define('pagename', 'admin_links');

$paginationArgs = [];

// $process = sanitize_text_field(_post('process'));
$tool_keyword = sanitize_text_field(_request("tool_keyword"));
$tool_status = sanitize_text_field(_request("tool_status"));
$tool_process = sanitize_text_field(_post("tool_process"));
$pagesize = sanitize_number(_get("pagesize", 50));

//
$story_statuses = kahuk_story_statuses(false);
$story_status_default = kahuk_story_status_default();
$stories_status = ($tool_status ? [$tool_status] : $story_statuses);

//
$page_url = kahuk_create_url("admin/admin_links.php");


//
if ($tool_process) {
	$token = _post("token");

	if ($tool_keyword) {
		$page_url = add_query_arg("tool_keyword", $tool_keyword, $page_url);
	}
	
	if ($tool_status) {
		$page_url = add_query_arg("tool_status", $tool_status, $page_url);
	}

	// if valid TOKEN, proceed. A valid integer must be equal to 2.
	if (!empty($token) && $CSRF->check_valid(sanitize($_POST["token"], 3), 'stories_edit') == 2) {
		
		$selectedBoxes = _post('cbid_', []);

		if(in_array($tool_process, $story_statuses)) {
			$selectedIds = [];

			foreach($selectedBoxes as $key => $value) {
				if ($value == 1) {
					$selectedIds[] = sanitize_number($key);
				}
			}

			$sql = "UPDATE " . table_links;
			$sql .= " SET link_status='" . $db->escape($tool_process) . "'";
			$sql .= " WHERE link_id IN (" . implode(",", $selectedIds) . ")";
		
			$rs = $db->query($sql);

			kahuk_log_schedule("Admin Action: [{$rs} rows affected]\nSQL: {$sql}");

			kahuk_set_session_message(
				"{$rs} stories updated.",
				"success"
			);
		}
	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}

	kahuk_redirect($page_url);
	exit;
}

//
$CSRF->create('stories_edit', true, true);

//
$storiesArgs = [
	"page_size" => $pagesize,
    "link_status" => $stories_status,
    "order_by" => "links.link_id DESC",
];

// If user is searching
if ($tool_keyword) {
	$where_clause = "";
	$sKeyword = kahuk_analyse_keyword($tool_keyword);

	// echo "<pre>tool_keyword: $tool_keyword</pre>";
	// print_r($sKeyword);

	if ($sKeyword) {
		$readyKeyword = $sKeyword["keyword"];
		$readyType = $sKeyword["type"];

		if ($readyType == "numeric") {
			$where_clause = "links.link_id = " . sanitize_number($readyKeyword);
		} else if ($readyType == "domain") {
			$where_clause = "links.link_url LIKE '%" . $db->escape($readyKeyword) . "%'";
			// $where_clause .= " OR links.link_content LIKE '%" . $db->escape($sKeyword["keyword"]) . "%'";
		} else if ($readyType == "author") {
			$where_clause = "users.user_login = '" . $db->escape($readyKeyword) . "'";
		} else if ($readyType == "quoted") {
			$where_clause = "(links.link_title LIKE '%" . $db->escape($readyKeyword) . "%'";
			$where_clause .= " OR links.link_content LIKE '%" . $db->escape($readyKeyword) . "%')";
		}
	} else {
		$readyKeyword = explode(" ", $tool_keyword);
        $readyKeyword = implode("%", $readyKeyword);

		$where_clause = "(links.link_title LIKE '%" . $db->escape($readyKeyword) . "%'";
		$where_clause .= " OR links.link_content LIKE '%" . $db->escape($readyKeyword) . "%')";
	}

	$storiesArgs["where_clause"] = $where_clause;
	$paginationArgs["tool_keyword"] = $tool_keyword;

	$rowCount = kahuk_count_stories($storiesArgs);

} else {
	$rowCount = kahuk_get_stories_total($stories_status);
}

if ($tool_status) {
	$paginationArgs["tool_status"] = $tool_status;
}

// 
$items = [];

if ($rowCount) {
	$items = kahuk_get_stories($storiesArgs);
}

/**
 * Pagination
 */
if ($pagesize>0) {
	$paginationArgs["pagesize"] = $pagesize;
}

$args = [
	"page_limit" => $pagesize,
	"total" => $rowCount,
	"permalink" => kahuk_create_url("admin/admin_links.php", $paginationArgs),
];

$main_smarty->assign("pagination", kahuk_pagination($args));

//
$attr = 'name="tool_process" name="tool_process" class="form-control capitalize"';
$first_opt='<option value="">Status Bulk Action</option>';
$tool_process_markup = kahuk_statuses_select_markup($story_statuses, $attr, $first_opt);

//
$attr = 'name="tool_status" name="tool_status" class="form-control capitalize"';
$first_opt='<option value="">Story Status</option>';
$tool_status_markup = kahuk_statuses_select_markup($story_statuses, $attr, $first_opt, $tool_status);

//
$attr='name="tool_pagesize" id="tool_pagesize" class="form-control capitalize"';
$first_opt='<option value="0">Select Record Limit</option>';
$pagesizes_markup = kahuk_page_size_options_markup($attr, $first_opt, $pagesize);

//
$main_smarty->assign("page_url", $page_url);
$main_smarty->assign("pagesizes_markup", $pagesizes_markup);
$main_smarty->assign("tool_process_markup", $tool_process_markup);
$main_smarty->assign("tool_status_markup", $tool_status_markup);
$main_smarty->assign("data_items", $items);
$main_smarty->assign("tool_keyword", $tool_keyword);

// show the template
$main_smarty->assign("tpl_center", "/admin/stories");

$main_smarty->display("/admin/admin.tpl");

exit;
