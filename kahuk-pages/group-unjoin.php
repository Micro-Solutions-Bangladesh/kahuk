<?php
if (!$globalGroup) {
    // TODO Add a session message for 404 page
    kahuk_redirect_404();

    exit;
}

check_referrer();

if (!$user_authenticated) {
    die("Invalid action!");
}


$group_id = $globalGroup['group_id'];
$group_safename = $globalGroup['group_safename'];
$url_redirect = kahuk_permalink_group($group_safename);

// 
$group_membership = kahuk_user_membership_group($group_id);

if (empty($group_membership)) {
    // Check if user already unjoined or never joined
    kahuk_set_session_message(
        'Invalid action found.',
        'notice'
    );

    kahuk_redirect($url_redirect);
    die();

} else if ($group_membership['member_status'] == 'admin') {
    // Check if the user is admin/creator
    kahuk_set_session_message(
        'An Admin can not be deleted from group members!',
        'notice'
    );

    kahuk_redirect($url_redirect);
    die();
}

//
$sql = "DELETE from " . table_group_member . " WHERE member_user_id = '{$current_user->user_id}' and member_group_id = '{$group_id}' ";	
$db->query($sql);

$group_membership = kahuk_user_membership_group($group_id);

if (empty($group_membership)) {
    $output['msg'] = 'You left the group.';
	$output['type'] = 'success';

    // member count update decrease
    $member_count = count_group_members(['group_id' => $group_id]);
    $sql = "update " . table_groups . " set group_members = '" . $member_count . "' where group_id = '" . $group_id . "' ";
    $db->query($sql);
} else {
    $output['msg'] = 'Something went wrong, we will fix it soon.';
    $output['type'] = 'notice';

    kahuk_log_debug("Record still exist even after executed the query!\n{$sql}");
}

kahuk_set_session_message($output['msg'], $output['type']);
kahuk_redirect($url_redirect);
die();
