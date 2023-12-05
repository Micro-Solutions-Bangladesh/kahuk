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

echo "<pre>{$step}</pre>";

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

















// //
// $file = KAHUKPATH . 'kahuk-configs.php';

// if (is_readable($file)) {
//     $hasError = false;
//     $errorMessage = "";
//     $redirectTo = "";

//     if ($step == "setup-3") {
//         if (
//             $_POST["uname"] && 
//             $_POST["uemail"] && 
//             $_POST["upassword"]
//         ) {
//             // OK
//         } else if ($_GET["config-status"] == "yes") {
//             // OK
//         } else {
//             // TODO if something missing
//             // $errorMessage = "Missing message goes here";
//             // $redirectTo = "./index.php?step=setup-1";
//             kahuk_redirect("./index.php?step=setup-1");
//         }
//     }



//     // if (
//     //     ($step == "setup-3") &&
//     //     (
//     //         (
//     //             $_POST["uname"] && 
//     //             $_POST["uemail"] && 
//     //             $_POST["upassword"]
//     //         ) ||
//     //         ($_GET["config-status"] == "yes")
//     //     )
//     // ) {
//     //     // TODO if something missing
//     //     // $errorMessage = "Missing message goes here";
//     //     // $redirectTo = "./index.php?step=setup-3&config-status=yes";
//     // } else {
//     //     $errorMessage = "We found <code>/kahuk-configs.php</code>, please delete the <code>/kahuk-configs.php</code> file. <strong>For security purpose, <code>/kahuk-configs.php</code> file need to delete manually.</strong>";
//     //     $redirectTo = "./index.php?step=setup";
//     // }
    
//     // if ($errorMessage) {
//     //     kahuk_set_session_message(
//     //         $errorMessage,
//     //         "warning"
//     //     );

//     //     kahuk_redirect($redirectTo);
//     // }
// } else {
//     if (!in_array($step, ["setup", "setup-1", "setup-2"])) {
//         kahuk_redirect($redirectTo);
//     }
// }











// // check kahuk-configs.php file exist, when step is more then 0
// if ( ( in_array( $step, [ "install-1", "install-13" ] ) ) && ! file_exists( KAHUKPATH . 'kahuk-configs.php' ) ) {
//     header( 'Location: ./index.php?step=install-0' );
//     exit;
// }

// $securedSteps = [ "install-1", "install-2", "install-3", "install-12", "install-13" ];

// // Check user is not just browsing with browser URL
// if ( in_array( $step, $securedSteps ) ) {
//     if ( ! isset( $_POST['step'] ) || ( $step != $_POST['step'] ) ) {
//         header( 'Location: ./index.php?step=install-0' );
//         exit;
//     }
// }

// exit;

/**
 * Install Kahuk
 */
// switch($step) {
//     case "install-1": // Ask for database detail

//         kahuk_template_header();

//         //
//         $file = KAHUKPATH . 'kahuk-configs.php';
//         $fileDefault = KAHUKPATH . 'kahuk-configs.php.sample';

//         if ( file_exists( $file ) ) {
//             $messagesArray[]['warning'] = "We found <code>/kahuk-configs.php</code>, please delete the <code>/kahuk-configs.php</code> file. <strong>For security purpose, <code>/kahuk-configs.php</code> file need to delete manually.</strong>";
//         }

//         if ( ! file_exists( $fileDefault ) ) {
//             $messagesArray[]['warning'] = "File <code>/kahuk-configs.php.sample</code> file does not found. Please upload all the files and folders from Kahuk package";
//         }


//         if ( empty( $messagesArray ) ) {
//             include( KAHUKPATH . "setup/templates/database-form.php" );
//         } else {
//             // $step = 0;
//             kahuk_template_step0();
//         }

//         kahuk_template_footer();

//         break;

//     case "install-11":
        

//         // $messagesArray[]['info'] = "We are working on switch from Plikli 4.1.5 to Kahuk 5.0.x, Please stay tuned.";

//         // kahuk_template_step0();

//         $messagesArray = [];

//         $messagesArray[]['info'] = "<strong>MAKE SURE A FULL BACKUP OF THE WEBSITE BEFORE START UPGRADE.</strong><br>Sothat, you can restore your website anytime if something goes wrong!";

//         $errorMessages = [];

//         //
//         $file = KAHUKPATH . 'kahuk-configs.php';
//         $fileDefault = KAHUKPATH . 'kahuk-configs.php.sample';

//         if ( file_exists( $file ) ) {
//             $errorMessages[]['warning'] = "We found <code>/kahuk-configs.php</code>, please delete the <code>/kahuk-configs.php</code> file. <strong>For security purpose, <code>/kahuk-configs.php</code> file need to delete manually.</strong>";
//         }

