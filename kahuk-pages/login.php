<?php
/**
 * Process logout request
 */
if ($pageTask == 'logout') {
    $current_user->kahuk_logout();
    die();
}

/**
 * Already logged in?!
 */
if ($user_authenticated) {
    kahuk_set_session_message(
        sprintf(
            "You are already logged in as <strong>%s</strong>! If it is not you, please <a href=\"%s\">Logout</a>.",
            $session_user_login, kahuk_url_logout()
        ),
        'notice'
    );

    kahuk_redirect(KAHUK_BASE_URL);
    die();
}

$errorMsg = '';
$processlogin = sanitize_number(_post('processlogin'));

/**
 * Process login request
 */
if ($processlogin === 1) {
    $username = sanitize_text_field(_post('username'));
    $password = sanitize_text_field(_post('password'));

    $persistent = sanitize_number(_post('persistent'));
    $redirectto = sanitize_text_field(_request('redirectto'));

    $dbusername = sanitize($db->escape($username), 4);
    $lastip     = check_ip_behind_proxy();
    
    $sql	= "SELECT *, UNIX_TIMESTAMP()-UNIX_TIMESTAMP(login_time) AS time FROM " . table_login_attempts . " WHERE login_ip='$lastip'";
    $login_attempts	= $db->get_row($sql);

    // Check login attempts BY IP ADDRESS
    if ($login_attempts && $login_attempts->login_id) {
        $login_id = $login_attempts->login_id;

        if ($login_attempts->time < 3) {
            $errorMsg = sprintf($main_smarty->get_config_vars('KAHUK_Visual_Login_Error'), 3);
        } elseif ($login_attempts->login_count >= 3) {
            if ($login_attempts->time < min(60 * pow(2, $login_attempts->login_count - 3), 3600)) {
                $errorMsg = sprintf($main_smarty->get_config_vars('KAHUK_Login_Incorrect_Attempts'), $login_attempts->login_count, min(60 * pow(2, $login_attempts->login_count - 3), 3600) - $login_attempts->time);
            }
        }
    } else {
        // if no entry in table_login_attempts, we insert one to monitor the login attempts.
        $db->query("INSERT INTO " . table_login_attempts . " SET login_username = '$dbusername', login_time=NOW(), login_ip='$lastip'");
        $login_id = $db->insert_id;

        if (!$login_id) {
            $errorMsg = sprintf($main_smarty->get_config_vars('KAHUK_Visual_Login_Error'), 3);
        }
    }

    if (!$errorMsg) {
        $checkAuthentication = $current_user->kahuk_authentication($username, $password, $persistent);
        
        if ($checkAuthentication["status"] == false) {
            //
            $sql = "UPDATE " . table_login_attempts . " SET login_username='" . $db->escape($dbusername) . "', login_count=login_count+1, login_time=NOW() WHERE login_id=" . $db->escape($login_id);
            $db->query($sql);

            // TODO Resend email validation email feature
            $sql = "SELECT * FROM " . table_users . " WHERE user_login = '" . $db->escape($username) . "' or user_email= '" . $db->escape($username) . "'";
            $user = $db->get_row($sql);

            if ($user && $user->user_lastlogin == NULL) {
                $errorMsg = $main_smarty->fetch($the_template . "/template-parts/resend-email-verification-form.tpl");
            } else {
                $errorMsg = $checkAuthentication["msg"];
            }
        } else {
            $sql = "DELETE FROM " . table_login_attempts . " WHERE login_ip='" . $db->escape($lastip) . "'";
            $db->query($sql);

            define('logindetails', $username . ";" . $password . ";" . $redirectto);

            if (strpos($_SERVER['SERVER_SOFTWARE'], "IIS") && strpos(php_sapi_name(), "cgi") >= 0) {
                echo '<SCRIPT LANGUAGE="JavaScript">window.location="' . $redirectto . '";</script>';
                echo $main_smarty->get_config_vars('KAHUK_Visual_IIS_Logged_In') . '<a href = "' . $redirectto . '">' . $main_smarty->get_config_vars('KAHUK_Visual_IIS_Continue') . '</a>';
            } else {
                kahuk_redirect($redirectto);
            }

            die;
        }
    }
}

$main_smarty->assign('errorMsg', $errorMsg);

// set the template
$main_smarty->assign('tpl_center', $the_template . '/login_center');
