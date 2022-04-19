<?php
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

check_referrer();

// require user to log in
force_authentication();

// restrict access to admins
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

if ($canIhaveAccess == 0) {
    header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
    die();
}


/**
 * Get the list of files
 * 
 * @return array
 */
function kahuk_log_files_list($path = KAHUK_LOG_DIR)
{
    $output = [];

    $files = glob($path . '*.log');

    foreach ($files as $file) {
        $file_info = pathinfo(getcwd() . '/' . $file);

        $output[] = [
            'filename' => $file_info['filename'],
            'extension' => $file_info['extension'],
            'basename' => $file_info['basename'],
            'dirname' => $file_info['dirname'],
        ];
    }

    return $output;
}


$logFiles = kahuk_log_files_list(KAHUK_LOG_DIR . 'error-logs/');


// Delete Error File Action
$deleteFile = _get('delete');

if (file_exists(KAHUK_LOG_DIR . "error-logs/" . $deleteFile . ".log")) {
    $deleteableFile = KAHUK_LOG_DIR . "error-logs/" . $deleteFile . ".log";

    if (!unlink($deleteableFile)) {
        trigger_error("$deleteableFile cannot be deleted due to an error", E_USER_WARNING);
    }

    header("Location: admin_log.php");
    exit;
}

// Display Error File Action
$showFile = _get('show');
$viewableFile = '';
$viewableFilePath = '';

if (file_exists(KAHUK_LOG_DIR . "error-logs/" . $showFile . ".log")) {
    $viewableFile = "error-logs/" . $showFile . ".log";
    // $viewableFilePath = KAHUK_LOG_DIR . $viewableFile;
}

if (empty($viewableFile)) {
    $defaultErrorLogFile = kahuk_error_log_file_name();

    if (file_exists(KAHUK_LOG_DIR . "error-logs/" . $defaultErrorLogFile)) {
        $viewableFile = "error-logs/" . $defaultErrorLogFile;
        // $viewableFilePath = KAHUK_LOG_DIR . "error-logs/" . $defaultErrorLogFile;
    }
}

//
$main_smarty->assign('kahuk_viewable_log_file', $viewableFile);

$kahuk_logs = file_get_contents(KAHUK_LOG_DIR . $viewableFile);
$main_smarty->assign('kahuk_logs', $kahuk_logs);


// pagename
define('pagename', 'admin_log');
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('logfiles', $logFiles);

// show the template
$main_smarty->assign('tpl_center', '/admin/error_log');
$main_smarty->display('/admin/admin.tpl');
