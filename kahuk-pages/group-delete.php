<?php
if (!$globalGroup) {
    kahuk_redirect_404();
    exit;
}

check_referrer();

force_authentication();

// 
include(KAHUK_LIBS_DIR . 'csrf.php');
$CSRF = new csrf();

$url_redirect = kahuk_permalink_group();
$action = _post('action');

if ($action == 'groupdelete') {
    if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		die();
	}

    // if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'delete_group') == 2) {
        if ($session_user_level == 'admin') {
            $group_id = $globalGroup['group_id'];

            // Delete all stories shared in this group
            $sql = "DELETE FROM " . table_group_shared . " WHERE share_group_id = {$group_id}";
            $db->query($sql);

            // Delete all user/member in this group
            $sql = "DELETE FROM " . table_group_member . " WHERE member_group_id = {$group_id}";
            $member_delete = $db->query($sql);

            // Delete group avatar image - original
            $original_image = KAHUK_GROUPS_AVATAR_DIR . "{$group_id}_original.jpg";

            if (file_exists($original_image)) {
                unlink($original_image);
            }

            // Delete group avatar image - thumb
            $thumb_image = KAHUK_GROUPS_AVATAR_DIR . "{$group_id}_" . group_avatar_size_width . ".jpg";

            if (file_exists($thumb_image)) {
                unlink($thumb_image);
            }

            // Delete the group
            $sql = "DELETE FROM " . table_groups . " WHERE group_id = {$group_id}";
            $group_delete = $db->query($sql);
            
            kahuk_set_session_message(
                'Group Deleted!',
                'success'
            );
        }
    } else {
        kahuk_set_session_message(
            kahuk_language_config('KAHUK_LANG_INVALID_ACTION'),
            'warning'
        );
    }
}

kahuk_redirect($url_redirect);
die();
