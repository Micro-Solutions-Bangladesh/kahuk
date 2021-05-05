<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * 
 * @since 4.1.5
 */
if ( !function_exists( 'gettext' ) ) {
	function _( $s ) {return $s;}
}

/**
 * Get messages from the session
 * 
 * @since 5.0.0
 * 
 * @return array
 */
function kahuk_get_session_messages() {
	session_start();

	$output = ( isset( $_SESSION['kahuk_session_message'] ) ? $_SESSION['kahuk_session_message'] : [] );
	$_SESSION['kahuk_session_message'] = [];

	return $output;
}

/**
 * Save message into the session
 * 
 * @since 5.0.0
 */
function kahuk_set_session_message( $msg, $msgtype = 'info', $msgcode = '' ) {
	session_start();
	$output = [];

	if ( isset( $_SESSION['kahuk_session_message'] ) ) {
		$output = $_SESSION['kahuk_session_message'];
	}

	$output[] = [
		'msgtype' => $msgtype,
		'msg' => $msg,
		'msgcode' => $msgcode,
	];

	$_SESSION['kahuk_session_message'] = $output;
}




/**
 * Sanitize numeric value from text
 * 
 * @since 5.0.0
 */
function sanitize_number( $text ) {
	$output = strip_tags( trim( $text ) );

	return (int) $output;
}


/**
 * Add or remove slash on the basis of $store variable
 * 
 * @since 5.0.0
 */
function kahuk_slashes( $text, $store = true ) {
	if ( $store ) {
		return addslashes( $text );
	} else {
		return stripslashes( $text );
	}
}

/**
 * Filters text content and strips out disallowed HTML.
 *
 * @since 5.0.0
 *
 * @param string         $string            Text content to filter.
 * @param array[]|string $allowable_tags    An array of allowed HTML elements and attributes, or a
 *                                          context name such as 'post'.
 * @param string[]       $allowed_protocols Array of allowed URL protocols.
 * 
 * @return string Filtered content containing only the allowed HTML.
 */
function kahuk_kses( $string, $allowable_tags ) {
	return stripslashes( preg_replace_callback(
		'/<([^>]+)>/is',
		function ( $m ) {
			return '<' . sanitize($m[1], 5) . '>';
		},
		strip_tags($string, $allowable_tags)
	) );
}

/**
 * Replaces double line breaks with paragraph elements.
 * 
 * @since 5.0.0
 * 
 * 
 */
function kahuk_autop( $text ) {
	if ( trim( $text ) === '' ) {
		return '';
	}

    // $text = nl2br( $text, false );

    // Just to make things a little easier, pad the end.
	$text = $text . "\n";

    // Change multiple <br>'s into two line breaks, which will turn into paragraphs.
	$text = preg_replace( '|<br\s*/?>\s*<br\s*/?>|', "\n\n", $text );

    // Remove more than two contiguous line breaks.
	$text = preg_replace( "/\n\n+/", "\n\n", $text );

    // Split up the contents into an array of strings, separated by double line breaks.
	$textLines = preg_split( '/\n\s*\n/', $text, -1, PREG_SPLIT_NO_EMPTY );

    // Reset $text prior to rebuilding.
	$text = '';

	// Rebuild the content as a string, wrapping every bit with a <p>.
	foreach ( $textLines as $tinkle ) {
		$text .= '<p>' . trim( $tinkle, "\n" ) . "</p>\n";
	}

    // Under certain strange conditions it could create a P of entirely whitespace.
	$text = preg_replace( '|<p>\s*</p>|', '', $text );

    // In some cases <li> may get wrapped in <p>, fix them.
	$text = preg_replace( '|<p>(<li.+?)</p>|', '$1', $text );

    return $text;
}


/**
 * 
 */
function add_query_arg( ...$args ) {
	if ( is_array( $args[0] ) ) {
		if ( count( $args ) < 2 || false === $args[1] ) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = $args[1];
		}
	} else {
		if ( count( $args ) < 3 || false === $args[2] ) {
			$uri = $_SERVER['REQUEST_URI'];
		} else {
			$uri = $args[2];
		}
	}

	$frag = strstr( $uri, '#' );

	if ( $frag ) {
		$uri = substr( $uri, 0, -strlen( $frag ) );
	} else {
		$frag = '';
	}

	if ( 0 === stripos( $uri, 'http://' ) ) {
		$protocol = 'http://';
		$uri      = substr( $uri, 7 );
	} elseif ( 0 === stripos( $uri, 'https://' ) ) {
		$protocol = 'https://';
		$uri      = substr( $uri, 8 );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		list( $base, $query ) = explode( '?', $uri, 2 );
		$base .= '?';
	} elseif ( $protocol || strpos( $uri, '=' ) === false ) {
		$base  = $uri . '?';
		$query = '';
	} else {
		$base  = '';
		$query = $uri;
	}

	parse_str( $query, $qs );

	// $qs = urlencode_deep( $qs ); // This re-URL-encodes things that were already in the query string.

	if ( is_array( $args[0] ) ) {
		foreach ( $args[0] as $k => $v ) {
			$qs[ $k ] = $v;
		}
	} else {
		$qs[ $args[0] ] = $args[1];
	}

	foreach ( $qs as $k => $v ) {
		if ( false === $v ) {
			unset( $qs[ $k ] );
		}
	}

	$ret = build_query( $qs );

	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );

	return $ret;
}



