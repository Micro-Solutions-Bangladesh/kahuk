<?php
if (!kahuk_has_configs_file()) {
    kahuk_set_session_message(
        "Something went wrong, please try from the first step!",
        "warning"
    );

    kahuk_redirect($redirectTo);
}

$output = create_kahuk_htaccess_file();

if ($output["status"] == "success") {
    kahuk_set_session_message(
        "Website is live! Feel free to delete the <code>setup</code> folder as soon as possible.",
        "success"
    );

    $root_url = (isset( $_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    kahuk_redirect($root_url . kahuk_base_path());
} else {
    die($output["message"]);
}

