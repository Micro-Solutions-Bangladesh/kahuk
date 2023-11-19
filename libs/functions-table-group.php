<?php
/** 
 * Create the url of group avatar image
 * 
 * @since 5.0.6
 * 
 * @return string group avatar image url
 */
function kahuk_group_avatar_url($group_id, $img_size = '', $relative_path = false) {
    $img_size = (empty($img_size) ? group_avatar_size_width : $img_size);
    $output = KAHUK_URL_GROUP_AVATAR_MEDIUM;

    $file_path = KAHUK_GROUPS_AVATAR_DIR . $group_id . "_{$img_size}.jpg";

    if (file_exists($file_path)) {
        $output = KAHUK_FOLDER_GROUPS_AVATAR . "/" . $group_id . "_{$img_size}.jpg";

        $output = kahuk_create_url($output, [], $relative_path);
        $output .= "?v=" . filemtime($file_path);
    }

    return $output;
}

/** 
 * Count the number of groups joined by the loggedin user
 * 
 * @since 5.0.6
 * 
 * @return int|false
 */
function kahuk_count_groups_by_user() {
    global $db, $current_user;

    if (!$current_user->authenticated) {
        return false;
    }

    $sql = "SELECT COUNT(*) FROM " . table_group_member . " WHERE member_user_id = '{$current_user->user_id}'";

    return $db->get_var($sql);
}

/**
 * Collect user id as group creator id from group table
 * 
 * @since 5.0.0
 * 
 * @return int
 */
function get_group_creator($group_or_id) {
	global $db;

    $group_id = 0;

    if (is_numeric($group_or_id)) {
        $group_id = $group_or_id;
    } else if (is_array($group_or_id) && isset($group_or_id['group_id'])) {
        $group_id = $group_or_id['group_id'];
    } else {
        die("Error: wrong group data!");
    }

	$creator = $db->get_row("SELECT group_creator FROM " . table_groups . " WHERE group_id = {$group_id}");
	
    return $creator->group_creator;
}

/** 
 * Find out if the user has already joined the maximum allowable number of groups.
 * To enforce "Max Joinable Groups" config option.
 * 
 * Depricated in favour of kahuk_count_groups_by_user
 */
function reached_max_joinable_groups($db, $current_user) {
	$user_id = $current_user->user_id;

	if (!is_numeric($user_id)) {
		die();
	}

	$current_memberships = $db->get_var('SELECT COUNT(*) FROM ' . table_group_member . " WHERE member_user_id = '$user_id'");


	// return true;
	return $current_memberships >= max_groups_to_join;
}

/**
 * Get the user (from session) find membership for a group from db
 * 
 * @since 5.0.6
 * 
 * @return array or empty
 */
function kahuk_user_membership_group($group_id, $shortResult = true) {
	global $db, $current_user;

	if (!is_numeric($group_id)) {
		die("Wrong group id!");
	}

    if (!$current_user->authenticated) {
        return [];
    }

    $sql = "SELECT * FROM " . table_group_member . " WHERE member_group_id = {$group_id} AND member_user_id = '{$current_user->user_id}' ";
    
    $rs = $db->get_row($sql, ARRAY_A);

    if (!$rs) {
        return [];
    }

    // $output = [
    //     'member_role' => '',
    //     'member_status' => '',
    // ];

    $output = [
        'member_role' => $rs['member_role'],
        'member_status' => $rs['member_status'],
    ];

    if ($shortResult) {
        // if ($rs) {
            $output = [
                'member_role' => $rs['member_role'],
                'member_status' => $rs['member_status'],
            ];
        // }
    } else {
        // $output['member_id'] = '';
        // $output['member_user_id'] = '';
        // $output['member_group_id'] = '';


        // if ($rs) {
            $output['member_id'] = $rs['member_id'];
            $output['member_user_id'] = $rs['member_user_id'];
            $output['member_group_id'] = $rs['member_group_id'];
        // }
    }

	return $output;
}


/**
 * Depricated in favor of kahuk_user_membership_group()
 */
function isMember($group_id)
{
	global $db, $current_user;
	if (!is_numeric($group_id)) die();

	return $db->get_var("SELECT count(*) FROM " . table_group_member . " WHERE member_group_id = $group_id AND member_user_id = '" . $current_user->user_id . "' ");
}

