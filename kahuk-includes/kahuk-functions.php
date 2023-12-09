<?php
/**
 * Kahuk CMS Version
 * 
 * @return string
 */
function kahuk_version($isNumber = false) {
	return ($isNumber ? "604" : "6.0.4");
}

/**
 * Get Website Name
 * 
 * @since 6.0.0
 * 
 * @return string website name
 */
function kahuk_site_name() {
    global $hooks;

    $site_name = kahuk_get_config("_site_name");

    return $hooks->apply_filters("site_name", $site_name);
}

/**
 * Check Admin section by IS_ADMIN constant and return true or false
 *
 * @return boolean
 */
function kahuk_is_admin()
{
    return (defined("IS_ADMIN") && IS_ADMIN);
}

/**
 * Create a file for antispam log
 *
 * @return string antispam log file
 */
function kahuk_log_file_antispam()
{
    global $hooks;

    return $hooks->apply_filters('kahuk_log_file_antispam', KAHUK_LOG_DIR . "antispam.log");
}

/**
 * Create a file for spam log
 *
 * @return string spam log file
 */
function kahuk_log_file_spam()
{
    global $hooks;

    return $hooks->apply_filters('kahuk_log_file_spam', KAHUK_LOG_DIR . "spam.log");
}

/**
 * Create a file for domain-blacklist log
 *
 * @return string domain-blacklist log file
 */
function kahuk_log_domain_blacklist()
{
    global $hooks;

    return $hooks->apply_filters('kahuk_log_domain_blacklist', KAHUK_LOG_DIR . "domain-blacklist.log");
}

/**
 * Create a file for domain-whitelist log
 *
 * @return string domain-whitelist log file
 */
function kahuk_log_domain_whitelist()
{
    global $hooks;

    return $hooks->apply_filters('kahuk_log_domain_whitelist', KAHUK_LOG_DIR . "domain-whitelist.log");
}

/**
 * Create a file name using the current date for error log
 *
 * @return string error log file name
 */
function kahuk_error_log_file_name()
{
	return date("dMY") . ".log";
}

/**
 * Create a file path using file name from kahuk_error_log_file_name() for error log
 *
 * @return string error log file path
 */
function kahuk_error_log_file_path($log_type = 'error') {
	return KAHUK_LOG_DIR . "{$log_type}-logs/" . kahuk_error_log_file_name();
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
 * Returns from either a POST/GET value or the default
 * 
 * @since 5.0.0
 * @updated 5.0.6
 * 
 * @return mixed The result.
 */
function _request($varname, $default = '')
{
	// $output = (isset($_REQUEST[$varname])) ? $_REQUEST[$varname] : $default;
	// $output = (isset($_POST[$varname])) ? $_POST[$varname] : (isset($_GET[$varname]) ? $_GET[$varname] : $default);

    return $_POST[$varname] ?? ($_GET[$varname] ?? $default);
}

/**
 * 
 */
function get_current_page() {
	$pagenum = intval(_request('page'));

	return ((0<$pagenum) ? $pagenum : 1);
}

/**
 * 
 */
function get_date($epoch) {
	// get date in the format year-month-day
    return date("Y-m-d", $epoch);
}

/**
 * Get the base of URL. 
 * For example, get_host_from_url will return kahuk.com if the URL was kahuk.com/support/
 * 
 * @since 5.0.0
 * @updated 5.0.7
 * 
 * @return string URL
 */
function get_host_from_url($url){
	$pos = strpos($url, '://');
	$protocol = strtolower(substr($url, 0, $pos));

	$req = substr($url, $pos+3);
	$pos = strpos($req, '/');
	
	if($pos === false) {
		$pos = strlen($req);
	}

	$host = substr($req, 0, $pos);

	return $host;
}

/**
 * Create slug from string by deleting everything except:
 * alphabets, numbers, and dash (-).
 *
 * @since 5.0.7
 * 
 * @return string
 */
function kahuk_create_slug($str) {
	$output = strtolower(trim($str));

    // replace spaces by dash
    $output = preg_replace('/\s+/', '-', $output);
    
    //
    $output = preg_replace('/[^a-z0-9-]/i', '', $output);

    // replace multiple dash by single dash
    $output = preg_replace( '|-+|', '-', $output );

    return $output;
}



/**
 * Create slug from story title (based on WP sanitize_title function)
 * 
 * @since 5.0.6
 * 
 * @return string
 */
function kahuk_create_slug_story( $title ) {
	$title = strip_tags( $title );
	// Preserve escaped octets.
	$title = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title );
	// Remove percent signs that are not part of an octet.
	$title = str_replace( '%', '', $title );
	// Restore octets.
	$title = preg_replace( '|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title );

	$title = strtolower( $title );

	// Convert &nbsp, &ndash, and &mdash to hyphens.
	$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );
	// Convert &nbsp, &ndash, and &mdash HTML entities to hyphens.
	$title = str_replace( array( '&nbsp;', '&#160;', '&ndash;', '&#8211;', '&mdash;', '&#8212;' ), '-', $title );
	// Convert forward slash to hyphen.
	$title = str_replace( '/', '-', $title );

	// $decoded_title = utf8_decode($title);
	// echo "decoded_title: " . $decoded_title . "<br>";

	// $encoded_title = utf8_encode($decoded_title);
	// echo "decoded_title: " . $encoded_title . "<br>";


	// Strip these characters entirely.
	$title = str_replace(
		array(
			// Soft hyphens.
			'%c2%ad',
			// &iexcl and &iquest.
			'%c2%a1',
			'%c2%bf',
			// Angle quotes.
			'%c2%ab',
			'%c2%bb',
			'%e2%80%b9',
			'%e2%80%ba',
			// Curly quotes.
			'%e2%80%98',
			'%e2%80%99',
			'%e2%80%9c',
			'%e2%80%9d',
			'%e2%80%9a',
			'%e2%80%9b',
			'%e2%80%9e',
			'%e2%80%9f',
			// Bullet.
			'%e2%80%a2',
			// &copy, &reg, &deg, &hellip, and &trade.
			'%c2%a9',
			'%c2%ae',
			'%c2%b0',
			'%e2%80%a6',
			'%e2%84%a2',
			// Acute accents.
			'%c2%b4',
			'%cb%8a',
			'%cc%81',
			'%cd%81',
			// Grave accent, macron, caron.
			'%cc%80',
			'%cc%84',
			'%cc%8c',
			// Some Unicode
			'&raquo;', // RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK
			'&laquo;', // LEFT-POINTING DOUBLE ANGLE QUOTATION MARK
			'&copy;', // COPYRIGHT SIGN
			'&reg;', // REGISTERED SIGN
			'&para;', // PILCROW SIGN
			'&divide;', // DIVISION SIGN
			'»',
			'&amp;',
			'&',
			'#',
			'?',
		),
		'',
		$title
	);

	// Convert &times to 'x'.
	$title = str_replace( '%c3%97', 'x', $title );

	// Kill entities.
	$title = preg_replace( '/&.+?;/', '', $title );
	$title = str_replace( '.', '-', $title );
	// $title = preg_replace( '/[^%a-z0-9 _-]/', '', $title );
	$title = preg_replace( '/\s+/', '-', $title );
	$title = preg_replace( '|-+|', '-', $title );
	$title = trim( $title, '-' );

	return $title;
}
