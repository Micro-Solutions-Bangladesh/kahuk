<?php

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');
include_once(KAHUK_LIBS_DIR.'dbtree.php');
include(KAHUK_LIBS_DIR.'csrf.php');
include(KAHUK_LIBS_DIR.'document_class.php');

$KahukDoc->add_js(my_base_url.my_kahuk_base."/templates/admin/js/move.js");

$KahukDoc->get_js();

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if($canIhaveAccess == 0){	
//	$main_smarty->assign('tpl_center', '/admin/access_denied');
//	$main_smarty->display('/admin/admin.tpl');		
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

if(caching == 1){
	// this is to clear the cache and reload it for settings_from_db.php
	clearCatCache();
}

// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel');
$navwhere['link1'] = getmyurl('admin', '');
$navwhere['text2'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel_2');
$navwhere['link2'] = my_kahuk_base . "/admin_categories.php";
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel'));

if($canIhaveAccess == 1)
{
	$CSRF = new csrf();

	// clear the category sidebar module from the cache so it can regenerate in case we make changes
	$main_smarty->cache = 2; 
	$main_smarty->cache_dir = "cache";
	$main_smarty->clear_cache();
	$main_smarty->cache = false; 

	//$main_smarty = do_sidebar($main_smarty);

	$smarty = $main_smarty;
	$main_smarty = $smarty;	

	// pagename
	define('pagename', 'admin_categories'); 
	$main_smarty->assign('pagename', pagename);
	
	// read the mysql database to get the kahuk version
	/* Redwine: kahuk version query removed and added to /libs/smartyvriables.php */

	rebuild_the_tree();
	ordernew();

// put the category tree into an array for use in the qeip dropdown

	$action = isset($_REQUEST['action']) && sanitize($_REQUEST['action'], 3) != '' ? sanitize($_REQUEST['action'], 3) : "view";
	
	if($action == "htaccess"){
		$htaccess = '../.htaccess';
		if (file_exists($htaccess)) {
		    echo "The file $htaccess already exists. To protect you from accidentally removing it, you must manually remove it from your server before moving on.";
		} else {
		    rename("../htaccess.default", "../.htaccess");
			echo "We have renamed htaccess.default to .htaccess for you. You still need to manually add the special category structure for it to fully work.";
		}
	}
	
	if($action == "save"){
		// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
		if (empty($_POST['token'])) {
			$CSRF->show_invalid_error(1);
			exit;
		}
		// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	    if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'category_manager') == 2){
		    if (!$_POST['safename'])
		    {
			$_POST['safename'] = makeCategoryFriendly($_POST['name']);
			$row = $db->get_row("SELECT * FROM ".table_categories." WHERE category_safe_name='".$db->escape(sanitize($_POST['safename'],4))."' AND category__auto_id!='{$_POST['auto_id']}'");
			$i = '';
			while ($row->category_id>0)
			{
			    $i++;
			    $row = $db->get_row("SELECT * FROM ".table_categories." WHERE category_safe_name='".$db->escape(sanitize($_POST['safename'].$i,4))."' AND category__auto_id!='{$_POST['auto_id']}'");
			}
			$_POST['safename'].=$i;
		    }
		    if ($_POST['auto_id'] && is_numeric($_POST['auto_id']))
		    {
			$id = sanitize($_POST['auto_id'], 3);
			$parent = sanitize($_POST['parent'], 3);
			if (!is_numeric($id)) die();
			if (!is_numeric($parent)) die();
			
			children_id_to_array($array, table_categories, $id);
			if(is_array($array)){
				if(in_array($parent, $array)){
					die('You cannot move a category into it\'s own subcategory. Click <a href = "admin_categories.php">here</a> to reload.');
				}
			}
			if($id == $parent) {header("Location: admin_categories.php");die();}
	
			$db->query("UPDATE `" . table_categories . "` SET category_name='".$db->escape(sanitize($_POST['name'],4))."',
								  category_safe_name='".$db->escape(sanitize($_POST['safename'],4))."',
								  category_parent='".$db->escape(sanitize($_POST['parent'],4))."',
								  category_desc='".$db->escape(sanitize($_POST['description'],4))."',
								  category_keywords='".$db->escape(sanitize($_POST['keywords'],4))."',
								  category_author_level='".$db->escape(sanitize($_POST['level'],4))."',
								  category_author_group='".$db->escape(sanitize($_POST['group'],4))."',
								  category_votes='".$db->escape(sanitize($_POST['votes'],4))."',
								  category_karma='".$db->escape(sanitize($_POST['karma'],4))."'
								  WHERE category__auto_id='{$_POST['auto_id']}'");
		    }
		    else
		    {
			$row = $db->get_row("SELECT * FROM ".table_categories." WHERE category_safe_name='".$db->escape(sanitize($_POST['safename'],4))."'");
			$i = '';
			while ($row->category_id>0)
			{
			    $i++;
			    $row = $db->get_row("SELECT * FROM ".table_categories." WHERE category_safe_name='".$db->escape(sanitize($_POST['safename'].$i,4))."'");
			}
			$_POST['safename'].=$i;
			$db->query("INSERT INTO `" . table_categories . "` SET category_name='".$db->escape(sanitize($_POST['name'],4))."',
									  category_safe_name='".$db->escape(sanitize($_POST['safename'],4))."',
									  category_parent='".$db->escape(sanitize($_POST['parent'],4))."',
									  category_desc='".$db->escape(sanitize($_POST['description'],4))."',
									  category_keywords='".$db->escape(sanitize($_POST['keywords'],4))."',
									  category_author_level='".$db->escape(sanitize($_POST['level'],4))."',
									  category_lang='$dblang',
									  category_votes='".$db->escape(sanitize($_POST['votes'],4))."',
									  category_karma='".$db->escape(sanitize($_POST['karma'],4))."',
									  category_author_group='".$db->escape(sanitize($_POST['group'],4))."'");
		    }
		    Cat_Safe_Names();
		    if(caching == 1){
			// we need to do this here to ensure that other users see our newly save name
			clearCatCache();
		    }
		    rebuild_the_tree();
		    header("Location: admin_categories.php");
	    } else {
		    $CSRF->show_invalid_error(1);
	    }
	    exit;
	}
	elseif($action == "add"){
	    // Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
		if (empty($_POST['token'])) {
			$CSRF->show_invalid_error(1);
			exit;
		}
		// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	    if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'category_manager') == 2){
		$sql = "insert into `" . table_categories . "` (`category_name`) VALUES ('new category');";
		$db->query($sql);
		$last_IDsql = $db->get_var("SELECT category__auto_id from " . table_categories . " where category_name = 'new category';");
		
		rebuild_the_tree();
		ordernew();
		Cat_Safe_Names();
		header("Location: admin_categories.php");
	    } else {
		    $CSRF->show_invalid_error(1);
	    }
	    exit;
	}
	elseif($action == "reset"){
	    $db->query("UPDATE ".table_users." SET user_categories=''");
	    header("Location: admin_categories.php");
	    exit;
	}
	elseif($action == "changecolor"){
		$id = sanitize($_REQUEST['id'], 3);
		$color = sanitize($_REQUEST['color'], 3);
		$color = utf8_str_replace('#', '', $color);
		if (!is_numeric($id)) die();
	
		$sql = "update ".table_categories." set category_color = '" . $color . "' where category__auto_id=" . $id . ";";
		echo $sql;
		$db->query($sql);

		Cat_Safe_Names();
	}
	elseif($action == "remove"){
		// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
		if (empty($_GET['token'])) {
			$CSRF->show_invalid_error(1);
			exit;
		}
		// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	    if ($CSRF->check_valid(sanitize($_GET['token'], 3), 'category_manager') == 2){
		$id = sanitize($_REQUEST['id'], 3);
		if (!is_numeric($id)) die();

		// Move/delete stories
		move_delete_stories($id);

		// Move/delete sub-categories
		if ($_REQUEST['sub1'] == 'delete')
		{
		    children_id_to_array($array, table_categories, $id);
		    if (is_array($array))
			foreach ($array as $cid)
			{
			    move_delete_stories($cid);
			    $db->query("DELETE FROM ".table_categories." WHERE category__auto_id=$cid");
			}
		}
		else
		{
		    $row = $db->get_row("SELECT * FROM ".table_categories." WHERE category__auto_id='$id'");
		    $db->query('UPDATE '.table_categories." SET category_parent='{$row->category_parent}' WHERE category_parent='$id'");
		}

		$sql = "delete from ".table_categories." where category__auto_id=" . $id . ";";
		$db->query($sql);
		header("Location: admin_categories.php");
	    } else {
		    $CSRF->show_invalid_error(1);
			exit;
	    }
	    
	}
        elseif($action == "changeparent"){
		// Redwine: if TOKEN is empty, no need to continue, just display the invalid token error.
		if (empty($_POST['token'])) {
			$CSRF->show_invalid_error(1);
			exit;
		}
		// Redwine: if valid TOKEN, proceed. A valid integer must be equal to 2.
	    if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'category_manager')== 2){
		$id = utf8_substr(sanitize($_REQUEST['id'], 3), 9, 100);
		$parent = utf8_substr(sanitize($_REQUEST['parent'], 3), 9, 100);
		if (!is_numeric($id)) die();
		
		children_id_to_array($array, table_categories, $id);
		if(is_array($array)){
			if(in_array($parent, $array)){
				die('You cannot move a category into it\'s own subcategory. Click <a href = "admin_categories.php">here</a> to reload.');
			}
		}
		
		if($id == $parent) {header("Location: admin_categories.php");die();}

		$sql = "update ".table_categories." set category_parent = " . $parent . " where category__auto_id=" . $id . ";";
		$db->query($sql);
		rebuild_the_tree();
		header("Location: admin_categories.php");
	    } else {
		    $CSRF->show_invalid_error(1);
	    }
	    exit;
	}
	elseif($action == "move_above"){
		$id = sanitize($_REQUEST['id_to_move'], 3);
		$move_id = sanitize($_REQUEST['moveabove_id'], 3);
		if (!is_numeric($id)) die();
		if (!is_numeric($move_id)) die();

		if($id == $move_id) {header("Location: admin_categories.php");die();}

		$array = "";
		children_id_to_array($array, table_categories, $id);
		if(is_array($array)){
			if(!in_array($move_id, $array))
			{
				$sql = "Select * from ".table_categories." where category__auto_id=" . $move_id . ";";
				$results = $db->get_row($sql);
				$move_sort = $results->category_order;
				
				$sql = "update ".table_categories." set category_parent = ".$results->category_parent.", category_order = " . ($move_sort - 1) . " where category__auto_id=" . $id . ";";
				$db->query($sql);
				rebuild_the_tree();
				header("Location: admin_categories.php");
				die();
			}else{
				die('You cannot move a category into it\'s own subcategory. Click <a href = "admin_categories.php">here</a> to reload.');
			}
		}else{
			$sql = "Select * from ".table_categories." where category__auto_id=" . $move_id . ";";
			$results = $db->get_row($sql);
			$move_sort = $results->category_order;
			
			$sql = "update ".table_categories." set category_parent = ".$results->category_parent.", category_order = " . ($move_sort - 1) . " where category__auto_id=" . $id . ";";
			$db->query($sql);
			rebuild_the_tree();
			header("Location: admin_categories.php");
			die();
		}
	}
	elseif($action == "move_below"){
		$id = utf8_substr(sanitize($_REQUEST['id_to_move'], 3), 9, 100);
		$move_id = utf8_substr(sanitize($_REQUEST['movebelow_id'], 3), 6, 100);
		if (!is_numeric($id)) die();
		if (!is_numeric($move_id)) die();
		
		if($id == $move_id) {header("Location: admin_categories.php");die();}

		$array = "";
		children_id_to_array($array, table_categories, $id);
		if(is_array($array)){
			if(!in_array($move_id, $array))
			{
				$sql = "Select * from ".table_categories." where category__auto_id=" . $move_id . ";";
				$results = $db->get_row($sql);
				$move_sort = $results->category_order;
				
				$sql = "update ".table_categories." set category_parent = ".$results->category_parent.", category_order = " . ($move_sort + 1) . " where category__auto_id=" . $id . ";";
				$db->query($sql);
				rebuild_the_tree();
				header("Location: admin_categories.php");
				die();
			}else{
				die('You cannot move a category into it\'s own subcategory. Click <a href = "admin_categories.php">here</a> to reload.');
			}
		}else{
			$sql = "Select * from ".table_categories." where category__auto_id=" . $move_id . ";";
			$results = $db->get_row($sql);
			$move_sort = $results->category_order;
			
			$sql = "update ".table_categories." set category_parent = ".$results->category_parent.", category_order = " . ($move_sort + 1) . " where category__auto_id=" . $id . ";";
			$db->query($sql);
			rebuild_the_tree();
			header("Location: admin_categories.php");
			die();
		}
	}
	elseif($action == "view"){
		$CSRF->create('category_manager', true, true);
	
		$categories = kahuk_categories_init();
		$catArray = $categories->get_items( false );

		$hierarchicalCategories = $categories->get_items();

		$main_smarty->assign('cat_count', count($catArray));
		$main_smarty->assign('cat_array', $catArray);
		$main_smarty->assign('hierarchical_categories', $hierarchicalCategories);
		$main_smarty->assign('tpl_center', '/admin/categories');
		$main_smarty->display('/admin/admin.tpl');
	}

}

