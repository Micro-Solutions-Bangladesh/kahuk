<?php

include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;
include('../config.php');
include(mnminclude.'html1.php');
include(mnminclude.'user.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'admin_config.php');
global $db;
check_referrer();
force_authentication();
$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
if($canIhaveAccess == 0){
//	$main_smarty->assign('tpl_center', '/admin/access_denied');
//	$main_smarty->display('/admin/admin.tpl');
		header("Location: " . getmyurl('admin_login', $_SERVER['REQUEST_URI']));
	die();
} // sidebar
//$main_smarty = do_sidebar($main_smarty);
	if(isset($_REQUEST['id'])){
		$user_id = $_REQUEST['id'];
		if(is_numeric($user_id)){
			$user = new User;
			$user->id=$user_id;
			$user->read();
			$db->query(" UPDATE ".table_users." SET `user_modification` = NOW() , `user_lastlogin` = NOW() WHERE `user_id` =".$user_id." LIMIT 1 ");
			echo '<div class="modal fade in">
	<div class="modal-header">
		<button data-dismiss="modal" class="close">&times;</button>
		<h3>Success!</h3>
    </div>
	<div class="modal-body">
		<p><strong> '.$user->username.'</strong>\'s email address has been confirmed.</p>
	</div>
	<div class="modal-footer">
		<a data-dismiss="modal" class="btn btn-primary" href="#">Close</a>
	</div>
</div>';
		}
		else die;
	}
// pagename
define('pagename', 'admin_user_validate');
$main_smarty->assign('pagename', pagename);

?>