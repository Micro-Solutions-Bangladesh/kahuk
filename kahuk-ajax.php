<?php
/**
 * Executing Ajax process.
 *
 * @since 5.0.0
 */
define('KAHUK_AJAX', true);

if (!defined('KAHUKPATH')) {
	define('KAHUKPATH', dirname( __FILE__ ) . '/');
}

include_once(KAHUKPATH . 'internal/Smarty.class.php');
$main_smarty = new Smarty;

include(KAHUKPATH . 'config.php');
include(KAHUKPATH_LIBS . 'smartyvariables.php');

check_referrer();

/**
 * Return status must be: none, success, error, warning, invalid
 */
$returnOutput = [
    'status' => 'none',
    'message' => '',
    'lastrequest' => time(),
];

$pagePrefix = sanitize_text_field(_get('prefix'));

if (file_exists(KAHUKPATH_PAGES . "ajax-prefixes/{$pagePrefix}.php")) {
    $action = _request('action');

    include KAHUKPATH_PAGES . "ajax-prefixes/{$pagePrefix}.php";
}

die();
