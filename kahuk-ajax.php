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

/**
 * Save or Unsave story
 */
if ( in_array( $action, ['save-story', 'unsave-story'] ) ) {
	$current_user_id = ( $current_user ? $current_user->user_id : 0 );

	if ( 0 < $current_user_id ) {
		$linkid = intval( _post( 'link_id' ) );

		if ( $action == 'save-story' ) {
			$count = $db->get_var("SELECT count(*) FROM " . table_saved_links . " WHERE saved_link_id = {$linkid} AND saved_user_id = {$current_user_id}");
			
			if ( $count == 0 ) {
				$sql="INSERT INTO " . table_saved_links . " (saved_user_id, saved_link_id) VALUES ({$current_user_id}, {$linkid})";
				$db->query($sql);
				echo "1";
			} else {
				echo "Error";
			}

		} else if ( $action == 'unsave-story' ) {
			$count = $db->get_var("SELECT count(*) FROM " . table_saved_links . " WHERE saved_link_id = {$linkid} AND saved_user_id = {$current_user_id}");
			
			if ( $count != 0 ) {
				$sql="DELETE FROM " . table_saved_links . " WHERE saved_user_id={$current_user_id} AND saved_link_id={$linkid}";
				$db->query($sql);
				echo "2";
			} else {
				echo "Error";
			}
		}
	}

	exit;
}