/**
 * Depricated in favor of kahuk_user_membership_group()
 */
function isMemberActive($group_id)
{
	global $db, $current_user;
	if (!is_numeric($group_id)) die();

	return $db->get_var("SELECT member_status FROM " . table_group_member . " WHERE member_group_id = $group_id AND member_user_id = '" . $current_user->user_id . "' ");
}


/**
 * Get the user (from session) role from db for a group
 * 
 * Depricated in favor of kahuk_user_membership_group()
 * 
 * @since 5.0.6
 * 
 * @return string 'admin','normal','moderator','flagged','banned'
 */
function kahuk_user_role_for_group($group_id) {
	global $db, $current_user;

	if (!is_numeric($group_id)) {
		die("Wrong group id!");
	}

    $sql = "SELECT member_role FROM " . table_group_member . " WHERE member_group_id = {$group_id} AND member_user_id = {$current_user->user_id} AND member_status ='active'";

	$role = $db->get_row($sql);

	if (!empty($role)) {
		return $role->member_role;
	} else {
		return 'member';
	}
}


/**
 * Create link to share story into group
 * 
 * @return string URL|empty
 */
function kahuk_url_share_story_into_group($group_or_slug, $story_or_id, $user_id = 0) {
    global $session_user_id;

    if (!$user_id) {
        $user_id = $session_user_id;
    }
    
    $story_id = 0;

    if (is_numeric($story_or_id)) {
        $story_id = $story_or_id;
    } else if (is_array($story_or_id) && isset($story_or_id['link_id'])) {
        $story_id = $story_or_id['link_id'];
    } else {
        return '';
    }
    
    $params = [
        'prefix' => 'share',
        'sId' => $story_id,
        'uId' => $user_id,
    ];

    return kahuk_permalink_group($group_or_slug, $params);
}


/**
 * Get the list of groups of a user joined
 * 
 * @since 5.0.6
 * 
 * @return array
 */
function kahuk_get_groups_to_share_story_for_user($story_id, $user_id = '', $output_type = 'markup') {
    global $db, $current_user, $main_smarty, $the_template;

    if (empty($user_id)) {
        $user_id = $current_user->user_id;
    }

    $sql = "
        SELECT  DISTINCT t1.group_id,t1.group_name,t1.group_safename 
        FROM `" . table_groups . "` t1 
            LEFT JOIN `" . table_group_member . "` t2 
            ON t2.member_group_id=t1.group_id and t2.member_user_id = {$user_id} 
            LEFT JOIN `" . table_group_shared . "` t3 
            ON t3.share_user_id = t2.member_user_id 
        WHERE 
            t2.member_user_id = {$user_id} AND 
            t2.member_role !='banned' AND 
            t2.member_role !='flagged' AND 
            t2.member_status !='inactive' AND 
            t1.group_id NOT IN (
                SELECT `share_group_id` 
                FROM `" . table_group_shared . "` 
                WHERE `share_link_id` = " . $story_id . "
            )
    ";

    $rs = $db->get_results($sql, ARRAY_A);

    if (!$rs) {
        $rs = [];
    }

    if ($output_type == 'markup') {
        $output = '';

        foreach ($rs as $row) {
            // $output .= "<a class='group_member_share' href='" . my_base_url . my_kahuk_base . "/group_share.php?link_id=" . $this->id . "&group_id=" . $results->group_id . "&user_id=" . $current_user->user_id . "' >" . $results->group_name . "</a><br />";
            
            // print_r($row);

            $group_share_meta = [
                'user_id' => $user_id,
                'story_id' => $story_id,
                'group_id' => $row['group_id'],
                'group_name' => $row['group_name'],

                'share_url' => kahuk_url_share_story_into_group($row, $story_id, $user_id),
            ];

            $main_smarty->assign('group_share_meta', $group_share_meta);

            $output .= $main_smarty->fetch($the_template . "/template-parts/group-share-story.tpl");
        }

        return $output;
    }

    return $rs;
}

/**
 * 
 */
