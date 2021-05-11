<?php

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if($canIhaveAccess == 0){	
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
}

$logFiles = [
    "error" => KAHUK_LOG_DIR . "error.log",
    "debug" => KAHUK_LOG_DIR . "debug.log",
];


if ( isset( $_GET['clear'] ) && array_key_exists( $_GET['clear'], $logFiles ) ) {
    $fp = fopen( $logFiles[ $_GET['clear'] ], "a" );
    ftruncate( $fp,0 );
    fclose( $fp );

    header("Location: admin_log.php");
    exit;
}

//
$showFile = '';

if ( isset( $_GET['show'] ) && array_key_exists( $_GET['show'], $logFiles ) ) {
    // $showFile = $logFiles[ $_GET['show'] ];
    $showFile = $_GET['show'];
}

// pagename
define('pagename', 'admin_log'); 
$main_smarty->assign('pagename', pagename);

$main_smarty->assign('logfiles', $logFiles);
$main_smarty->assign('showfile', $showFile);

// show the template
$main_smarty->assign('tpl_center', '/admin/error_log');
$main_smarty->display('/admin/admin.tpl');
