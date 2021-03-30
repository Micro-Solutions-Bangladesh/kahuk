<?php
set_time_limit(180);
ini_set('session.gc_maxlifetime', 3600);

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include(mnminclude.'smartyvariables.php');
/* Redwine: added isset to eliminate the Notice Undefined index: referrer and url. */
if (!isset($_COOKIE['referrer'])){
	if(empty($_POST['phase']) && (!empty($_GET['url']))) {
		if(!empty($_GET['url']))
		{
			$_POST['url'] = $_GET['url'];
		}
	}
	if (isset($_POST['url'])) {
		$url = htmlspecialchars(sanitize($_POST['url']), 3);
		check_referrer($url);
	}
	
}

// html tags allowed during submit
if (checklevel('admin')) {
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_God;
} elseif (checklevel('moderator')){
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Admin;
} else {
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Normal;
}
$main_smarty->assign('Story_Content_Tags_To_Allow', htmlspecialchars($Story_Content_Tags_To_Allow));

// breadcrumbs and page titles
$navwhere['text1'] = $main_smarty->get_config_vars('PLIKLI_Visual_Breadcrumb_Submit');
$navwhere['link1'] = getmyurl('submit', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIKLI_Visual_Breadcrumb_Submit'));
$main_smarty = do_sidebar($main_smarty);

//to check anonymous mode activated
global $current_user;
if($current_user->authenticated != TRUE)
{
	$vars = '';
	check_actions('anonymous_story_user_id', $vars);
	if ($vars['anonymous_story'] != true){
		force_authentication();
	}
}
/*
if ($vars['anonymous_story'] == true)
{
	$anonymous_userid = $db->get_row("SELECT user_id from " . table_users . " where user_login = 'anonymous' ");
	$anonymous_user_id = $anonymous_userid->user_id;
	//echo "val".$anonymous_user_id;
}
*/

// module system hook
$vars = '';
check_actions('submit_post_authentication', $vars);

// this is for direct links from weblogs
if(empty($_POST['phase']) && (!empty($_GET['url']) || (!empty($_GET['id']) && is_numeric($_GET['id'])))) {
	$_POST['phase'] = 1;
	if(!empty($_GET['url'])) 
	{
	    $_POST['url'] = $_GET['url'];
	}
	else
	{
	    $row = $db->get_row("SELECT * FROM ".table_links." WHERE link_id='".$db->escape($_GET['id'])."' AND link_author='{$current_user->user_id}'",ARRAY_A);
	    if (!$row['link_id'])
	    {
		define('pagename', 'submit'); 
		$main_smarty->assign('pagename', pagename);
		$main_smarty->assign('submit_error', 'badkey');
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
		$main_smarty->display($the_template . '/plikli.tpl');
		die();
	    }
	    $_POST['url'] = $row['link_url'];
	}
	    $_POST['randkey'] = rand(10000,10000000);
	if(!empty($_GET['trackback'])) 
	    $_POST['trackback'] = $_GET['trackback'];
}

// determine which step of the submit process we are on
$phase = isset($_POST["phase"]) && is_numeric($_POST["phase"]) ? $_POST["phase"] : 0;

// If show URL input box is disabled, go straight to step 2
if($phase == 0 && Submit_Show_URL_Input == false) {
	$phase = 1;
}
switch ($phase) {
	case 0:
		do_submit0();
		break;
	case 1:
		do_submit1();
		break;
	case 2:
		do_submit2();
		break;
	case 3:
		do_submit3();
		break;
}

exit;

// enter URL before submit process
function do_submit0() {
	global $main_smarty, $the_template;
	$main_smarty->assign('submit_rand', rand(10000,10000000));
	$main_smarty->assign('Submit_Show_URL_Input', Submit_Show_URL_Input);
	$main_smarty->assign('Submit_Require_A_URL', Submit_Require_A_URL);
	
	define('pagename', 'submit'); 
	$main_smarty->assign('pagename', pagename);
	
	$main_smarty->assign('tpl_center', $the_template . '/submit_step_1_center');
	$vars = '';
	check_actions('do_submit0', $vars);
	$main_smarty->display($the_template . '/plikli.tpl');
}

// submit step 1
function do_submit1() {
	global $main_smarty, $db, $dblang, $current_user, $the_template;

	$url = htmlspecialchars(sanitize($_POST['url'], 3));
	$url = str_replace('&amp;', '&', $url);  
	$url = html_entity_decode($url);
	
	if (strpos($url,'http')!==0){
	    $url = "http://$url";
	}
	
	$linkres=new Link;
	$linkres->randkey = sanitize($_POST['randkey'], 3);

	if(Submit_Show_URL_Input == false) {
		$url = "http://";	
		$linkres->randkey = rand(10000,10000000);
	}
	$Submit_Show_URL_Input = Submit_Show_URL_Input;
	if($url == "http://" || $url == ""){
		$Submit_Show_URL_Input = false;
	}
	
	$edit = false;
	if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
	    $linkres->id = $_GET['id'];
	    $linkres->read(FALSE);
	    if (isset($trackback)) $trackback=$_GET['trackback'];
	}
	else
	{
	    $linkres->get($url);
	    if (isset($_POST['title']) && !empty($_POST['title']))
	    	$linkres->title = $db->escape(stripslashes(sanitize(trim($_POST['title']), 4, $Story_Content_Tags_To_Allow)));
	    if (isset($_POST['tags']) && !empty($_POST['tags']))
	    	$linkres->tags = stripslashes(sanitize($_POST['tags'], 4));
			$linkres->tags = preg_replace('/[^\p{L}\p{N}_\s\,]/u', '', $linkres->tags);
	    if (isset($_POST['description']) && !empty($_POST['description']))
	    	$linkres->content = stripslashes(sanitize($_POST['description'], 4, $Story_Content_Tags_To_Allow));

	    if (isset($_POST['category']) && !empty($_POST['category']))
	    {
		$cats = explode(',',$_POST['category']);
		foreach ($cats as $cat)
		    if ($cat_id = $db->get_var("SELECT category_id FROM ".table_categories." WHERE category_name='".$db->escape(trim($cat))."'"))
		    {
			$linkres->category = $cat_id;
			break;
		    }
	    }
	    $trackback=$linkres->trackback;
	}
	$main_smarty->assign('randkey', $linkres->randkey);	
	$main_smarty->assign('submit_url', $url);
	$data = parse_url($url);
	$main_smarty->assign('url', $url);
	$main_smarty->assign('url_short', 'http://'.$data['host']);
	$main_smarty->assign('Submit_Show_URL_Input', $Submit_Show_URL_Input);
	$main_smarty->assign('Submit_Require_A_URL', Submit_Require_A_URL);

	// check if URL is valid format
	/* Redwine: fixed the regex to account for new domains up to 15 characters long */
	$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w]([-\d\w]{0,253}[\d\w])?\.)+[\w]{2,15}(:[\d]+)?(\/([-+_~.,\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.,\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.,\/\d\w]|%[a-fA-f\d]{2,2})*)?$/'; 
