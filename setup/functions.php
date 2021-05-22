<?php
/**
 * Generate Password
 * 
 * @since 5.0.3
 * 
 * @return string
 */
function kahuk_generate_password( $psaa_length = 10 ) {
	$output = [];

	$alphabets = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#*';
    $alphaLength = strlen($alphabets) - 1; //put the length -1 in cache
    
	for ($i = 0; $i < $psaa_length; $i++) {
        $n = rand(0, $alphaLength);
        $output[] = $alphabets[$n];
    }

    return implode($output); //turn the array into a string
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
	$output = false;

	if ( $conn = @mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD ) ) {
		$db_selected = mysqli_select_db( $conn, DB_NAME );

		if ( !$db_selected ) {
			die ('Error [' . DB_NAME . '] : ' . mysqli_error( $conn ) );
		}

		$output = true;

	} else {
		die ( 'Error: database connection failed!' );
	}

	return $output;
}

/**
 * Check databse settings
 * 
 * @since 5.0.2
 */
function kahuk_check_db_settings() {
	$errors = [];
	$isError = false;

	$dbuser = $_POST['dbuser'];
	$dbpass = $_POST['dbpass'];
	$dbname = $_POST['dbname'];
	$dbhost = $_POST['dbhost'];

	if ( empty( $dbuser ) ) {
		$errors[] = "invaliddbuser";
		$isError = true;
	}

	if ( empty( $dbpass ) ) {
		$errors[] = "invaliddbpass";
		$isError = true;
	}

	if ( empty( $dbname ) ) {
		$errors[] = "invaliddbname";
		$isError = true;
	}

	if ( empty( $dbhost ) ) {
		$errors[] = "invaliddbhost";
		$isError = true;
	}

	if ( $isError ) {
		return implode(",", $errors);
	}

	$tblprefix = ( ! empty( trim( $_POST['tblprefix'] ) ) ) ? $_POST['tblprefix'] : 'kahuk_life_';
	$seourl    = ( isset( $_POST['seourl'] ) && ( 2 && intval( $_POST['seourl'] ) ) ) ? "true" : "false";

	define( 'DB_USER', $dbuser );
	define( 'DB_PASSWORD', $dbpass );
	define( 'DB_NAME', $dbname );
	define( 'DB_HOST', $dbhost );
	define( 'TABLE_PREFIX', $tblprefix );
	define( 'SEO_FRIENDLY_URL', $seourl );

	return '';
}



/**
 * @since 5.0.0
 */
function kahuk_template_action_messages() {
	global $messagesArray;
	// print_r( $messagesArray );
    
    foreach( $messagesArray as $row ) {
		foreach( $row as $i => $msg ) {
			_kahuk_messages_markup( $msg, $i );
		}
    }

	//
	$builtInMessages = [
		'invaliddbuser' => "Please provide a valid user for Database!",
		'invaliddbpass' => "Database password is required!",
		'invaliddbname' => "Database name is required!",
		'invaliddbhost' => "Database host is required!",
	];

	if ( isset( $_REQUEST['errors'] ) ) {
		$errors = explode( ",", $_REQUEST['errors'] );

		foreach( $errors as $i => $code ) {
			if ( isset( $builtInMessages[$code] ) ) {
				_kahuk_messages_markup( $builtInMessages[$code], 'warning' );
			}
		}
	}
}



/**
 * 
 */
// function kahuk_template_step4() {
// 	global $messagesArray;
// 	$hasError = false;

//     foreach( $messagesArray as $row ) {
// 		foreach( $row as $i => $msg ) {
// 			_kahuk_messages_markup( $msg, $i );

// 			if ( 'danger' == $i ) {
// 				$hasError = true;
// 			}
// 		}
//     }

// 	if ( $hasError ) {
// 		_kahuk_messages_markup( '<strong>Installation Unsuccessfull!</strong>', 'danger' );
// 	} else {
// 		_kahuk_messages_markup( '<strong>Cheers! Installation Successfull!</strong>', 'danger' );
// 	}
// }


/**
 * 
 */
function kahuk_template_step0() {
	// global $step;

	// kahuk_template_action_messages();
?>
<p>
	Before getting started, we need some information on the database. You will need to know the following items before proceeding.
</p>
<ol>
	<li>Database name</li>
	<li>Database username</li>
	<li>Database password</li>
	<li>Database host</li>
	<li>Table prefix (just as a good practice)</li>
</ol>
<p>
	These information will require to create the <code>kahuk-configs.php</code> file.
</p>

<div class="row">
	<div class="col tac">
		<a href="index.php?step=1" class="link-btn lets-go">Install New Site &raquo;</a>
	</div>
	<div class="col tac">
		<a href="index.php?step=11" class="link-btn lets-go">Upgrade Plikli 4.1.5 to Kahuk 5.0.x &raquo;</a>
	</div>
</div>
<?php
}

/**
 * 
 */
function kahuk_template_header() {
	global $step, $bodyClass; 
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="assets/css/styles.css" rel="stylesheet">

    <title>Kahuk CMS Setup</title>
</head>
<body class="<?php echo 'step-' . $step . ' ' .$bodyClass; ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="main-content">
                    <div class="header-content">
                        <h1 class="logo-wrap">
                            <img src="../templates/admin/img/kahuk.png" alt="Kahuk CMS Setup">
                            <span class="screen-reader-text">Welcome to Kahuk CMS Setup.</span>
                        </h1>
                    </div>
                    <div class="main-content">
<?php
						kahuk_template_action_messages();
}

