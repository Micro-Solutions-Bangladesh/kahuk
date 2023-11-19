<?php
include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

//
// kahuk_debug_url();
// exit;
$loadablePage = kahuk_loadable_page_path();
include $loadablePage;

//
$main_smarty->assign('action_messages', $action_messages);

// show the template
$main_smarty->display($the_template . '/kahuk.tpl');
