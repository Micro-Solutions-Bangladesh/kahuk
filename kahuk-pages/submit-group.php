<?php
check_referrer();

// make sure user is logged in
force_authentication();

// Check group feature is enabled 
if (!enable_group) {
    kahuk_set_session_message(
        'Group feature is disabled!',
        'notice'
    );

    kahuk_redirect(KAHUK_BASE_URL);
    die();
}

// Check user role has permission to submit new group
$allowed_user_roles = [];

if (group_submit_level == 'normal') { // allowed for all 
    $allowed_user_roles = ['normal', 'moderator', 'admin'];
} else if(group_submit_level == 'moderator') {
    $allowed_user_roles = ['moderator', 'admin'];
} else {
    $allowed_user_roles = ['admin'];
}

if(!in_array($session_user_level, $allowed_user_roles)) {
    kahuk_set_session_message(
        'You are not allowed to submit new group!',
        'notice'
    );

    kahuk_redirect(KAHUK_BASE_URL);
    die();
}

// 
include(KAHUKPATH_LIBS . 'csrf.php');
$CSRF = new csrf();

/**
 * Check if form is submitted for new group
 */
if (isset($_POST["action"])) {

    if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		die();
	}

    // if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize_text_field($_POST['token']), 'submit_group') == 2) {
        $data = [];

        //
        if (isset($_POST['group_title'])) {
            $group_name = $globalGroupsObj->check_group_name($_POST['group_title']);

            if (is_kahuk_error($group_name)) {
                kahuk_set_session_message(
                    $group_name->get_error_message(),
                    'notice'
                );
            
                kahuk_redirect($permalink);
                die();
            } else {
                $group_safename = $globalGroupsObj->check_group_safename($group_name);

                if (is_kahuk_error($group_safename)) {
                    kahuk_set_session_message(
                        $group_safename->get_error_message(),
                        'notice'
                    );
                
                    kahuk_redirect($permalink);
                    die();
                }

                $data['group_name'] = $group_name;
                $data['group_safename'] = $group_safename;
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
        $group_privacy = sanitize_text_field(_post('group_privacy'));

        if (in_array($group_privacy, $globalGroupPrivacies)) {
            $data['group_privacy'] = $group_privacy;
        }

        //
        if (auto_approve_group == 'true') {
            $data['group_status'] = 'enable';
        } else {
            $data['group_status'] = 'pending';
        }

        $data['group_creator'] = $session_user_id;

        $group_id = $globalGroupsObj->insert($data);

        if ($group_id) {
            // to make group creator a member
            $sql = "INSERT IGNORE INTO " . table_group_member . " (`member_user_id`, `member_group_id`, `member_role`) VALUES ({$session_user_id}, {$group_id}, 'admin')";
            
            $db->query($sql);

            // Redirecting to group
            $group = $globalGroupsObj->get_global_group($group_id);
            $permalink = kahuk_permalink_group($group, ['prefix' => 'edit']);

            kahuk_redirect($permalink);
            die();
        }

        kahuk_set_session_message(
            'Something went wrong, please try again!',
            'notice'
        );
        kahuk_redirect($permalink);
        die();
    }
}

// Create CSRF token
$CSRF->create('submit_group', true, true);

$main_smarty->assign('group_allow', true); // TODO check user role and status for it.

$main_smarty->assign('tpl_center', $the_template . '/submit_groups_center');
