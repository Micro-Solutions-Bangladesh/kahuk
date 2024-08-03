<?php
if (kahuk_redirect_group_access()) {
    kahuk_redirect_404();
    die();
}

/**
 * Check group status and privacy to avoid unauthorize access
 */
$group_id = $globalGroup['group_id'];
$group_slug = $globalGroup['group_safename'];
$group_creator = $globalGroup['group_creator'];
$group_status = $globalGroup['group_status']; // 'enable', 'pending', 'disable'
$group_privacy = $globalGroup['group_privacy']; // 'private', 'public', 'restricted'

//
include_once KAHUKPATH_LIBS . 'group.php';

//
include(KAHUKPATH_LIBS . 'csrf.php');
$CSRF = new csrf();

if (kahuk_check_user_role("admin")) {
    $CSRF->create('delete_group', true, true);
}

//
$group = $globalGroup;
$group['meta'] = get_group_meta($globalGroup);
$main_smarty->assign('group', $group);

// Create URL to submit story in group
$main_smarty->assign('group_submit_url', add_query_arg('gId', $group_id, $page_submit_url_begin));

// Get the role of logged in user for the group
$session_user_membership = kahuk_user_membership_group($group_id);
$main_smarty->assign('session_user_membership', $session_user_membership);

// Count the number of groups the user already joined
$main_smarty->assign('user_groups_total', kahuk_count_groups_by_user());

//
$group_edit_url = kahuk_permalink_group($group, ['prefix' => 'edit']);
$main_smarty->assign('group_edit_url', $group_edit_url);

//
$join_url = kahuk_permalink_group($group, ['prefix' => 'join']);
$main_smarty->assign('join_group_url', $join_url);

//
$unjoin_group_url = kahuk_permalink_group($group, ['prefix' => 'unjoin']);
$main_smarty->assign('unjoin_group_url', $unjoin_group_url);

//
$urlArgs = ['prefix' => 'update', 'task' => 'withdraw'];
$join_group_withdraw = kahuk_permalink_group($group, $urlArgs);
$main_smarty->assign('join_group_withdraw', $join_group_withdraw);

//
$urlArgs['task'] = 'approve';
$group_approve_url = kahuk_permalink_group($group, $urlArgs);
$main_smarty->assign('group_approve_url', $group_approve_url);

//
$group_delete_url = kahuk_permalink_group($group, ['prefix' => 'delete']);
$main_smarty->assign('group_delete_url', $group_delete_url);


/**
 * 
 */
//
$group_view_shared = kahuk_permalink_group($group, ['view' => 'shared']);
$main_smarty->assign('group_view_shared', $group_view_shared);

//
$group_view_published = kahuk_permalink_group($group, ['view' => 'published']);
$main_smarty->assign('group_view_published', $group_view_published);

//
$group_view_new = kahuk_permalink_group($group, ['view' => 'new']);
$main_smarty->assign('group_view_new', $group_view_new);

//
$group_view_trending = kahuk_permalink_group($group, ['view' => 'trending']);
$main_smarty->assign('group_view_trending', $group_view_trending);

//
$group_view_members = kahuk_permalink_group($group, ['view' => 'members']);
$main_smarty->assign('group_view_members', $group_view_members);

/**
 * 
 */
$groupViews = ['trending', 'published', 'shared', 'members'];
$groupView = _get('view');

if (!in_array($groupView, $groupViews)) {
    $groupView = 'shared';
}

$main_smarty->assign('groupview', $groupView);




$pageContent = "";
$pagination = "";
$stories = [];
$members = [];

if ($groupView == 'members') {
	$args = [
		'page_size' => 250,
	];

	$members = kahuk_get_members_by_group([$group_id], $args);
	$pageContent = '';

	foreach($members as $member) {
		$main_smarty->assign('member', $member);
		$pageContent .= $main_smarty->fetch($the_template . "/template-parts/group_member_item.tpl");
	}
} else {
	/** in_array($groupView, ['trending', 'published', 'shared']) */

	$storiesArgs = [];
	$rowCount = 0;

	if (in_array($groupView, ['trending', 'published'])) {
		$storiesArgs["where_clause"] = "links.link_group_id = {$group_id}";

		if ($groupView == 'trending') {
			$storiesArgs["link_status"] = kahuk_story_statuses();
			$storiesArgs["order_by"] = "links.link_karma DESC, links.link_date DESC";
		} else {
			$storiesArgs["link_status"] = [$groupView];
		}
		
		$rowCount = kahuk_count_stories($storiesArgs);

		if ($rowCount) {
			$stories = kahuk_get_stories($storiesArgs);
		}
	} elseif ($groupView == 'shared') {
		
		$rowCount = kahuk_get_shared_stories_count(["group_id" => $group_id]);

		if ($rowCount) {
			$stories = kahuk_get_shared_stories(["group_id" => $group_id]);
		}
	}

	foreach($stories as $i => $story) {
		$main_smarty->assign('story', $story);
		$story_summary = $main_smarty->fetch($the_template . "/link_summary.tpl");

		$pageContent .= $hooks->apply_filters('kahuk_story_markup', $story_summary, $i);
	}

	/**
	 * Pagination - Stories
	 */
	$args = [
		"total" => $rowCount,
		"permalink" => $permalink,
	];

	$pagination = kahuk_pagination($args);
}

$main_smarty->assign('members', $members);
$main_smarty->assign('stories', $stories);
$main_smarty->assign('groupViewContent', $pageContent);
$main_smarty->assign('pagination', $pagination);

//
$main_smarty->assign('tpl_center', $the_template . "/group_center");
