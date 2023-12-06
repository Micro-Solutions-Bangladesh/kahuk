<?php
if (!$globalGroup) {
    // TODO Add a session message for 404 page
    kahuk_redirect_404();

    exit;
}

//
if (!$user_authenticated) {
    die("Invalid action!");
}

//
check_referrer();

//
$group_id = $globalGroup['group_id'];
$group_safename = $globalGroup['group_safename'];
$group_privacy = $globalGroup['group_privacy'];

$url_redirect = kahuk_permalink_group($group_safename);

if (isMember($group_id)) {
    kahuk_set_session_message(
        'Hurrah! You are already in the group!',
        'info'
    );

    kahuk_redirect($url_redirect);
    die();
}

// Enforce "Max Joinable Groups" config option
$user_groups_total = kahuk_count_groups_by_user();

if ($current_user->user_level != "admin") {
    if ($user_groups_total >= max_groups_to_join) {
        kahuk_set_session_message(
            'You are not allowed to join the group because You have already joined the number of maximum groups!',
            'notice'
        );
    
        kahuk_redirect($url_redirect);
        die();
    }
}

// Determine the status of join request
$member_status = 'inactive';

if ($group_privacy == 'public') {
    $member_status = 'active';
} elseif ($group_privacy == 'private') {
    $member_status = 'requested';
}

//
$redirect_msg = [];

// Save record to join user into the group
$sql = "INSERT IGNORE INTO " . table_group_member . " ( `member_user_id` , `member_group_id`, `member_role`,`member_status` ) VALUES ('" . $current_user->user_id . "', '" . $group_id . "','normal','" . $member_status . "' ) ";
$db->query($sql);

$group_membership = kahuk_user_membership_group($group_id);

if ($group_membership['member_status'] == $member_status) {
    $redirect_msg['msg'] = 'You joined the group!';
    $redirect_msg['type'] = 'success';
    
    // increase member count
    $member_count = count_group_members(['group_id' => $group_id]);
    $sql = "update " . table_groups . " set group_members = '" . $member_count . "' where group_id = '" . $group_id . "'";
    $db->query($sql);
} else {
    $redirect_msg['msg'] = 'Something went wrong, we will fix it soon.';
    $redirect_msg['type'] = 'notice';

    kahuk_log_unexpected("Record not saved!\n{$sql}");
}

kahuk_set_session_message($redirect_msg['msg'], $redirect_msg['type']);
kahuk_redirect($url_redirect);
die();
