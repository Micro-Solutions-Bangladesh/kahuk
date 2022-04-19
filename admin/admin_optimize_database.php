<?php
include_once('../internal/Smarty.class.php');
$main_smarty = new Smarty;

include('../config.php');
include(KAHUK_LIBS_DIR . 'link.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

// If called from a browser, required authentication. Cron version does not require.
if ($_SERVER['SERVER_ADDR']) {
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
}

/* new query created to get the optimize table query in one shot and therefore save some processing time and cpu.*/
$query = "SELECT CONCAT('OPTIMIZE TABLE ', GROUP_CONCAT(table_name) , ';' ) AS statement FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "' AND table_name LIKE '" . TABLE_PREFIX . "%';";
$result = $db->get_var($query);
?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">
				<?php echo $main_smarty->get_config_vars('KAHUK_Visual_AdminPanel_Optimized') ?>
			</h4>
		</div>
		<div class="modal-body">
			<?php
			if (!empty($result)) {
				$db->query($result);
				echo '<p>' . $main_smarty->get_config_vars("KAHUK_Visual_AdminPanel_Optimized_Message") . '</p>';
			}
			?>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div><!-- /.modal-content -->
</div>