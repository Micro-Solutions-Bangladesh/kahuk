<?php
if ($globalUser) {
    /**
     * Just redirect to Home for 'spammer', or 'unverified' users.
     * Because the are not suppose to be available online.
     */
    if (in_array($globalUser['user_level'], ['spammer','unverified'])) {
        kahuk_redirect($kahuk_root);
    }
} else {
    /**
     * Wrong user name found, redirect to 404 page.
     */
    kahuk_redirect_404();
    die();
}

// print_r($globalUser);
// die("********* Got User *********");


/**
 * Story Status By User
 */
$story_statuses_public = kahuk_story_statuses();
$story_status = _get('story-status', ''); // 'new', 'trending'
$storyStatus = [];

if (in_array($story_status, $story_statuses_public)) {
    $storyStatus = [$story_status];
} else {
    $storyStatus = $story_statuses_public;
}

/**
 * 
 */
$user_id = $globalUser['user_id'];
$user_login = $globalUser['user_login'];

//
$url_user_profile = kahuk_create_url("user/{$user_login}/", []);
$main_smarty->assign('url_user_profile', $url_user_profile);

$url_user_following = add_query_arg(['task' => 'following'], $url_user_profile);
$main_smarty->assign('url_user_following', $url_user_following);

$url_user_followers = add_query_arg(['task' => 'followers'], $url_user_profile);
$main_smarty->assign('url_user_followers', $url_user_followers);



//
$user_total_comments = kahuk_count_comments_by_user($user_id);
$main_smarty->assign('user_total_comments', $user_total_comments);

//
$sql = "SELECT count(*) FROM " . table_votes . "," . table_links . " WHERE link_id = vote_link_id";

$storyStatusesPublic = kahuk_story_statuses();
$sql .= " AND link_status IN ('" . implode("','", $storyStatusesPublic) . "') AND vote_user_id = {$user_id}";
$user_total_votes = $db->get_var($sql);
$main_smarty->assign('user_total_votes', $user_total_votes);

//
$main_smarty->assign('following', $globalFriendsObj->get_list_following($user_id));
$main_smarty->assign('followers', $globalFriendsObj->get_list_follower($user_id));

// 
$friendshipType = '';

if ($user_authenticated && ($session_user_id != $user_id)) {
    $friendshipType = $globalFriendsObj->get_friend_status($user_id, $session_user_id);
}

$main_smarty->assign('friendship_type', $friendshipType);


/**
 * Group Display
 */
// user_group_read($user_id);

$sql = "
    SELECT * FROM " . table_group_member . "  	
        LEFT JOIN " . table_groups . " ON group_id = member_group_id
    WHERE member_user_id = $user_id 
        AND member_status = 'active'
        AND group_status = 'enable'
        ORDER BY group_members DESC";

// echo "<pre>Group Display SQL: {$sql}</pre>";

$groups = $db->get_results($sql);
$group_display = '';

if ($groups) {
    foreach($groups as $groupid){
        $group_display .= "<tr><td><a href='".getmyurl("group_story_title", $groupid->group_safename)."'>".$groupid->group_name."</a></td><td style='text-align:center;'>".$groupid->group_members."</td></tr>"; 
    }
}

$main_smarty->assign('groups', $groups);
$main_smarty->assign('group_display', $group_display);


/**
 * User Stories
 */
$where_clause = "links.link_author = {$user_id}";
$storiesCount = [];
$userStoriesTotal = 0;

foreach($story_statuses_public as $v) {
    $args = [
        'link_status' => [$v],
        'where_clause' => $where_clause,
    ];

    $storiesCount[$v] = kahuk_count_stories($args);

    $main_smarty->assign("user_stories_{$v}_total", $storiesCount[$v]);

    $userStoriesTotal += $storiesCount[$v];
}

$main_smarty->assign("user_stories_total", $userStoriesTotal);


//
$curTotalStories = 0;

foreach($storyStatus as $v) {
    $curTotalStories += $storiesCount[$v];
}

$stories = [];
$stories_markup = '';

if ($curTotalStories) {
    $storiesArgs = [
        'where_clause' => $where_clause,
        "link_status" => $storyStatus,
    ];

    $stories = kahuk_get_stories($storiesArgs);

    foreach($stories as $i => $story) {
        $main_smarty->assign('story', $story);
        $story_summary = $main_smarty->fetch($the_template . "/link_summary.tpl");
    
        $stories_markup .= $hooks->apply_filters('kahuk_story_markup', $story_summary, $i);
    }
}

$main_smarty->assign('stories_markup', $stories_markup);



/**
 * Pagination - Stories
 */
$args = [
	'total' => $curTotalStories,
    'permalink' => $url_user_profile,
];

$main_smarty->assign('pagination', kahuk_pagination($args));


//
$main_smarty->assign('user', $globalUser);

$main_smarty->assign('tpl_center', $the_template . '/user_profile_center');

