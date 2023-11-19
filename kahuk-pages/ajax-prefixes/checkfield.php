<?php
$type = sanitize_text_field(_request('type'));

$name = sanitize_text_field(_post("name"));
$email = sanitize_email(_post("email"));
$password = sanitize_text_field(_post("password"));

switch ($type) {
	case 'username':
		if (utf8_strlen($name) < 3) { // if username is less than 3 characters
			echo $main_smarty->get_config_vars("KAHUK_Visual_CheckField_UserShort");
			die();
		}

		$sanitized_username = sanitize_username($name);

		if ($sanitized_username != $name) {
			// echo $main_smarty->get_config_vars("KAHUK_Visual_CheckField_AllowedChars") . " [$sanitized_username == $name]";
			echo $main_smarty->get_config_vars("KAHUK_Visual_CheckField_AllowedChars");
			die();
		}

		if (user_exists($name)) { // if username already exists
			echo $main_smarty->get_config_vars("KAHUK_Visual_CheckField_UserExists");
			die();
		}

		echo 'OK';
			
		break;

	case 'email':
		if ($email=='') { // if email contains invald characters
			echo $main_smarty->get_config_vars("KAHUK_Visual_CheckField_EmailInvalid");
			die();
		}

		if ($email) {
			if (email_exists($email)) { // if email already exists
				echo $main_smarty->get_config_vars("KAHUK_Visual_CheckField_EmailExists");
				die();
			}

			echo 'OK';
		}

		break;

	case 'password':
		if (strlen($password) < 9) { // if password is less than 5 characters
			echo $main_smarty->get_config_vars('KAHUK_Visual_Register_Error_FiveCharPass');
			die();
		}

		break;

	default:
		break;
}

die();
