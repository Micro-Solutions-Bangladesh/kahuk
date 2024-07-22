<?php
if (!(defined('enable_group') || enable_group)) {
	kahuk_redirect_404();
}

include(KAHUKPATH_LIBS . 'group.php');

$args = [];

if ($user_authenticated) {
	if (kahuk_check_user_role('admin')) {
		$args['group_status'] = $globalGroupStatuses;
	} else if (kahuk_check_user_role('moderator')) {
		$args['group_status'] = ['enable', 'pending'];
	} else if (kahuk_check_user_role('normal')) {
		$args['group_status'] = ['enable'];
		$args['where_clause'] = " OR (group_status IN ('enable', 'pending') AND group_creator = {$session_user_id})";
	}
} else {
	$args['group_status'] = ['enable'];
}

//
$groups = $globalGroupsObj->get_groups($args);
$groups_count = $globalGroupsObj->count_groups($args);

//
$groups_markup = "";

foreach ($groups as $index => $group) {
	$group['meta'] = get_group_meta($group);
	$groups_markup .= create_markup_group_summery($group);
}

$main_smarty->assign('groups', $groups);
$main_smarty->assign('groups_markup', $groups_markup);

$main_smarty->assign('groups_count', $groups_count);


/**
 * Pagination
 */
$args = [
	'total' => $groups_count,
	'first_last' => false,
];

$main_smarty->assign('pagination', kahuk_pagination($args));

// show the template
$main_smarty->assign('tpl_center', $the_template . '/groups_center');
