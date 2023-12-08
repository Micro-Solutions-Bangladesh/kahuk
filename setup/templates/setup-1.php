<?php
if (kahuk_has_configs_file()) {
    echo kahuk_create_markup_message(
        "File <code>/kahuk-configs.php</code> is exist!",
        "info"
    );

    kahuk_set_session_message(
        "You need to delete the <code>/kahuk-configs.php</code> file to continue the setup steps.",
        "warning"
    );

    kahuk_redirect($redirectTo);
}

global $messagesArray;

include_once(KAHUKPATH . "setup/templates/header.php");

echo kahuk_get_session_messages(true);
?>

<h2>Database Connection Detail</h2>

<p>
    Enter your MySQL database settings below. 
    If you don't know your MySQL database settings, please contact your hosting provider.
</p>

<form class="form-horizontal" id="form1" name="form1" action="index.php?step=setup-2" method="post">
    <div class="form-row">
        <label for="dbname" class="form-label"><?php echo $lang['DatabaseName']; ?></label>
        <input name="dbname" id="dbname" type="text" class="form-control" value="" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <label for="dbuser" class="form-label"><?php echo $lang['DatabaseUsername']; ?></label>
        <input name="dbuser" id="dbuser" type="text" class="form-control" value="" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <label for="dbpass" class="form-label"><?php echo $lang['DatabasePassword']; ?></label>
        <input name="dbpass" id="dbpass" type="password" class="form-control" value="" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <label for="dbhost" class="form-label"><?php echo $lang['DatabaseServer']; ?></label>
        <input name="dbhost" id="dbhost" type="text" class="form-control" value="localhost" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <label for="tblprefix" class="form-label"><?php echo $lang['TablePrefix']; ?></label>
        <input name="tblprefix" id="tblprefix" type="text" class="form-control" value="" placeholder="kahuk_">
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <input type="hidden" name="step" value="setup-2">
        <input type="submit" class="btn btn-primary" name="Submit" value="<?php echo $lang['StartInstall']; ?>" />
    </div>
</form>

<?php include_once(KAHUKPATH . "setup/templates/footer.php"); ?>

