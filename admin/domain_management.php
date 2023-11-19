<?php
define('IS_ADMIN', true);

/**
 * This file is used to display and manage your domain blacklist and whitelist files
 */
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

check_referrer();
force_authentication();

// File locations
$domain_blacklist = kahuk_log_domain_blacklist();
$blacklist_file = file($domain_blacklist);
$main_smarty->assign('blacklist_file', $blacklist_file);

$domain_whitelist = kahuk_log_domain_whitelist();
$whitelist_file = file($domain_whitelist);
$main_smarty->assign('whitelist_file', $whitelist_file);


// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Ban_This_URL');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', " / " . $main_smarty->get_config_vars('KAHUK_Visual_Ban_This_URL'));

// pagename
define('pagename', 'domain_management');
$main_smarty->assign('pagename', pagename);

if (isset($_REQUEST["id"]) && is_numeric($_REQUEST["id"])) {
	$id = $_REQUEST["id"];
}

if (in_array($session_user_level, ['admin', 'moderator'])) {
	// setup breadcrumbs for the various views
	$view = isset($_GET['view']) && sanitize($_GET['view'], 3) != '' ? sanitize($_GET['view'], 3) : 'domains';
	$main_smarty->assign('view', $view);

	if (isset($_GET["remove"])) {
		$domain = sanitize($_GET["remove"], 3);

		if ($domain == '') {
			$main_smarty->assign('errorText', "No domain was specified");
			$main_smarty->assign('tpl_center', '/admin/domain_management');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}

			exit;
		}

		if (is_writable($domain_whitelist)) {
			$txt = file_get_contents($domain_whitelist);
			$txt = str_replace(trim($domain), '', $txt);
			$txt = preg_replace('/^\n+|^[\t\s]*\n+/m', '', $txt);
			file_put_contents($domain_whitelist, $txt);

			// Prepare the blacklist data for display
			$main_smarty->assign('errorText', "Removed the domain $domain from $domain_whitelist <META http-equiv='refresh' content='1;URL=domain_management.php'> ");
			$main_smarty->assign('blacklist', $domain_whitelist);
			$main_smarty->assign('domain', $domain);
			$main_smarty->assign('tpl_center', '/admin/domain_management');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}
		} else {
			$main_smarty->assign('errorText', "The file $domain_whitelist is not writable");
		}

		if (is_writable($domain_whitelist)) {
			$txt = file_get_contents($domain_whitelist);
			$txt = str_replace(trim($domain), '', $txt);
			$txt = preg_replace('/^\n+|^[\t\s]*\n+/m', '', $txt);
			file_put_contents($domain_whitelist, $txt);

			// Prepare the whitelist data for display
			$main_smarty->assign('errorText', "Removed the domain $domain from $domain_whitelist <META http-equiv='refresh' content='1;URL=domain_management.php'> ");
			$main_smarty->assign('whitelist', $domain_whitelist);
			$main_smarty->assign('domain', $domain);
			$main_smarty->assign('tpl_center', '/admin/domain_management');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}
		} else {
			$main_smarty->assign('errorText', "The file $domain_whitelist is not writable");
		}

		$main_smarty->assign('tpl_center', '/admin/domain_management');

		if (kahuk_check_user_role("moderator")) {
			$main_smarty->display('/admin/moderator.tpl');
		} else {
			$main_smarty->display('/admin/admin.tpl');
		}
	} elseif (isset($_REQUEST['blacklist_add'])) {
		// echo "<pre>blacklist_add</pre>";
		// exit;
		$main_smarty->assign('story_id', sanitize($_REQUEST['id'], 3));
		$main_smarty->assign('domain_to_add',  sanitize($_REQUEST['blacklist_add'], 3));
		$main_smarty->assign('tpl_center', '/admin/domain_blacklist_add');

		if (kahuk_check_user_role("moderator")) {
			$main_smarty->display('/admin/moderator.tpl');
		} else {
			$main_smarty->display('/admin/admin.tpl');
		}
	} elseif (isset($_REQUEST['whitelist_add'])) {
		// echo "<pre>whitelist_add</pre>";
		// exit;
		$main_smarty->assign('story_id', sanitize($_REQUEST['id'], 3));
		$main_smarty->assign('domain_to_add',  sanitize($_REQUEST['whitelist_add'], 3));
		$main_smarty->assign('tpl_center', '/admin/domain_whitelist_add');

		if (kahuk_check_user_role("moderator")) {
			$main_smarty->display('/admin/moderator.tpl');
		} else {
			$main_smarty->display('/admin/admin.tpl');
		}
	} elseif (isset($_REQUEST['doblacklist'])) {
		$domain = strtoupper(sanitize($_REQUEST['doblacklist'], 3)) . "\n";

		if (is_writable($domain_blacklist)) {
			if (!$handle = fopen($domain_blacklist, 'a')) {
				$main_smarty->assign('errorText', "Cannot open file ($domain_blacklist)");
				$main_smarty->assign('tpl_center', '/admin/domain_blacklist_add');

				if (kahuk_check_user_role("moderator")) {
					$main_smarty->display('/admin/moderator.tpl');
				} else {
					$main_smarty->display('/admin/admin.tpl');
				}

				exit;
			}

			if (fwrite($handle, $domain) === FALSE) {
				$main_smarty->assign('errorText', "Cannot write to file ($domain_blacklist)");
				$main_smarty->assign('tpl_center', '/admin/domain_blacklist_add');

				if (kahuk_check_user_role("moderator")) {
					$main_smarty->display('/admin/moderator.tpl');
				} else {
					$main_smarty->display('/admin/admin.tpl');
				}

				exit;
			}

			$main_smarty->assign('domain', $domain);
			$main_smarty->assign('blacklist', $domain_blacklist);
			$main_smarty->assign('errorText', "The domain $domain has been added to the Blacklist file $domain_blacklist <META http-equiv='refresh' content='1;URL=domain_management.php'> ");
			$main_smarty->assign('tpl_center', '/admin/domain_management');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}

			fclose($handle);
		} else {
			$main_smarty->assign('errorText', "The file $domain_blacklist is not writable");
			$main_smarty->assign('tpl_center', '/admin/domain_blacklist_add');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}
		}
	} elseif (isset($_REQUEST['dowhitelist'])) {

		$domain = strtoupper(sanitize($_REQUEST['dowhitelist'], 3)) . "\n";

		if (is_writable($domain_whitelist)) {
			if (!$handle = fopen($domain_whitelist, 'a')) {
				$main_smarty->assign('errorText', "Cannot open file ($domain_whitelist)");
				$main_smarty->assign('tpl_center', '/admin/domain_whitelist_add');

				if (kahuk_check_user_role("moderator")) {
					$main_smarty->display('/admin/moderator.tpl');
				} else {
					$main_smarty->display('/admin/admin.tpl');
				}

				exit;
			}

			if (fwrite($handle, $domain) === FALSE) {
				$main_smarty->assign('errorText', "Cannot write to file ($domain_whitelist)");
				$main_smarty->assign('tpl_center', '/admin/domain_blacklist_add');

				if (kahuk_check_user_role("moderator")) {
					$main_smarty->display('/admin/moderator.tpl');
				} else {
					$main_smarty->display('/admin/admin.tpl');
				}

				exit;
			}

			$main_smarty->assign('domain', $domain);
			$main_smarty->assign('whitelist', $domain_whitelist);
			$main_smarty->assign('storyurl', getmyurl("story", $id));
			$main_smarty->assign('errorText', "The domain $domain has been added to the Whitelist file $domain_whitelist <META http-equiv='refresh' content='1;URL=domain_management.php'> ");
			$main_smarty->assign('tpl_center', '/admin/domain_management');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}

			fclose($handle);
		} else {
			$main_smarty->assign('errorText', "The file $domain_whitelist is not writable");
			$main_smarty->assign('tpl_center', '/admin/domain_whitelist_add');

			if (kahuk_check_user_role("moderator")) {
				$main_smarty->display('/admin/moderator.tpl');
			} else {
				$main_smarty->display('/admin/admin.tpl');
			}
		}
	} else {
		// Default Manage Domains page
		$main_smarty->assign('tpl_center', '/admin/domain_management');

		if (kahuk_check_user_role("moderator")) {
			$main_smarty->display('/admin/moderator.tpl');
		} else {
			$main_smarty->display('/admin/admin.tpl');
		}
	}
} else {
	// You need to login as an admin
	header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
}
