<?php
if ( ! defined( 'KAHUKPATH' ) ) {
	die();
}

$main_smarty->compile_dir = KAHUKPATH."cache/";
$main_smarty->template_dir = KAHUKPATH."templates/";
$main_smarty->cache_dir = KAHUKPATH."cache/";

// determine if we're in root or another folder like admin
if(!defined('lang_loc')){
	$pos = strrpos($_SERVER["SCRIPT_NAME"], "/");
	$path = substr($_SERVER["SCRIPT_NAME"], 0, $pos);
	if ($path == "/"){$path = "";}
	
	if($path != my_kahuk_base){
		define('lang_loc', '..');
	} else {
		define('lang_loc', '.');
	}
}


$main_smarty->config_dir = "";
$main_smarty->force_compile = false; // has to be off to use cache
$main_smarty->config_load(lang_loc . "/languages/lang_" . KAHUK_LANG . ".conf");

if(isset($_GET['id']) && is_numeric($_GET['id'])){$main_smarty->assign('request_id', $_GET['id']);}
if(isset($_GET['category']) && sanitize($_GET['category'], 3) != ''){$main_smarty->assign('request_category', sanitize($_GET['category'], 3));}
if(isset($_GET['search']) && sanitize($_GET['search'], 3) != ''){$main_smarty->assign('request_search', sanitize($_GET['search'], 3));}
if(isset($_POST['username']) && sanitize($_POST['username'], 0) != ''){$main_smarty->assign('login_username', sanitize($_POST['username'], 0));}

$main_smarty->assign('dblang', $dblang);
// $main_smarty->assign('kahuk_language', KAHUK_LANG);
$main_smarty->assign('user_logged_in', $current_user->user_login);
$main_smarty->assign('user_id', $current_user->user_id);

if ($current_user->authenticated == true) {
	$main_smarty->assign('user_level', $current_user->user_level);

	$current_user_level = $current_user->user_level; /* Redwine: user_level is not part oof the $current_user array when user is not auhtenticated (logged in), hence this variable to be used on line 74. otherwise, generating a Notice in case user is not authenticated. */
}else{
	$main_smarty->assign('user_level', "");
	$current_user_level = ''; /* user_level is not part oof the $current_user array when user is not auhtenticated (logged in), hence this variable to be used on line 74. otherwise, generating a Notice in case user is not authenticated. */
}

$main_smarty->assign('user_authenticated', $current_user->authenticated);
$main_smarty->assign('kahuk_base_url', KAHUK_BASE_URL);
$main_smarty->assign('my_base_url', my_base_url);
$main_smarty->assign('my_kahuk_base', my_kahuk_base);
$main_smarty->assign('Allow_Friends', Allow_Friends);

//groups
$main_smarty->assign('enable_group', enable_group);
$main_smarty->assign('group_submit_level', group_submit_level);
$group_submit_level = group_submit_level;
if(group_submit_level == $current_user_level || group_submit_level == 'normal' || $current_user_level == 'admin')
	$main_smarty->assign('group_allow', 1);

$main_smarty->assign('SearchMethod', SearchMethod);
$main_smarty = SetSmartyURLs($main_smarty);

if ($main_smarty->get_template_vars('tpl_center')) {
	$main_smarty->display('blank.tpl');
}

$the_template = THE_TEMPLATE;
$main_smarty->assign('the_template', THE_TEMPLATE);
$main_smarty->assign('tpl_head', $the_template . '/head');
$main_smarty->assign('tpl_body', $the_template . '/body');
$main_smarty->assign('tpl_first_sidebar', $the_template . '/sidebar');
$main_smarty->assign('tpl_second_sidebar', $the_template . '/sidebar2');
$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_footer', $the_template . '/footer');

// Admin Template
$main_smarty->assign('tpl_header_admin', '/header');

//remove this after we eliminate the need for do_header
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if($canIhaveAccess == 1){
	$main_smarty->assign('isadmin', 1);
}

$canIhaveAccess = $canIhaveAccess + checklevel('moderator');

if($canIhaveAccess == 1){
	$main_smarty->assign('isadmin', 1);
}

/* Redwine: I removed the block of code that was before this and which runs 3 queries and added the below block with only one query. */

/* Redwine: all submissions status. Also added a varibale to hold the total of published and new to be used in the sidebar stats module */
$sidebar_stats_stories = 0;
$stats = $db->get_results('select link_status, count(*) total from '.table_links. ' group by link_status');
$total_submissions = 0;
$published_submissions_count = 0;
$new_submissions_count = 0;
$draft_submissions_count = 0;
$scheduled_submissions_count = 0;
$moderated_submissions_count = 0;
$abuse_submissions_count = 0;
$discarded_submissions_count = 0;
$duplicate_submissions_count = 0;
$page_submissions_count = 0;
$spam_submissions_count = 0;

