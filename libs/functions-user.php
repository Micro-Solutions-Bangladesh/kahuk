<?php
/**
 * Get Users
 * 
 * @since 5.0.8
 * 
 * return string
 */
function kahuk_users($args = []) {
	global $globalUsersObj;
    
    $rows = $globalUsersObj->get_users($args);

    if (!$rows) {
        $rows = [];        
    }

	return $rows;
}

/**
 * Get Users with meta information
 * 
 * @since 5.0.8
 * 
 * return string
 */
function kahuk_get_users($args = []) {
	global $hooks;
    
    $rows = kahuk_users($args);
    $output = [];

    foreach($rows as $row) {
        $output[] = $hooks->apply_filters('process_user', $row);
    }

	return $output;
}

/**
 * Delete USER permanently
 * 
 * @since 5.0.6
 * 
 * @return array
 */
function kahuk_delete_user_completely($user_id) {
    global $current_user, $globalUsersObj, $db;
    $output = [
        'status' => "error",
        'message' => '',
    ];

    if (
        $current_user && 
        $current_user->authenticated &&
        ($current_user->user_level == "admin")
    ) {
        $user = $globalUsersObj->get_user(['user_id' => $user_id]);

        if ($user) {
            if ($user['user_status'] == "enable") {
                $output['message'] = "User with the status ENABLED can not be deleted.";
            } elseif (($user['user_id'] == 1) || (in_array($user['user_level'], ["admin", "moderator"]))) {
                $output['message'] = "Moderator user is not deletable.";
            } else {
                $storiesDeleted = kahuk_delete_stories_by_user($user);

                // exit;

                $db->query("DELETE FROM " . table_group_member . " WHERE member_user_id = {$user_id}");
                $db->query("DELETE FROM " . table_group_shared . " WHERE share_user_id = {$user_id}");

                $db->query("DELETE FROM " . table_groups . " WHERE group_creator = {$user_id}");

                // Delete User
                $db->query("DELETE FROM " . table_users . " WHERE user_id = {$user_id}");

                $output["status"] = "success";
                $output["message"] = "User deleted (Including " . $storiesDeleted["total_deleted"] . " stories)";
            }
        } else {
            $output["message"] = "User not found!";
        }

    } else {
        kahuk_log_unexpected("UNAUTHENTIC USER DELETE REQUEST FOUND for {$user_id}");
        die("INVALID ACTION!");
    }

    return $output;
}
