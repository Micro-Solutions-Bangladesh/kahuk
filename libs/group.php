<?php

/**
 * 
 */
//to return name from userid
function get_group_user_email($uid)
{
	$user_id = $uid;

	if (!is_numeric($user_id)) {
		die();
	}

	$user = new User;
	$user->id = $user_id;
	$user->read('short');

	return $user->email;
}

/**
 * 
 */
//returns the groups of the loggedin user
function get_groupid_user($userid)
{
	global $db, $current_user;

	if (!is_numeric($userid)) {
		die();
	}

	$group_array = array();
	// DB 01/08/09
	$groupdetail = $db->get_results("SELECT DISTINCT (group_id) AS group_id FROM " . table_group_member . " WHERE member_user_id =" . $userid . "");

	$group_array = $groupdetail;

	return $group_array;
}

/**
 * 
 */
function get_groupdetail_user()
{
	global $db, $current_user;

	// DB 01/08/09
	$groupdetail = $db->get_results("SELECT DISTINCT (group_id) AS group_id,group_name FROM " . table_group_member . " WHERE member_user_id ='" . $current_user->user_id . "'");
	//	$groupdetail = $db->get_results("SELECT DISTINCT (group_id) AS group_id,group_name FROM " . table_group_member . " , " . table_groups . " , ".table_links." WHERE link_group_id = member_group_id AND member_group_id = group_id AND member_user_id ='".$current_user->user_id ."'");
	/////
	return $groupdetail;
}

/**
 * Set single group in main_smarty
 * TODO Delete $requestID parameter or delete the function itself
 */
function group_display($group_or_id) {
	global $db, $main_smarty;

	$group = $group_or_id;

	if (is_numeric($group_or_id)) {
		$group = $db->get_row("SELECT * FROM " . table_groups . " WHERE group_id = {$group_or_id}", ARRAY_A);		
	}

	// print_r($group);

	if ($group) {
		// die("***");
		$group_id = $group['group_id'];
		$group_name = $group['group_name'];
		$group_safename = $group['group_safename'];
		$group_description = $group['group_description'];
		$group_creator = $group['group_creator'];
		$group_status = $group['group_status'];
		$group_members = $group['group_members'];
		$group_date = $group['group_date'];
		$group_privacy = $group['group_privacy'];
		$group_avatar = $group['group_avatar'];
		
		$date = $db->get_var("SELECT DATE_FORMAT(group_date, '%b, %e %Y') FROM " . table_groups . " WHERE group_id = {$group->group_id}");
		//echo $date;
		$group_date = $date;
		//$group_date = date('M j, Y', $group->group_date);

		//smarty variables	
		$main_smarty->assign('pretitle', "$group_name - $group_description");
		$main_smarty->assign('group_id', $group_id);
		$main_smarty->assign('group_name', $group_name);
		$main_smarty->assign('group_safename', $group_safename);
		$main_smarty->assign('group_description', $group_description);
		$main_smarty->assign('group_creator', $group_creator);
		$main_smarty->assign('group_status', $group_status);
		$main_smarty->assign('group_members', $group_members);
		$main_smarty->assign('group_privacy', $group_privacy);
		$main_smarty->assign('group_avatar', $group_avatar);
		$main_smarty->assign('group_date', $group_date);

		//get group avatar path
		$imgsrc = kahuk_get_config("_avatar_group_medium");
		$group_avatar_medium_width = kahuk_get_config("_group_avatar_medium_width");

		if (
			allow_groups_avatar == 'true' && 
			$group_avatar == "uploaded" && 
			is_readable(KAHUKPATH . "avatars/groups_uploaded/" . $group_id . "_" . $group_avatar_medium_width . ".jpg")
		) {
			$imgsrc = KAHUK_BASE_URL . "/avatars/groups_uploaded/" . $group_id . "_" . $group_avatar_medium_width . ".jpg";
		}

		$main_smarty->assign('imgsrc', $imgsrc);

		//get group creator and his urls
		$g_name = kahuk_user_login_by_id($group_creator);
		$main_smarty->assign('group_submitter', $g_name);
		$main_smarty->assign('submitter_profile_url', getmyurl('user', $g_name));
		$main_smarty->assign('group_avatar_url', getmyurl('group_avatar', $group_id));

		//check group admin
		global $current_user;
		/* Redwine: Roles and permissions and Groups fixes */
		//$canIhaveAccess = $canIhaveAccess + checklevel('admin');
		//$canIhaveAccess = $canIhaveAccess + checklevel('moderator');
		if ($current_user->user_id == $group_creator) {
			$main_smarty->assign('is_group_admin', 1);
		}
		// Get the Group Admin/Moderator to use the assigned permissions
		$gr_roles = kahuk_user_role_for_group($group_id);
		$is_gr_Admin = 0;
		$is_gr_Moderator = 0;
		if ($gr_roles == "admin") {
			$is_gr_Admin = 1;
		} elseif ($gr_roles == "moderator") {
			$is_gr_Moderator = 1;
		}
		$main_smarty->assign('is_gr_Admin', $is_gr_Admin);
		$main_smarty->assign('is_gr_Moderator', $is_gr_Moderator);

		//check member
		//include_once(KAHUK_LIBS_DIR.'group.php');
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


		$main_smarty->assign('join_group_url', $join_url);
		$main_smarty->assign('join_group_privacy_url', $join_url);

		$main_smarty->assign('unjoin_group_url', getmyurl("unjoin_group", $group_id, $group_privacy));
		$main_smarty->assign('join_group_withdraw', getmyurl("join_group_withdraw", $group_id, $current_user->user_id));

		//check logged or not
		$main_smarty->assign('user_logged_in', $current_user->user_login);

		//$main_smarty->assign('form_action', $_SERVER["PHP_SELF"]);
		$group_story_url = getmyurl("group_story_title", $group_safename);
		$main_smarty->assign('group_story_url', $group_story_url);
		$main_smarty->assign('form_action', $group_story_url);
		$main_smarty->assign('edit_form_action', getmyurl("editgroup", $group_id));
		$group_array = array($group_name, $group_description, $group_privacy);
		
		return $group_array;
	}
}
