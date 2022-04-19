<?php
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'group.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

$group_display = "";

$start_up = isset($_REQUEST['start_up']) ? $_REQUEST['start_up'] : "";
$page_size = isset($_REQUEST['pagesize']) ? $_REQUEST['pagesize'] : "";

$group = $db->get_results("SELECT * FROM " . table_groups . " WHERE group_status='Enable' ORDER BY group_status, group_date  DESC LIMIT $start_up, $page_size");

if ($group) {
	foreach ($group as $groupid) {
		$group_display .= group_print_summary($groupid->group_id);
	}

	echo $group_display;
}
