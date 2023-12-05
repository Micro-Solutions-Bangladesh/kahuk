<?php
include_once(KAHUKPATH . "setup/templates/header.php");

echo kahuk_get_session_messages(true);
?>

<p>
	Before getting started, we need some information on the database. You will need to know the following items before proceeding.
</p>
<ol>
	<li>Database name</li>
	<li>Database username</li>
	<li>Database password</li>
	<li>Database host</li>
	<li>Table prefix (just as a good practice)</li>
	<li>Admin Email</li>
</ol>

<div class="row">
	<div class="col">
		<a href="index.php?step=setup-1" class="link-btn lets-go">Install &raquo;</a>
	</div>
</div>

<?php include_once(KAHUKPATH . "setup/templates/footer.php"); ?>
