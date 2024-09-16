<?php

if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

class UserAuth {
	var $user_id       = 0;
	var $user_login    = "";
	var $user_email    = "";
	var $md5_pass      = "";
	var $authenticated = false;
	var $user_level = "";
	var $auth_session_name = "_kahuk_user_authentication_data";

	// Additional parameters for cookie security
	protected $ip, $user_agent;

	// To be implemented for proper security
	private $token, $validator;

	function __construct() {
		kahuk_session_start();

		$this->ip = check_ip_behind_proxy();
		// $this->user_agent = $_SERVER['HTTP_USER_AGENT'];

		$sesRecord = (isset($_SESSION[$this->auth_session_name]) ? $_SESSION[$this->auth_session_name] : []);

		if (
			!empty($sesRecord) &&
			isset($sesRecord["user_email"]) &&
			$sesRecord["authenticated"] == true &&
			$sesRecord["session_ip"] == $this->ip
		) {
			$this->user_level = $sesRecord["user_level"];
			$this->user_email = $sesRecord["user_email"];
			$this->user_login = $sesRecord["user_login"];
			$this->user_id = $sesRecord["user_id"];
			$this->authenticated = true;
		}
	}

	/**
	 * User Authentication (Login)
	 * 
	 * This function will depricate the $this->Authenticate() function
	 */
	function kahuk_authentication($username, $pass, $remember = false) {
		global $db, $main_smarty;

		$output = [
			"status" => false,
		];
		$dbusername =  $db->escape(sanitize_text_field($username));
		$isEmail = sanitize_email($dbusername);

		if ($isEmail) {
			$sql = "SELECT * FROM " . table_users . " WHERE user_email= '$dbusername'";
		} else {
			$sql = "SELECT * FROM " . table_users . " WHERE user_login = '$dbusername'";
		}

		$dbRecord = $db->get_row($sql, ARRAY_A);

		if (!empty($dbRecord)) {
			$user_id = $dbRecord["user_id"];
			$hashedPass = $dbRecord["user_pass"];

			$hashTrim = substr($hashedPass, (SALT_LENGTH + 7));
			$salt = substr($hashedPass, 7, SALT_LENGTH);

			if (password_verify(sha1($salt . $pass), $hashTrim)) {
				if ($dbRecord["user_level"] == "unverified") {
					// User email is NOT verified
					$output["msg"] = "You need to verify your email address before login.";
				} else {
					if ($dbRecord["user_status"] == "enable") {
						// Let's create user session
						kahuk_session_start();
	
						$this->user_level = $dbRecord["user_level"];
						$this->user_login = $dbRecord["user_login"];
						$this->user_email = $dbRecord["user_email"];
						$this->user_id = $dbRecord["user_id"];
						$this->authenticated = true;
	
						$authDataSession = [
							"authenticated" => true,
							"session_ip" => $this->ip,
							"user_id" => $dbRecord["user_id"],
							"user_email" => $dbRecord["user_email"],
							"user_login" => $dbRecord["user_login"],
							"user_level" => $dbRecord["user_level"],
						];
	
						$_SESSION[$this->auth_session_name] = $authDataSession;
	
						$sql = "UPDATE " . table_users . " SET user_lastip = '{$this->ip}', user_lastlogin = NOW() WHERE user_id = '{$user_id}' LIMIT 1";
						$db->query($sql);
	
						// Authentication Successful
						$output["status"] = true;
						$output["msg"] = "Login Successful.";
					} else {
						$output["msg"] = "Login successful, however the user is disable.";
					}
				}
			} else {
				$output["msg"] = $main_smarty->get_config_vars('KAHUK_Visual_Login_Error');
			}
		} else {
			$output["msg"] = $main_smarty->get_config_vars('KAHUK_Visual_Login_Error');
		}

		return $output;
	}

	/**
	 * Delete user session related to authentication
	 * 
	 * This function will depricate the $this->Logout() function
	 */
	function kahuk_logout($url='./') {
		if (isset($_SESSION[$this->auth_session_name])) {
			$_SESSION[$this->auth_session_name] = [];
			unset($_SESSION[$this->auth_session_name]);
		}

		kahuk_redirect(KAHUK_BASE_URL);
	}
}

$current_user = new UserAuth();