//         if ( ! file_exists( $fileDefault ) ) {
//             $errorMessages[]['warning'] = "File <code>/kahuk-configs.php.sample</code> file does not found. Please upload all the files and folders from Kahuk package";
//         }

//         $messagesArray = array_merge( $messagesArray, $errorMessages );


//         kahuk_template_header();

//         if ( empty( $errorMessages ) ) {
//             include( KAHUKPATH . "setup/templates/database-form.php" );
//         } else {
//             kahuk_template_step0();
//         }

//         kahuk_template_footer();

//         break;

    
//     case "install-2":  // Create all settings / configs files and ask for admin user detail

//             if ( isset( $_POST['Submit'] ) ) {
//                 $settings_check = kahuk_check_db_settings();

//                 if ( ! empty( $settings_check ) ) {
//                     header ( "Location: ./index.php?step=install-1&errors={$settings_check}" ); // Redirect to first page
//                     exit;
//                 }

//                 $connection_check = kahuk_check_db_connection();

//                 if ( $connection_check ) {
//                     kahuk_template_header();

//                     create_kahuk_configs_file(); // create /kahuk-configs.php file
                    
//                     // create_settings_file(); // create /settings.php file

//                     include( KAHUKPATH . "setup/templates/admin-detail-form.php" );

//                     kahuk_template_footer();
//                 }

//             } else {
//                 header ("Location: ./index.php?step=install-1"); // Redirect to first page
//             }

//             break;

//     case "install-12": // Create all settings / configs files and ask for start switching to Kahuk from Plikli 4.1.5

//         if ( isset( $_POST['Submit'] ) ) {
//             $settings_check = kahuk_check_db_settings();

//             if ( ! empty( $settings_check ) ) {
//                 header ( "Location: ./index.php?step=install-1&errors={$settings_check}" ); // Redirect to first page
//                 exit;
//             }

//             $connection_check = kahuk_check_db_connection();

//             if ( $connection_check ) {
//                 kahuk_template_header();

//                 create_kahuk_configs_file(); // create `/kahuk-configs.php` file
                
//                 // create_settings_file(); // create `/settings.php` file

//                 include( KAHUKPATH . "setup/templates/lets-upgrade.php" );

//                 kahuk_template_footer();
//             }

//         } else {
//             header ("Location: ./index.php?step=install-1"); // Redirect to first page
//         }

//         break;
    
//     case "install-3": // Create tables and initail data ( including admin user and sample posts)

//         include_once KAHUKPATH . 'kahuk-configs.php';
//         include_once KAHUKPATH . 'libs/define_tables.php';
//         include_once KAHUKPATH . 'libs/html1.php';
        
//         include_once KAHUKPATH . 'setup/libs/db.php';
//         include_once KAHUKPATH . 'setup/libs/create-tables.php';
//         include_once KAHUKPATH . 'setup/libs/insert-sample-data.php';

//         kahuk_template_header();

//         kahuk_create_primary_tables();

//         kahuk_insert_sample_data();



//         kahuk_template_footer();

//         break;


//     case "install-13":

//         include_once KAHUKPATH . 'kahuk-configs.php';
//         include_once KAHUKPATH . 'libs/define_tables.php';
//         include_once KAHUKPATH . 'libs/html1.php';
        
//         include_once KAHUKPATH . 'setup/libs/db.php';
//         include_once KAHUKPATH . 'setup/libs/replace-old-cms-instances.php';


//         kahuk_template_header();

//         $tbl_config_exist = kahuk_table_exist( table_config );

//         if ( $tbl_config_exist ) {
//             kahuk_replace_old_cms_instances();


//         } else {
//             _kahuk_messages_markup(
//                 "Table {$tbl_config_exist} not found! Upgrade not possible!",
//                 'danger'
//             );
//         }

//         kahuk_template_footer();


//         break;
    

//     case "install-14":
//     case "install-4":
//             include_once KAHUKPATH . 'kahuk-configs.php';
            
//             $messagesArray[]['info'] = "PLEASE <strong>DELETE THE <code>/setup</code> FOLDER</strong>.";

//             // TODO Clean the cache folder

//             //
//             kahuk_template_header();

//             create_kahuk_htaccess_file(); // create htaccess file

//             kahuk_template_footer();

//         break;


//     default: // step == 0 :: kahuk-configs.php file not exist, ask developer to create one

//             kahuk_template_header();

//             kahuk_template_step0();

//             kahuk_template_footer();
//         break;
// }