#	$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w]([-\d\w]{0,253}[\d\w])?\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.,\d\w]|%[a-fA-f\d]{2,2})*)*(\??(&?([-+_~.,\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.,\/\d\w]|%[a-fA-f\d]{2,2})*)?$/'; 
#	$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?(\d\w?)+[\w]{2,4}(:[\d]+)?(([\/#!+-~.,\d\w]+|%[a-fA-f\d]{2,2}))(\??(&?([-+~.,\d\w]|%[a-fA-f\d]{2,2})=?))?(#([-+_~.,\/\d\w]|%[a-fA-f\d]{2,2}))?$/';

	$isLink = preg_match($pattern, $url); // Returns true if a link
	
	if($url == "http://" || $url == ""){
		if(Submit_Require_A_URL == false){
			$linkres->valid = true;}
		else{
			$linkres->valid = false;
		}
		$linkres->url_title = "";
	} elseif ($isLink == false){
		$linkres->valid = false;
	}
	
	$vars = array("url" => $url,'linkres'=>$linkres);
	check_actions('submit_validating_url', $vars);
	$linkres = $vars['linkres'];
	
	if(!$linkres->valid) {
		$main_smarty->assign('submit_error', 'invalidurl');
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
		$main_smarty->display($the_template . '/plikli.tpl');
		return;
	}
	
	if(Submit_Require_A_URL == true || ($url != "http://" && $url != "")){
		if(!is_numeric($_GET['id']) && $linkres->duplicates($url) > 0) {
			$main_smarty->assign('submit_search', getmyurl("search_url", htmlentities($url)));
			$main_smarty->assign('submit_error', 'dupeurl');
			$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
			
			define('pagename', 'submit'); 
		     	$main_smarty->assign('pagename', pagename);
			
			$main_smarty->display($the_template . '/plikli.tpl');
			return;
		}
	}

	$vars = array("url" => $url);
	check_actions('submit_validating_url', $vars);
	
	totals_adjust_count('discard', 1);
	//echo 'id'.$current_user->user_id;
	$linkres->status='discard';
	$linkres->author=$current_user->user_id;
	$linkres->store();

	$main_smarty->assign('StorySummary_ContentTruncate', StorySummary_ContentTruncate);
	$main_smarty->assign('SubmitSummary_Allow_Edit', SubmitSummary_Allow_Edit);
	$main_smarty->assign('enable_tags', Enable_Tags);
	$main_smarty->assign('submit_url_title', str_replace('"',"&#034;",$linkres->url_title));
	$main_smarty->assign('submit_url_description', $linkres->url_description);
	$main_smarty->assign('submit_id', $linkres->id);
	$main_smarty->assign('submit_type', $linkres->type());
	if(isset($link_title)){$main_smarty->assign('submit_title', str_replace('"',"&#034;",$link_title));}
	if(isset($link_content)){$main_smarty->assign('submit_content', $link_content);}
	$main_smarty->assign('submit_trackback', $trackback);
	$main_smarty->assign('submit_link_field1', $linkres->link_field1);
	$main_smarty->assign('submit_link_field2', $linkres->link_field2);
	$main_smarty->assign('submit_link_field3', $linkres->link_field3);
	$main_smarty->assign('submit_link_field4', $linkres->link_field4);
	$main_smarty->assign('submit_link_field5', $linkres->link_field5);
	$main_smarty->assign('submit_link_field6', $linkres->link_field6);
	$main_smarty->assign('submit_link_field7', $linkres->link_field7);
	$main_smarty->assign('submit_link_field8', $linkres->link_field8);
	$main_smarty->assign('submit_link_field9', $linkres->link_field9);
	$main_smarty->assign('submit_link_field10', $linkres->link_field10);
	$main_smarty->assign('submit_link_field11', $linkres->link_field11);
	$main_smarty->assign('submit_link_field12', $linkres->link_field12);
	$main_smarty->assign('submit_link_field13', $linkres->link_field13);
	$main_smarty->assign('submit_link_field14', $linkres->link_field14);
	$main_smarty->assign('submit_link_field15', $linkres->link_field15);
	$main_smarty->assign('submit_link_group_id', $linkres->link_group_id);