function create_markup_group_summery($group) {
    global $db, $main_smarty, $current_user;

    $group_id = $group['group_id'];
    $group_privacy = $group['group_privacy'];

    //smarty variables
    $main_smarty->assign('group', $group);

    //
    $group_story_url = getmyurl("group_story_title", $group['group_safename']);
    $main_smarty->assign('group_story_url', $group_story_url);

    //check member
    $main_smarty->assign('is_group_member', isMember($group_id));

    //check isMemberActive
    $main_smarty->assign('is_member_active', isMemberActive($group_id));

    // Joining and unjoining member links
    // Set the url to an empty string if the user has already joined the maximum
    // allowable number of groups
    
    if (reached_max_joinable_groups($db, $current_user)) {
        $join_url = '';
    } else {
        $join_url = getmyurl('join_group', $group_id, $group_privacy);
    }

    // $join_url = getmyurl('join_group', $group_id, $group_privacy);

// echo $group_id . " :: " . $group_privacy;

    //
    $main_smarty->assign('join_group_url', $join_url);
    // $main_smarty->assign('unjoin_group_url', getmyurl("unjoin_group", $group_id));
    $main_smarty->assign('unjoin_group_url', getmyurl("unjoin_group", $group_id, $group_privacy));

    //check logged or not
    $main_smarty->assign('user_logged_in', $current_user->user_login);


    $main_smarty->assign('join_group_privacy_url', $join_url);

    $main_smarty->assign('join_group_withdraw', getmyurl("join_group_withdraw", $group_id, $current_user->user_id));

    //
    $group_edit_url = getmyurl("editgroup", $group_id);
    $main_smarty->assign('group_edit_url', $group_edit_url);

    //
    $group_delete_url = getmyurl("deletegroup", $group_id);
    $main_smarty->assign('group_delete_url', $group_delete_url);

    /* initializing $group_output to eliminate the Notice:  Undefined variable: group_output. */
    $group_output = $main_smarty->fetch(THE_TEMPLATE . '/group_summary.tpl');

    return $group_output;		
}



/**
 * Count the list of users in a group by group id and status
 * 
 * @since 5.0.6
 * 
 * @return int number
 */
function count_group_members($argsCustom = []) {
	global $db;

    $defaults = [
		'group_id' => '',
        'member_status' => ['active'],
	];

	$args = array_merge($defaults, $argsCustom);

	if (!is_numeric($args['group_id'])) {
		die();
	}

    $sql = "SELECT COUNT(*) FROM " . table_group_member . " WHERE member_group_id = " . $args['group_id'];
    $sql .= " AND member_status IN ('" . implode("','", $args['member_status']). "')";

	return $db->get_var($sql);
}

/**
 * Get the list of users in a group
 * 
 * @params $group_ids array required
 * @params $customArgs array optional
 * 
 * @return array | empty
 */
function kahuk_get_members_by_group($group_ids, $customArgs = [])
{
    global $db, $page_size, $globalUsersObj;

    $defaults = [       
        "columns_all"   => true,

        'columns'       => [
            'member_id',
            'member_user_id',
            'member_group_id', 
            'member_role',
            'member_status',
        ],

        "page_size" => 0,
        "member_status" => '',
        'output_type'    => 'array', // or empty (consider as object)
        "debug" => false,
    ];

    $args = array_merge($defaults, $customArgs);

    $where = "member_user_id != 0 AND member_group_id IN ('" . implode(",", $group_ids) . "')";

    if (!empty($args['member_status'])) {
        $where .= " AND member_status = '" . $args['member_status'] . "'";
    }

    $pagesize = ((0 < $args['page_size']) ? $args['page_size'] : $page_size);
    $offset = (get_current_page() - 1) * $pagesize;
    
    $sql = "SELECT *";

    if (!($args['columns_all'] || empty($args['columns']))) {
        $sql = "SELECT " . implode(',', $args['columns']);
    }

    $sql .= " FROM " . table_group_member . " WHERE " . $where . " LIMIT {$offset}, {$pagesize}";

    $output = $db->get_results($sql);

    if ('array' == $args['output_type']) {
        $outputArray = [];
        $counter = 0;

        $outputColumns = $args['columns'];

        foreach($output as $row) {
            foreach($outputColumns as $column_name) {
                $outputArray[$counter][$column_name] = $row->$column_name;
            }

            $outputArray[$counter]["user"] = $globalUsersObj->get_user_profile(['user_id' => $row->member_user_id]);

            $counter++;
        }

        $output = $outputArray;
    }

    return $output;
}
