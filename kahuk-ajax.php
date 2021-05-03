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

    $post_id = sanitize_number( $_POST['id'] );
	$user_id = sanitize_number( $_POST['user'] );

	$is_positivevote = ( isset( $_POST['value'] ) && ( 0 < $_POST['value'] ) );
	$is_unvote = ( "unvote" == $action );

	$args = [
		'link_id' => $post_id,
		'user_id' => $user_id,
		'is_positivevote' => $is_positivevote,
		'is_unvote' => $is_unvote,
	];

	$kahukVoting = new KahukVoting();
	$kahukVoting->init( $args );

	if ( $kahukVoting->errors->has_errors() ) {
		echo "ERROR: " . $kahukVoting->errors->get_error_message();
	} else {
		echo $kahukVoting->linkRecord['link_votes'] . '~--~' . $post_id;
	}

	exit;
}










// if ( in_array( $action, ['vote', 'unvote'] ) ) {

//     require_once KAHUK_LIBS_DIR.'class-votes.php';
//     // require_once KAHUK_LIBS_DIR . 'link.php';
//     require_once KAHUK_LIBS_DIR . 'class-voting.php';
//     // require_once KAHUK_LIBS_DIR.'smartyvariables.php';

//     $post_id = sanitize($_POST['id'], 3);

//     $link = new LinkTotal;
// 	$link->id = $post_id;
// 	$link->read_basic();

// // print_r( $link );
// // echo "<br>===<br>";

// // echo "current_user->user_id: " . $current_user->user_id . "<br>";

// 	if ($current_user->user_id == 0 && !anonymous_vote) {
// 		// error($main_smarty->get_config_vars('KAHUK_Visual_Vote_NoAnon'));
//         die( 'User not found!' );
// 	}

// 	$post_user = sanitize($_POST['user'], 3);

// 	if( $current_user->user_id != $post_user) {
// 		// error($main_smarty->get_config_vars('KAHUK_Visual_Vote_BadUser'). $current_user->user_id . '-'. $post_user);
//         die( 'Hack Attempt Found!' );
// 	}

//     //
// 	$md5 = md5( $post_user.$link->randkey );
    
// 	if ( $md5 !== sanitize( $_POST['md5'], 3 ) ) {
// 		// error($main_smarty->get_config_vars('KAHUK_Visual_Vote_BadKey'));
//         die( 'Invalid Security Code Found!' );
// 	}

// 	$value = intval( $_POST['value'] );

// 	// if( isset($_POST['unvote']) && sanitize($_POST['unvote'], 3) == 'true' ) {
// 	if( "unvote" == $action ) {
// 	    $link->remove_vote( $current_user->user_id, $value );
// 	} else {
//         //Checking for ip vote
//         if ( $current_user->user_id!=0) {
//             if ( $link->votes($current_user->user_id) > 0) {
//                 // error( $main_smarty->get_config_vars('KAHUK_Visual_Vote_AlreadyVoted').$link->votes($current_user->user_id).'/'.$value);
//                 die( 'Already Voted!' );
//             }
//         } else {
//             if ( $value==10 && votes_per_ip > 0 && $link->votes_from_ip() >= votes_per_ip+1 ) {
//                 // error( $main_smarty->get_config_vars('KAHUK_Visual_Vote_AlreadyVoted').'/'.$value);
//                 die( 'Already Voted!' );
//             }
            
//             if ( $value==-10 && votes_per_ip > 0 && $link->reports_from_ip() >= votes_per_ip+1 ) {
//                 // error( $main_smarty->get_config_vars('KAHUK_Visual_Vote_AlreadyVoted').'/'.$value );
//                 die( 'Already Voted!' );
//             }
//         }

//         $link->insert_vote( $current_user->user_id, $value );
// 	}

// 	if ( $link->status == 'discard' ) {
// 		$link->read();
// 		$link->status = 'new';
// 		$link->store();
// 	}

// 	if ( Voting_Method == 2 ) {
// 		$link_rating = $link->rating($link->id)/2;
// 		$rating_width = $link_rating * 25;
// 		$vote_count = $link->countvotes();
// 		echo $rating_width . "~" . $link_rating . "~" . $vote_count . "~<li class='one-star-noh'>1</li><li class='two-stars-noh'>2</li><li class='three-stars-noh'>3</li><li class='four-stars-noh'>4</li><li class='five-stars-noh'>5</li>";
// 	} else {
// 		$count=$link->votes;
// 		echo "$count ~--~".$post_id;
// 	}

// 	$link->evaluate_formulas();
// }


