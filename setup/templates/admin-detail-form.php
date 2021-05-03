<h2>Admin User Detail</h2>

<form class="form-horizontal" id="form1" name="form1" action="index.php?step=<?php echo ( $step + 1); ?>" method="post">

    <div class="form-row">
        <label for="uname" class="form-label">Admin Username</label>
        <input name="uname" id="uname" type="text" class="form-control" value="" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <label for="uemail" class="form-label">Admin Email</label>
        <input name="uemail" id="uemail" type="email" class="form-control" value="" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <label for="upassword" class="form-label">Admin Password</label>
        <input name="upassword" id="upassword" type="text" class="form-control" value="" required>
        <div class="form-text"></div>
    </div>

    <div class="form-row">
        <input type="hidden" name="step" value="<?php echo ( $step + 1); ?>">
        <input type="submit" class="btn btn-primary" name="Submit" value="Submit" />
    </div>

</form>
