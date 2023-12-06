<?php
/*****************************************************
 * All functions about meta of group/groups
 *****************************************************/

/**
 * Create an array of group meta information
 * 
 * @since 5.0.6
 * 
 * @return array cantaining: ...
 */
function get_group_meta($group_or_id, $args = []) {
    global $globalGroupsObj, $globalUsersObj;

    $output = [
        'avatar_size_with' => kahuk_get_config("_group_avatar_medium_width"),
    ];

    $group_id = 0;
    $group = [];

    if (is_numeric($group_or_id)) {
        $group_id = $group_or_id;
        $group = $globalGroupsObj->get_item($group_or_id);
    } else if (is_array($group_or_id) && isset($group_or_id['group_id'])) {
        $group_id = $group_or_id['group_id'];
        $group = $group_or_id;
    } else {
        die("Error: wrong group data!");
    }


    //
    $output['permalink'] = getmyurl("group_story_title", $group['group_safename']);

    //
    $output['avatar_image'] = kahuk_group_avatar_url($group_id, '', true);

    //
    $output['count_active_members'] = count_group_members(['group_id' => $group_id]);


    // Check if any user is logged in and wheather the user is the creator of the group
    // $output['is_group_admin'] = ($current_user->authenticated && ($current_user->user_id == $group['group_creator']));

    // Check if any user is logged in and role in the group
    // $session_user_membership = kahuk_user_membership_group($group_id);
    // $output['session_user_membership'] = $session_user_membership;
    // $output['session_user_role'] = (($session_user_membership['member_status']=="active") ? $session_user_membership['member_role'] : ""); // TODO to depricate the $output['is_group_admin']

    // Get group submitter user profile
    $creator_profile = $globalUsersObj->get_user_profile(['user_id' => $group['group_creator']]);
    $output['creator_profile'] = $creator_profile;
    


    //
    $output['submit_timeago'] = txt_time_diff(strtotime($group['group_date']));

    return $output;
}