/**
 * Redirects to another page.
 *
 * @since 5.0.0
 *
 * @param string $location      The path or URL to redirect to.
 * @param int    $status        Optional. HTTP response status code to use. Default '302' (Moved Temporarily).
 * 
 * @return void
 */
function kahuk_redirect( $location, $status = 302 ) {
	if ( $status < 300 || 399 < $status ) {
		die( 'HTTP redirect status code must be a redirection code, 3xx.' );
	}

	header( "Location: $location", true, $status );
	exit;
}

/**
 * Log message to the debug.log file
 * Edit the constant DEV_MODE_ON while you are debugging the Kahuk CMS
 * DEV_MODE_ON should always false in a live/production site
 * 
 * @since 5.0.0
 */
function kahuk_debug_log($message, $line = '', $func = '', $file = '')
{
	$output = "";

	if (defined('DEV_MODE_ON') && (true == DEV_MODE_ON)) {
		$output .= "\n===";
	}

	if (!empty($file)) {
		$output .= "\n" . "File: " . $file;
	}

	if (!empty($func)) {
		$output .= "\t" . "Function: " . $func;
	}

	if (!empty($line)) {
		$output .= "\t" . "[Line: " . $line . "]";
	}

	if (!empty($message)) {
		$output .= "\n" . $message;
	}

	_kahuk_debug_log($output);
}

/**
 * Log message to the debug.log file
 * 
 * @since 5.0.0
 */
function _kahuk_debug_log( $message )
{
	if (defined('DEV_MODE_ON') && (true == DEV_MODE_ON)) {
		error_log( "\n" . $message, 3, KAHUK_LOG_DIR . "debug.log");
	} else {
		kahuk_error_log( $message );
	}
}

/**
 * Log message to the error.log file
 * 
 * @since 5.0.0
 */
function kahuk_error_log($message, $file = '', $line = '')
{
	$output = "";

	if (!empty($file)) {
		$output .= "\n\n===\n";

		$output .= "File: " . $file;

		if (!empty($line)) {
			$output .= " [ Line: {$line} ]";
		}
	}

	$output .= "\n" . $message;

	error_log($output, 3, KAHUK_LOG_DIR . "error.log");
}


/**
 * check if URL is valid format
 * fixed the regex to account for new domains up to 15 characters long
 * 
 * @since 5.0.0
 */
function kahuk_check_url_format($url)
{
	$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w]([-\d\w]{0,253}[\d\w])?\.)+[\w]{2,15}(:[\d]+)?(\/([-+_~.,\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.,\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.,\/\d\w]|%[a-fA-f\d]{2,2})*)?$/';

	return preg_match($pattern, $url); // Returns true if a link
}


/**
 * Redirect to 404 page
 * 
 * @since 5.0.0
 */
function kahuk_redirect_404()
{
	header("Location: " . my_kahuk_base . "/error_404.php");
	die();
}

/**
 * Retrieve a list of protocols to allow in HTML attributes.
 *
 * @since 5.0.0
 *
 * @return string[] Array of allowed protocols. Defaults to an array containing 'http', 'https'.
 */
function kahuk_allowed_protocols() {
	static $protocols = array();

	if ( empty( $protocols ) ) {
		$protocols = array( 'http', 'https' );
	}

	return $protocols;
}

/**
 * Returns either a GET value or the default
 * 
 * @since 5.0.0
 * 
 * @return mixed The result.
 */
function _get($varname, $default = '')
{
	$output = (isset($_GET[$varname])) ? $_GET[$varname] : $default;

    return $output;
}

/**
 * Returns either a POST value or the default
 * 
 * @since 5.0.0
 * 
 * @return mixed The result.
 */
function _post($varname, $default = '')
{
	$output = (isset($_POST[$varname])) ? $_POST[$varname] : $default;

    return $output;
}

/**
 * Returns either a REQUEST value or the default
 * 
 * @since 5.0.0
 * 
 * @return mixed The result.
 */
function _request($varname, $default = '')
{
	$output = (isset($_REQUEST[$varname])) ? $_REQUEST[$varname] : $default;

    return $output;
}
