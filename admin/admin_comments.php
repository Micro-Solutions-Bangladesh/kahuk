<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');

include(KAHUK_LIBS_DIR.'smartyvariables.php');
include(KAHUK_LIBS_DIR.'csrf.php');

check_referrer();

force_authentication();

if (!in_array($session_user_level, ['admin','moderator'])) {
    die(".");
}

$CSRF = new csrf();

// pagename
define('pagename', 'admin_comments');

$comments_statuses = kahuk_comments_statuses();
$page_url = kahuk_create_url("admin/admin_comments.php");

$process = sanitize_text_field(_post('submit'));
$pagesize = sanitize_number(_get('pagesize', $page_size));

if ($process == "Apply") {
	$token = _post('token');

	// if valid TOKEN, proceed. A valid integer must be equal to 2.
	if (!empty($token) && $CSRF->check_valid(sanitize($_POST['token'], 3), 'comments_edit') == 2) {
		
		$tool_process = sanitize_text_field(_post('tool_process'));
		$comments = _post('comment', []);

		if ($tool_process=='trash') {
			foreach($comments as $key => $value) {
				if ($value == 1) {
					$commentId = sanitize_number($key);
					kahuk_delete_comment($commentId);
				}
			}
		} else if(in_array($tool_process, $comments_statuses)) {
			foreach($comments as $key => $value) {
				if ($value == 1) {
					$commentId = sanitize_number($key);

					$sql = "UPDATE " . table_comments;
					$sql .= " SET comment_status='" . $db->escape($tool_process) . "'";
					$sql .= " WHERE comment_id='{$commentId}'";
				
					$rs = $db->query($sql);
	
					if ($rs !== 1) {
						kahuk_log_unexpected("DB Warning: [output: {$rs}]\nSQL: {$sql}");
					}
				}
			}
		}
	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}

	// TODO


	kahuk_redirect($page_url);
	exit;
}


$CSRF->create('comments_edit', true, true);




$args = [
	'page_size' => $pagesize,
	'comment_status' => $comments_statuses,
	'order_by' => 'cmnts.comment_date DESC',
];

$data_items = $globalCommentsObj->get_comments($args);

/**
 * Pagination
 */
$rowCount = $globalCommentsObj->get_count($args);

$permalinkArgs = [];

if ($pagesize>0) {
	$permalinkArgs['pagesize'] = $pagesize;
}

$args = [
	'page_limit' => $pagesize,
	'total' => $rowCount,
	'permalink' => kahuk_create_url("admin/admin_comments.php", $permalinkArgs),
];

// print_r($args);

$main_smarty->assign('pagination', kahuk_pagination($args));

//
$attr = 'name="tool_process" name="tool_process" class="form-control capitalize"';
$first_opt='<option value="">Status Bulk Action</option>';
$comment_statuses_markup = kahuk_statuses_select_markup($comments_statuses, $attr, $first_opt);

//
$attr='name="tool_pagesize" id="tool_pagesize" class="form-control capitalize"';
$first_opt='<option value="0">Select Record Limit</option>';
$comment_pagesizes_markup = kahuk_page_size_options_markup($attr, $first_opt);

//
$main_smarty->assign('pagename', 'admin_comments');
$main_smarty->assign('page_url', $page_url);
$main_smarty->assign('comment_pagesizes_markup', $comment_pagesizes_markup);
$main_smarty->assign('comment_statuses_markup', $comment_statuses_markup);
$main_smarty->assign('data_items', $data_items);

// show the template
$main_smarty->assign('tpl_center', '/admin/comments');

if (kahuk_check_user_role('admin')){
	$main_smarty->display('/admin/admin.tpl');
} else {
	$main_smarty->display('/admin/moderator.tpl');
}
