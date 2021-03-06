<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include_once(KAHUK_LIBS_DIR.'link.php');
include_once(KAHUK_LIBS_DIR.'search.php');
include_once(KAHUK_LIBS_DIR.'smartyvariables.php');

// pagename	
define('pagename', 'module'); 
$main_smarty->assign('pagename', pagename);

$res_for_update=$db->get_row("select var_value from " . table_config . "  where var_name = 'uninstall_module_updates'");
$data_for_update_uninstall_mod=$res_for_update;
//count uninstalled modules with updates available
$main_smarty->assign('un_no_module_update_require', $data_for_update_uninstall_mod->var_value);
$vars = '';
check_actions('module_page', $vars);
