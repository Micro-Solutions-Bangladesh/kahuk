<?php
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');
include(KAHUK_LIBS_DIR . 'admin_config.php');

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

// breadcrumbs and page titles
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel');
$navwhere['link1'] = getmyurl('admin', '');
$navwhere['text2'] = $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel_5') . $main_smarty->get_config_vars('KAHUK_Visual_Name');
$navwhere['link2'] = my_kahuk_base . "/admin/admin_config.php";
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Header_AdminPanel'));

// pagename	
define('pagename', 'admin_config');
$main_smarty->assign('pagename', pagename);

// show the template
$main_smarty->assign('tpl_center', '/admin/configure');
if (isset($_REQUEST['action'])) {
	dowork();
} else {
	$main_smarty->display('/admin/admin.tpl');
}

function dowork()
{
	global $db, $main_smarty;

	$canIhaveAccess = 0;
	$canIhaveAccess = $canIhaveAccess + checklevel('admin');

	if ($canIhaveAccess == 1) {
		$action = isset($_REQUEST['action']) && sanitize($_REQUEST['action'], 3) != '' ? sanitize($_REQUEST['action'], 3) : "view";

		if ($action == "view") {
			$config = new kahukconfig;

			if (isset($_REQUEST['page'])) {
				$config->var_page = sanitize($_REQUEST['page'], 3);
				$config->showpage();
			}
		}

		if ($action == "save") {
			$config = new kahukconfig;
			//			$config->var_id = substr(sanitize($_REQUEST['var_id'], 3), 6, 10);
			$config->var_id = sanitize($_REQUEST['var_id'], 3);
			$config->read();

			// Check if template exists
			if ($config->var_name == '$thetemp' && $config->var_value != js_urldecode($_REQUEST['var_value'])) {
				if (!file_exists('../templates/' . js_urldecode($_REQUEST['var_value']))) {
					print "alert('" . $main_smarty->get_config_vars('KAHUK_Visual_AdminPanel_NoTemplate') . "')";
					exit;
				} else {
					if (file_exists('../templates/' . js_urldecode($_REQUEST['var_value']) . '/template_details.php'))
						include('../templates/' . js_urldecode($_REQUEST['var_value']) . '/template_details.php');
					/* Redwine: replaced the kahuk version() callwith the kahuk version variable from /libs/smartyvriables.php */
					if ($template_info['designed_for_kahuk_version'] < $kahuk_version && !$_REQUEST['force']) {
						if (!$template_info['designed_for_kahuk_version']) $template_info['designed_for_kahuk_version'] = 'unknown';
						print sprintf(
							"if (confirm('" . $main_smarty->get_config_vars('KAHUK_Visual_AdminPanel_Template_Version') . "')) {XMLHttpRequestObject.open('GET', '?action=save&var_id={$config->var_id}&var_value=" . urlencode($_REQUEST['var_value']) . "&force=1', true); XMLHttpRequestObject.send(null);}",
							$template_info['designed_for_kahuk_version'],
							kahuk_version()
						);
						exit;
					}
				}
			}
			$config->var_value = $db->escape(js_urldecode($_REQUEST['var_value']));
			$config->store(false);

			// added the below function call to delete the cache to make the new settings effect immediate.
			recursive_remove_directory('../cache', TRUE);
		}
	}
}
