<?php
/**
 * Already logged in?!
 */
if ($user_authenticated) {
    kahuk_redirect($kahuk_root);
    die();
}

if (!$globalUser || ($globalUser['user_level'] != 'unverified')) {
    kahuk_redirect_404();
    die();
}

//
$is_verification_sent = sanitize_number(_get('verification'));

//
$main_smarty->assign('user', $globalUser);
$main_smarty->assign('is_verification_sent', $is_verification_sent);

// set the template
$main_smarty->assign('tpl_center', $the_template . '/register_complete_center');
