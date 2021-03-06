<?php

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR.'comment.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');
include(KAHUK_LIBS_DIR.'csrf.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
$amIadmin = 0;
$amIadmin = $amIadmin + checklevel('admin');
$main_smarty->assign('amIadmin', $amIadmin);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
$canIhaveAccess = $canIhaveAccess + checklevel('moderator');

$is_moderator = checklevel('moderator'); // Moderators have a value of '1' for the variable $is_moderator

if($canIhaveAccess == 0){	
//	$main_smarty->assign('tpl_center', '/admin/access_denied');
//	$main_smarty->display('/admin/admin.tpl');		
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

// sidebar
//$main_smarty = do_sidebar($main_smarty);

if($canIhaveAccess == 1) {
	global $offset;
	$CSRF = new csrf();
	
	// Items per page drop-down
	if(isset($_GET["pagesize"]) && is_numeric($_GET["pagesize"])) {
		misc_data_update('pagesize',$_GET["pagesize"]);
	}
	$pagesize = get_misc_data('pagesize');
	if ($pagesize <= 0) $pagesize = 30;
	$main_smarty->assign('pagesize', $pagesize);

	// figure out what "page" of the results we're on
	$offset=(get_current_page()-1)*$pagesize;
	
	// if user is searching
	$search_sql = '';
	if(isset($_GET["keyword"]) && $_GET["keyword"]!= $main_smarty->get_config_vars('KAHUK_Visual_Search_SearchDefaultText') && !empty($_GET["keyword"])){
		$search_sql = " AND (comment_content LIKE '%".sanitize($_GET["keyword"], 3)."%' OR user_login LIKE '%".sanitize($_GET["keyword"], 3)."%')";
	}
	$user_sql = '';
	if (isset($_GET['user']) && !empty($_GET['user']))
	{
		$user = $db->get_row("SELECT * FROM " . table_users . " where user_login='".$db->escape(sanitize($_GET['user'], 3))."'");
		$user_sql = " AND comment_user_id='".$user->user_id."'";
	}

	// if admin uses the filter
	if(isset($_GET["filter"])) {
		switch (sanitize($_GET["filter"], 3)) {
			case 'all':
				$filter_sql = " comment_status != 'spam' AND comment_status != 'discard'";
				break;
			case 'today':
				$filter_sql = " comment_date > DATE_SUB(NOW(),INTERVAL 1 DAY)";
				break;
			case 'yesterday':
				$filter_sql = " comment_date  BETWEEN DATE_SUB(NOW(),INTERVAL 2 DAY) AND DATE_SUB(NOW(),INTERVAL 1 DAY) ";
				break;
			case 'week':
			 	$filter_sql = " comment_date > DATE_SUB(NOW(),INTERVAL 7 DAY)";
			 	break;					
		  	default:
				$filter_sql = " comment_status = '".$db->escape($_GET["filter"])."' ";
				break;
	  	}	
	}	
	else
		$filter_sql = " comment_status != 'spam' AND comment_status != 'discard'";
	
	$filtered = $db->get_results("SELECT SQL_CALC_FOUND_ROWS * FROM " . table_comments . " 
							LEFT JOIN ".table_users." ON user_id=comment_user_id
							WHERE $filter_sql $search_sql $user_sql 
							ORDER BY comment_date DESC LIMIT $offset,$pagesize");
	$rows = $db->get_var("SELECT FOUND_ROWS()");

	// read comments from database 
	$user = new User;
	$comment = new Comment;
	if($filtered) {
      $template_comments = array();
	  foreach($filtered as $dbfiltered) {
	    $comment->id = $dbfiltered->comment_id;
 	    $cached_comments[$dbfiltered->comment_id] = $dbfiltered;
	    $comment->read();
	    $user->id = $comment->author;
	    $user->read();
		/*Redwine: to restrict changing the comment status where authors are admins, only to admins*/
		if ($amIadmin) {
		  $template_comments[] = array(
			'comment_id' => $comment->id,
			'comment_content' => txt_shorter($comment->content, 90),
			'comment_content_long' => $comment->content,
			'comment_votes' => $comment->votes,
			'comment_author' => $dbfiltered->user_login,
			'comment_link_id' => $comment->link,
			'comment_status' => $comment->status,
			'comment_date' => $dbfiltered->comment_date
		  );
		/*Redwine: moderators are restricted to changing the comment status to normal level and moderators authors only*/
		}elseif ($is_moderator && $user->level != 'admin') {
			$template_comments[] = array(
			'comment_id' => $comment->id,
			'comment_content' => txt_shorter($comment->content, 90),
			'comment_content_long' => $comment->content,
			'comment_votes' => $comment->votes,
			'comment_author' => $dbfiltered->user_login,
			'comment_link_id' => $comment->link,
			'comment_status' => $comment->status,
			'comment_date' => $dbfiltered->comment_date
		  );
		}
		check_actions('comment_extra', $vars);
	  $main_smarty->assign('template_comments', $template_comments);
	  }
	  
	}
	
	// breadcrumbs and page title
	$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel');
	$navwhere['link1'] = getmyurl('admin', '');
	$navwhere['text2'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel_Comments');
	$main_smarty->assign('navbar_where', $navwhere);
	$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel'));
	
	if (isset($_GET['action']) && sanitize($_GET['action'], 3) == "bulkmod" && isset($_POST['admin_acction'])) {
		// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
		if (empty($_POST['token'])) {
			$CSRF->show_invalid_error(1);
			exit;
		}

		$killspammed = array();
		$admin_acction=$_POST['admin_acction'];
		// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
		if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'comments_edit') == 2){
			foreach($_POST["comment"] as $key => $value) {
				$comment_status=$db->get_var('select comment_status from ' . table_comments . '  WHERE comment_id = "'.$key.'"');
				
				if($comment_status!=$admin_acction){
				
				if ($admin_acction == "published") {
					$db->query($sql='UPDATE `' . table_comments . '` SET `comment_status` = "published" WHERE `comment_id` = "'.$key.'"');
				}
				elseif ($admin_acction == "moderated") {
					$db->query($sql='UPDATE `' . table_comments . '` SET `comment_status` = "moderated" WHERE `comment_id` = "'.$key.'"');
				}
				elseif ($admin_acction == "discard" || $admin_acction == "delete") {
					$db->query($sql='UPDATE `' . table_comments . '` SET `comment_status` = "discard" WHERE `comment_id` = "'.$key.'"');

				   	$vars = array('comment_id' => $key);
				   	check_actions('comment_discard', $vars);
				}
				elseif ($admin_acction == "spam" && !$killspammed[$user_id]) {
					$user_id = $db->get_var("SELECT comment_user_id FROM `" . table_comments . "` WHERE `comment_id` = ".$key.";");
#					$db->query($sql='UPDATE `' . table_comments . '` SET `comment_status` = "spam" WHERE `comment_id` = "'.$key.'"');
					killspam($user_id);
					$killspammed[$user_id] = 1;
				}
                }
						
			
			}
			header("Location: ".my_kahuk_base."/admin/admin_comments.php?page=".sanitize($_GET['page'],3));
			die();

		} else {
		    $CSRF->show_invalid_error(1);
		    exit;
		}
	} else {
		$CSRF->create('comments_edit', true, true);
	}
	
	// pagename
	define('pagename', 'admin_comments'); 
	$main_smarty->assign('pagename', pagename);
	
	// read the mysql database to get the kahuk version
	/* Redwine: kahuk version query removed and added to /libs/smartyvriables.php */
	
	// show the template
	$main_smarty->assign('tpl_center', '/admin/comments');
	
	if ($is_moderator == '1'){
		$main_smarty->display('/admin/moderator.tpl');
	} else {
		$main_smarty->display('/admin/admin.tpl');
	}

}
else {
	echo 'not for you! go away!';
}
