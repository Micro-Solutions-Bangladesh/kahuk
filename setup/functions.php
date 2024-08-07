<?php
/**
 * Check if the "kahuk-configs.php" file is readable
 * 
 * @since 5.0.8
 * 
 * @return boolean
 */
function kahuk_has_configs_file() {
    $file = KAHUKPATH . 'kahuk-configs.php';

    return is_readable($file);
}

/**
 * @since 5.0.0
 */
function kahuk_template_action_messages() {
	global $messagesArray;
	// print_r( $messagesArray );
    
    foreach( $messagesArray as $row ) {
		foreach( $row as $i => $msg ) {
			echo kahuk_create_markup_message( $msg, $i );
		}
    }

	//
	$builtInMessages = [
		"invaliddbuser" => "Please provide a valid user for Database!",
		"invaliddbpass" => "Database password is required!",
		"invaliddbname" => "Database name is required!",
		"invaliddbhost" => "Database host is required!",
	];

	if ( isset( $_REQUEST["errors"] ) ) {
		$errors = explode( ",", $_REQUEST["errors"] );

		foreach( $errors as $i => $code ) {
			if ( isset( $builtInMessages[$code] ) ) {
				kahuk_create_markup_message( $builtInMessages[$code], "warning" );
			}
		}
	}
}



/**
 * Check if table exist
 * 
 * @since 5.0.2
 * 
 * @return boolean true|false
 */
function kahuk_table_exist( $table_name ) {
	global $kahukDB;
	$output = false;

	$sql = "select 1 from `{$table_name}` LIMIT 1";

	$val = mysqli_query( $kahukDB, $sql );

	if ( $val !== false ) {
		$output = true;
	}

	return $output;
}

/**
 * Check database connection for Kahuk CMS setup
 * 
 * @since 5.0.2
 * 
 * @return boolean true|false
 */
function kahuk_check_db_connection() {
	$output = [
		"status" => false,
	];

	if ($conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
		$db_selected = mysqli_select_db( $conn, DB_NAME );

		if ($db_selected) {
			$output["status"] = true;
		} else {
			$output["message"] = "Error [" . DB_NAME . "]: " . mysqli_error($conn);
		}
	} else {
		$output["message"] = "Error: database connection failed!";
	}

	return $output;
}

/**
 * Check databse settings
 * 
 * @since 5.0.2
 * 
 * @return string|empty
 */
function kahuk_check_db_settings() {
	$errors = [];
	$isError = false;

	// print_r($_POST);
	// exit;

	$dbuser = $_POST["dbuser"];
	$dbpass = $_POST["dbpass"];
	$dbname = $_POST["dbname"];
	$dbhost = $_POST["dbhost"];

	if (empty($dbuser)) {
		$errors[] = "invaliddbuser";
		$isError = true;
	}

	if (empty($dbpass)) {
		$errors[] = "invaliddbpass";
		$isError = true;
	}

	if (empty($dbname)) {
		$errors[] = "invaliddbname";
		$isError = true;
	}

	if (empty($dbhost)) {
		$errors[] = "invaliddbhost";
		$isError = true;
	}

	if ($isError) {
		return implode(",", $errors);
	}

	$tblprefix = (!empty(trim($_POST["tblprefix"]))) ? $_POST["tblprefix"] : "kahuk_";

	define("DB_USER", $dbuser);
	define("DB_PASSWORD", $dbpass);
	define("DB_NAME", $dbname);
	define("DB_HOST", $dbhost);
	define("TABLE_PREFIX", $tblprefix);

	return "";
}

/**
 * Only for setup mode
 * 
 * @since 5.0.0
 */
function get_kahuk_file_uri( $file_path ) {
    $file = ltrim($file_path, "/");
	$output = "";

	if (defined("KAHUK_BASE_URL")) {
		$output = KAHUK_BASE_URL;
	} else {
		$output = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	}

	return $output . "/" . $file_path;
}


/**
 * Depricated, use kahuk_create_markup_message() function
 * 
 * @return void|string
 */
