<?php
header('Content-Type: text/html; charset=utf-8');
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

$sanitezedPOST = array();
foreach ($_POST as $key => $value) {
	if ($key == 'reg_username') {
		$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_STRING); 
	}elseif ($key == 'reg_email') {
		if (filter_var(filter_var($value, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
			$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_EMAIL);
		}else{
			$sanitezedPOST[$key] = '';
		}
	}else{
		$sanitezedPOST[$key] = filter_var($value, FILTER_SANITIZE_STRING);
	}
}
$_REQUEST = $_POST = $sanitezedPOST;

/*Redwine: preventing the register page from loading if the user is already signed in! */
global $current_user;
if ($current_user->authenticated == 1) {
	header('Location: '.my_base_url.my_kahuk_base);
}
/*Redwine: END preventing the register page from loading if the user is already signed in! */
$vars = '';
check_actions('register_top', $vars);

$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Breadcrumb_Register');
$navwhere['link1'] = getmyurl('register', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('KAHUK_Visual_Breadcrumb_Register'));

// pagename
define('pagename', 'register'); 
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('user_language', 'english');


// sidebar
$main_smarty = do_sidebar($main_smarty);

$kahuk_regfrom = isset($_POST["regfrom"]) && sanitize($_POST['regfrom'], 3) != '' ? sanitize($_POST['regfrom'], 3) : '';
if($kahuk_regfrom != ''){

	$error = false;
	switch($kahuk_regfrom){
		case 'full':
			$username = sanitize($_POST["reg_username"], 3);
			$username = preg_replace('/[^\p{L}\p{N}_\s\/]/u', '', $username);
			$email = sanitize($_POST["reg_email"], 3);
			$password = sanitize($_POST["reg_password"], 3);
			$password2 = sanitize($_POST["reg_password2"], 3);
			if (isset($_POST["user_language"])) $user_language = sanitize($_POST["user_language"], 3);
			break;

		case 'sidebar':
			$username = sanitize($_POST["username"], 3);
			$username = preg_replace('/[^\p{L}\p{N}_\s\/]/u', '', $username);
			$email = sanitize($_POST["email"], 3);
			$password = sanitize($_POST["password"], 3);
			$password2 = sanitize($_POST["password2"], 3);	
			break;
	}

	if(isset($username)){$main_smarty->assign('reg_username', htmlspecialchars($username,ENT_QUOTES));}
	if(isset($email)){$main_smarty->assign('reg_email', htmlspecialchars($email,ENT_QUOTES));}
	if(isset($password)){$main_smarty->assign('reg_password', htmlspecialchars($password,ENT_QUOTES));}
	if(isset($password2)){$main_smarty->assign('reg_password2', htmlspecialchars($password2,ENT_QUOTES));}
	if(isset($user_language)){$main_smarty->assign('user_language', htmlspecialchars($user_language,ENT_QUOTES));}
	if(isset($user_language)) {
		$error = register_check_errors($username, $email, $password, $password2, $user_language);
	}else{
		$error = register_check_errors($username, $email, $password, $password2);
	}

	if($error == false){
		register_add_user($username, $email, $password, $password2, $user_language);
	} else {
//		print "Error";
		print_r($form_email_error);
	}

} else {

	$testing = false; // changing to true will populate the form with random variables for testing.
	if($testing == true){
		$main_smarty->assign('reg_username', mt_rand(1111111, 9999999));
		$main_smarty->assign('reg_email', mt_rand(1111111, 9999999) . '@test.com');
		$main_smarty->assign('reg_password', '12345');
		$main_smarty->assign('reg_password2', '12345');
	}

}

//Redwine: the hook below is so far for captcha. can always be used for any other suitable module!
$vars = '';
check_actions('register_showform', $vars);

$main_smarty->assign('tpl_center', $the_template . '/register_center');
$main_smarty->display($the_template . '/kahuk.tpl');

die();

function register_check_errors( $username, $email, $password, $password2 ) {

	global $main_smarty;
	$userip = check_ip_behind_proxy();

	if ( is_ip_banned( $userip ) ) { 
		$form_username_error[] = $main_smarty->get_config_vars( 'KAHUK_Visual_Register_Error_YourIpIsBanned' );
		$error = true;
	}	

	if(!isset($username) || strlen($username) < 3) { // if no username was given or username is less than 3 characters
		$form_username_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserTooShort');
		$error = true;
	}	
	if (preg_match('/\pL/u', 'a')) {	// Check if PCRE was compiled with UTF-8 support
	    if (!preg_match('/^[_\-\d\p{L}\p{M}]+$/iu', $username)) { // if username contains invalid characters
		$form_username_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserInvalid');
		$error = true;
	    }
	} else {
	    if (!preg_match('/^[^~`@%&=\\/;:\\.,<>!"\\\'\\^\\.\\[\\]\\$\\(\\)\\|\\*\\+\\-\\?\\{\\}\\\\]+$/', $username)) {
		$form_username_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserInvalid');
		$error = true;
	    }
	}
	if(user_exists(trim($username)) ) { // if username already exists
		$form_username_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_UserExists');
		$error = true;
	}
	if(!check_email(trim($email))) { // if email is not valid
		$form_email_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_BadEmail');
		$error = true;
	}
	if(check_email(trim($email))) {
	if(email_exists(trim($email)) ) { // if email already exists
		$form_email_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_EmailExists');
		$error = true;
	}
	}
	if(strlen($password) < 5 ) { // if password is less than 5 characters
		$form_password_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_FiveCharPass');
		$error = true;
	}
	if($password !== $password2) { // if both passwords do not match
		$form_password_error[] = $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_NoPassMatch');
		$error = true;
	}	

	$vars = array('username' => $username, 'email' => $email, 'password' => $password);
	check_actions('register_check_errors', $vars);
	/*** Redwine: the above 2 lines check for errors in /modules/captcha/captcha_main.php. The return was not registering the errors found by function register_check_errors on line 120. I corrected the if statement by adding  || $error == true to assign the errors to smarty and display them in the register form ***/
	if((isset($vars['error']) && $vars['error'] == true) || $error == true){
		$error = true;
		if ($vars['username_error'])
		    $form_username_error[] = $vars['username_error'];
			$main_smarty->assign('form_username_error', $form_username_error);
		if ($vars['email_error'])
		    $form_email_error[] = $vars['email_error'];
			$main_smarty->assign('form_email_error', $form_email_error);
		if ($vars['password_error'])
		    $form_password_error[] = $vars['password_error'];
			$main_smarty->assign('form_password_error', $form_password_error);
	}

	if (!empty($error)) return $error;
}

function register_add_user($username, $email, $password, $password2, $user_language){

	global $current_user;

	$user = new User();
	
	$user->user_language = $user_language;
	$user->username = $username;
	$user->pass = $password;
	$user->email = $email;
	
	
	if($user->Create()){

		$user->read('short');
		
		$registration_details = array(
			'user_language' => $user_language,
			'username' => $username,
			'password' => $password,
			'email' => $email,
			'id' => $user->id
		);
	
		check_actions('register_success_pre_redirect', $registration_details);

		$current_user->Authenticate($username, $password, false);
		if (isset($registration_details['redirect']))
		    header('Location: '.$registration_details['redirect']);
		elseif(kahuk_validate()){
		    header('Location: '.my_base_url.my_kahuk_base.'/register_complete.php?user='.$username);
		} else {
		    header('Location: ' . getmyurl('user', $username));
		}
		die();
	}
}
