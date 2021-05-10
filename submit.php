<?php
set_time_limit( 180 );
ini_set( 'session.gc_maxlifetime', 3600 );

include_once( 'internal/Smarty.class.php' );
$main_smarty = new Smarty;

include( 'config.php' );
include( KAHUK_LIBS_DIR . 'kahuk-manage-spam.php' );
include( KAHUK_LIBS_DIR . 'class-kahuk-http-request.php' );
include( KAHUK_LIBS_DIR.'smartyvariables.php' );

include( KAHUK_LIBS_DIR . 'page-submit.php' );

/**
 * to check anonymous mode activated
 */
global $current_user, $main_smarty, $the_template, $the_template_center, $submit_error_code, $actioncode;

$submit_error_code = '';
$phase = ( isset( $_POST["phase"] ) && is_numeric( $_POST["phase"] ) ) ? $_POST["phase"] : 0;

/** */
if ( true != $current_user->authenticated ) {
	$vars = '';
	check_actions( 'anonymous_story_user_id', $vars );

	if ( true != $vars['anonymous_story'] ) {
		force_authentication();
	}
}

/**
 * html tags allowed during submit
 */
if ( checklevel( 'admin' ) ) {
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_God;
} elseif ( checklevel( 'moderator' ) ){
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Admin;
} else {
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Normal;
}

$main_smarty->assign( 'Story_Content_Tags_To_Allow', htmlspecialchars( $Story_Content_Tags_To_Allow ) );

/**
 * Check wheather the URL is submitted directly form a website or the submit form in our website
 */
$isDirectSubmit = false;
$urlFound = '';

if ( isset( $_GET['url'] ) && ! isset( $_POST['url'] ) ) {
	$isDirectSubmit = true;
	$phase = 1;
	$urlFound = esc_url( $_GET['url'] );
}

if ( isset( $_POST['url'] ) && isset( $_POST['phase'] ) ) {
	$isDirectSubmit = false;
	$urlFound = esc_url( $_POST['url'] );
}

if ( ! empty( $urlFound ) ) {
	// kahuk_debug_log( "Phase: {$phase} and the URL: {$urlFound}\nreferrer: " . $_SERVER["HTTP_REFERER"], __LINE__, '', __FILE__ );
	check_referrer( $urlFound );
}

/**
 * Check if the url contain action code to display any redirection message
 * If action-code is not empty then we will by force make the phase 0
 */
$actioncode = _get( 'actioncode', '' );

if ( ! empty( $actioncode ) ) {
	$main_smarty->assign( 'actioncode', sanitize_text_field( $actioncode ) );
}

/**
 * Check for session messages
 */
$sessionMessages = kahuk_get_session_messages();

if ( empty( $sessionMessages ) ) {
	$main_smarty->assign( 'sessionMessages', '' );
} else {
	$main_smarty->assign( 'sessionMessages', $sessionMessages );
}

// breadcrumbs and page titles
$navwhere['text1'] = $main_smarty->get_config_vars( 'KAHUK_Visual_Breadcrumb_Submit' );
$navwhere['link1'] = getmyurl( 'submit', '' );
$main_smarty->assign( 'navbar_where', $navwhere );
$main_smarty->assign( 'posttitle', $main_smarty->get_config_vars( 'KAHUK_Visual_Breadcrumb_Submit' ) );
$main_smarty = do_sidebar( $main_smarty );

define( 'pagename', 'submit' ); 
$main_smarty->assign( 'pagename', pagename );

switch ( $phase ) {
	case 1:
		do_submit1();
		break;

	case 2:
		do_submit2();
		break;

	default: // ( 1/2 != $phase )
		do_submit0();
		break;
}

$main_smarty->assign( 'submit_error', $submit_error_code );
$main_smarty->assign( 'tpl_center', $the_template_center );
$main_smarty->display( $the_template . '/kahuk.tpl' );

exit;



/**
 * Save Link Detail
 */
