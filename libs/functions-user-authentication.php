<?php
/**
 * 
 * @return string admin|moderator|normal|Spammer
 */
function kahuk_current_user_role() {
    global $current_user;

    return (isset($current_user->user_level) ? $current_user->user_level : "");
}

/**
 * 
 * @return boolean true|false
 */
function kahuk_check_user_role($levl){
    $user_role = kahuk_current_user_role();

    return ($user_role == $levl);
}
