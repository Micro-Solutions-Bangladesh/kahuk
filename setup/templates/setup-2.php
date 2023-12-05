<?php
include_once(KAHUKPATH . "setup/templates/header.php");

if (kahuk_has_configs_file()) {
    echo kahuk_create_markup_message(
        "File <code>/kahuk-configs.php</code> is exist!",
        "info"
    );
} else {
    // print_r($_POST);
    $settings_check = kahuk_check_db_settings();

    if ($settings_check) {
        kahuk_redirect("./index.php?step=install-1&errors={$settings_check}"); // Redirect to first page
    } else {
        // kahuk_redirect("./index.php?step=install-2&config-status=yes");
    }

    $connection_check = kahuk_check_db_connection();

    if ($connection_check["status"] !== true) {
        kahuk_set_session_message(
            $connection_check["message"],
            "warning"
        );

        // foreach($messagesArray as $msg) {
        //     kahuk_set_session_message(
        //         $msg["message"],
        //         $msg["status"]
        //     );
        // }
        kahuk_redirect($redirectTo . "-1");

    } else {
        $messagesArray[] = [
            "status" => "success",
            "message" => "Database connection done.",
        ];

        create_kahuk_configs_file(); // create /kahuk-configs.php file

        foreach($messagesArray as $msg) {
            echo kahuk_create_markup_message(
                $msg["message"],
                $msg["status"]
            );
        }
    }
}

    echo kahuk_get_session_messages(true);

    // include( KAHUKPATH . "setup/templates/admin-detail-form.php" );
?>
<h2>Admin User Detail</h2>

<form id="form1" name="form1" action="index.php?step=setup-3" method="POST">
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
        <!-- <input type="hidden" name="step" value="setup-3"> -->
        <input type="hidden" name="config_status" value="yes">
        <input type="hidden" name="htaccess_status" value="no">
        <input type="submit" class="btn btn-primary" name="Submit" value="Submit">
    </div>
</form>

<?php
include_once(KAHUKPATH . "setup/templates/footer.php");
