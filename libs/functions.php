<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * @since 5.0.6
 */
function kahuk_session_start() {
	if(!isset($_SESSION)) {
		session_start();
	}
}

/**
 * 
 * @since 4.1.5
 */
if ( !function_exists( 'gettext' ) ) {
	function _( $s ) {return $s;}
}

/**
 * a value 0 or 1 will be returned from the script that checks whether the content is left-to-right or right-to-left language. This varibale will be passed to /templates/bootstrap/link_summary.tpl and used on line 214 to apply dir="rtl" when applicable.
 * As per http://php.net/manual/en/regexp.reference.unicode.php and https://www.w3.org/International/questions/qa-scripts#which
 * 
 * @since 5.0.6
 * 
 * @return boolean
 */
function kahuk_is_rtl( $str ) {
	return (preg_match('/\p{Arabic}/u', $str) == 1 || preg_match('/\p{Hebrew}/u', $str) == 1 || preg_match('/\p{Nko}/u', $str) == 1 || preg_match('/\p{Syloti_Nagri}/u', $str) == 1 || preg_match('/\p{Thaana}/u', $str) == 1);
}


/**
 * Format the date, in localized format.
 * 
 * @since 5.0.6
 * 
 * @param int $timestamp Optional. Unix timestamp. Defaults to current time.
 * @param string $format PHP date format.
 * 
 * @return string|empty The date, translated if locale specifies it. False on invalid timestamp input.
 */
function kahuk_date($timestamp = '', $format = 'date') {
	if ($format == 'date') {
		$format = 'F j, Y';
	} else if ($format == 'datetime') {
		$format = 'F j, Y \a\t g:ia';
	}

	if ($timestamp == '') {
		$timestamp = time();
	}

	if (!is_numeric($timestamp)) {
		$timestamp = strtotime($timestamp);
	}

	return date($format, $timestamp);
}



/**
 * Count the number of words seperated by spaces
 * 
 * @since 5.0.6
 * 
 * return int number of words determined by spaces
 */
function kahuk_word_count($str) {
	// return str_word_count($str);

	$rs = explode(" ", $str);

	return count($rs);
}

/**
 * Get messages from the session
 * 
 * @since 5.0.0
 * 
 * @return array
 */
function kahuk_shorten_the_url($url) {
	if (!$url) {
		return '';
	}

	$parsed = parse_url($url);
	$scheme = ($parsed['scheme'] ?? "https");

	return  $scheme . "://" . $parsed['host'];
}

/**
 * Create markup of notification message
 * 
 * @since 6.0.0
 * 
 * @return string markup
 */
function kahuk_create_markup_message($msg, $msgtype = "info") {
	$markup = "<div class=\"alert alert-%s mb-4\"><div class=\"m-0\">%s</div></div>";

	return sprintf(
		$markup, $msgtype, $msg
	);
}

/**
 * Create markup of messages
 * 
 * @since 6.0.0
 * 
 * @return string markup
 */
function kahuk_markup_messages($messages, $wrapper="<div class=\"messages-wrapper\">%s</div>") {
	$output = "";
	$markup = "<div class=\"alert alert-%s mb-4\"><div class=\"m-0\">%s</div></div>";

	foreach($messages as $message) {
		$output .= sprintf(
			$markup, $message["msgtype"], $message["msg"]
		);
	}

	if ($wrapper) {
		return sprintf(
			$wrapper, $output
		);
	}

	return $output;
}

/**
 * Get messages from the session
 * 
 * @since 5.0.0
 * 
 * @return array
 */
function kahuk_get_session_messages($isMarkup = false, $wrapper="", $sessionName = "kahuk_session_message") {
	kahuk_session_start();

	$output = ( isset( $_SESSION[$sessionName] ) ? $_SESSION[$sessionName] : [] );
	$_SESSION[$sessionName] = [];

	if ($isMarkup) {
		$output = kahuk_markup_messages($output, $wrapper);
	}

	return $output;
}

/**
 * Save message into the session
 * 
 * @param $msg Message
 * @param msgType could be 'success', 'info', 'warning', 'error'
 * 
 * @since 5.0.0
 */
