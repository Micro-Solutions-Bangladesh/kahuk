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
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

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

if (file_exists(KAHUK_PAGES_DIR . "ajax-prefixes/{$pagePrefix}.php")) {
    $action = _request('action');

    include KAHUK_PAGES_DIR . "ajax-prefixes/{$pagePrefix}.php";
}

die();
