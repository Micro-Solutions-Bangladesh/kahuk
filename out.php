<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;
include('config.php');
include(KAHUK_LIBS_DIR.'link.php');

$requestID = sanitize($_REQUEST['id'], 3);
$requestTitle = sanitize($_REQUEST['title'], 3);
$requestURL = sanitize($_REQUEST['url'], 3);
$requestTitle = sanitize($requestTitle,4);
$requestURL = sanitize($requestURL,4);
$requestURL = preg_replace('/(https?:\/)([^\/])/','$1/$2',$requestURL);

if($requestTitle != ''){
	$requestID = $db->get_var("SELECT link_id FROM " . table_links . " WHERE `link_title_url` = '" . $db->escape($requestTitle) . "';");
}

if($requestURL != ''){
	$requestID = $db->get_var("SELECT link_id FROM " . table_links . " WHERE `link_url` = '" . $db->escape($requestURL) . "';");
}

if(is_numeric($requestID)) {
	$id = $requestID;
	$link = new Link;
	$link->id=$requestID;
	$link->read();

	if(!isset($_SESSION)){session_start();}
	if (!is_array($_SESSION['outphpclicks']) || !isset($_SESSION['outphpclicks'][$id]))
	{
	    $sql = "UPDATE " . table_links . " set link_out=link_out+1 WHERE link_id='$id'";
	    $db->query($sql);
	    $_SESSION['outphpclicks'][$id] = 1;
	}

	header("HTTP/1.1 301 Moved Permanently");
	header('Location: '. $link->url);
}
