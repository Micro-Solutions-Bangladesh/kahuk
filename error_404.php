<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

$vars = '';
check_actions('error_404', $vars);

define('pagename', 'error_404');
$main_smarty->assign('pagename', pagename);

// sidebar
$main_smarty = do_sidebar($main_smarty);

// show the template
header("HTTP/1.1 404 Not Found");
$main_smarty->assign('tpl_center', $the_template . '/error_404_center');
$main_smarty->display($the_template . '/kahuk.tpl');
exit;
