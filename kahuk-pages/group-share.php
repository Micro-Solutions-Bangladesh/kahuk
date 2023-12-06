<?php
if (!$globalGroup) {
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
$sId = sanitize_number(_get('sId', 0));
$gId = $globalGroup['group_id'];
$group_safename = $globalGroup['group_safename'];
$group_privacy = $globalGroup['group_privacy'];
$group_status = $globalGroup['group_status'];

// if ($group_status != 'enable') {
//     if (!in_array($session_user_level, ['admin', 'moderator'])) {
//         kahuk_redirect_404();
//         exit;
//     }
// }


//
$url_redirect = kahuk_permalink_group($group_safename);

if (!isMember($gId)) {
    kahuk_set_session_message(
        'Please join the group to share an story in the group.',
        'notice'
    );

    kahuk_redirect($url_redirect);
    die();
}

//
$story = $globalStories->get_item($sId, ["link_status" => kahuk_story_statuses()]);

//
if ($story) {
    if (in_array($group_privacy, ['public', 'private'])) {
        /** All members may not have right to share story in "restricted" group */

        $unshare = sanitize_number(_get('unshare', 0));

        if ($unshare == 1) {
            $sql = "DELETE FROM " . table_group_shared . " WHERE `share_link_id` = " . $sId . " AND `share_group_id` = " . $gId . " AND `share_user_id` = " . $session_user_id;
            $results = $db->query($sql);

            if ($results > 1) {
                kahuk_log_queries("DELETED {$results}\nSQL: {$sql}");
            }
        } else {
            $sql = "INSERT IGNORE INTO " . table_group_shared . " ( `share_link_id` , `share_group_id`, `share_user_id` ) VALUES ('" . $sId . "', '" . $gId . "','" . $session_user_id . "' ) ";
			$results = $db->query_insert($sql);
        }

        if ($results) {
            kahuk_set_session_message(
                'Story shared successfully!',
                'success'
            );

            $url_redirect = add_query_arg(['view' => 'shared'], $url_redirect);
        } else {
            kahuk_set_session_message(
                'Something went wrong, story possibly not shared!',
                'error'
            );
        }
    }
} else {
    kahuk_set_session_message(
        'Something went wrong, story not shared!',
        'error'
    );
}

kahuk_redirect($url_redirect);
die();

