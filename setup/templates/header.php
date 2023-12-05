<?php
global $step, $bodyClass;
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="assets/css/styles.css" rel="stylesheet">

    <title>Kahuk CMS Setup</title>
</head>
<body class="<?php echo 'step-' . $step . ' ' .$bodyClass; ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="main-content">
                    <div class="header-content">
                        <h1 class="logo-wrap">
                            <img src="../templates/admin/img/kahuk.png" alt="Kahuk CMS Setup">
                            <span class="screen-reader-text">Welcome to Kahuk CMS Setup.</span>
                        </h1>
                    </div>
                    <div class="main-content">
                        <?php kahuk_template_action_messages(); ?>
                        
