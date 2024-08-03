<?php
check_referrer();

force_authentication();

if ($globalUser) {
    /**
     * Just redirect to Home for 'spammer', or 'unverified' users.
     * Because the are not suppose to be available online.
     */
    if (in_array($globalUser['user_level'], ['spammer','unverified'])) {
        kahuk_redirect($kahuk_root);
    }
} else {
    /**
     * Wrong user name found, redirect to 404 page.
     */
    kahuk_redirect_404();
    die();
}

//
include(KAHUKPATH_LIBS . 'csrf.php');
$CSRF = new csrf();

$user_id = $globalUser['user_id'];
$user_login = $globalUser['user_login'];

/**
 * Save User Settings
 */
if (isset($_POST['action']) && ($_POST['action'] == "save-user-settings")) {
    if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}

    // if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'user_settings') == 2) {
        $data = [];

        if ($session_user_level == "admin") {
            $data['user_email'] = sanitize_email(_post("user_email"));
        }
        
        //
        $data['user_url'] = esc_url(_post("user_url"));

        //
        $data['user_names'] = sanitize_text_field(_post("user_names"));

        //
        $data['user_occupation'] = sanitize_text_field(_post("user_occupation"));

        //
        $data['user_location'] = sanitize_text_field(_post("user_location"));

        //
        $data['user_skype'] = sanitize_text_field(_post("user_skype"));

        //
        $data['user_facebook'] = esc_url(_post("user_facebook"));
        // $user_facebook = sanitize_text_field(_post("user_facebook"));
        // preg_match("/https?:\/\/(www\.)?facebook\.com\/([^\/]*)/", $user_facebook, $matches);
        // if ($matches) {
		// 	$data['user_facebook'] = $matches[2];
		// } else {
        //     $data['user_facebook'] = $user_facebook;
        // }

        //
        $data['user_twitter'] = esc_url(_post("user_twitter"));

        //
        $data['user_linkedin'] = esc_url(_post("user_linkedin"));

        //
        $data['user_pinterest'] = esc_url(_post("user_pinterest"));

        //
        // $oldpassword = sanitize_text_field(trim(_post("oldpassword")));
        $newpassword = sanitize_text_field(trim(_post("newpassword")));
        $newpassword2 = sanitize_text_field(trim(_post("newpassword2")));

        if (!empty($newpassword)) {
            if ($newpassword != $newpassword2) {
                $action_messages[] = [
                    'message' => $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_NoPassMatch'),
                    'type' => 'notice'
                ];
            }

            if (empty($action_messages) && (strlen($newpassword) < 5)) {
                $action_messages[] = [
                    'message' => $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_FiveCharPass'),
                    'type' => 'notice'
                ];
            }

            if (empty($action_messages)) {
                $data['user_pass'] = $newpassword;
            }
        }
        
        /**
         * Update user when no error found
         */
        if (empty($action_messages)) {
            $rs = $globalUsersObj->update($data, $user_id);

            if ($rs) {
                kahuk_set_session_message(
                    'Information saved!',
                    'success'
                );

                kahuk_redirect($permalink);
                die();
            } else {
                $action_messages[] = [
                    'message' => 'Something went wrong! Please try again!',
                    'type' => 'fail'
                ];
            }
        }
    }
}

//
$CSRF->create('user_settings', true, true);
$main_smarty->assign('form_action', $permalink);

//
$main_smarty->assign('user', $globalUser);

//
$main_smarty->assign('tpl_center', $the_template . '/user_settings_center');
