<?php
// 
global $kahukPlugins, $pluginsInstalledArray;

//
require_once KAHUKPATH_INC . "kahuk-functions.php";
require_once KAHUKPATH_INC . "functions-data-enum.php";
require_once KAHUKPATH_INC . "functions-html.php";

// Set Error Log File
$error_log_file_name = kahuk_error_log_file_name();
ini_set("error_log", KAHUKPATH_LOGS . "error-logs/" . $error_log_file_name); // Error log file location

/**
 * Set PHP error reporting based on Kahuk debug settings.
 *
 * @since 5.0.0
 * @updated 5.0.6
 * @access private
 */
if (KAHUK_DEBUG_DISPLAY) {
    ini_set("display_errors", 1);
} else {
    ini_set("display_errors", 0);
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
include_once KAHUKPATH_INC . "settings-from-db.php";

//
require_once KAHUKPATH_LIBS . "kahuk-class-error.php";

//
include_once KAHUKPATH_LIBS . "functions-data.php";

//
require_once KAHUKPATH_LIBS . "wp/formatting.php";
require_once KAHUKPATH_LIBS . "wp/functions.php";
require_once KAHUKPATH_LIBS . "kahuk-functions-sanitize.php";

//
require_once KAHUKPATH_LIBS . "functions-process-url.php";
require_once KAHUKPATH_LIBS . "functions.php";

//
require_once(KAHUKPATH_LIBS . "check_behind_proxy.php");

//
include_once KAHUKPATH_LIBS . "login.php";

//
require_once KAHUKPATH_LIBS . "utils.php";

//
require_once KAHUKPATH_LIBS . "functions-user-authentication.php";

// Load early Kahuk files.
require_once KAHUKPATH_LIBS . "functions-create-url.php";
require_once KAHUKPATH_LIBS . "functions-components.php";
require_once KAHUKPATH_LIBS . "html1.php";

// Load the L10n library.
require_once KAHUKPATH_LIBS . "wp/l10n.php";

// Load most of Kahuk library.
require_once KAHUKPATH_LIBS . "wp/kses.php";


// Lately included functions and class files
require_once KAHUKPATH_LIBS . "user.php"; // To be depricate
require_once KAHUKPATH_LIBS . "kahuk-class-users.php";

require_once KAHUKPATH_LIBS . "kahuk-class-story.php";

//
require_once KAHUKPATH_LIBS . "kahuk-class-categories.php";

//
require_once KAHUKPATH_LIBS . "kahuk-class-groups.php";

//
require_once KAHUKPATH_LIBS . "kahuk-class-comments.php";

//
require_once KAHUKPATH_LIBS . "kahuk-class-votes.php";

//
require_once KAHUKPATH_LIBS . "kahuk-class-friends.php";


// 
require_once KAHUKPATH_LIBS . "functions-user.php";
require_once KAHUKPATH_LIBS . "functions-table-user.php";

// 
require_once KAHUKPATH_LIBS . "functions-story.php";

//
require_once KAHUKPATH_LIBS . "functions-saved-story.php";

//
require_once KAHUKPATH_LIBS . "functions-comment.php";

//
require_once KAHUKPATH_LIBS . "functions-vote.php";

//
require_once KAHUKPATH_LIBS . "functions-table-category.php";

//
require_once KAHUKPATH_LIBS . "functions-table-group.php";

//
require_once KAHUKPATH_LIBS . "functions-group-meta.php";

//
require_once KAHUKPATH_INC . "functions-hook-callback.php";



//
require_once KAHUKPATH_INC . "functions-admin-dashboard.php";
require_once KAHUKPATH_INC . "kahuk-plugins.php";



// Create global variable for single story
$globalStory = kahuk_get_story();
