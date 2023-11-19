<?php

/**
 * 
 */
$dblang = 'en';

$main_smarty->assign('my_base_url', my_base_url);
$main_smarty->assign('my_kahuk_base', my_kahuk_base);
$main_smarty->assign('kahuk_base_url', KAHUK_BASE_URL);

$main_smarty->assign('page_task', $pageTask);

$upname = kahuk_build_unique_page();
$main_smarty->assign('upname', $upname);

$pagename = kahuk_get_pagename();
$main_smarty->assign('pagename', $pagename);

// Check maintenance mode
$maintenance_mode = kahuk_get_config('maintenance_mode');
$main_smarty->assign('maintenance_mode', $maintenance_mode);


define('MAX_NUMBER_OF_WORD_STORY_DESC', (int) kahuk_get_config('MAX_NUMBER_OF_WORD_STORY_DESC'));


$kahuk_root = kahuk_root_url();
$main_smarty->assign('kahuk_root', $kahuk_root);

$kahuk_dashboard_url = kahuk_create_url('admin/admin_index.php');
$main_smarty->assign('kahuk_dashboard_url', $kahuk_dashboard_url);

$permalink = kahuk_get_permalink();
$main_smarty->assign('permalink', $permalink);

$kahuk_url_ajax = kahuk_url_ajax();
$main_smarty->assign('kahuk_url_ajax', $kahuk_url_ajax);

//
$redirectto = kahuk_get_permalink();
$exclude_redirectPages = [
    'group-delete',
    'login',
    'password-reset',
    'recover',
    'register-complete',
    'register',
    'validation',
    'submit-comment',
];
if (in_array($pagename, $exclude_redirectPages)) {
    $redirectto = '';
}
$page_login_url = kahuk_url_login($redirectto);
$main_smarty->assign('page_login_url', $page_login_url);

//
$page_logout_url = kahuk_url_logout();
$main_smarty->assign('page_logout_url', $page_logout_url);

//
$page_register_url = kahuk_create_url("register");
$main_smarty->assign('page_register_url', $page_register_url);

//
$page_submit_url_begin = kahuk_create_url("submit", ["pn" => "begin"]);
$main_smarty->assign('page_submit_url_begin', $page_submit_url_begin);

$page_submit_url = kahuk_create_url("submit");
$main_smarty->assign('page_submit_url', $page_submit_url);

//
$page_submit_comment = kahuk_create_url("submit-comment");
$main_smarty->assign('page_submit_comment', $page_submit_comment);

//
$page_groups_url = kahuk_create_url("groups");
$main_smarty->assign('page_groups_url', $page_groups_url);

$page_group_submit_url = kahuk_create_url("submit-group");
$main_smarty->assign('page_group_submit_url', $page_group_submit_url);

//
$page_pass_reset_url = kahuk_url_pass_reset();
$main_smarty->assign('page_pass_reset_url', $page_pass_reset_url);

//
$page_topusers_url = kahuk_create_url('topusers');
$main_smarty->assign('page_topusers_url', $page_topusers_url);

//
$page_search_url = kahuk_create_url('search');
$main_smarty->assign('page_search_url', $page_search_url);

// TODO
$page_rssfeeds_url = kahuk_create_url('rssfeeds');
$main_smarty->assign('page_rssfeeds_url', $page_rssfeeds_url);


/**
 * Create new, trending, and published pages URLs
 */
//
$page_new_story_url = kahuk_create_url('new');
$main_smarty->assign('page_new_story_url', $page_new_story_url);

//
$page_trending_story_url = kahuk_create_url('trending');
$main_smarty->assign('page_trending_story_url', $page_trending_story_url);

//
$page_published_story_url = kahuk_create_url('published');
$main_smarty->assign('page_published_story_url', $page_published_story_url);

//
$storyDurations = [
    'day', 'week', 'month', 'year',
];

$urlsStoryDurations = [];

foreach($storyDurations as $value) {
    $urlsStoryDurations['published'][$value] = add_query_arg(['duration' => $value], $page_published_story_url);
    $urlsStoryDurations['new'][$value] = add_query_arg(['duration' => $value], $page_new_story_url);
    $urlsStoryDurations['trending'][$value] = add_query_arg(['duration' => $value], $page_trending_story_url);
}

$main_smarty->assign('urls_story_durations', $urlsStoryDurations);

/**
 * Check User Sessions
 */
$user_authenticated = kahuk_is_user_authenticated();
$main_smarty->assign('user_authenticated', $user_authenticated);

$session_user_level = kahuk_session_user_level();
$main_smarty->assign('session_user_level', $session_user_level);

$session_user_login = kahuk_session_user_login();
$main_smarty->assign('session_user_login', $session_user_login);

$session_user_id = kahuk_session_user_id();
$main_smarty->assign('session_user_id', $session_user_id);


/**
 * 
 */
$allowed_html_tags = '';
$login_special_attribute = '';

if ($user_authenticated) {
    //
    $page_profile_url = kahuk_create_url("user/{$session_user_login}/", []);
    $main_smarty->assign('page_profile_url', $page_profile_url);

    $page_profile_settings = add_query_arg(['prefix' => 'settings'], $page_profile_url);
    $main_smarty->assign('page_profile_settings', $page_profile_settings);

    $page_profile_history = add_query_arg(['task' => 'history'], $page_profile_url);
    $main_smarty->assign('page_profile_history', $page_profile_history);

    $page_profile_upvoted = add_query_arg(['task' => 'upvoted'], $page_profile_url);
    $main_smarty->assign('page_profile_upvoted', $page_profile_upvoted);

    $page_profile_downvoted = add_query_arg(['task' => 'downvoted'], $page_profile_url);
    $main_smarty->assign('page_profile_downvoted', $page_profile_downvoted);

    $page_profile_saved = add_query_arg(['task' => 'saved'], $page_profile_url);
    $main_smarty->assign('page_profile_saved', $page_profile_saved);

    //
    $allowed_html_tags = kahuk_get_config("_allowed_html_tags_{$session_user_level}", '');

} else {
    $login_special_attribute = 'data-bs-toggle="modal" data-bs-target="#LoginModal"';
}

$main_smarty->assign('allowed_html_tags', $allowed_html_tags);
define('allowed_html_tags', $allowed_html_tags);

$main_smarty->assign('login_special_attribute', $login_special_attribute);



/**
 * Check for session messages
 */
$session_messages = kahuk_get_session_messages();
// print_r($session_messages);

if (empty($session_messages)) {
	$main_smarty->assign('session_messages', '');
} else {
	$main_smarty->assign('session_messages', $session_messages);
}

/**
 * Store messages of actions in pages and assign right before the display assign.
 */
$action_messages = [];