function do_submit2() {
	global $current_user, $the_template, $the_template_center, $submit_error_code, $urlFound;

	$randkey = _post( 'randkey' );
	$hasRecord = kahuk_has_url_in_record( $randkey, $urlFound );

	if ( ! $hasRecord ) {
		kahuk_set_session_message(
			sprintf( 'Session expired for the url: %s !', $urlFound ),
			'warning'
		);

		kahuk_redirect( KAHUK_BASE_URL . '/submit.php' );
		return;
	}

	// Check Captcha
	$vars = array('username' => $current_user->user_login);
	check_actions('submit_step_3_after_first_store', $vars);

	if ( isset($vars['error']) && $vars['error'] == true ) {
		$the_template_center = $the_template . '/submit_errors_center';
		$submit_error_code = 'register_captcha_error';
		return;
	}

	// Check Summary For the Link
	$desc = kahuk_kses( $_POST['summarytext'], $Story_Content_Tags_To_Allow );

	$link_content = kahuk_autop( $desc );
	$link_summary = kahuk_link_summary( $desc );

	if ( empty( $link_content ) ) {
		kahuk_set_session_message(
			'Summary text required some more text!',
			'error'
		);

		kahuk_redirect( KAHUK_BASE_URL . '/submit.php' );
	}


	// Check for the Link Title
	$title = sanitize_text_field( $_POST['title'] );
	$title_url = sanitize_title( $title ); // START TODO

	if ( empty( $title ) || empty( $title_url ) ) {
		kahuk_set_session_message(
			'Please write a appropriate title!',
			'error'
		);

		kahuk_redirect( KAHUK_BASE_URL . '/submit.php' );
	}

	//
	$unique_title_slug = kahuk_unique_title_slug( $title_url );

	if ( false == $unique_title_slug ) {
		kahuk_set_session_message(
			sprintf( 'Sorry, We are unable to create an unique slug from your title!', $urlFound ),
			'error'
		);

		kahuk_redirect( KAHUK_BASE_URL . '/submit.php' );
	}

	// Store Link
	$linksData = [];
	// $linksData['link_author'] = $current_user->user_id;
	$linksData['link_status'] = 'new';
	$linksData['link_randkey'] = ''; // TODO Delete
	$linksData['link_category'] = sanitize_number( $_POST['category'] );
	$linksData['link_url'] = $urlFound;
	$linksData['link_url_title'] = $title;
	$linksData['link_title'] = $title;
	$linksData['link_title_url'] = $unique_title_slug;
	$linksData['link_content'] = $link_content;
	$linksData['link_summary'] = $link_summary;

	$linksData['link_group_id'] = sanitize_number( _post( "link_group_id", '0' ) );
	$linksData['link_tags'] = kahuk_slashes( sanitize_text_field( $_POST['link_tags'] ) );

	$linkId = kahuk_insert_link( $linksData ); //
	$newPost = [];

	if ( 0 < $linkId ) {
		$newPost = kahuk_get_link_by_id( $linkId );
	}

	if ( ! $newPost ) {
		kahuk_debug_log( "Unable to fetch newly created post using the slug {$unique_title_slug}", "", "", __FILE__ );

		kahuk_set_session_message(
			sprintf( 'Got an unexpected error, we will fix it!', $urlFound ),
			'error'
		);

		kahuk_redirect( KAHUK_BASE_URL );
	}

	require_once KAHUK_LIBS_DIR . 'class-voting.php';

	$args = [
		'link_id' => $linkId,
		'user_id' => $newPost['link_author'],
		'is_positivevote' => true,
		'is_unvote' => false,
	];

	$kahukVoting = new KahukVoting();
	$kahukVoting->init( $args );

	//
	$categories = kahuk_categories_init();
	$category = $categories->get_item( $linksData['link_category'] );

	$story_url = getmyurl( "storyURL", $category['category_safe_name'], urlencode( $newPost['link_title_url'] ) );

	kahuk_redirect( $story_url );
}



// enter URL before submit process
function do_submit0() {
	global $main_smarty, $the_template, $the_template_center;

	$the_template_center = $the_template . '/submit_step_1_center';

	$main_smarty->assign( 'submit_rand', rand( 10000,10000000 ) );

	$vars = '';

	check_actions( 'do_submit0', $vars );
}


/**
 * 
 */
function do_submit1() {
	global $db, $current_user, $main_smarty, $the_template, $urlFound, $the_template_center, $submit_error_code;

	include( KAHUK_LIBS_DIR . 'kahuk-link.php' );
	$kahukLink = kahuk_link_init( $urlFound );

	if ( $kahukLink->errors->has_errors() ) {
		do_submit0();

		$the_template_center = $the_template . '/submit_errors_center';
		$submit_error_code = $kahukLink->errors->get_error_code();

		return;
	}

	$randkey = _post( 'randkey' );

	kahuk_create_url_records( $randkey, $kahukLink->url );

	$main_smarty->assign( 'randkey', $randkey );
	$main_smarty->assign( 'submit_url', $kahukLink->url );
	$data = parse_url( $kahukLink->url );
	$main_smarty->assign( 'url', $kahukLink->url );
	$main_smarty->assign( 'url_short', 'http://'.$data['host'] ); // TODO

	$main_smarty->assign( 'StorySummary_ContentTruncate', StorySummary_ContentTruncate );
	$main_smarty->assign( 'submit_url_description', $kahukLink->url_description );
	$main_smarty->assign( 'submit_trackback', $kahukLink->trackback );
	
	//
	$submit_url_title = str_replace( '"', "&#034;", $kahukLink->url_title );
	$main_smarty->assign( 'submit_url_title', $submit_url_title );

	//
	$categories = kahuk_categories_init();
	$main_smarty->assign( 'submit_cat_array', $categories->get_items() );

	//
	$featured_img = ( empty( $kahukLink->linkImages ) ) ? "" : $kahukLink->linkImages[0]['url']; // Get the first image
	$main_smarty->assign( 'og_twitter_image', $featured_img );

	$main_smarty->assign( 'tpl_extra_fields', $the_template . '/submit_extra_fields' );

	$the_template_center = $the_template . '/submit_step_2_center';

	// 
	if ( "true" == enable_group ) {
		$sql = "SELECT group_id,group_name FROM " . table_groups . " LEFT JOIN ".table_group_member." ON member_group_id=group_id
		WHERE member_user_id = $current_user->user_id AND group_status = 'Enable' AND member_status='active' 
		AND (member_role != 'banned' && member_role != 'flagged') ORDER BY group_name ASC";

		$output = '';
		$group_membered = $db->get_results( $sql );
		
		if ( $group_membered ) {
			$output .= "<select id='link_group_id' name='link_group_id' tabindex='3' class='form-control submit_group_select'>";
			$output .= "<option value = ''>" . $main_smarty->get_config_vars('PLIKLI_Visual_Group_Select_Group') . "</option>";

			foreach($group_membered as $results) {
				$output .= "<option value = ".$results->group_id. ($linkres->link_group_id ? ' selected' : '') . ">".$results->group_name."</option>";
			}

			$output .= "</select>";
		}

		$main_smarty->assign( 'output', $output );
	}
	
	$vars = '';
	check_actions('do_submit1', $vars);
	$_SESSION['step'] = 1;
}