function makeCategoryFriendly($output) {
	// this was moved out of utils.php because it's only needed when changing
	// category information

	if(function_exists('utils_makeUrlFriendly')) {
		$output = utils_makeUrlFriendly($output);
	}
	
	return $output;	   
}

function Cat_Safe_Names(){
	// this was moved out of dbtree.php because it's only needed when changing
	// category information
	
	global $db;
	$db->query("UPDATE `" . table_categories . "` SET category_id = category__auto_id");
/*	$cats = $db->get_col("Select category_name from " . table_categories . ";");
	if ($cats) {
		foreach($cats as $catname) {
			$db->query("UPDATE `" . table_categories . '` SET `category_name` = "'.safeAddSlashes($catname).'"' . ", `category_safe_name` = '".makeCategoryFriendly($catname)."' WHERE `category_name` =".'"'.safeAddSlashes($catname).'";');
		}
	}
	$cats = $db->get_col("Select category__auto_id from " . table_categories . ";");
	if ($cats) {
		foreach($cats as $catid) {
			$db->query("UPDATE `" . table_categories . "` SET `category_id` = ".$catid." WHERE `category__auto_id` ='".$catid."';");
		}
	}
*/
}

function clearCatCache() {
	global $db, $cached_categories;
	$db->cache_dir = KAHUKPATH.'cache';
	$db->use_disk_cache = true;
	$db->cache_queries = true;
	$db->cache_timeout = 0;
	$cached_categories = loadCategoriesForCache(true);
	$db->cache_queries = false;
}