function kahuk_set_session_message( $msg, $msgtype = "info", $msgcode = "", $sessionName = "kahuk_session_message" ) {
	kahuk_session_start();

	$output = [];

	if ( isset( $_SESSION[$sessionName] ) ) {
		$output = $_SESSION[$sessionName];
	}

	$output[] = [
		'msgtype' => $msgtype,
		'msg' => $msg,
		'msgcode' => $msgcode,
	];

	$_SESSION[$sessionName] = $output;
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
 * Retrieves a modified URL query string.
 *
 * You can rebuild the URL and append query variables to the URL query by using this function.
 * There are two ways to use this function; either a single key and value, or an associative array.
 *
 * Using a single key and value:
 *
 *     add_query_arg( 'key', 'value', 'http://example.com' );
 *
 * Using an associative array:
 *
 *     add_query_arg( array(
 *         'key1' => 'value1',
 *         'key2' => 'value2',
 *     ), 'http://example.com' );
 *
 * Omitting the URL from either use results in the current URL being used
 * (the value of `$_SERVER['REQUEST_URI']`).
 *
 * Values are expected to be encoded appropriately with urlencode() or rawurlencode().
 * 
 * @param string|array $key   Either a query variable key, or an associative array of query variables.
 * @param string       $value Optional. Either a query variable value, or a URL to act upon.
 * @param string       $url   Optional. A URL to act upon.
 * @return string New URL query string (unescaped).
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
function kahuk_redirect( $location = '', $status = 302 ) {
	if ( $status < 300 || 399 < $status ) {
		die( 'HTTP redirect status code must be a redirection code, 3xx.' );
	}

	if (empty($location)) {
		$location = kahuk_root_url();
	}

	header( "Location: $location", true, $status );
	exit;
}


/**
 * Log message of unexpected activities
 * 
 * @since 5.0.6
 * 
 * return void
 */
function kahuk_log_unexpected($message) {
	if (!KAHUK_DEBUG_MANUAL) {
		return;
	}

	static $permalink = false;
	$current_time = kahuk_date('', 'datetime');
	$output = "";

	if (!$permalink) {
		$permalink = kahuk_get_permalink();
		$output = "\n\n" . "*** ATTENTION *** [{$current_time}] {$permalink}\n";
	}

	$output .= $message . "\n";
	$logFile = kahuk_error_log_file_path('attention');

	error_log($output, 3, $logFile);
}

/**
 * Log query message
 * 
 * @since 5.0.7
 * 
 * return void
 */
function kahuk_log_queries($message) {
	if (!LOG_QUERY_ACTIVITY) {
		return;
	}

	static $permalink = false;
	$current_time = kahuk_date('', 'datetime');
	$output = "";

	if (!$permalink) {
		$permalink = kahuk_get_permalink();
		$output = "\n\n" . "*** QUERY LOGS *** [{$current_time}] {$permalink}\n";
	}

	$output .= $message . "\n";
	$logFile = kahuk_error_log_file_path('attention');

	error_log($output, 3, $logFile);
}

/**
 * Log message of schedule activities
 * 
 * @since 5.0.7
 * 
 * return void
 */
function kahuk_log_schedule($message) {
	$permalink = kahuk_get_permalink();
	$current_time = kahuk_date('', 'datetime');

	$output = "*** SCHEDULE *** [{$current_time}] {$permalink}\n{$message}\n\n";
	$logFile = kahuk_error_log_file_path('attention');

	error_log($output, 3, $logFile);
}

/**
 * Log message to the file comes from kahuk_error_log_file_path()
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

	$logFile = kahuk_error_log_file_path();

	error_log($output, 3, $logFile);
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
	header("Location: " . kahuk_create_url("error-404/"));
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
 * Generate Password
 * 
 * @since 5.0.3
 * 
 * @return string
 */
function kahuk_generate_password( $psaa_length = 8 ) {
	$output = [];

	// $alphabets = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890~!@#*';
	$alphabets = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890{}[]';

    $alphaLength = strlen($alphabets) - 1; //put the length -1 in cache
    
	for ($i = 0; $i < $psaa_length; $i++) {
        $n = rand(0, $alphaLength);
        $output[] = $alphabets[$n];
    }

    return implode($output); //turn the array into a string
}



/**
 * Verifies that an email is valid.
 *
 * @since 5.0.5
 *
 * @param string $email      Email address to verify.
 * 
 * @return string|array Valid email address on success, array on failure.
 */
function is_email( $email ) {
	// Test for the minimum length the email can be.
	if ( strlen( $email ) < 6 ) {
		return [
			'status' => false,
			'code' => 'email_too_short'
		];
	}

	// Test for an @ character after the first position.
	if ( strpos( $email, '@', 1 ) === false ) {
		return [
			'status' => false,
			'code' => 'email_no_at'
		];
	}

	// Split out the local and domain parts.
	list( $local, $domain ) = explode( '@', $email, 2 );

	// LOCAL PART
	// Test for invalid characters.
	if ( ! preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) {
		return [
			'status' => false,
			'code' => 'local_invalid_chars'
		];
	}

	// DOMAIN PART
	// Test for sequences of periods.
	if ( preg_match( '/\.{2,}/', $domain ) ) {
		return [
			'status' => false,
			'code' => 'domain_period_sequence'
		];
	}

	// Test for leading and trailing periods and whitespace.
	if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) {
		return [
			'status' => false,
			'code' => 'domain_period_limits'
		];
	}

	// Split the domain into subs.
	$subs = explode( '.', $domain );

	// Assume the domain will have at least two subs.
	if ( 2 > count( $subs ) ) {
		return [
			'status' => false,
			'code' => 'domain_no_periods'
		];
	}

	// Loop through each sub.
	foreach ( $subs as $sub ) {
		// Test for leading and trailing hyphens and whitespace.
		if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) {
			return [
				'status' => false,
				'code' => 'sub_hyphen_limits'
			];
		}

		// Test for invalid characters.
		if ( ! preg_match( '/^[a-z0-9-]+$/i', $sub ) ) {
			return [
				'status' => false,
				'code' => 'sub_invalid_chars'
			];
		}
	}

	// Congratulations, your email made it!
	return $email;
}


