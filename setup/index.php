<?php
define( 'KAHUK_INSTALLING', true );


if ( ! defined( 'KAHUKPATH' ) ) {
	define( 'KAHUKPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

global $step, $isNewSetup, $bodyClass, $messagesArray;


include_once( KAHUKPATH . 'setup/languages/lang_english.php' );

require_once KAHUKPATH . 'setup/functions.php';


$isNewSetup    = true;
$bodyClass     = '';
$messagesArray = [];
$step          = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

// check kahuk-configs.php file exist, when step is more then 0
if ( ( in_array( $step, [ 3, 13 ] ) ) && ! file_exists( KAHUKPATH . 'kahuk-configs.php' ) ) {
    header( 'Location: ./index.php?step=0' );
    exit;
}

$securedSteps = [ 2, 3, 12, 13 ];

// Check user is not just browsing with browser URL
if ( in_array( $step, $securedSteps ) ) {
    if ( ! isset( $_POST['step'] ) || ( $step != $_POST['step'] ) ) {
        header( 'Location: ./index.php?step=0' );
        exit;
    }
}

/**
 * Install Kahuk
 */
switch( $step ) {
    case 1: // Ask for database detail

        kahuk_template_header();

        //
        $file = KAHUKPATH . 'kahuk-configs.php';
        $fileDefault = KAHUKPATH . 'kahuk-configs.php.default';

        if ( file_exists( $file ) ) {
            $messagesArray[]['warning'] = "We found <code>/kahuk-configs.php</code>, please delete the <code>/kahuk-configs.php</code> file. <strong>For security purpose, <code>/kahuk-configs.php</code> file need to delete manually.</strong>";
        }

        if ( ! file_exists( $fileDefault ) ) {
            $messagesArray[]['warning'] = "File <code>/kahuk-configs.php.default</code> file does not found. Please upload all the files and folders from Kahuk package";
        }

        //
        $file = KAHUKPATH . 'settings.php';
        $fileDefault = KAHUKPATH . 'settings.php.default';

        if ( file_exists( $file ) ) {
            $messagesArray[]['warning'] = "We found <code>/settings.php</code>, please delete the <code>/settings.php</code> file. <strong>For security purpose, <code>/settings.php</code> file need to delete manually.</strong>";
        }

        if ( ! file_exists( $fileDefault ) ) {
            $messagesArray[]['warning'] = "File <code>/settings.php.default</code> file does not found. Please upload all the files and folders from Kahuk package";
        }

        // print_r( $messagesArray );

        if ( empty( $messagesArray ) ) {
            include( KAHUKPATH . "setup/templates/database-form.php" );
        } else {
            // $step = 0;
            kahuk_template_step0();
        }

        kahuk_template_footer();

        break;

    case 11:
        

        // $messagesArray[]['info'] = "We are working on switch from Plikli 4.1.5 to Kahuk 5.0.x, Please stay tuned.";

        // kahuk_template_step0();

        $messagesArray = [];

        $messagesArray[]['info'] = "<strong>MAKE SURE A FULL BACKUP OF THE WEBSITE BEFORE START UPGRADE.</strong><br>Sothat, you can restore your website anytime if something goes wrong!";

        $errorMessages = [];

        //
        $file = KAHUKPATH . 'kahuk-configs.php';
        $fileDefault = KAHUKPATH . 'kahuk-configs.php.default';

        if ( file_exists( $file ) ) {
            $errorMessages[]['warning'] = "We found <code>/kahuk-configs.php</code>, please delete the <code>/kahuk-configs.php</code> file. <strong>For security purpose, <code>/kahuk-configs.php</code> file need to delete manually.</strong>";
        }

        if ( ! file_exists( $fileDefault ) ) {
            $errorMessages[]['warning'] = "File <code>/kahuk-configs.php.default</code> file does not found. Please upload all the files and folders from Kahuk package";
        }

        //
        $file = KAHUKPATH . 'settings.php';
        $fileDefault = KAHUKPATH . 'settings.php.default';

        if ( file_exists( $file ) ) {
            $errorMessages[]['warning'] = "We found <code>/settings.php</code>, please delete the <code>/settings.php</code> file. <strong>For security purpose, <code>/settings.php</code> file need to delete manually.</strong>";
        }

        if ( ! file_exists( $fileDefault ) ) {
            $errorMessages[]['warning'] = "File <code>/settings.php.default</code> file does not found. Please upload all the files and folders from Kahuk package";
        }

        $messagesArray = array_merge( $messagesArray, $errorMessages );


        kahuk_template_header();

        if ( empty( $errorMessages ) ) {
            include( KAHUKPATH . "setup/templates/database-form.php" );
        } else {
            kahuk_template_step0();
        }

        kahuk_template_footer();

        break;

    
    case 2:  // Create all settings / configs files and ask for admin user detail

            if ( isset( $_POST['Submit'] ) ) {
                $settings_check = kahuk_check_db_settings();

                if ( ! empty( $settings_check ) ) {
                    header ( "Location: ./index.php?step=1&errors={$settings_check}" ); // Redirect to first page
                    exit;
                }

                $connection_check = kahuk_check_db_connection();

                if ( $connection_check ) {
                    kahuk_template_header();

                    create_kahuk_configs_file(); // create /kahuk-configs.php file
                    
                    create_settings_file(); // create /settings.php file

                    include( KAHUKPATH . "setup/templates/admin-detail-form.php" );

                    kahuk_template_footer();
                }

            } else {
                header ("Location: ./index.php?step=1"); // Redirect to first page
            }

            break;

    case 12: // Create all settings / configs files and ask for start switching to Kahuk from Plikli 4.1.5

        if ( isset( $_POST['Submit'] ) ) {
            $settings_check = kahuk_check_db_settings();

            if ( ! empty( $settings_check ) ) {
                header ( "Location: ./index.php?step=1&errors={$settings_check}" ); // Redirect to first page
                exit;
            }

            $connection_check = kahuk_check_db_connection();

            if ( $connection_check ) {
                kahuk_template_header();

                create_kahuk_configs_file(); // create `/kahuk-configs.php` file
                
                create_settings_file(); // create `/settings.php` file

                include( KAHUKPATH . "setup/templates/lets-upgrade.php" );

                kahuk_template_footer();
            }

        } else {
            header ("Location: ./index.php?step=1"); // Redirect to first page
        }

        break;
    
    case 3: // Create tables and initail data ( including admin user and sample posts)

        include_once KAHUKPATH . 'kahuk-configs.php';
        include_once KAHUKPATH . 'libs/define_tables.php';
        include_once KAHUKPATH . 'libs/html1.php';
        
        include_once KAHUKPATH . 'setup/libs/db.php';
        include_once KAHUKPATH . 'setup/libs/create-tables.php';
        include_once KAHUKPATH . 'setup/libs/insert-sample-data.php';

        kahuk_template_header();

        kahuk_create_primary_tables();

        kahuk_insert_sample_data();

        kahuk_good_to_go_site_markup();

        kahuk_template_footer();

        break;


    case 13:

        include_once KAHUKPATH . 'kahuk-configs.php';
        include_once KAHUKPATH . 'libs/define_tables.php';
        include_once KAHUKPATH . 'libs/html1.php';
        
        include_once KAHUKPATH . 'setup/libs/db.php';
        include_once KAHUKPATH . 'setup/libs/replace-old-cms-instances.php';


        kahuk_template_header();

        $tbl_config_exist = kahuk_table_exist( table_config );

        if ( $tbl_config_exist ) {
            kahuk_replace_old_cms_instances();

            kahuk_good_to_go_site_markup();
        } else {
            _kahuk_messages_markup(
                "Table {$tbl_config_exist} not found! Upgrade not possible!",
                'danger'
            );
        }

        kahuk_template_footer();


        break;
    

    case 14:
    case 4:
            
            $messagesArray[]['info'] = "PLEASE <strong>DELETE THE <code>/setup</code> FOLDER</strong>.";

            //
            $file = KAHUKPATH . 'kahuk-configs.php';

            if ( ! file_exists( $file ) ) {
                $messagesArray[]['danger'] = "<strong>Error:</strong> <code>/kahuk-configs.php</code> file is missing.";
            }

            // TODO Clean the cache folder

            //
            kahuk_template_header();

            create_kahuk_htaccess_file(); // create htaccess file

            kahuk_template_footer();

        break;


    default: // step == 0 :: kahuk-configs.php file not exist, ask developer to create one

            kahuk_template_header();

            kahuk_template_step0();

            kahuk_template_footer();
        break;
}



/**
 * 
 */
function kahuk_good_to_go_site_markup() {
    global $step;
    ?>
<div class="complete-setup">
    <form action="index.php?step=<?php echo $step + 1; ?>" method="post">
        <input type="hidden" name="step" value="<?php echo ( $step + 1); ?>">
        <input type="submit" class="btn btn-primary" name="Submit" value="Finish Setup &raquo;" />
    </form>
</div>
    <?php
}

