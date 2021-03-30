<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'user.php');

$user=$_GET['user'];

$usr = new User();
$usr->username = $user;
if ($usr->read()){
	$email = $usr->email;
}

// pagename
define('pagename', 'register_complete'); 
$main_smarty->assign('pagename', pagename);

/***************************
Redwine: if we are testing the smtp email send, WITH A FAKE EMAIL ADDRESS, the SESSION variable will allow us to print the email message when the register_complete.php is loaded, so that the account that is created can be validated and activated.
***************************/
if (allow_smtp_testing == 1 && smtp_fake_email == 1) {
	$main_smarty->assign('validationEmail', $_SESSION['validationEmail']);
	unset($_SESSION['validationEmail']);
}
$main_smarty = do_sidebar($main_smarty, $navwhere);
$main_smarty->assign('tpl_center', $the_template . '/register_complete_center');
$main_smarty->display($the_template . '/plikli.tpl');
?>