function move_delete_stories($id) {
	global $db;

	if ($_REQUEST['sub'] == 'delete')
	{
	    if (Multiple_Categories)
		$sql = "SELECT link_id FROM " . table_links . " 
				LEFT JOIN " . table_additional_categories . " ON ac_link_id=link_id
				WHERE link_category=$id OR ac_cat_id=$id
				GROUP BY link_id";
	    else
		$sql = "SELECT link_id FROM " . table_links . " WHERE link_category='$id'";
	    $links = $db->get_results($sql);
  	    foreach($links as $link)
	    {
	 	   $db->query('UPDATE '.table_comments." SET comment_status='discard' WHERE comment_link_id={$link->link_id}");

		   $vars = array('link_id' => $link->link_id);
		   check_actions('story_discard', $vars);
	    	   $db->query('UPDATE '.table_links." SET link_status='discard' WHERE link_id={$link->link_id}");
	    }

	    $db->query('DELETE FROM '.table_additional_categories." WHERE ac_cat_id='$id'");
	}
	elseif ($_REQUEST['move'] && is_numeric($_REQUEST['move']))
	{
	    $db->query('UPDATE '.table_links." SET link_category='{$_REQUEST['move']}' WHERE link_category='$id'");

	    // Update additional categories
	    $db->query('UPDATE IGNORE '.table_additional_categories." SET ac_cat_id='{$_REQUEST['move']}' WHERE ac_cat_id='$id'");
	    // Delete duplicates that were not updated
	    $db->query('DELETE FROM '.table_additional_categories." WHERE ac_cat_id='$id'");
	}
	else
	    die();
}
