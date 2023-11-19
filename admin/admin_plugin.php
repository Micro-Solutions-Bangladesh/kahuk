<?php
define('IS_ADMIN', true);

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

check_referrer();

force_authentication();

if ($session_user_level != "admin") {
    die(".");
}

$plugin = sanitize_text_field(_get('plugin'));
$key = array_search($plugin, $pluginsInstalledArray);

if ($key === false) {
    die("Invalid plugin!");
}


ob_start();
$hooks->do_action("kahuk_settings_page_" . $plugin);
$kahuk_page_markup = ob_get_clean();



$main_smarty->assign('kahuk_page_markup', $kahuk_page_markup);

$main_smarty->assign('tpl_center', '/admin/plugin');

echo $main_smarty->fetch('/admin/admin.tpl');


