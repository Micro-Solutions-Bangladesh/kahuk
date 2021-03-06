<?php

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

check_referrer();

force_authentication();

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if($canIhaveAccess == 0){	
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

// pagename
define('pagename', 'admin_widgets');
$main_smarty->assign('pagename', pagename);

// read the mysql database to get the kahuk version
/* Redwine: kahuk version query removed and added to /libs/smartyvriables.php */

// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel');
$navwhere['link1'] = getmyurl('admin', '');
$navwhere['text2'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel_6');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel_6'));

// sidebar
//$main_smarty = do_sidebar($main_smarty);


if($canIhaveAccess == 1){
	if (isset($_POST["enabled"])) {
		foreach($_POST["enabled"] as $id => $value) 
		{
			$sql = "UPDATE " . table_widgets . " set enabled = ".$db->escape(sanitize($value,3)). " where id=". $db->escape(sanitize($id,3));
			$db->query($sql);
		}
		header("Location: admin_widgets.php");
		exit;
	}

	if(isset($_GET['action']) && $_GET['action']== 'disable'){
		$module = $db->escape(sanitize($_REQUEST['module'],3));
		$sql = "UPDATE " . table_widgets . " set enabled = 0 where `name` = '" . $module . "';";
		//echo $sql;
		$db->query($sql);

		clear_widget_cache();

		header('Location: admin_widgets.php');
		die();
	}	
	if(isset($_GET['action']) && $_GET['action'] == 'enable'){
		$module = $db->escape(sanitize($_REQUEST['module'],3));
		$sql = "UPDATE " . table_widgets . " set enabled = 1 where `name` = '" . $module . "';";
		//echo $sql;
		$db->query($sql);

		clear_widget_cache();

		header('Location: admin_widgets.php');
		die();
	}
if(isset($_GET['action']) && $_GET['action']== 'install'){
	$widget = $db->escape(sanitize($_REQUEST['widget'],3));

	if($widget_info = include_widget_settings($widget))
	{
		$version = $widget_info['version'];
		$name = $widget_info['name'];
#		$requires = $widget_info['requires'];
#		check_widget_requirements($requires);
#		process_db_requirements($widget_info);
		
	} else {
		die('no init.php file exists');
	}
		
	$db->query("INSERT IGNORE INTO " . table_widgets . " (`name`, `version`, `folder`, `enabled`) values ('".$name."', '" . $version . "', '".$widget."', 1);");

	clear_widget_cache();

	header('Location: admin_widgets.php?status=uninstalled');
	die();
}	
if(isset($_GET['action']) && $_GET['action']== 'remove'){
	$widget = $db->escape(sanitize($_REQUEST['widget'],3));
	$sql = "SELECT * FROM " . table_widgets . " WHERE `name` = '" . $widget . "';";
	$row = $db->get_row($sql);

	$sql = "Delete from " . table_widgets . " where `name` = '" . $widget . "';";
	//echo $sql;
	$db->query($sql);

	clear_widget_cache();

	header('Location: admin_widgets.php?status=uninstalled');
	die();
}	


	

	$main_smarty->assign('tpl_center', '/admin/widgets');
	$output = $main_smarty->fetch('/admin/admin.tpl');		

	if (!function_exists('clear_widget_cache')) {
		echo "Your template is not compatible with this version of Kahuk. Missing the 'clear_widgets_cache' function in admin_widgets_center.tpl.";
	} else {
		echo $output;
	}

}

function clear_widget_cache () {
	global $db;
	if(caching == 1){
		// this is to clear the cache and reload it for settings_from_db.php
		$db->cache_dir = KAHUKPATH.'cache';
		$db->use_disk_cache = true;
		$db->cache_queries = true;
		$db->cache_timeout = 0;
		// if this query is changed, be sure to also change it in modules_init.php
		$modules = $db->get_results('SELECT * from ' . table_widgets . ' where enabled=1;');
		$db->cache_queries = false;
	}
}

function include_widget_settings($name)
{
	if(file_exists(KAHUKPATH . '/widgets/'. $name . '/' . 'init.php'))
	{
		include_once(KAHUKPATH . '/widgets/' . $name . '/' . 'init.php');		

		return $widget;
	} else {
		return false;
	}
}