function kahuk_messages_markup( $msg, $type = "primary" ) {
    $output = [];

	$alertClass = $type;

	if ("error" == $type) {
		$alertClass = "danger";
	}


    $output[] = "<div class=\"alert alert-{$alertClass}\">";

	if( is_array( $msg ) ) {
		foreach( $msg as $v ) {
			$output[] = sprintf("<p>%s</p>", $v);
		}
	} else {
		$output[] = sprintf("<p>%s</p>", $msg);
	}
    
    $output[] = "</div>";

    return implode( "", $output );
}

/**
 * Create markup and display of different type of messages
 * 
 * @return void
 */
function _kahuk_messages_markup( $msgArray, $type = "info" ) {
    echo kahuk_messages_markup( $msgArray, $type );
}

/**
 * Create site base path
 * 
 * @since 5.0.0
 */
function kahuk_base_path() {
	$pos = strrpos( $_SERVER["SCRIPT_NAME"], "/" );

	if ( defined( "KAHUK_INSTALLING" ) && KAHUK_INSTALLING ) {
		$pos = strrpos($_SERVER["SCRIPT_NAME"], "/setup");
	}

	$path = substr( $_SERVER["SCRIPT_NAME"], 0, $pos );

	if ( $path == "/" ) {
		$path = "";
	}

	return $path;
}

/**
 * Create /kahuk-configs.php file
 * 
 * @since 5.0.0
 */
function create_kahuk_configs_file() {
	global $messagesArray;

	$path = kahuk_base_path();

	$root_url = (isset( $_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

	$sample_content = file_get_contents(KAHUKPATH . "kahuk-configs.php.sample");

	$new_content = str_replace("database_name_here", DB_NAME, $sample_content);
	$new_content = str_replace("database_username_here", DB_USER, $new_content);
	$new_content = str_replace("database_login_password_here", DB_PASSWORD, $new_content);
	$new_content = str_replace("database_host_here", DB_HOST, $new_content);
	$new_content = str_replace("database_tables_prefix_here", TABLE_PREFIX, $new_content);

	$new_content = str_replace("kahuk_base_url_here", $root_url . $path, $new_content);
	$new_content = str_replace("my_base_url_here", $root_url, $new_content);
	$new_content = str_replace("my_kahuk_base_here", $path, $new_content);

	//
	$path_to_kahuk_configs = KAHUKPATH . "kahuk-configs.php";

	if ($handle = fopen($path_to_kahuk_configs, "w")) {
		if (fwrite($handle, $new_content)) {
			fclose($handle);

			// echo kahuk_create_markup_message("<code>/kahuk-configs.php</code> file has been created!", "success");
			$messagesArray[] = [
				"status" => "success",
				"message" => "<code>/kahuk-configs.php</code> file has been created.",
			];
		}

		chmod($path_to_kahuk_configs, 0644);
	} else {
		$messagesArray[] = [
			"status" => "warning",
			"message" => "Fail to create <code>/kahuk-configs.php</code>.",
		];
	}
}


/**
 * Create /.htaccess file
 * 
 * @since 5.0.2
 */
function create_kahuk_htaccess_file() {
	$messagesArray = [];

	$path = defined("my_kahuk_base") ? my_kahuk_base : kahuk_base_path();
	$site_base = $path . "/";

	$sample_content = file_get_contents(KAHUKPATH . "htaccess.sample");

	$new_content = str_replace("__my_kahuk_base__", $site_base, $sample_content);

	//
	$path_to_file = KAHUKPATH . ".htaccess";

	if ($handle = fopen($path_to_file, "w")) {
		if (fwrite($handle, $new_content)) {
			fclose($handle);

			$messagesArray = [
				"status" => "success",
				"message" => "<code>{$path_to_file}</code> file has been created!",
			];
		}

		chmod($path_to_file, 0644);
	} else {
		$messagesArray = [
			"status" => "warning",
			"message" => "Fail to create <code>{$path_to_file}</code>.",
		];
	}

	return $messagesArray;
}
