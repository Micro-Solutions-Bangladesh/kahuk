<?php
/**
 * Executing Ajax process.
 *
 * @since 5.0.0
 */
define( 'KAHUK_AJAX', true );

/** Load Kahuk Bootstrap */
require_once dirname( __FILE__ ) . '/config.php';

check_referrer();

global $current_user;
$action = _request( 'action' );

if ( in_array( $action, ['vote', 'unvote'] ) ) {
	require_once KAHUK_LIBS_DIR . 'class-voting.php';

    $story_id = sanitize_number( $_POST['id'] );
	$user_id = sanitize_number( $_POST['user'] );

	$is_positivevote = ( isset( $_POST['value'] ) && ( 0 < $_POST['value'] ) );
	$is_unvote = ( "unvote" == $action );

	$args = [
		'link_id' => $story_id,
		'user_id' => $user_id,
		'is_positivevote' => $is_positivevote,
		'is_unvote' => $is_unvote,
	];

	$kahukVoting = new KahukVoting();
	$kahukVoting->init( $args );

	// Check for Story karma and status
	kahuk_change_story_status( $story_id );

	if ( $kahukVoting->errors->has_errors() ) {
		echo "ERROR: " . $kahukVoting->errors->get_error_message();
	} else {
		echo $kahukVoting->linkRecord['link_votes'] . '~--~' . $story_id;
	}

	exit;
}
