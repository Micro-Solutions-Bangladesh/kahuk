<?php
$uname = _post("uname");
$uemail = _post("uemail");
$upassword = _post("upassword");


// echo "<pre>[uname: {$uname}, uemail: {$uemail}, upassword: {$upassword}]</pre>";
// exit;

if (
    $uname && 
    $uemail && 
    $upassword
) {
    $uname = sanitize_text_field($uname);
    $uemail = sanitize_email($uemail);
    $upassword = sanitize_text_field($upassword);

    if (strlen($uname)<8) {
        kahuk_set_session_message(
            "Admin <code>user name</code> require minimum 8 characters.",
            "warning"
        );

        kahuk_redirect($redirectTo."-2");
    }

    if (!$uemail) {
        kahuk_set_session_message(
            "Valid admin <code>email</code> is required.",
            "warning"
        );

        kahuk_redirect($redirectTo."-2");
    }

    if (strlen($upassword)<8) {
        kahuk_set_session_message(
            "Admin <code>password</code> require minimum 8 characters.",
            "warning"
        );

        kahuk_redirect($redirectTo."-2");
    }
} else {
    if ($_GET["config-status"] == "yes") {
        kahuk_set_session_message(
            "Admin <code>user name</code>, <code>email</code>, and <code>password</code> is required.",
            "warning"
        );

        kahuk_redirect($redirectTo."-2");
    } else {
        kahuk_set_session_message(
            "<code>/kahuk-configs.php</code> file is missing!",
            "warning"
        );

        kahuk_redirect($redirectTo."-1");
    }
}


include_once KAHUKPATH . 'kahuk-configs.php';
include_once KAHUKPATH . 'libs/define_tables.php';

// include_once KAHUKPATH . 'libs/db.php';
include_once KAHUKPATH . 'setup/libs/db.php';


include_once KAHUKPATH . 'libs/html1.php';

include_once KAHUKPATH . 'setup/libs/create-tables.php';
include_once KAHUKPATH . 'setup/libs/insert-sample-data.php';



include_once(KAHUKPATH . "setup/templates/header.php");


echo kahuk_get_session_messages(true);



kahuk_create_primary_tables();

kahuk_insert_sample_data();

?>
<div class="complete-setup">
    <form action="index.php?step=setup-4" method="post">
        <input type="hidden" name="step" value="setup-4">
        <input type="submit" class="btn btn-primary" name="Submit" value="Finish Setup &raquo;" />
    </form>
</div>
<?php
include_once(KAHUKPATH . "setup/templates/footer.php");
        