if ($stats) {
	foreach($stats as $row) {
		if ($row->link_status == 'published') {
			$published_submissions_count = $row->total;
			$main_smarty->assign('published_submissions_count', $published_submissions_count);
			$total_submissions += $row->total;
			$sidebar_stats_stories += $row->total;
		}elseif ($row->link_status == 'new') {
			$new_submissions_count = $row->total;
			$main_smarty->assign('new_submissions_count', $new_submissions_count);
			$total_submissions += $row->total;
			$sidebar_stats_stories += $row->total;
		}elseif ($row->link_status == 'draft') {
			$draft_submissions_count = $row->total;
			$main_smarty->assign('draft_submissions_count', $draft_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'scheduled') {
			$scheduled_submissions_count = $row->total;
			$main_smarty->assign('scheduled_submissions_count', $scheduled_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'moderated') {
			$moderated_submissions_count = $row->total;
			$main_smarty->assign('moderated_submissions_count', $moderated_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'abuse') {
			$abuse_submissions_count = $row->total;
			$main_smarty->assign('abuse_submissions_count', $abuse_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'discard') {
			$discarded_submissions_count = $row->total;
			$main_smarty->assign('discarded_submissions_count', $discarded_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'duplicate') {
			$duplicate_submissions_count = $row->total;
			$main_smarty->assign('duplicate_submissions_count', $duplicate_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'page') {
			$page_submissions_count = $row->total;
			$main_smarty->assign('page_submissions_count', $page_submissions_count);
			$total_submissions += $row->total;
		}elseif ($row->link_status == 'spam') {
			$spam_submissions_count = $row->total;
			$main_smarty->assign('spam_submissions_count', $spam_submissions_count);
			$total_submissions += $row->total;
		}
	}
}

$main_smarty->assign('total_submissions', $total_submissions);
$main_smarty->assign('sidebar_stats_stories', $sidebar_stats_stories);
/* Redwine: all user levels */
$users_total = 0;
/* Redwine: added a variable to hold the total of active users that will be used in the sidebar stats module */
$sidebar_stats_members = 0;
$moderated_users_count = 0;
$spammer_users_count = 0;
$admin_users_count = 0;
$moderator_users_count = 0;
$normal_users_count = 0;
$usersstats = $db->get_results("select  user_level, count(*) as TTL, 'DISABLED' as LEVEL from " . table_users. " where (user_level = 'normal' OR user_level = 'spammer') AND user_status = 'disable' group by user_level, user_status UNION select user_level, count(*) as ENB, 'ENABLED' as LEVEL
from " . table_users . " where user_status = 'enable' group by user_level");
foreach($usersstats as $user) {
	if ($user->LEVEL == 'DISABLED' && $user->user_level == 'normal') {
		$moderated_users_count = $user->TTL;
		$main_smarty->assign('moderated_users_count', $moderated_users_count);
		$users_total += $user->TTL;
	}elseif ($user->LEVEL == 'DISABLED' && $user->user_level == 'spammer') {	
		$spammer_users_count = $user->TTL;
		$main_smarty->assign('spammer_users_count', $spammer_users_count);
		$users_total += $user->TTL;
	}elseif ($user->LEVEL == 'ENABLED') {
		$tempVariable = "$".$user->user_level . "_users_count";
		$smartyTempVariable  = str_replace("$", "", $tempVariable);
		$tempVariable = $user->TTL;
		$main_smarty->assign($smartyTempVariable, $tempVariable);
		$users_total += $user->TTL;
		$sidebar_stats_members += $user->TTL;
	}
}
$main_smarty->assign('users_total', $users_total);
$main_smarty->assign('sidebar_stats_members', $sidebar_stats_members);
/* Get the last user to sign up */
/* Redwine: Changed the query to get the last legitimate (excluding spammers) user to sign up */
$last_user = $db->get_var("SELECT user_login FROM " . table_users . " where user_level != 'spammer' ORDER BY user_id DESC LIMIT 1");
$main_smarty->assign('last_user', $last_user); 
/* Redwine: added the query to get the version and removed 16 duplicate queries from all admin and some other files */
// read the mysql database to get the kahuk version
$kahuk_version = kahuk_version();
$main_smarty->assign('version_number', $kahuk_version); 
