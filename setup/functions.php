<?php

/**
 * 
 */
function kahuk_template_action_messages() {
	global $messagesArray;
	// print_r( $messagesArray );
    
    foreach( $messagesArray as $row ) {
		foreach( $row as $i => $msg ) {
			_kahuk_messages_markup( $msg, $i );
		}
    }
}



/**
 * 
 */
function kahuk_template_step4() {
	global $messagesArray;
	$hasError = false;

    foreach( $messagesArray as $row ) {
		foreach( $row as $i => $msg ) {
			_kahuk_messages_markup( $msg, $i );

			if ( 'danger' == $i ) {
				$hasError = true;
			}
		}
    }

	if ( $hasError ) {
		_kahuk_messages_markup( '<strong>Installation Unsuccessfull!</strong>', 'danger' );
	} else {
		_kahuk_messages_markup( '<strong>Cheers! Installation Successfull!</strong>', 'danger' );
	}
}


/**
 * 
 */
function kahuk_template_step0() {
	// global $step;

	kahuk_template_action_messages();
?>
<p>
	Before getting started, we need some information on the database. You will need to know the following items before proceeding.
</p>
<ol>
	<li>Database name</li>
	<li>Database username</li>
	<li>Database password</li>
	<li>Database host</li>
	<li>Table prefix (just as a good practice)</li>
</ol>
<p>
	These information will require to create the <code>kahuk-configs.php</code> file.
</p>

<div class="row">
	<div class="col tac">
		<a href="index.php?step=1" class="link-btn lets-go">Install New Site &raquo;</a>
	</div>
	<div class="col tac">
		<a href="index.php?step=11" class="link-btn lets-go">Upgrade Plikli 4.1.5 to Kahuk 5.0.x &raquo;</a>
	</div>
</div>
<?php
}

/**
 * 
 */
function kahuk_template_header() {
	global $step, $bodyClass; 
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="assets/css/styles.css" rel="stylesheet">

    <title>Kahuk CMS Setup</title>
</head>
<body class="<?php echo 'step-' . $step . ' ' .$bodyClass; ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="main-content">
                    <div class="header-content">
                        <h1 class="logo-wrap">
                            <img src="../templates/admin/img/kahuk.png" alt="Kahuk CMS Setup">
                            <span class="screen-reader-text">Welcome to Kahuk CMS Setup.</span>
                        </h1>
                    </div>
                    <div class="main-content">
<?php
}

/**
 * 
 */
function kahuk_template_footer() {
?>
                    </div>
                    <!-- <div class="footer-content"></div> -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}






/**
 * Only for setup mode
 * 
 * @since 5.0.0
 */
function get_kahuk_file_uri( $file_path ) {
    $file = ltrim( $file_path, '/' );
	$output = '';

	if ( defined( 'KAHUK_BASE_URL' ) ) {
		$output = KAHUK_BASE_URL;
	} else {
		$output = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" ) . "://$_SERVER[HTTP_HOST]";
	}

	return $output . "/" . $file_path;
}






/**
 * 
 * 
 * @return void|string
 */
function kahuk_messages_markup( $msg, $type = 'primary' ) {
    $output = [];

	$alertClass = $type;

	if ( 'error' == $type ) {
		$alertClass = 'danger';
	}


    $output[] = '<div class="alert alert-' .$alertClass . '">';

	if( is_array( $msg ) ) {
		foreach( $msg as $v ) {
			$output[] = sprintf("<p>%s</p>", $v);
		}
	} else {
		$output[] = sprintf("<p>%s</p>", $msg);
	}
    
    $output[] = '</div>';

    return implode( "", $output );
}

/**
 * Create markup and display of different type of messages
 * 
 * @return void
 */
function _kahuk_messages_markup( $msgArray, $type = 'info' ) {
    echo kahuk_messages_markup( $msgArray, $type );
}

