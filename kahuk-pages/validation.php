<?php
/**
 * Already logged in?!
 */
if ($user_authenticated) {
    kahuk_redirect($kahuk_root);
    die();
}

//
$user_login = sanitize_text_field(_get('slug'));
$verification_code = sanitize_text_field(_get('code'));
$message = '';

/**
 * Visiting the page by mistake
 */
if (empty($verification_code) || empty($user_login)) {
    kahuk_redirect($kahuk_root);
    die();
}

$message = $main_smarty->get_config_vars('KAHUK_Validation_Invalid_Code');
$user = $globalUsersObj->get_item(['user_login' => $user_login]);

if ($user) {
    if (($user['user_level'] == 'unverified') && ($user['verification_code'] == $verification_code)) {
        /**
         * Correct page with correct user and code
         */

        // Delete all previous login attempts
        $sql = "DELETE FROM " . table_login_attempts . " WHERE login_username='{$user_login}'";
        $db->query($sql);

        // Update user as email verified
        $sql = "UPDATE " . table_users . " SET `user_level` = 'normal', `verification_code` = 'verified', `user_modification` = NOW() , `user_lastlogin` = NOW() WHERE `user_id` = " . $user['user_id'] . " LIMIT 1";
        $db->query($sql);

        $message = $main_smarty->get_config_vars('KAHUK_Visual_User_Profile_Email_Validated');

    } else if ($user['verification_code'] == 'verified') {
        $message = sprintf(
            $main_smarty->get_config_vars('KAHUK_Validation_Already_Activated'),
            kahuk_url_login()
        );
    }
}

//
$main_smarty->assign('error', $message);
$main_smarty->assign('user', $user);

// set the template
$main_smarty->assign('tpl_center', $the_template . '/user_validation_center');
