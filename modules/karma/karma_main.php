<?php
//
// Settings page
//
function karma_showpage(){
	global $db, $main_smarty, $the_template;
		
	include_once('config.php');
	include_once(KAHUK_LIBS_DIR.'link.php');
	include_once(KAHUK_LIBS_DIR.'smartyvariables.php');

	force_authentication();
	$canIhaveAccess = 0;
	$canIhaveAccess = $canIhaveAccess + checklevel('admin');
	
	if($canIhaveAccess == 1)
	{	
		// Save settings
		if (isset($_POST['submit']))
		{
			misc_data_update('karma_submit_story', sanitize($_REQUEST['karma_submit_story'], 3));
			misc_data_update('karma_submit_comment', sanitize($_REQUEST['karma_submit_comment'], 3));
			misc_data_update('karma_story_publish', sanitize($_REQUEST['karma_story_publish'], 3));
			misc_data_update('karma_story_vote', sanitize($_REQUEST['karma_story_vote'], 3));
			misc_data_update('karma_story_unvote', sanitize($_REQUEST['karma_story_vote_remove'], 3));
			misc_data_update('karma_comment_vote', sanitize($_REQUEST['karma_comment_vote'], 3));
			misc_data_update('karma_story_discard', sanitize($_REQUEST['karma_story_discard'], 3));
			misc_data_update('karma_story_spam', sanitize($_REQUEST['karma_story_spam'], 3));
			misc_data_update('karma_comment_delete', sanitize($_REQUEST['karma_comment_delete'], 3));

			if ($_REQUEST['karma_username'] && $_REQUEST['karma_value']!=0)
			{
        		    $db->query($sql="UPDATE " . table_users . " SET user_karma=user_karma+'".$db->escape($_REQUEST['karma_value'])."' WHERE user_login='".$db->escape($_REQUEST['karma_username'])."'");
			    if (!$db->rows_affected)
				$error = "Wrong username ".sanitize($_REQUEST['karma_username'], 1);
			}

			$main_smarty->assign('error', $error);
		}
		// breadcrumbs
			$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel');
			$navwhere['link1'] = getmyurl('admin', '');
			$navwhere['text2'] = "Modify Karma";
			$navwhere['link2'] = my_kahuk_base . "/module.php?module=karma";
			$main_smarty->assign('navbar_where', $navwhere);
			$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel'));
		// breadcrumbs
		define('modulename', 'karma'); 
		$main_smarty->assign('modulename', modulename);
		
		if (!defined('pagename')) define('pagename', 'admin_modify_karma'); 
		$main_smarty->assign('pagename', pagename);
		$main_smarty->assign('karma_settings', str_replace('"','&#034;',get_karma_settings()));
		$main_smarty->assign('tpl_center', karma_tpl_path . 'karma_main');
		$main_smarty->display('/admin/admin.tpl');
	}
	else
	{
		header("Location: " . getmyurl('login', $_SERVER['REQUEST_URI']));
	}
}	

function karma_do_submit3(&$vars)
{
	global $db, $current_user;


	$karma_settings = get_karma_settings();
	/* Redwine: fix to some bugs in the Karma system. To also update the user_karma with the value of "Voted on an article" when the auto vote is set to true upon story submission. https://github.com/Pligg/pligg-cms/commit/737770202d22ec938465fe66e52f2ae7cdcf5240 */
	if (auto_vote == true) {
        $db->query("UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['submit_story']}' + '{$karma_settings['story_vote']}' WHERE user_id={$current_user->user_id}");
	}else{
		$db->query("UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['submit_story']}' WHERE user_id={$current_user->user_id}");
	}
}

function karma_comment_submit(&$vars)
{
	global $db, $current_user;

	$karma_settings = get_karma_settings();
        $db->query($sql="UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['submit_comment']}' WHERE user_id={$current_user->user_id}");
}

function karma_published(&$vars)
{
	global $db, $current_user;

	$link_id = $vars['link_id'];
	if (!$link_id) return;
	$user_id = $db->get_var("SELECT link_author FROM ".table_links." WHERE link_id='".$db->escape($link_id)."'");

	$karma_settings = get_karma_settings();
        $db->query("UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['story_publish']}' WHERE user_id=$user_id");
}

function karma_vote(&$vars)
{
	global $db, $current_user;
	$karma_settings = get_karma_settings();
        $db->query($sql="UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['story_vote']}' WHERE user_id={$current_user->user_id}");
}

function karma_unvote(&$vars)
{
	global $db, $current_user;
	$karma_settings = get_karma_settings();
        $db->query($sql="UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['story_vote_remove']}' WHERE user_id={$current_user->user_id}");
}

function karma_comment_vote(&$vars)
{
	global $db, $current_user;

	$karma_settings = get_karma_settings();
        $db->query("UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['comment_vote']}' WHERE user_id={$current_user->user_id}");
}

function karma_story_discard(&$vars)
{
	global $db;

	$link_id = $vars['link_id'];
	if (!$link_id) return;
	$user_id = $db->get_var("SELECT link_author FROM ".table_links." WHERE link_id='".$db->escape($link_id)."'");

	$karma_settings = get_karma_settings();
        $db->query("UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['story_discard']}' WHERE user_id='$user_id'");
}

function karma_story_spam(&$vars)
{
	global $db;

	$link_id = $vars['link_id'];
	if (!$link_id) return;
	$user_id = $db->get_var("SELECT link_author FROM ".table_links." WHERE link_id='".$db->escape($link_id)."'");

	$karma_settings = get_karma_settings();
        $db->query("UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['story_spam']}' WHERE user_id='$user_id'");
}

function karma_comment_spam(&$vars)
{
	global $db, $current_user;

	$comment_id = $vars['comment_id'];
	if (!$comment_id) return;
	$user_id = $db->get_var("SELECT comment_user_id FROM ".table_comments." WHERE comment_id='".$db->escape($comment_id)."'");

	$karma_settings = get_karma_settings();
        $db->query($sql="UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['story_spam']}' WHERE user_id='$user_id'");
}

function karma_comment_deleted(&$vars)
{
	global $db;

	$comment_id = $vars['comment_id'];
	if (!$comment_id) return;

	$user_id = $db->get_var("SELECT comment_user_id FROM ".table_comments." WHERE comment_id='".$db->escape($comment_id)."'");

	$karma_settings = get_karma_settings();
    $db->query($sql="UPDATE " . table_users . " SET user_karma=user_karma+'{$karma_settings['comment_delete']}' WHERE user_id='$user_id'");
}


// 
// Read module settings
//
function get_karma_settings()
{
    return array(
		'submit_story' => get_misc_data('karma_submit_story'), 
		'submit_comment' => get_misc_data('karma_submit_comment'),
		'story_publish' => get_misc_data('karma_story_publish'),
		'story_vote' => get_misc_data('karma_story_vote'),
		'story_vote_remove' => get_misc_data('karma_story_unvote'),
		'comment_vote' => get_misc_data('karma_comment_vote'),
		'story_discard' => get_misc_data('karma_story_discard'),
		'story_spam' => get_misc_data('karma_story_spam'),
		'comment_delete' => get_misc_data('karma_comment_delete')
		);
}
