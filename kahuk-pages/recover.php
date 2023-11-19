<?php
/**
 * Already logged in?!
 */
if ($user_authenticated) {
    kahuk_redirect(kahuk_root_url());
    die();
}

// 
$actionStatus = [
    'msgcode' => '',
];

//
$id = sanitize_text_field(_get('id'));
$n = sanitize_text_field(_get('n'));

$username = sanitize_text_field(urlsafe_base64_decode($id));
$user = [];

if ($username && $n) {
    $user = $globalUsersObj->get_item(['user_login' => $username, 'user_level' => ['admin', 'moderator', 'normal']]);
}

if ($user) {
    list($hashed_fields, $secret_code) = explode('/', $user['last_reset_code']);

    if (check_valid($hashed_fields, urlsafe_base64_decode($n)) == 1) {
        $generated_password = kahuk_generate_password();
        $time = time();
        $saltedPass = generatePassHash($generated_password);
        
        $sql = "UPDATE `" . table_users . "` SET `user_pass` = '" . $saltedPass . "', `last_reset_request` = FROM_UNIXTIME('{$time}'), `last_reset_code` = '' WHERE `user_login` = '" . $user['user_login'] . "'";
        $rs = $db->query($sql);

        if ($rs) {
            $actionStatus['msgcode'] = 'generated-password-success';
            $actionStatus['msg'] = sprintf(
                $main_smarty->get_config_vars("KAHUK_Lang_Recover_password_Generate_Success"),
                $generated_password
            );
        } else {
            $actionStatus['msgcode'] = 'generated-password-fail';
            $actionStatus['msg'] = sprintf(
                $main_smarty->get_config_vars("KAHUK_Lang_Recover_password_Generate_Fail"),
                $main_smarty->get_config_vars("KAHUK_LANG_EMAIL_CONTACT")
            );
        }
    } else {
        $actionStatus['msgcode'] = 'password-recover-invalid-request';
        $actionStatus['msg'] = $main_smarty->get_config_vars("KAHUK_Lang_Recover_password_Invalid_request");
    }
} else {
    // 
    kahuk_set_session_message(
        'Invalid request to recover password!',
        'warning'
    );

    kahuk_redirect();
}


// misc smarty 
$main_smarty->assign('errorMsg', $errorMsg);

$main_smarty->assign('actionStatus', $actionStatus);
$main_smarty->assign('id', $id);
$main_smarty->assign('n', $n);

$main_smarty->assign('generated_password', $generated_password);

// show the template
$main_smarty->assign('tpl_center', $the_template . '/recover_password_center');
