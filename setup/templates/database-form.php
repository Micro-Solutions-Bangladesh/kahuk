
<h2>Database Connection Detail</h2>

<!-- <div class="alert alert-warning">
    Kahuk CMS needs to get the MySQL Server Version in order to determine if it is suitable for the installation or upgrade.
    In order to do that, we need to establish a connection to your database. Please enter the required information below. 
</div> -->

<p>
    Enter your MySQL database settings below. 
    If you don't know your MySQL database settings, please contact your hosting provider.
</p>


<form class="form-horizontal" id="form1" name="form1" action="index.php?step=<?php echo ( $step + 1); ?>" method="post">
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
        <input name="tblprefix" id="tblprefix" type="text" class="form-control" value="" placeholder="kahuk_life_">
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <input type="hidden" name="step" value="<?php echo ( $step + 1); ?>">
        <input type="submit" class="btn btn-primary" name="Submit" value="<?php echo $lang['StartInstall']; ?>" />
    </div>
</form>


