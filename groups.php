<?php
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'group.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

global $globalGroups, $page_size;

// pagename
define('pagename', 'groups');
$main_smarty->assign('pagename', pagename);
$main_smarty = do_sidebar($main_smarty);

// kahuk_reset_groups( ['debug' => false, 'columns_all' => false] );
kahuk_reset_groups();

$group_display = "";

foreach ($globalGroups['rows'] as $index => $group) {
	// $group_display .= group_print_summary($group->group_id);

	$group_display .= create_markup_group_summery($group);
}

$main_smarty->assign('group_rows', $globalGroups['rows']);
$main_smarty->assign('group_display', $group_display);

$main_smarty->assign('total_row_for_group', $globalGroups['count_enable']);

$main_smarty->assign('group_pagination', do_pages($globalGroups['count_enable'], $page_size, "groups", true));

// $main_smarty->assign('link_pagination', do_pages($globalGroups['count_enable'], $page_size, "published", true));

// show the template
$main_smarty->assign('tpl_center', $the_template . '/group_center');
$main_smarty->display($the_template . '/kahuk.tpl');

