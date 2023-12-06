<?php
define( 'KAHUK_INSTALLING', true );
ini_set('display_errors', 1);

if ( ! defined( 'KAHUKPATH' ) ) {
	define( 'KAHUKPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

global $step, $isNewSetup, $bodyClass, $messagesArray, $redirectTo;

$redirectTo = "./index.php?step=setup";
$isNewSetup    = true;
$bodyClass     = '';
$messagesArray = [];

$step          = isset( $_GET["step"] ) ? $_GET["step"] : "setup";
$nextStep = "";

/**
 * Check for the required files
 */
$file = KAHUKPATH . "libs/functions.php";

if (!is_readable($file)) {
    die("Upload the full package of Kahuk CMS hierarchical as it is.");
}

require_once KAHUKPATH . "kahuk-includes/kahuk-functions.php";
require_once KAHUKPATH . "libs/functions.php";

require_once KAHUKPATH . "libs/wp/formatting.php";
require_once KAHUKPATH . "libs/wp/functions.php";
require_once KAHUKPATH . "libs/kahuk-functions-sanitize.php";

//
$fileSample = KAHUKPATH . "kahuk-configs.php.sample";

if (!is_readable($fileSample)) {
    kahuk_set_session_message(
        "File <code>/kahuk-configs.php.sample</code> file does not found. Please upload all the files and folders from Kahuk package",
        "warning"
    );

    kahuk_redirect($redirectTo);
}

// Check for the valid parameters
$pages = ["setup", "setup-1", "setup-2", "setup-3", "setup-4"];

if (!in_array($step, $pages)) {
    kahuk_redirect($redirectTo);
}

include_once KAHUKPATH . "setup/languages/lang_english.php";
require_once KAHUKPATH . "setup/functions.php";
include_once(KAHUKPATH . "setup/templates/{$step}.php");

