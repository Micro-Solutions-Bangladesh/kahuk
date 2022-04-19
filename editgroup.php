<?php
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'group.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');
include(KAHUK_LIBS_DIR . 'csrf.php');

// global $globalGroups;

check_referrer();

$requestID = 0;

if (isset($_REQUEST['id'])) {
	$requestID = sanitize_number($_REQUEST['id']);
}

if (isset($_REQUEST['title'])) {
	$requestTitle = $db->escape(strip_tags($_REQUEST['title']));
}

/* Redwine: Roles and permissions and Groups fixes. We need the user_level to determine the site wide Admin & Moderators to give access according to their permissions */
global $main_smarty;
$isAdmin = $main_smarty->get_template_vars('isAdmin');
$isModerator = $main_smarty->get_template_vars('isModerator');
$main_smarty->assign('isAdmin', $isAdmin);
$main_smarty->assign('isModerator', $isModerator);

/* Redwine: Roles and permissions and Groups fixes. Check if the logged in user is the group creator */
$is_gr_creator = 0;
$gr_creator = get_group_creator($requestID);

if ($gr_creator == $current_user->user_id) {
	$is_gr_creator = 1;
}

$main_smarty->assign('is_gr_creator', $is_gr_creator);

//check if the logged in user is the group Admin or Moderator
$role = get_group_roles($requestID);
$is_gr_Admin = 0;
$is_gr_Moderator = 0;

if ($role == "admin") {
	$is_gr_Admin = 1;
} elseif ($role == "moderator") {
	$is_gr_Moderator = 1;
}

$main_smarty->assign('is_gr_Admin', $is_gr_Admin);
$main_smarty->assign('is_gr_Moderator', $is_gr_Moderator);

if ($is_gr_creator != 1 && $isAdmin != 1 && $isModerator != 1 && $is_gr_Admin != 1 && $is_gr_Moderator != 1) {
	//page redirect
	$redirect = '';
	$redirect = getmyurl("group_story", $requestID);
	//	header("Location: $redirect");
	die;
}

// pagename
define('pagename', 'editgroup');
$main_smarty->assign('pagename', pagename);

$CSRF = new csrf();

