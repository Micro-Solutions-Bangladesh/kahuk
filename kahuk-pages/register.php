<?php
/**
 * Already logged in?!
 */
if ($user_authenticated) {
    kahuk_redirect(kahuk_root_url());
    die();
}

/**
 * 
 */
$action = sanitize_text_field(_post('action'));

$hasError = false;
$username = '';
$email = '';
$userip = check_ip_behind_proxy();

if (is_ip_banned($userip)) {
    $errorMessages[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_YourIpIsBanned');
} else {
    /**
     * Process create account request
     */
    if ($action === 'create-account') {
        //
        $saveAbleData = [];

        $username = sanitize_text_field(_post("reg_username"));
        $email = sanitize_email(_post("reg_email"));
        $password = sanitize_text_field(_post("reg_password"));
        $password2 = sanitize_text_field(_post("reg_password2"));

        // Verify username
        $sanitized_username = sanitize_username($username);

        if ($sanitized_username != $username) {
            $hasError = true;
			kahuk_set_session_message(
                $main_smarty->get_config_vars("KAHUK_Visual_CheckField_AllowedChars"),
                "warning"
            );
		} else {
            if (strlen($username) < 5) { // if no username was given or username is less than 5 characters
                $hasError = true;
                kahuk_set_session_message(
                    $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserTooShort'),
                    "warning"
                );
            } else {
                if (user_exists($username)) { // if username already exists
                    $hasError = true;
                    kahuk_set_session_message(
                        $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserExists'),
                        "warning"
                    );
                } else {
                    // $username = $sanitized_username;
                    $saveAbleData['user_login'] = $username;
                }
            }
        }

        // Verify email
        if ($email == '') { // if email is not valid
            $hasError = true;
            kahuk_set_session_message(
                $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_BadEmail'),
                "warning"
            );
        } else {
            if (email_exists($email)) { // if email already exists
                $hasError = true;
                kahuk_set_session_message(
                    $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_EmailExists'),
                    "warning"
                );
            } else {
                $saveAbleData['user_email'] = $email;
            }
        }

        // Verify password
        if (strlen($password) < 5) { // if password is less than 5 characters
            $hasError = true;
            kahuk_set_session_message(
                $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_FiveCharPass'),
                "warning"
            );
        } else {
            if ($password !== $password2) { // if both passwords do not match
                $hasError = true;
                kahuk_set_session_message(
                    $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_NoPassMatch'),
                    "warning"
                );
            } else {
                $saveAbleData['user_pass'] = $password;
            }
        }

        $saveAbleData['user_ip'] = $userip;
        $saveAbleData['user_lastip'] = $userip;

        // Verify submit via action hook
        $verify = $hooks->apply_filters(
            "submit_verify",
            ['data' => $saveAbleData, 'success' => true]
        );

        if (isset($verify["success"]) && ($verify["success"] == false)) {
            $hasError = true;
            kahuk_set_session_message(
                $verify["message"],
                "warning"
            );
        }
    
        if ($hasError) {
            kahuk_redirect(kahuk_get_permalink());
            exit;
        }

        $saveAbleData = $verify['data'];

        $verification_code = kahuk_create_user_verification_code($saveAbleData);
        $saveAbleData['verification_code'] = $verification_code;

        $url_redirect = '';
        $user_id = kahuk_insert_user($saveAbleData);

        if ($user_id > 0) {
            /**
             * User is saved, now sent a verification email
             */
            $domain = $main_smarty->get_config_vars('KAHUK_Visual_Name');

            $validation = kahuk_create_url('validation/', ['code' => $verification_code, 'slug' => $username]);

            $subject = $main_smarty->get_config_vars('KAHUK_PassEmail_Subject_verification');

            //
            $str = sprintf(
                $main_smarty->get_config_vars('KAHUK_PassEmail_verification_message'), 
                $username, 
                kahuk_get_config("_site_email_contact")
            );
            eval('$str = "'.str_replace('"','\"',$str).'";');
            $message = "$str";
            
            $data = [
                'to_email' => $email,
                'subject' => $subject,
                'message' => $message,
            ];
            
            // $isMailSent = $globalMailerObj->sendMe($data);
            $isMailSent = kahuk_send_email($data);

            if ($isMailSent == "success") {
                /**
                 * Save The Email message in log file for developer
                 */
                // kahuk_log_unexpected("Mail delivery seccess for {$email}, Validation link: {$validation}");

                // User is saved, now create the page permalink to redirect
                $url_redirect = kahuk_create_url('register-complete/', ['slug' => $username, 'verification' => 1]);
            } else {
                /**
                 * Save The Email message in log file for developer
                 */
                kahuk_log_unexpected("Mail delivery fail for {$email}\n--------- Message Body ---------\n" . $message);

                // User is saved but failed to sent verification email 
                $url_redirect = kahuk_create_url('register-complete/', ['slug' => $username, 'verification' => 0]);
            }

        } else {
            kahuk_log_unexpected("User Registration Not Done although all the Data Seemed to be OK\n" . print_r($data, true));

            kahuk_set_session_message(
                'Something went wrong, registration is not complete!',
                'warning'
            );

            $url_redirect = $kahuk_root;
        }

        kahuk_redirect($url_redirect);
        die();
    }
}

//
$main_smarty->assign('reg_username', htmlspecialchars($username, ENT_QUOTES));
$main_smarty->assign('reg_email', htmlspecialchars($email, ENT_QUOTES));


// set the template
$main_smarty->assign('tpl_center', $the_template . '/register_center');