/**
 * 
 */
function kahuk_template_footer() {
?>
                    </div>
                    <!-- <div class="footer-content"></div> -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}






/**
 * Only for setup mode
 * 
 * @since 5.0.0
 */
function get_kahuk_file_uri( $file_path ) {
    $file = ltrim( $file_path, '/' );
	$output = '';

	if ( defined( 'KAHUK_BASE_URL' ) ) {
		$output = KAHUK_BASE_URL;
	} else {
		$output = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]";
	}

	return $output . "/" . $file_path;
}






/**
 * 
 * 
 * @return void|string
 */
function kahuk_messages_markup( $msg, $type = 'primary' ) {
    $output = [];

	$alertClass = $type;

	if ( 'error' == $type ) {
		$alertClass = 'danger';
	}


    $output[] = '<div class="alert alert-' .$alertClass . '">';

	if( is_array( $msg ) ) {
		foreach( $msg as $v ) {
			$output[] = sprintf("<p>%s</p>", $v);
		}
	} else {
		$output[] = sprintf("<p>%s</p>", $msg);
	}
    
    $output[] = '</div>';

    return implode( "", $output );
}

/**
 * Create markup and display of different type of messages
 * 
 * @return void
 */
function _kahuk_messages_markup( $msgArray, $type = 'info' ) {
    echo kahuk_messages_markup( $msgArray, $type );
}

/**
 * Create /settings.php file
 * 
 * @since 5.0.0
 */
function create_settings_file() {
	$default_content = file_get_contents( KAHUKPATH . 'settings.php.default' );

	$new_content = str_replace( "table_prefix_here", TABLE_PREFIX, $default_content );

	$path_to_file = KAHUKPATH . 'settings.php';

	if ( $handle = fopen( $path_to_file, 'w' ) ) {
		if ( fwrite( $handle, $new_content ) ) {
			fclose( $handle );

			_kahuk_messages_markup( '<code>/settings.php</code> file has been created!', 'success' );
		}

		chmod( $path_to_file, 0644 );
	}
}


/**
 * Create site base path
 * 
 * @since 5.0.0
 */
function kahuk_base_path() {
	$pos = strrpos( $_SERVER['SCRIPT_NAME'], '/' );

	if ( defined( 'KAHUK_INSTALLING' ) && KAHUK_INSTALLING ) {
		$pos = strrpos($_SERVER['SCRIPT_NAME'], "/setup");
	}

	$path = substr( $_SERVER['SCRIPT_NAME'], 0, $pos );

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
	$path = kahuk_base_path();

	$root_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]";


	// $default_file_path = KAHUKPATH . 'kahuk-configs.php.default';
	$default_content = file_get_contents( KAHUKPATH . 'kahuk-configs.php.default' );

	$new_content = str_replace( "database_name_here", DB_NAME, $default_content );
	$new_content = str_replace( "database_username_here", DB_USER, $new_content );
	$new_content = str_replace( "database_login_password_here", DB_PASSWORD, $new_content );
	$new_content = str_replace( "database_host_here", DB_HOST, $new_content );
	$new_content = str_replace( "database_tables_prefix_here", TABLE_PREFIX, $new_content );

	$new_content = str_replace( "kahuk_base_url_here", $root_url . $path, $new_content );
	$new_content = str_replace( "my_base_url_here", $root_url, $new_content );
	$new_content = str_replace( "my_kahuk_base_here", $path, $new_content );

	$new_content = str_replace( "seo_friendly_url_here", SEO_FRIENDLY_URL, $new_content );

	//
	$path_to_kahuk_configs = KAHUKPATH . 'kahuk-configs.php';

	if ( $handle = fopen( $path_to_kahuk_configs, 'w' ) ) {
		if ( fwrite( $handle, $new_content ) ) {
			fclose( $handle );

			_kahuk_messages_markup( '<code>/kahuk-configs.php</code> file has been created!', 'success' );
		}

		chmod( $path_to_kahuk_configs, 0644 );
	}
}


/**
 * Create /.htaccess file
 * 
 * @since 5.0.2
 */
function create_kahuk_htaccess_file() {

	if ( defined( "SEO_FRIENDLY_URL" ) && SEO_FRIENDLY_URL ) {
		$path = defined( 'my_kahuk_base' ) ? my_kahuk_base : kahuk_base_path();
		$site_base = $path . '/';

		$default_content = file_get_contents( KAHUKPATH . 'htaccess.default' );

		$new_content = str_replace( "__my_kahuk_base__", $site_base, $default_content );

		//
		$path_to_file = KAHUKPATH . '.htaccess';

		if ( $handle = fopen( $path_to_file, 'w' ) ) {
			if ( fwrite( $handle, $new_content ) ) {
				fclose( $handle );

				_kahuk_messages_markup( '<code>/.htaccess</code> file has been created!', 'success' );
			}

			chmod( $path_to_file, 0644 );
		}
	}
}