// uploading avatar
if (isset($_POST["avatar"]) && $_POST["avatar"] == "uploaded") {
	// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
	if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}

	// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'edit_group') == 2) {
		$user_image_path = "avatars/groups_uploaded" . "/";
		$user_image_apath = "/" . $user_image_path;
		$allowedFileTypes = array("image/jpeg", "image/gif", "image/png", 'image/x-png', 'image/pjpeg');
		unset($imagename);
		
		$myfile = $_FILES['image_file']['name'];
		$imagename = basename($myfile);
		$mytmpfile = $_FILES['image_file']['tmp_name'];
		
		if (!in_array($_FILES['image_file']['type'], $allowedFileTypes)) {
			$error['Type'] = 'Only these file types are allowed : jpeg, gif, png';
		}

		if (empty($error)) {
			$imagesize = getimagesize($mytmpfile);
			$width = $imagesize[0];
			$height = $imagesize[1];
			$idname = $_POST["idname"];
			
			if (!is_numeric($idname)) {
				die();
			}

			$imagename = $idname . "_original.jpg";
			$newimage = $user_image_path . $imagename;
			$result = @move_uploaded_file($_FILES['image_file']['tmp_name'], $newimage);
			
			if (empty($result)) {
				$error["result"] = "There was an error moving the uploaded file.";
			} else {
				$avatar_source = cleanit($_POST['avatarsource']);

				$sql = "UPDATE " . table_groups . " set group_avatar='uploaded' WHERE group_id=$idname";
				$db->query($sql);
				$main_smarty->assign('Avatar_uploaded', 'Avatar uploaded successfully! You may need to refresh the page to see the new image.');
			}
		}

		// create large avatar
		include KAHUK_LIBS_DIR . "class.pThumb.php";

		$img = new pThumb();
		$img->pSetSize(group_avatar_size_width, group_avatar_size_height);
		$img->pSetQuality(100);
		$img->pCreate($newimage);
		$img->pSave($user_image_path . $idname . "_" . group_avatar_size_width . ".jpg");
		$img = "";
	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}
} elseif (isset($_POST["action"])) {
	// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
	if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}

	// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'edit_group') == 2) {
		$errorMsg = '';
		$data = [];

		if (isset($_POST['group_name'])) {
			$group_name = $globalGroups->check_group_name($_POST['group_name']);

			if (is_kahuk_error($group_name)) {
				$errorMsg = $group_name->get_error_message();
			} else {
				$data['group_name'] = $group_name;
			}
		}

		if (isset($_POST['group_safename'])) {
			$group_safename = $globalGroups->check_group_safename($_POST['group_safename'], $requestID);

			if (is_kahuk_error($group_safename)) {
				$errorMsg = $group_safename->get_error_message();
			} else {
				$data['group_safename'] = $group_safename;
			}
		}

		if (empty($errorMsg) && isset($_POST['group_description'])) {
			$group_description = $globalGroups->check_group_description($_POST['group_description']);

			if (is_kahuk_error($group_description)) {
				$errorMsg = $group_description->get_error_message();
			} else {
				$data['group_description'] = $group_description;
			}
		}

		if (isset($_POST['group_vote_to_publish'])) {
			// $group_vote_to_publish = sanitize_number($_POST['group_vote_to_publish']);
			$data['group_vote_to_publish'] = sanitize_number($_POST['group_vote_to_publish']);
		}

		if (isset($_POST['group_notify_email']) && $_POST['group_notify_email'] > 0) {
			$data['group_notify_email'] = 1;
		} else {
			$data['group_notify_email'] = 0;
		}		

		if (empty($errorMsg)) {
			if (isset($_POST['group_privacy'])) {
				$data['group_privacy '] = sanitize_text_field($_POST['group_privacy']);
			}

			if (kahuk_check_user_role("admin")) {
				if (isset($_POST['group_status'])) {
					$group_status = sanitize_text_field($_POST['group_status']);

					if ($group_status == "Enable" || $group_status == "disable") {
						$data['group_status '] = $group_status;
					}					
				}
			}

			$isUpdated = $globalGroups->update($data, $requestID);

			if ($isUpdated) {
				$errorMsg = "Group updated!";
			} else {
				$errorMsg = "Group update failed!";
			}
		}

		$main_smarty->assign("errors", $errorMsg);



		// $group_name = $group_title;
		// $group_safename = makeUrlFriendly($group_title, true);

		// if (!$group_title) {
		// 	$errors = $main_smarty->get_config_vars('KAHUK_Visual_Group_Empty_Title');
		// } elseif ($group_vote_to_publish <= 0) {
		// 	$errors = $main_smarty->get_config_vars('KAHUK_Visual_Group_Empty_Votes');
		// } else {
		// 	$exists = $db->get_var("select COUNT(*) from " . table_groups . " WHERE group_name='$group_name' AND group_id != '$requestID'");

		// 	if ($exists) {
		// 		$errors = $main_smarty->get_config_vars('KAHUK_Visual_Group_Title_Exists');
		// 	}
		// }

		// $sql = "update " . table_groups . " set group_name = '" . $group_title . "', group_safename='$group_safename', group_description = '" . $group_description . "', group_privacy = '" . $group_privacy . "', group_vote_to_publish = '" . $group_vote_to_publish . "', group_notify_email=$group_notify_email where group_id = '" . $requestID . "'";

		// $db->query($sql);

		// $errors = $main_smarty->get_config_vars('KAHUK_Visual_Group_Saved_Changes');
		// $main_smarty->assign("errors", $errorMsg);

	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}
}

$CSRF->create('edit_group', true, true);

//displaying group as story
if (isset($requestID)) {
	group_display($requestID);
}

$main_smarty->assign('tpl_center', $the_template . '/edit_group_center');
$main_smarty->display($the_template . '/kahuk.tpl');

function cleanit($value)
{
	$value = strip_tags($value);
	$value = trim($value);
	return $value;
}