//	$main_smarty->assign('submit_id', $_GET['id']);
	$main_smarty->assign('submit_title', str_replace('"',"&#034;",$linkres->title));
	$main_smarty->assign('submit_content', str_replace("<br />", "\n", $linkres->content));
	$main_smarty->assign('storylen', utf8_strlen(str_replace("<br />", "\n", $linkres->content)));
	$main_smarty->assign('submit_summary', $linkres->link_summary);
	$main_smarty->assign('submit_group', $linkres->link_group_id);
	$main_smarty->assign('submit_category', $linkres->category);
	$main_smarty->assign('submit_additional_cats', $linkres->additional_cats);
	$main_smarty->assign('tags_words', $linkres->tags);
	$main_smarty->assign('og_twitter_image', $linkres->og_twitter_image);

	include_once(mnminclude.'dbtree.php');
	$array = tree_to_array(0, table_categories, FALSE);

	$array = array_values(array_filter($array, "allowToAuthorCat"));

	$main_smarty->assign('submit_lastspacer', 0);
	$main_smarty->assign('submit_cat_array', $array);

	/*include_once(mnminclude.'group.php');
	$group_arr=array();
	$group_arr = get_groupdetail_user();
	//echo "group".print_r($group_arr);
	$main_smarty->assign('submit_group_array', get_groupdetail_user());*/
	
	//to display group drop down
	if(enable_group == "true")
	{
/* Redwine: Roles and permissions and Groups fixes (already implemented https://github.com/redwinefireplace/plikli-cms/commit/65ed15efb51c1c0fc714e11863c6c332a66bb2ca. Fix: Banned and inactive group members could still see the group when submitting a story. */
		$output = '';
		$group_membered = $db->get_results("SELECT group_id,group_name FROM " . table_groups . " 
			LEFT JOIN ".table_group_member." ON member_group_id=group_id
			WHERE member_user_id = $current_user->user_id AND group_status = 'Enable' 
				AND member_status='active'
				AND (member_role != 'banned' && member_role != 'flagged') 
			ORDER BY group_name ASC");
		
		if ($group_membered)
		{
			$output .= "<select id='link_group_id' name='link_group_id' tabindex='3' class='form-control submit_group_select'>";
			$output .= "<option value = ''>".$main_smarty->get_config_vars('PLIKLI_Visual_Group_Select_Group')."</option>";
			foreach($group_membered as $results)
			{
				$output .= "<option value = ".$results->group_id. ($linkres->link_group_id ? ' selected' : '') . ">".$results->group_name."</option>";
			}
			$output .= "</select>";
		}
		$main_smarty->assign('output', $output);
	}
	if($current_user->authenticated != TRUE){
		$vars = '';
		check_actions('register_showform', $vars);
	}
	//Redwine: detect if ckeditor module is installed to know which textarea to hide.
	$isCKEditor = $db->get_row("SELECT * FROM ".table_modules." WHERE `folder`='ckeditor'",ARRAY_A);
	    if ($isCKEditor['folder']) {
			$main_smarty->assign('isCKEditor', 'ckeditorinstalled');
		}else{
			$main_smarty->assign('isCKEditor', 'ckeditornotinstalled');
		}
	
	
	$main_smarty->assign('tpl_extra_fields', $the_template . '/submit_extra_fields');
	$main_smarty->assign('tpl_center', $the_template . '/submit_step_2_center');
	
	define('pagename', 'submit'); 
	$main_smarty->assign('pagename', pagename);
	
	$vars = '';
	check_actions('do_submit1', $vars);
	$_SESSION['step'] = 1;
	$main_smarty->display($the_template . '/plikli.tpl');
}

