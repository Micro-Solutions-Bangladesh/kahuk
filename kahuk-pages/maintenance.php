<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Website is under maintenance</title>

    <style>
        body {background-color: #ccc;}
        p {
            margin: 0 0 .5rem;
        }
        h3 {
            margin: .5rem 0 0;
        }
        .w-screen {width: 100vw;}
        .h-screen {height: 100vh;}
        .flex {display: flex;}
        .flex-col {flex-direction: column;}
        .justify-center {justify-content: center;}
        .items-center {align-items: center;}
        .gap-4 {gap: 1rem;}
        .text-center {text-align: center;}
    </style>
</head>
<body class="">
    <div class="w-screen h-screen flex justify-center items-center text-center">
        <div class="flex flex-col">
            <p>Website is under maintenance</p>
            <p>We will be back in few minutes ...</p>
            <?php 
            $site_logo = kahuk_site_logo();

            if ($site_logo) {
                echo "<img src=\"{$site_logo}\" alt=\"Logo\" />";
            } else {
                echo "<h3>" . kahuk_site_name() . "</h3>";
            }
            ?>
        </div>
    </div>
</body>
</html>
