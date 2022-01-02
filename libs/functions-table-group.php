<?php

/**
 * Count the rows of group in table_groups table
 * 
 * @since 5.0.4
 * 
 * @return int
 */
function kahuk_count_groups($customArgs = [])
{
    global $db;

    $defaults = [
        'status'       => [
            'Enable'
        ],
        'debug'        => false,
    ];

    $args = array_merge($defaults, $customArgs);

    $where = "group_status IN ('" . implode("','", $args['status']) . "')";

    $output = $db->count_rows(table_groups, 'group_id', $where);

    if ($args['debug']) {
        echo "<pre>Where Condition: {$where}\nOutput: {$output}</pre>";
    }

    // return output
    return $output;
}


/**
 * Reset global `$globalGroups` variable
 */
function kahuk_get_groups($customArgs = [])
{
    // global $db, $main_smarty, $rows, $page_size, $offset;
    global $db, $page_size;

    $defaults = [
        'columns_all'   => false,
        'columns'       => [
            'group_id',
            'group_creator', 'group_status', 
            'group_members', 'group_date', 
            'group_name', 'group_safename', 
            'group_description', 'group_privacy', 
            'group_avatar', 
            'group_vote_to_publish', 
            'group_field1', 'group_field2',
            'group_field3', 'group_field4', 
            'group_field5', 'group_field6', 
            'group_notify_email',
        ],
        'columns_minimum'       => [
            'group_id',
            'group_creator', 'group_status', 
            'group_members', 'group_date', 
            'group_name', 'group_safename', 
            'group_description', 'group_privacy', 
            'group_avatar',
        ],
        'status'        => [
            'Enable'
        ],
        'order_by'      => 'group_members DESC, group_date DESC',
        'output_type'    => 'array', // or object
        'debug'         => false,
    ];

    $args = array_merge($defaults, $customArgs);

    $pagesize = ((0 < $args['page_size']) ? $args['page_size'] : $page_size);
    $offset = (get_current_page() - 1) * $pagesize;

    // $orederby = ((empty($args['order_by']) ? '' : 'OREDE BY ' . $args['order_by']));

    $where = "group_status IN ('" . implode("','", $args['status']) . "')";

    if (!empty($args['order_by'])) {
        $where .= ' ORDER BY ' . $args['order_by'];
    }

    
    $sql = "SELECT " . implode(',', $args['columns']);

    if ($args['columns_all']) {
        $sql = "SELECT *";
    }

    $sql .= " FROM " . table_groups . " WHERE " . $where . " LIMIT {$offset}, {$pagesize}";

    $output = $db->get_results($sql);

    if ('array' == $args['output_type']) {
        $outputArray = [];
        $counter = 0;

        foreach($output as $row) {
            $outputColumns = $args['columns_minimum'];

            if ($args['columns_all']) {
                $outputColumns = $args['columns'];
            }

            foreach($outputColumns as $column_name) {
                $outputArray[$counter][$column_name] = $row->$column_name;
            }

            $counter++;
        }

        $output = $outputArray;
    }

    if ($args['debug']) {
        echo "<pre>pagesize: {$pagesize}, page_size: {$page_size}, SQL: {$sql}<br>";
        print_r($args);
        print_r($output);
        echo "</pre>";
    }

    return $output;
}

/**
 * Reset global `$globalGroups` variable
 */
function kahuk_reset_groups($args = [])
{
    global $globalGroups;
    // global $db, $main_smarty, $rows, $page_size, $offset;

    $globalGroups['count_enable'] = kahuk_count_groups($args);
    // $globalGroups['count_disable'] = kahuk_count_groups(['status' => ['disable']]);

    $globalGroups['rows'] = kahuk_get_groups($args);
}


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
