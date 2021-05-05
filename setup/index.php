<?php
define( 'KAHUK_INSTALLING', true );


if ( ! defined( 'KAHUKPATH' ) ) {
	define( 'KAHUKPATH', dirname( dirname( __FILE__ ) ) . '/' );
}

global $step, $isNewSetup, $bodyClass, $messagesArray;


include_once( KAHUKPATH . 'setup/languages/lang_english.php' );

require_once KAHUKPATH . 'setup/functions.php';



// include( KAHUKPATH . "setup/templates/template.php" );
// exit;






$isNewSetup = true;

$bodyClass = '';

$messagesArray = [];

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

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
        kahuk_template_header();

        $messagesArray[]['info'] = "We are working on switch from Plikli 4.1.5 to Kahuk 5.0.x, Please stay tuned.";

        kahuk_template_step0();

        kahuk_template_footer();

        break;

    
    case 2:  // Create all settings / configs files and ask for admin user detail

            if ( isset( $_POST['Submit'] ) ) {
                $tblprefix = ( ! empty( trim( $_POST['tblprefix'] ) ) ) ? $_POST['tblprefix'] : 'kahuk_life_';

                $dbuser = $_POST['dbuser'];
                $dbpass = $_POST['dbpass'];
                $dbname = $_POST['dbname'];
                $dbhost = $_POST['dbhost'];

                define( 'DB_USER', $dbuser );
                define( 'DB_PASSWORD', $dbpass );
                define( 'DB_NAME', $dbname );
                define( 'DB_HOST', $dbhost );
                define( 'TABLE_PREFIX', $tblprefix );

                if ( $conn = @mysqli_connect( $dbhost, $dbuser, $dbpass ) ) {
                    $db_selected = mysqli_select_db( $conn, $dbname );

                    if ( !$db_selected ) {
                        die ('Error: '.$dbname.' : '.mysqli_error($conn) );
                    }

                    kahuk_template_header();

                    //
                    require_once( KAHUKPATH . "setup/libs/create-config-files.php" );

                    create_kahuk_configs_file(); // create /kahuk-configs.php file
                    
                    create_settings_file(); // create /settings.php file

                    if ( 2 == $step ) {
                        include( KAHUKPATH . "setup/templates/admin-detail-form.php" );
                    } elseif ( 12 == $step ) {
                        include( KAHUKPATH . "setup/templates/lets-upgrade.php" );
                    }

                    kahuk_template_footer();

                } else {
                    die ('Error: fail to connect ' . $dbname );
                }

            } else {
                header ("Location: ./index.php?step=1"); // Redirect to first page
            }

            break;

    case 12: // Create all settings / configs files and ask for start switching to Kahuk from Plikli 4.1.5

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

        break;
    

    case 4:
            kahuk_template_header();

            //
            $file = KAHUKPATH . 'kahuk-configs.php';

            if ( file_exists( $file ) ) {
                // $messagesArray[]['success'] = "We find <code>/kahuk-configs.php</code> file.";
            } else {
                $messagesArray[]['danger'] = "<strong>Error:</strong> <code>/kahuk-configs.php</code> file is missing.";
            }

            kahuk_template_step4();

            kahuk_template_footer();

        break;


    case 14:

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

