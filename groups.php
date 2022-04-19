<?php
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'group.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

global $globalGroups, $page_size;

if (!("true" == enable_group)) {
	kahuk_redirect_404();
}

// pagename
define('pagename', 'groups');
$main_smarty->assign('pagename', pagename);
$main_smarty = do_sidebar($main_smarty);

$args = ["debug" => false]; // TODO Fix Searching and pagination
$groups = $globalGroups->get_groups($args);
$groups_count = $globalGroups->count_groups($args);

$group_display = "";

foreach ($groups as $index => $group) {
	$group_display .= create_markup_group_summery($group);
}

$main_smarty->assign('group_rows', $groups);
$main_smarty->assign('group_display', $group_display);

$main_smarty->assign('total_row_for_group', $groups_count);

$main_smarty->assign('group_pagination', do_pages($groups_count, $page_size, "groups", true));

// show the template
$main_smarty->assign('tpl_center', $the_template . '/group_center');
$main_smarty->display($the_template . '/kahuk.tpl');

