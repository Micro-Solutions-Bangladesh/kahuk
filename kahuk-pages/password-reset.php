<?php
/**
 * Already logged in?!
 */
if ($user_authenticated) {
    kahuk_redirect(kahuk_root_url());
    die();
}

$action = sanitize_text_field(_post('action'));
$errorMsg = '';

/**
 * Process reset password request
 */
if ($action === 'reset') {
    $user_email = sanitize_email(_post("email"));

    $user = [];

    if ($user_email) {
        $user = $globalUsersObj->get_item(['user_email' => $user_email]);
    }

    if ($user) {
        $username = $user['user_login'];

        $combined = create_token();
        
        // we want to use the salt with the function generateHash to strengthen it.
        $salt = substr(md5(uniqid(rand(), true)), 0, 32);
        
        // saltedlogin is the hashed $combined and that will be saved in the users table and serves as the identifier.
        $saltedlogin = hash('sha256', $combined);

        $subject = $site_name . ' ' . $main_smarty->get_config_vars("KAHUK_PassEmail_Subject");

        $message = sprintf(
            $main_smarty->get_config_vars("KAHUK_PassEmail_Body"), 
            $salt, 
            $site_name
        );

        $message .= "\n \n";
        $message .= kahuk_create_url('recover/', ['id' => urlsafe_base64_encode($username), 'n' => urlsafe_base64_encode($saltedlogin)]);

        $data = [
            'to_email' => $user['user_email'],
            'subject' => $subject,
            'message' => $message,
        ];

        // $isMailSent = $globalMailerObj->sendMe($data);
        $isMailSent = kahuk_send_email($data);

        if ($isMailSent) {
            $times = time();
            // update reset code in database
            $sql = "UPDATE `" . table_users . "` SET `last_reset_code` = '" . $saltedlogin . "/" . $salt . "', `last_reset_request` = FROM_UNIXTIME('" . $times . "') WHERE `user_login` = '" . $username . "'";
            $db->query($sql);

            // Set message for browser
            $errorMsg = $main_smarty->get_config_vars('KAHUK_Visual_Password_Sent');

            if (KAHUK_DEBUG) { // Log for developer/admin
                kahuk_log_unexpected("Email send success. [password-reset.php]\n" . $message);
            }
        } else {
            // Set message for browser
            $errorMsg = $main_smarty->get_config_vars('KAHUK_Visual_Login_Delivery_Failed');

            // Log for developer/admin
            kahuk_log_unexpected("Email send fail. [password-reset.php]\n" . $message);
        }

    } else {
        // Set message for browser
        $errorMsg = $main_smarty->get_config_vars('KAHUK_Visual_Password_Sent');
    }
}

//
$main_smarty->assign('errorMsg', $errorMsg);

// set the template
$main_smarty->assign('tpl_center', $the_template . '/login_center');
