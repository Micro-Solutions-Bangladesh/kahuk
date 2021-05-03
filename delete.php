<?php
include_once( 'internal/Smarty.class.php' );
$main_smarty = new Smarty;

include( 'config.php');
include( KAHUK_LIBS_DIR.'link.php' );
include( KAHUK_LIBS_DIR.'smartyvariables.php' );

check_referrer();

force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel( 'admin' );

if ( $canIhaveAccess == 0 ) {	
	header( "Location: " . getmyurl( 'login', $_SERVER['REQUEST_URI'] ) );
	die();
}



/**
 * Depricated
 * 
 * @TODO delete
 */
// function dowork() {
// 	$canIhaveAccess = 0;
// 	$canIhaveAccess = $canIhaveAccess + checklevel('admin');
	
// 	if( $canIhaveAccess == 1 ) {
// 		if ( is_writable( 'settings.php' ) == 0 ){
// 			die( "Error: settings.php is not writeable." );
// 		}

// 		if ( isset( $_REQUEST['action'] ) ) {
// 			$action = $_REQUEST['action'];
// 		} else {
// 			$action = "view";
// 		}

// 		if ( $action == "view" ) {
// 			$config = new kahukconfig;
// 			if ( isset( $_REQUEST['page'] ) ) {
// 				$config->var_page = $_REQUEST['page'];
// 				$config->showpage();
// 			}
// 		}

// 		if ( $action == "save" ) {
// 			$config = new kahukconfig;

// 			$config->var_id = substr( $_REQUEST['var_id'], 6, 10 );
// 			$config->var_value = $_REQUEST['var_value'];
// 			$config->store();
// 		}
// 	}
// }

// pagename
define( 'pagename', 'delete' ); 
$main_smarty->assign( 'pagename', pagename );

if ( isset( $_REQUEST['link_id'] ) ) {
	global $db;

	$link_id = $_REQUEST['link_id'];
	
	if ( !is_numeric( $link_id ) ) {
		die( "Invalid Link ID!" );
	}

	$linkres = new Link;
	$linkres->id = $link_id;
	$linkres->read();

	totals_adjust_count( $linkres->status, -1 );
	
    // module system hook
	$vars = array( 'link_id' => $linkres->id );
	check_actions( 'admin_story_delete', $vars );
	
	/*********find out the page slug dynamically ***********/
	$sql = "SELECT ".table_links.".link_category, ".table_categories.".category_safe_name FROM ".table_categories." LEFT JOIN ".table_links. " ON ".table_links.".link_category = ".table_categories.".category__auto_id WHERE ".table_links.".link_id = '".$link_id."' LIMIT 0,1";
	$linkslugvalue =  $db->get_results( $sql );
	
	$linkslug = '';

	foreach( $linkslugvalue as $slug ) {
		$linkslug = $slug->category_safe_name;
	}

	$delete_referrer = $_SERVER["HTTP_REFERER"];
		
	if ( $URLMethod == 1 ) {
		$delete_referrer_no_query= strtok($_SERVER["HTTP_REFERER"],'?');

		if ( strstr( $delete_referrer, 'group_story.php' ) ) {
			$redirectUrl = $delete_referrer;
		} elseif ( strstr( $delete_referrer, 'story.php' ) ) {
			if ( $linkres->status == 'new' ) {
				$redirectUrl = KAHUK_BASE_URL . "/".$linkres->status.".php?category=$linkslug";
			} else {
				$redirectUrl = KAHUK_BASE_URL . "/?category=$linkslug";
			}
		} else {
			$redirectUrl = $delete_referrer;
		}
	} elseif ( $URLMethod == 2 ) {
		/*Redwine: the first part of the below conditional statement: strstr($delete_referrer, "story.php?title=".$linkres->title_url) is to account for when an Admin is discarding a story; the links are in url method 1 and having the link safe title instead of the link id!*/
		if ( strstr( $delete_referrer, "story.php?title=".$linkres->title_url ) || strstr( $delete_referrer, "/$linkslug/".$linkres->title_url ) ) {
			if ( $linkres->status == 'new' ) {
				$redirectUrl = KAHUK_BASE_URL . "/".$linkres->status."/$linkslug/";
			} else {
				$redirectUrl = KAHUK_BASE_URL . "/$linkslug/";
			}
		} else {
			$redirectUrl = $delete_referrer;
		}
	} 
		
	$link_delete = $db->query( " Delete from " . table_links . " where link_id =" . $linkres->id );
	$vote_delete = $db->query( " Delete from " . table_votes." where vote_link_id =" . $linkres->id );
	$comment_delete = $db->query( " Delete from " . table_comments . " where comment_link_id =" . $linkres->id );
	$saved_delete = $db->query( " Delete from " . table_saved_links . " where saved_link_id =" . $linkres->id );
	$trackback_delete = $db->query( " Delete from " . table_trackbacks . " where trackback_link_id =" . $linkres->id );

	$db->query( "DELETE FROM " . table_additional_categories . " WHERE ac_link_id =" . $linkres->id );

	if ( $_SERVER['HTTP_REFERER'] && strpos( $_SERVER['HTTP_REFERER'], KAHUK_BASE_URL )  !== false ){
		header( 'Location: ' . $redirectUrl );
	} else {
		header( 'Location: ' . KAHUK_BASE_URL );
	}
}

if( isset( $_REQUEST['comment_id'] ) ) {
	global $db;

	$comment_id = $_REQUEST['comment_id'];

	if( !is_numeric( $comment_id ) ){
		die( "Invalid Comment ID!" );
	}

	$link_id = $db->get_var("SELECT comment_link_id FROM `" . table_comments . "` WHERE `comment_id` = $comment_id" );
	
	$vars = array( 'comment_id' => $comment_id );
	check_actions( 'comment_deleted', $vars );
	
	$db->query('DELETE FROM `' . table_comments . '` WHERE `comment_id` = "'.$comment_id.'"');
	$comments = $db->get_results($sql="SELECT comment_id FROM " . table_comments . " WHERE `comment_parent` = '$comment_id'");
	
	if (!empty($comments)) {
		foreach($comments as $comment)
		{
			$vars = array('comment_id' => $comment->comment_id);
			check_actions('comment_deleted', $vars);
		}
	}

	$db->query( 'DELETE FROM `' . table_comments . '` WHERE `comment_parent` = "'.$comment_id.'"' );
	$link = new Link;
	$link->id = $link_id;
	$link->read();
	$link->recalc_comments();
	$link->store();
	$link='';

	if ( $_SERVER['HTTP_REFERER'] && strpos( $_SERVER['HTTP_REFERER'], KAHUK_BASE_URL ) === 0 ) {
		header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
	} else {
		header('Location: ' . $redirectUrl );
	}
}
