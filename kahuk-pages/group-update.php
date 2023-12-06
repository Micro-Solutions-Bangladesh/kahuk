<?php
if (!$globalGroup) {
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

if ($pageTask == "approve") {
    if ($session_user_level == 'admin') {
        $group_id = $globalGroup['group_id'];
    
        $data = [
            'group_status' => 'enable',
        ];
    
        $globalGroupsObj->update($data, $group_id);
    
        kahuk_set_session_message(
            sprintf("Group <strong>%s</strong> approved.", $globalGroup['group_name']),
            'success'
        );
    }
}

kahuk_redirect($url_redirect);
die();
