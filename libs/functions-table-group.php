<?php
/**
 * Return group submitter username
 */
function get_group_submitter_username($user_id)
{
	if (!is_numeric($user_id)) {
		die();
	}

	$user = new User;
	$user->id = $user_id;
	$user->read('short');

	return $user->username;
}

/**
 * 
 */
function create_markup_group_summery($group) {
    global $db, $main_smarty, $the_template, $current_user;

    $group_id = $group['group_id'];
    $group_avatar = $group['group_avatar'];
    $group_creator = $group['group_creator'];
    $group_privacy = $group['group_privacy'];

    //smarty variables
    $main_smarty->assign('group_row', $group);

    //
    if ($current_user->user_id == $group_creator) {
        $main_smarty->assign('is_group_admin', 1);
    }

    //get group avatar path
    if ($group_avatar == "uploaded" && file_exists(KAHUKPATH . "avatars/groups_uploaded/" . $group_id . "_" . group_avatar_size_width . ".jpg")) {
        $imgsrc = my_base_url . my_kahuk_base . "/avatars/groups_uploaded/" . $group_id . "_" . group_avatar_size_width . ".jpg";
    } else {
        $imgsrc = my_base_url . my_kahuk_base . "/templates/" . $the_template . "/img/group_large.gif";
    }

	$main_smarty->assign('imgsrc', $imgsrc);

    //
    // $main_smarty->assign('group_avatar_url', getmyurl('group_avatar', $group_id));

    //
    $group_story_url = getmyurl("group_story_title", $group['group_safename']);
    $main_smarty->assign('group_story_url', $group_story_url);

    //
    $group_submitter = get_group_submitter_username($group_creator);
	$main_smarty->assign('group_submitter', $group_submitter);

    //
    $submitter_profile_url = getmyurl('user', $group_submitter);
    $main_smarty->assign('submitter_profile_url', $submitter_profile_url);

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

    //
    $main_smarty->assign('join_group_url', $join_url);
    $main_smarty->assign('unjoin_group_url', getmyurl("unjoin_group", $group_id));

    //check logged or not
    $main_smarty->assign('user_logged_in', $current_user->user_login);


    $main_smarty->assign('join_group_privacy_url', $join_url);

    $main_smarty->assign('unjoin_group_url', getmyurl("unjoin_group", $group_id, $group_privacy));
    $main_smarty->assign('join_group_withdraw', getmyurl("join_group_withdraw", $group_id, $current_user->user_id));

    //
    $group_edit_url = getmyurl("editgroup", $group_id);
    $main_smarty->assign('group_edit_url', $group_edit_url);

    //
    $group_delete_url = getmyurl("deletegroup", $group_id);
    $main_smarty->assign('group_delete_url', $group_delete_url);

    /* initializing $group_output to eliminate the Notice:  Undefined variable: group_output. */
    $group_output = $main_smarty->fetch(The_Template . '/group_summary.tpl');

    return $group_output;		
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
    global $db, $page_size, $globalUsers;

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

            $outputArray[$counter]["user"] = $globalUsers->get_user_profile($row->member_user_id);

            $counter++;
        }

        $output = $outputArray;
    }

    if ($args['debug']) {
        echo "<pre>SQL: {$sql}<br>";
        print_r($args);
        print_r($output);
        echo "</pre>";
    }

    return $output;
}
