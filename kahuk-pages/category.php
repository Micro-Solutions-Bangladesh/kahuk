<?php
if (!$globalCategory) {
    kahuk_redirect_404();
    exit;
}

$storyStatuses = kahuk_story_statuses(false);
$storyStatusesPublic = kahuk_story_statuses();

/**
 * By default stories from 'new', and 'published' should display.
 * There is a option to find stories by status using `story-status` parameter in url.
 */
$storiesArgs = [];
$story_status = sanitize_text_field(strtolower(_request('story-status')));

if (($session_user_level == "admin") && in_array($story_status, $storyStatuses)) {
	$storiesArgs['link_status'] = [$story_status];
} else if (in_array($story_status, $storyStatusesPublic)) {
	$storiesArgs['link_status'] = [$story_status];
} else {
	$storiesArgs['link_status'] = $storyStatusesPublic;
}

/**
 * Set category in where clause
 */
$storiesArgs['where_clause'] = "link_category = '" . $globalCategory['category_id'] . "'";

/**
 * Stories
 */
$rowCount = kahuk_count_stories($storiesArgs);
$stories = [];

if ($rowCount) {
	$stories = kahuk_get_stories($storiesArgs);
} else {
	kahuk_set_session_message(
		"Be the first to submit a new story in the <strong>" . $globalCategory['category_name']. "</strong> category!"
	);

	kahuk_redirect();
	die();
}


/**
 * Creating Story Markup
 */
$stories_markup = '';

foreach($stories as $i => $story) {
	$main_smarty->assign('story', $story);
	$story_summary = $main_smarty->fetch($the_template . "/link_summary.tpl");

	$stories_markup .= $hooks->apply_filters('kahuk_story_markup', $story_summary, $i);
}

$main_smarty->assign('stories_markup', $stories_markup);


/**
 * Pagination
 */
$args = [
	'total' => $rowCount,
];

$main_smarty->assign('pagination', kahuk_pagination($args));

//
$main_smarty->assign('tpl_center', $the_template . '/stories_center');
