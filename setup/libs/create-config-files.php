<?php
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
 * Create /kahuk-configs.php file
 * 
 * @since 5.0.0
 */
function create_kahuk_configs_file() {

	$pos = strrpos( $_SERVER['SCRIPT_NAME'], '/' );

	if ( defined( 'KAHUK_INSTALLING' ) && KAHUK_INSTALLING ) {
		$pos = strrpos($_SERVER['SCRIPT_NAME'], "/setup");
	}

	$path = substr( $_SERVER['SCRIPT_NAME'], 0, $pos );

	if ( $path == "/" ) {
		$path = "";
	}

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

