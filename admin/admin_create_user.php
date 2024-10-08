<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');

include(KAHUKPATH_LIBS . 'smartyvariables.php');
include(KAHUKPATH_LIBS . 'csrf.php');
include(KAHUKPATH_LIBS . 'document_class.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins and moderators
$amIadmin = 0;
$amIadmin = $amIadmin + checklevel('admin');
$main_smarty->assign('amIadmin', $amIadmin);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
$canIhaveAccess = $canIhaveAccess + checklevel('moderator');

if ($canIhaveAccess == 0) {
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

if (isset($_REQUEST["mode"]) && sanitize($_REQUEST["mode"], 3) == "newuser") {
	// if TOKEN is empty, no need to continue, just display the invalid token error.
	if (empty($_POST['token'])) {
		$CSRF->show_invalid_error(1);
		exit;
	}

	// if valid TOKEN, proceed. A valid integer must be equal to 2.
	if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'admin_users_create') == 2) {
		$username = trim($db->escape($_POST['username']));
		$password = trim($db->escape($_POST['password']));
		$email = trim($db->escape($_POST['email']));
		$level = trim($db->escape($_POST['level']));
		$saltedpass = kahuk_hashed_password($password);

		if (!isset($username) || strlen($username) < 3) {
			$main_smarty->assign(username_error, $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserTooShort'));
		} elseif (!preg_match('/^[a-zA-Z0-9\-]+$/', $username)) {
			$main_smarty->assign(username_error, $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserInvalid'));
		} elseif (user_exists(trim($username))) {
			$main_smarty->assign(username_error, $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserExists'));
		} elseif (!check_email(trim($email))) {
			$main_smarty->assign(email_error, $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_BadEmail'));
		} elseif (email_exists(trim($email))) {
			$main_smarty->assign(email_error, $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_EmailExists'));
		} elseif (strlen($password) < 5) {
			$main_smarty->assign(password_error, $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_FiveCharPass'));
		} else {
			$db->query("INSERT IGNORE INTO " . table_users . " (user_login, user_level, user_email, user_pass, user_date) VALUES ('$username', '$level', '$email', '$saltedpass', NOW())");
			echo "User Added successfully";
		}
		// if invalid TOKEN, display TOKEN invalid error.	
	} else {
		$CSRF->show_invalid_error(1);
		exit;
	}
}
