<?php
// 
global $kahukPlugins, $pluginsInstalledArray;

//
require_once KAHUK_INC_DIR . 'kahuk-functions.php';
require_once KAHUK_INC_DIR . 'functions-data-enum.php';
require_once KAHUK_INC_DIR . 'functions-html.php';

// Set Error Log File
$error_log_file_name = kahuk_error_log_file_name();
ini_set('error_log', KAHUK_LOG_DIR . "error-logs/" . $error_log_file_name); // Error log file location

/**
 * Set PHP error reporting based on Kahuk debug settings.
 *
 * @since 5.0.0
 * @updated 5.0.6
 * @access private
 */
if (KAHUK_DEBUG_DISPLAY) {
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
}

if (KAHUK_DEBUG) {
	error_reporting(E_ALL);
} else {
	if (defined("KAHUK_DEBUG_LEVEL")) {
		error_reporting(KAHUK_DEBUG_LEVEL);
	} else {
		error_reporting(E_CORE_ERROR);
	}
}

//
include_once KAHUK_INC_DIR . 'settings-from-db.php';

//
require_once KAHUK_LIBS_DIR . 'kahuk-class-error.php';

//
include_once KAHUK_LIBS_DIR . 'functions-data.php';

//
require_once KAHUK_LIBS_DIR . 'wp/formatting.php';
require_once KAHUK_LIBS_DIR . 'wp/functions.php';
require_once KAHUK_LIBS_DIR . 'kahuk-functions-sanitize.php';

//
require_once KAHUK_LIBS_DIR . 'functions-process-url.php';
require_once KAHUK_LIBS_DIR . 'functions.php';

//
require_once(KAHUK_LIBS_DIR . 'check_behind_proxy.php');

//
include_once KAHUK_LIBS_DIR . 'login.php';

//
require_once KAHUK_LIBS_DIR . 'utils.php';

//
require_once KAHUK_LIBS_DIR . 'functions-user-authentication.php';

// Load early Kahuk files.
require_once KAHUK_LIBS_DIR . 'functions-create-url.php';
require_once KAHUK_LIBS_DIR . 'functions-components.php';
require_once KAHUK_LIBS_DIR . 'html1.php';

// Load the L10n library.
require_once KAHUK_LIBS_DIR . 'wp/l10n.php';

// Load most of Kahuk library.
require_once KAHUK_LIBS_DIR . 'wp/kses.php';


// Lately included functions and class files
require_once KAHUK_LIBS_DIR . 'user.php'; // To be depricate
require_once KAHUK_LIBS_DIR . 'kahuk-class-users.php';

require_once KAHUK_LIBS_DIR . 'kahuk-class-story.php';

//
require_once KAHUK_LIBS_DIR . 'kahuk-class-categories.php';

//
require_once KAHUK_LIBS_DIR . 'kahuk-class-groups.php';

//
require_once KAHUK_LIBS_DIR . 'kahuk-class-comments.php';

//
require_once KAHUK_LIBS_DIR . 'kahuk-class-votes.php';

//
require_once KAHUK_LIBS_DIR . 'kahuk-class-friends.php';


// 
require_once KAHUK_LIBS_DIR . 'functions-user.php';
require_once KAHUK_LIBS_DIR . 'functions-table-user.php';

// 
require_once KAHUK_LIBS_DIR . 'functions-story.php';

//
require_once KAHUK_LIBS_DIR . 'functions-saved-story.php';

//
require_once KAHUK_LIBS_DIR . 'functions-comment.php';

//
require_once KAHUK_LIBS_DIR . 'functions-vote.php';

//
require_once KAHUK_LIBS_DIR . 'functions-table-category.php';

//
require_once KAHUK_LIBS_DIR . 'functions-table-group.php';

//
require_once KAHUK_LIBS_DIR . 'functions-group-meta.php';

//
require_once KAHUK_INC_DIR . 'functions-hook-callback.php';



//
require_once KAHUK_INC_DIR . 'functions-admin-dashboard.php';
require_once KAHUK_INC_DIR . 'kahuk-plugins.php';



// Create global variable for single story
$globalStory = kahuk_get_story();
