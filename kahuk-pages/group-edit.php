<?php
if (kahuk_redirect_group_access()) {
    kahuk_redirect_404();
    die();
}

// if (!$globalGroup) {
//     kahuk_redirect_404();
//     exit;
// }

include_once KAHUK_LIBS_DIR . 'group.php';

//
$group_id = $globalGroup['group_id'];
$group_creator = $globalGroup['group_creator'];
$group_safename = $globalGroup['group_safename'];

$url_redirect = kahuk_permalink_group($group_safename);

// Only Admin or Group Creator/Admins are allowed to update group
if (!((in_array($session_user_level, ['admin'])) || ($group_creator == $session_user_id))) {
    kahuk_set_session_message(
        'Invalid action!',
        'warning'
    );

    kahuk_redirect($url_redirect);
    die();
}

// 
include(KAHUK_LIBS_DIR . 'csrf.php');
$CSRF = new csrf();

/**
 * 
 */
if (isset($_POST["action"])) {
    if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		die();
	}

    // if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize_text_field($_POST['token']), 'edit_group') == 2) {

        $action = _post('action');

        if ($action == 'groupavatar') {
            if (!in_array($_FILES['image_file']['type'], $fileTypesUploadAllowed)) {
                kahuk_set_session_message(
                    'Only these file types are allowed : jpeg, gif, png.',
                    'notice'
                );
            
                kahuk_redirect($permalink);
                die();
            }

            $file_size = $_FILES['image_file']['size'];

            if (($file_size/1024) > max_group_avatar_size) {
                kahuk_set_session_message(
                    'Maximum file size for group avatar is ' . max_group_avatar_size . ' KB!',
                    'notice'
                );
            
                kahuk_redirect($permalink);
                die();
            }

            if (($file_size/1024) < 50) {
                kahuk_set_session_message(
                    'Minimum file size for group avatar is 50 KB!',
                    'notice'
                );
            
                kahuk_redirect($permalink);
                die();
            }

            $original_image = KAHUK_GROUPS_AVATAR_DIR . "{$group_id}_original.jpg";
            $thumb_image = KAHUK_GROUPS_AVATAR_DIR . "{$group_id}_" . group_avatar_size_width . ".jpg";

            $result = @move_uploaded_file($_FILES['image_file']['tmp_name'], $original_image);
            
            if (empty($result)) {
                kahuk_set_session_message(
                    'There was an error moving the uploaded file.',
                    'notice'
                );
            
                kahuk_redirect($permalink);
                die();
            } else {
                $sql = "UPDATE " . table_groups . " set group_avatar='uploaded' WHERE group_id=$group_id";
                $db->query($sql);

                kahuk_set_session_message(
                    'Group avatar changed.',
                    'success'
                );

                // create large avatar
                include KAHUK_LIBS_DIR . "class.pThumb.php";

                $img = new pThumb();
                $img->pSetSize(group_avatar_size_width, group_avatar_size_height);
                $img->pSetQuality(100);
                $img->pCreate($original_image);
                $img->pSave($thumb_image);
                $img = "";
            
                kahuk_redirect($permalink);
                die();
            }
        }

        if ($action == 'groupedit') {
            $data = [];
            $reladGlobalGroup = false;

            //
            if (isset($_POST['group_name'])) {
                $group_name = $globalGroupsObj->check_group_name($_POST['group_name']);
    
                if (is_kahuk_error($group_name)) {
                    kahuk_set_session_message(
                        $group_name->get_error_message(),
                        'notice'
                    );
                
                    kahuk_redirect($permalink);
                    die();
                } else {
                    $data['group_name'] = $group_name;
                }
            }

            //
            if (isset($_POST['group_safename'])) {
                $group_safename = $globalGroupsObj->check_group_safename($_POST['group_safename'], $group_id);
    
                if (is_kahuk_error($group_safename)) {
                    kahuk_set_session_message(
                        $group_safename->get_error_message(),
                        'notice'
                    );
                
                    kahuk_redirect($permalink);
                    die();
                } else {
                    $data['group_safename'] = $group_safename;
                    $reladGlobalGroup = true;
                }
            }

            //
            if (isset($_POST['group_description'])) {
                $group_description = $globalGroupsObj->check_group_description($_POST['group_description']);
    
                if (is_kahuk_error($group_description)) {
                    kahuk_set_session_message(
                        $group_description->get_error_message(),
                        'notice'
                    );
                
                    kahuk_redirect($permalink);
                    die();
                } else {
                    $data['group_description'] = $group_description;
                }
            }

            //
            $group_privacy = _post('group_privacy');

            if (in_array($group_privacy, $globalGroupPrivacies)) {
				$data['group_privacy '] = sanitize_text_field($_POST['group_privacy']);
			}

            //
			if (kahuk_check_user_role("admin")) {
                //
                $group_status = sanitize_text_field(_post('group_status'));

				if (in_array($group_status, $globalGroupStatuses)) {
                    $data['group_status '] = $group_status;
				}
			}

            //
			$isUpdated = $globalGroupsObj->update($data, $group_id);

			if ($isUpdated) {
                kahuk_set_session_message(
                    'Group updated!',
                    'success'
                );
			} else {
                kahuk_set_session_message(
                    'Group update failed!',
                    'notice'
                );

                $reladGlobalGroup = false;
			}

            if ($reladGlobalGroup) {
                $globalGroup = $globalGroupsObj->get_global_group($group_id);
                $permalink = kahuk_permalink_group($globalGroup);
            }

            kahuk_redirect($permalink);
            die();
        }
    }
}

// 
$CSRF->create('edit_group', true, true);

//
$group = $globalGroup;

$group['meta'] = get_group_meta($globalGroup);

// $group['meta_test'] = print_r($group['meta'], true);

$main_smarty->assign('group', $group);

// group_display($group);

//
$main_smarty->assign('tpl_center', $the_template . "/edit_group_center");
