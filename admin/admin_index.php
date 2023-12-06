<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');

include(KAHUK_LIBS_DIR.'smartyvariables.php');
include(KAHUK_LIBS_DIR.'csrf.php');

check_referrer();

force_authentication();

if (!in_array($session_user_level, ['admin','moderator'])) {
    die(".");
}

$CSRF = new csrf();

// pagename
define('pagename', 'admin_index');




// show the template
$main_smarty->assign('tpl_center', '/admin/home');
$main_smarty->display('/admin/admin.tpl');
