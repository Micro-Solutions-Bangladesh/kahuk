<?php
if (!defined('KAHUK_AJAX')) {die();}

/**
 * Follow, Unfollow User
 */
if (in_array($action, ['follow', 'unfollow'])) {
	if (kahuk_is_user_authenticated()) {
        $current_user_id = kahuk_session_user_id();
        $target_user_login = sanitize_username(_post('user_login'));

		$returnOutput['toggle_action'] = '';
		$returnOutput['status'] = "fail";
		$returnOutput['message'] = "Appologies! something went wrong ...";

        if ($action == "follow") {
			$result = $globalFriendsObj->follow_user($target_user_login, $current_user_id);

			if (in_array($result, ['following', 'mutual'])) {
				$returnOutput['status'] = "success";
				$returnOutput['toggle_action'] = "unfollow";
				$returnOutput['message'] = "You followed @{$target_user_login}";
			}           
        } else {
			$result = $globalFriendsObj->unfollow_user($target_user_login, $current_user_id);

			if (in_array($result, ['follower', 'none'])) {
				$returnOutput['status'] = "success";
				$returnOutput['toggle_action'] = "follow";
				$returnOutput['message'] = "You unfollowed @{$target_user_login}";
			}
        }

        // $returnOutput['message-test'] = "current_user_id: {$current_user_id}, target_user_login: {$target_user_login}";
		
	} else {
        $returnOutput['status'] = "invalid";
    }

    echo json_encode($returnOutput);
	exit;
}
