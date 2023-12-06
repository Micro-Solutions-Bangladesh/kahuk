<?php
/**
 * Redirect to login page when user is not authenticated
 * 
 * @since 5.0.0
 * @updated 5.0.7
 * 
 * @return true when user is authenticated
 */
function force_authentication($message = '', $redirectto = '') {
	global $current_user, $page_login_url;

	if (!$current_user->authenticated) {
        if (!$redirectto) {
            $redirectto = $page_login_url;
        }

        if ($message) {
            kahuk_set_session_message($message, "warning");
        }

        kahuk_redirect($page_login_url);
        die;
	}

	return true;
}

/**
 * Check and return boolean wheather user is logged in or not
 * 
 * @since 5.0.6
 * 
 * @return boolean true|false
 */
function kahuk_is_user_authenticated() {
	global $current_user;

	return ($current_user && $current_user->authenticated);
}

/**
 * Get user numeric id from $current_user and return when user is logged in or empty
 * 
 * @since 5.0.6
 * 
 * @return string user numeric id
 */
function kahuk_session_user_id() {
    global $current_user;

    return (kahuk_is_user_authenticated() ? $current_user->user_id : 0);
}

/**
 * Get user login id from $current_user and return when user is logged in or empty
 * 
 * @since 5.0.6
 * 
 * @return string user login id
 */
function kahuk_session_user_login() {
    global $current_user;

    return (kahuk_is_user_authenticated() ? $current_user->user_login : "");
}

/**
 * 
 * @return string admin|moderator|normal|spammer|unverified
 */
function kahuk_session_user_level() {
    global $current_user;
    // echo "<pre class=\"debug\">kahuk_session_user_level() [user_role: {$current_user->user_level}]</pre>";

    return ($current_user && $current_user->authenticated ? $current_user->user_level : "");
}

/**
 * 
 * @return boolean true|false
 */
function kahuk_check_user_role($role){
    $sessionRole = kahuk_session_user_level();

    return ($sessionRole == $role);
}

/**
 * Check if the user is authenticated and has permission
 * 
 * @since 5.0.8
 * 
 * @return boolean true|false
 */
function kahuk_has_access($role){
    $output = false;

    if (kahuk_is_user_authenticated()) {
        $sessionRole = kahuk_session_user_level();

        if (($role == "admin") && ($sessionRole == "admin")) {
            $output = true;
        } else if ($role == "moderator") {
            if (in_array($sessionRole, ["moderator", "admin"])) {
                $output = true;
            }
        } else if ($role == "normal") {
            if (in_array($sessionRole, ["normal", "moderator", "admin"])) {
                $output = true;
            }
        }
    } else {
        if (!$role) {
            $output = true;
        }
    }

    return $output;
}

/**
 * Check group status and privacy to avoid unauthorize access
 * 
 * @since 5.0.6
 * 
 * @return boolean true|false
 */
function kahuk_redirect_group_access() {
    global $globalGroup;

    // var_dump($globalGroup);
    // exit;

    $user_authenticated = kahuk_is_user_authenticated();
    $session_user_id = kahuk_session_user_id();

    $isRedirect = false;

    // echo "<pre class=\"debug\">kahuk_redirect_group_access() [user_authenticated: {$user_authenticated}]</pre>";
    // echo "<pre class=\"debug\">kahuk_redirect_group_access() [session_user_id: {$session_user_id}]</pre>";

    if (!$globalGroup) {
        $isRedirect = true;
    } else {

        if ($user_authenticated) {
            // if (kahuk_check_user_role('admin')) {
            //     // Everything is authorized
            // } else 
            if (kahuk_check_user_role('moderator')) {
                if ($globalGroup['group_status'] == 'disable') {
                    $isRedirect = true;
                }
            } else if (kahuk_check_user_role('normal')) {
                if ($globalGroup['group_status'] == 'disable') {
                    $isRedirect = true;
                } else if (($globalGroup['group_status'] == 'pending') && ($session_user_id != $globalGroup['group_creator'])) {
                    $isRedirect = true;
                }
            }
        } else {
            if ($globalGroup['group_status'] != 'enable') {
                $isRedirect = true;
            }
        }
    }

    return $isRedirect;
}