// submit step 2
function do_submit2() {
	global $db, $main_smarty, $dblang, $the_template, $linkres, $current_user, $Story_Content_Tags_To_Allow;

	$main_smarty->assign('auto_vote', auto_vote);
	$main_smarty->assign('Submit_Show_URL_Input', Submit_Show_URL_Input);
	$main_smarty->assign('Submit_Require_A_URL', Submit_Require_A_URL);
	$main_smarty->assign('link_id', sanitize($_POST['id'], 3));
	define('pagename', 'submit');
	$main_smarty->assign('pagename', pagename);

	if($current_user->authenticated != TRUE){
		$vars = array('username' => $current_user->user_login);
		check_actions('register_check_errors', $vars);
	}
	
	check_actions('submit2_check_errors', $vars);
	
	/*if($vars['error'] == true){
		// No action
	}*/

	$linkres=new Link;
	$linkres->id = sanitize($_POST['id'], 3);
	
	if($_SESSION['step']!=1)die('Wrong step');
	if(!is_numeric($linkres->id))die();
	if(!$linkres->verify_ownership($current_user->user_id))	die($main_smarty->get_config_vars('PLIKLI_Visual_Submit2Errors_NoAccess'));
		
	$linkres->read(FALSE);

	if($linkres->votes($current_user->user_id) == 0 && auto_vote == true) {
		$linkres->insert_vote($current_user->user_id, '10');
		$linkres->store_basic();
		$linkres->read(FALSE); 
	}
	
	if (is_array($_POST['category']))
	{
	    $linkres->category=sanitize($_POST['category'][0], 3);
	    $linkres->additional_cats=array_slice($_POST['category'],1);
	} else {
	    $linkres->category=sanitize($_POST['category'], 3);
	}
	
	$thecat = get_cached_category_data('category_id', $linkres->category);
	$main_smarty->assign('request_category_name', $thecat->category_name);

	$linkres->title = stripslashes(sanitize(trim($_POST['title']), 3));
	$linkres->title_url = makeUrlFriendly($linkres->title, $linkres->id);
	$linkres->tags = tags_normalize_string(stripslashes(sanitize($_POST['tags'], 3)));
	$linkres->content = close_tags(stripslashes(sanitize($_POST['bodytext'], 4, $Story_Content_Tags_To_Allow)));
	//$linkres->content = str_replace("\n", "<br />", $linkres->content);

	if(isset($_POST['link_field1'])){$linkres->link_field1 = sanitize($_POST['link_field1'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field2'])){$linkres->link_field2 = sanitize($_POST['link_field2'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field3'])){$linkres->link_field3 = sanitize($_POST['link_field3'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field4'])){$linkres->link_field4 = sanitize($_POST['link_field4'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field5'])){$linkres->link_field5 = sanitize($_POST['link_field5'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field6'])){$linkres->link_field6 = sanitize($_POST['link_field6'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field7'])){$linkres->link_field7 = sanitize($_POST['link_field7'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field8'])){$linkres->link_field8 = sanitize($_POST['link_field8'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field9'])){$linkres->link_field9 = sanitize($_POST['link_field9'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field10'])){$linkres->link_field10 = sanitize($_POST['link_field10'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field11'])){$linkres->link_field11 = sanitize($_POST['link_field11'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field12'])){$linkres->link_field12 = sanitize($_POST['link_field12'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field13'])){$linkres->link_field13 = sanitize($_POST['link_field13'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field14'])){$linkres->link_field14 = sanitize($_POST['link_field14'], 4, $Story_Content_Tags_To_Allow);}
	if(isset($_POST['link_field15'])){$linkres->link_field15 = sanitize($_POST['link_field15'], 4, $Story_Content_Tags_To_Allow);}

	if(!isset($_POST['summarytext'])){
		$linkres->link_summary = utf8_substr(sanitize($_POST['bodytext'], 4, $Story_Content_Tags_To_Allow), 0, StorySummary_ContentTruncate - 1);
		//$linkres->link_summary = close_tags(str_replace("\n", "<br />", $linkres->link_summary));		
	} else {
		$linkres->link_summary = sanitize($_POST['summarytext'], 4, $Story_Content_Tags_To_Allow);
		//$linkres->link_summary = close_tags(str_replace("\n", "<br />", $linkres->link_summary));
		if(utf8_strlen($linkres->link_summary) > StorySummary_ContentTruncate){
			loghack('SubmitAStory-SummaryGreaterThanLimit', 'username: ' . sanitize($_POST["username"], 3).'|email: '.sanitize($_POST["email"], 3), true);
			$linkres->link_summary = utf8_substr($linkres->link_summary, 0, StorySummary_ContentTruncate - 1);
			//$linkres->link_summary = close_tags(str_replace("\n", "<br />", $linkres->link_summary));
		}
	}
	
	//get link_group_id
	if((isset($_REQUEST['link_group_id']))&&($_REQUEST['link_group_id']!='')){
		$linkres->link_group_id = intval($_REQUEST['link_group_id']);
	}
	else{
		$linkres->link_group_id=0;
	}

	$linkres->store();
/* Redwine: Tag data inserted later in the submission process so that incomplete stories don't add extra data to the tag db tables. https://github.com/Pligg/pligg-cms/commit/2930e716be8f9ce3fcf71ba9289fc280f8f2f4e7 */

	if (link_errors($linkres)) {
		return;
	}

	//comment subscription
	if(isset($_POST['comment_subscription']))
	{
		
		$vars = array('link_id' => $linkres->id);
		check_actions('comment_subscription_insert_function', $vars);
	}
	
	//comment subscription
	if(isset($_POST['timestamp_date_day']))
	{
		//open date
		$timestamp_date_day = $_POST['timestamp_date_day'];
		$timestamp_date_month = $_POST['timestamp_date_month'];
		$timestamp_date_year = $_POST['timestamp_date_year'];
		if (!is_numeric($timestamp_date_day) || !is_numeric($timestamp_date_month) || !is_numeric($timestamp_date_year)){
			$timestamp_date = date("m-d-Y");
		} else {
			$timestamp_date = $timestamp_date_month."-".$timestamp_date_day."-".$timestamp_date_year;
		}
		$vars = array('link_id' => $linkres->id);
		$vars = array('timestamp_date' => $timestamp_date,'link_id' => $linkres->id);
		check_actions('comment_subscription_insert_function', $vars);
	}

	$vars = '';
	check_actions('submit_step_3_after_first_store', $vars);
	
	if (!empty($vars['error']) && $vars['error'] == true && link_catcha_errors('captcha_error')){
		return;
	}
	
	$linkres->read(FALSE);
	$edit = true;
	$link_title = $linkres->title;
	$link_content = $linkres->content;
	$link_title = stripslashes(sanitize($_POST['title'], 3));
	$main_smarty->assign('the_story', $linkres->print_summary('full', true));
	$main_smarty->assign('tags', $linkres->tags);
	
	if (!empty($linkres->tags)) {
		$tags_words = str_replace(",", ", ", $linkres->tags);
		$tags_url = urlencode($linkres->tags);
		$main_smarty->assign('tags_words', $tags_words);
		$main_smarty->assign('tags_url', $tags_url);
	}

	if(isset($url)){
		$main_smarty->assign('submit_url', $url);
	} else {
		$main_smarty->assign('submit_url', '');
	}
	
	$data = parse_url($linkres->url);
	$main_smarty->assign('url_short', $data['host']);
	$main_smarty->assign('submit_url_title', $linkres->url_title);
	$main_smarty->assign('submit_id', $linkres->id);
	$main_smarty->assign('submit_type', $linkres->type());
	$main_smarty->assign('submit_title', str_replace('"',"&#034;",$link_title));
	$main_smarty->assign('submit_content', $link_content);
	
	if(isset($trackback)){
		$main_smarty->assign('submit_trackback', $trackback);
	} else {
		$main_smarty->assign('submit_trackback', '');
	}
	
	$main_smarty->assign('tpl_extra_fields', $the_template . '/submit_extra_fields');
	$main_smarty->assign('tpl_center', $the_template . '/submit_step_3_center');
	

	$vars = '';	
	check_actions('do_submit2', $vars);
	$_SESSION['step'] = 2;
	if (Submit_Complete_Step2){
	    do_submit3();
	} else {
	    $main_smarty->display($the_template . '/plikli.tpl');
	}
}

// submit step 3
function do_submit3() {
	global $db, $dblang, $current_user;

	$linkres=new Link;
	$linkres->id = sanitize($_POST['id'], 3);
	
	if(!is_numeric($linkres->id))die();
	if(!Submit_Complete_Step2 && $_SESSION['step']!=2)die('Wrong step');
	
	$linkres->read();
	if (isset($_REQUEST['draft'])) {
		totals_adjust_count($linkres->status, -1);
		totals_adjust_count('draft', 1);
		$linkres->status = 'draft';
	}elseif (isset($_REQUEST['date_schedule']) && $_REQUEST['date_schedule'] != '') {

		
/*********************************
Redwine: the user's scheduled date, given that it is only composed from the date portion without the time. In order to accurately make a date of it, we have to go through the following process to avoid the discrepancy resulting from the scheduled date entered and the computed date , which is done based on the server timezone calculated by the GMT time.
*********************************/

/* Redwine: now is the the Epoch time in seconds of the current time function */
$now = time();

/* Redwine: make a full date time of the Epoch time  */
$current_date_time = date('Y-m-d H:i:s', $now);
//echo "<br />current_date_time is<br />$current_date_time<br />";

/* Redwine: current date is the epoch time $now converted to date without the time portion */
$currentDate = date('Y-m-d', $now);


/* Redwine: make_current_date is used to calculate the days difference below. */
$make_current_date = date_create($currentDate);

/* Redwine: This is the scheduled date entered by the user (notice that no time portion) */
$scheduled = $_REQUEST['date_schedule'];
/* Redwine: we create a date of it. It would be like this 2017-08-27 00:00:00 */
$scheduled_date = date_create($scheduled);

/*Redwine: the object(DateTime) is an array
object(DateTime)[2]
  public 'date' => string '2017-08-27 00:00:00' (length=19)
  public 'timezone_type' => int 3
  public 'timezone' => string 'UTC' (length=3)
  
  we are interrested in the 'date' string.
*/
$scheduled_current_date = '';
foreach($scheduled_date as $key => $value) {
	if ($key == 'date') {
		$scheduled_current_date = $value;
	}
}


/*Redwine: convert the date string to a date object. */
$make_scheduled_date = date_create($scheduled_current_date);

/*Redwine: caculating the date difference (returns an object) 
object(DateInterval)[4]
  public 'y' => int 0
  public 'm' => int 0
  public 'd' => int 1
  public 'h' => int 0
  public 'i' => int 0
  public 's' => int 0
  public 'invert' => int 1
  public 'days' => int 1 	*** we are interrested in this one ***
*/
$days_diff = date_diff($make_scheduled_date,$make_current_date);

/*Redwine: now we make a new date from the current_date_time calculated above, added to it the days difference. 
NOTE: this calculated date is based on the server timezone calculated by the GMT time.
*/
$new_scheduled_date = date('Y-m-d H:i:s', strtotime($current_date_time. " + $days_diff->days days"));

/*Redwine: if we leave it at the step above, we end up with a discrepancy. Ex: the user enters 2017-08-29. after calculation all the above steps, and based on the time portion of the day he is submitting, the scheduled date ends up being 2017-08-28.
So, we have to also make a date based on the user's timezone detected by the javascript.
*/

/*Redwine: we create the user's timezone and we make a new DateTime object based on time() of the new timezone
The end result is a full date equivalent to the server's time() added to it the days difference. EX: the time() converted to a date object was 2017-08-27 13:45:01 and the days difference was +1 day (the user's scheduled date) then the final scheduled date will be 2017-08-28 13:45:01. This final scheduled date is the $_REQUEST['date_schedule'] from the submit_step_2_center.tpl and passed on to /submit.php do_submit3(), which in turn sends it to be the date to assign to link_date and link_published_date in /libs/link.php
*/
$userTimeZone = $_REQUEST['timezone'];

$userDateTimeZone = new DateTimeZone($userTimeZone);
$userDateTime = new DateTime("now", $userDateTimeZone);

$new_accurate_scheduled_date = '';
foreach($userDateTime as $key => $value) {
	if ($key == 'date') {
		$new_accurate_scheduled_date = $value;
	}
}

/*Redwine: the below code will bring the user's scheduled date to be equal to the current date created from the time() function. */
$userDateTime = $userDateTime->format('Y-m-d H:i:s');

/*Redwine: now we add the days difference calculated above to make it a full user's scheduled accurate date */
$new_accurate_scheduled_date = date('Y-m-d H:i:s', strtotime($userDateTime. " + $days_diff->days days"));		
		
$new_accurate_scheduled_date = strtotime($new_accurate_scheduled_date);		
		
		
		
		totals_adjust_count($linkres->status, -1);
		totals_adjust_count('scheduled', 1);
		$linkres->status = 'scheduled';
		$linkres->published_date = $new_accurate_scheduled_date;
		$linkres->date = $new_accurate_scheduled_date;
	}else{
	totals_adjust_count($linkres->status, -1);
	totals_adjust_count('new', 1);

	$linkres->status='new';
	}
	$vars = array('linkres'=>&$linkres);
	check_actions('do_submit3', $vars);
/* Redwine: Tag data inserted later in the submission process so that incomplete stories don't add extra data to the tag db tables. https://github.com/Pligg/pligg-cms/commit/2930e716be8f9ce3fcf71ba9289fc280f8f2f4e7 */
	$linkres->status = $vars['linkres']->status;
	if ($vars['linkres']->status=='discard')
	{
		$vars = array('link_id' => $linkres->id);
		check_actions('story_discard', $vars);
	}
	elseif ($vars['linkres']->status=='spam')
	{
		$vars = array('link_id' => $linkres->id);
		check_actions('story_spam', $vars);
	}
	

	$linkres->store_basic();
	$linkres->check_should_publish();
/* Redwine: Tag data inserted later in the submission process so that incomplete stories don't add extra data to the tag db tables. https://github.com/Pligg/pligg-cms/commit/2930e716be8f9ce3fcf71ba9289fc280f8f2f4e7 */
	tags_insert_string($linkres->id, $dblang, $linkres->tags);
	
	if(isset($_POST['trackback']) && sanitize($_POST['trackback'], 3) != '') {
		require_once(mnminclude.'trackback.php');
		$trackres = new Trackback;
		$trackres->url=sanitize($_POST['trackback'], 3);
		$trackres->link=$linkres->id;
		$trackres->title=$linkres->title;
		$trackres->author=$linkres->author;
		$trackres->content=$linkres->content;
		$res = $trackres->send();
	}

	$vars = array('linkres'=>$linkres);
	check_actions('submit_pre_redirect', $vars);
	if (isset($vars['redirect'])) {
	    header('Location: '.$vars['redirect']);
	} elseif($linkres->link_group_id == 0){
		/* Redwine: Making sure that the index or New page load appropriately depensing on the settings.
		If votes to publish, in the dashboard voting section is set to 0 or 1, and auto vote in the submit section is set to true, then the story will automatically appear in the published page. Users may get confused with that when the new.php page loads and it's empty. */

		if ($linkres->status == 'draft') {
			$redirect_url = getmyurl('user2', $current_user->user_login, 'draft');
			
			$redirect_url = $my_base_url.str_replace("&amp;", "&", $redirect_url);
			header("Location: " . $redirect_url);
		}elseif ($linkres->status == 'scheduled') {
			$redirect_url = getmyurl('user2', $current_user->user_login, 'scheduled');
			$redirect_url = $my_base_url.str_replace("&amp;", "&", $redirect_url);
			//echo $redirect_url;die();
			header("Location: " . $redirect_url);
		}elseif ( ((int) votes_to_publish == 0 || (int) votes_to_publish == 1 && auto_vote == true) && $linkres->status != 'draft') {
			header("Location: " . getmyurl('root'));
		}else{
			header("Location: " . getmyurl('new'));
		}
	} else {
		$redirect = getmyurl("group_story", $linkres->link_group_id);
		header("Location: $redirect");
	}
	die;
}

// assign any errors found during submit
function link_errors($linkres)
{
	global $main_smarty, $the_template, $cached_categories;
	$error = false;

	if(sanitize($_POST['randkey'], 3) !== $linkres->randkey) { // random key error
		$main_smarty->assign('submit_error', 'badkey');
		$error = true;
	}
	if($linkres->status != 'discard' && $linkres->status != 'draft') { // if link has already been submitted
		$main_smarty->assign('submit_error', 'hashistory');
		$main_smarty->assign('submit_error_history', $linkres->status);
		$error = true;
	}
	$story = preg_replace('/[\s]+/',' ',strip_tags($linkres->content));
	if(utf8_strlen($linkres->title) < minTitleLength  || utf8_strlen($story) < minStoryLength ) {
		$main_smarty->assign('submit_error', 'incomplete');
		$error = true;
	}
	if(utf8_strlen($linkres->title) > maxTitleLength) {
		$main_smarty->assign('submit_error', 'long_title');
		$error = true;
	}
  	if (utf8_strlen($linkres->content) > maxStoryLength ) { 
		$main_smarty->assign('submit_error', 'long_content');
		$error = true;
	}
	/* Redwine: obsolete code. the constant minTagsLength is not even in the config table. */
	/*if(utf8_strlen($linkres->tags) < minTagsLength && $linkres->tags!="" ) {
		$main_smarty->assign('submit_error', 'short_tags');
		$error = true;
	}*/
	
	if(utf8_strlen($linkres->tags) > maxTagsLength) {
		$main_smarty->assign('submit_error', 'long_tags');
		$error = true;
	}
	
  	if (utf8_strlen($linkres->link_summary) > maxSummaryLength ) { 
		$main_smarty->assign('submit_error', 'long_summary');
		$error = true;
	}
	if(preg_match('/.*http:\//', $linkres->title)) { // if URL is found in link title
		$main_smarty->assign('submit_error', 'urlintitle');
		$error = true;
	}
	if(!$linkres->category > 0) { // if no category is selected
		$main_smarty->assign('submit_error', 'nocategory');
		$error = true;
	}
	foreach($cached_categories as $cat) {
		if($cat->category__auto_id == $linkres->category && !allowToAuthorCat($cat)) { // category does not allow authors of this level
			$main_smarty->assign('submit_error', 'nocategory');
			$error = true;
		}
	}
	
	if($error == true){
		$main_smarty->assign('link_id', $linkres->id);
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
		$main_smarty->display($the_template . '/plikli.tpl');
		die();
	}
	
	return $error;
}
// assign any errors found during captch checking
function link_catcha_errors($linkerror)
{
	global $main_smarty, $the_template;
	$error = false;

	if($linkerror == 'captcha_error') { // if no category is selected
		$main_smarty->assign('submit_error', 'register_captcha_error');
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors_center');
		$main_smarty->display($the_template . '/plikli.tpl');
#		$main_smarty->display($the_template . '/submit_errors.tpl');
		$error = true;
	}
	return $error;
}
?>