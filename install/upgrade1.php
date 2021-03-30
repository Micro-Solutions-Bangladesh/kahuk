<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
$page = 'upgrade';
include ('header.php');
define("mnmpath", dirname(__FILE__).'/../');
define("mnminclude", dirname(__FILE__).'/../libs/');
define("mnmmodules", dirname(__FILE__).'/../modules/');
include_once(mnmpath.'settings.php');

if ($_GET['language'])
    $language = addslashes(strip_tags($_GET['language']));

echo '<style type="text/css">
h1{font-size:15px;}
h2 {
margin:0 0 5px 0;
line-height:30px;
}
.language_list li {
display:inline-block;
clear:both;
margin:0 0 8px 0;
text-align:left;
padding:3px 3px 2px 10px;
}
.language_list {
margin:0;
padding:0;
}
.well {
background-color: #0073AA;
border:none;
}
fieldset {
width:100%;
-webkit-border-radius: 8px;
-moz-border-radius: 8px;
border-radius: 8px;
background-color: #0073AA;
color:#ffffff;
-webkit-box-shadow: 7px 7px 5px 0px rgba(50, 50, 50, 0.75);
-moz-box-shadow:    7px 7px 5px 0px rgba(50, 50, 50, 0.75);
box-shadow:         7px 7px 5px 0px rgba(50, 50, 50, 0.75);
padding-bottom: 10px;
}
legend {
width: auto;
background: #FF9;
color:#000000;
font-weight:bold;
border: solid 1px black;
-webkit-border-radius: 8px;
-moz-border-radius: 8px;
border-radius: 8px;
padding: 6px;
font-size: 0.9em;
}
.iconalign {vertical-align: bottom;}
.alert-danger, .alert-error {
background-color: #FF0000;
border-color: #F4A2AD;
color: #ffffff;
margin: 0 10px 0 10px;
padding:5px;
}
li{margin-left:30px;}
a:link, a:hover, a:visited, a:active{color:#000000}
.btn-primary, btn {margin-left:10px}
</style>';
echo '<fieldset><legend>'.$lang['Upgrade'].'</legend><div class="well">';

//$include='header.php'; if (file_exists($include)) { include_once($include); }
$include='functions.php'; if (file_exists($include)) { include_once($include); }

if ($_SESSION['cms_name'] == 'pligg_version') {
	echo '<h2>' . $lang['Upgrade-Pligg'] . '</h2>';
}elseif ($_SESSION['cms_name'] == 'kliqqi_version') {
	echo '<h2>' . $lang['Upgrade-Kliqqi'] . '</h2>';
}
$file='../config.php';
if (!file_exists($file)) { $errors[]="$file " . $lang['NotFound'] ; }
elseif (filesize($file) <= 0) { $errors[]="$file " . $lang['ZeroBytes'] ; }

$file='../settings.php';
if (!file_exists($file)) { $errors[]="$file " . $lang['NotFound'] ; }
elseif (filesize($file) <= 0) { $errors[]="$file " . $lang['ZeroBytes'] ; }
elseif (!is_writable($file)) { $errors[]="$file " . $lang['NotEditable'] ; }

$file='../libs/dbconnect.php';
if (!file_exists($file)) { $errors[]="$file " . $lang['NotFound'] ; }
elseif (filesize($file) <= 0) { $errors[]="$file " . $lang['ZeroBytes'] ; }

if (!$errors) {
    //this checks to see if they actually do want to upgrade.
    if (!$_POST['submit']) {
	echo '<p><strong>' . $lang['UpgradeAreYouSure'] . '</strong></p>
		<form id="form" name="form" method="post">
			<input type="submit" class="btn btn-primary" name="submit" value="' . $lang['UpgradeYes'] . '" />
		</form>';
    } else { //they clicked yes!
	$include='../config.php';
	if (file_exists($include)) {
		include_once($include);
		include(mnminclude.'html1.php');
	}
		$tbl_prefix = $_SESSION['table_prefix'];
		$old_version = $_SESSION['old_version'];
		$cms_name = $_SESSION['cms_name'];

	echo '<p>'.$lang['UpgradingTables'] . '<ul>';
			if ($cms_name == 'pligg_version' && $old_version == '122') {
				echo "<li>upgrading from Pligg $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli4-p122.php');
			}elseif (($cms_name == 'pligg_version' && $old_version == '200rc1') || ($cms_name == 'pligg_version' && $old_version == '200rc2')) {
				echo "<li>upgrading from Pligg $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli4-p2rc1.php');
			
			}elseif (($cms_name == 'pligg_version' && $old_version == '200') || ($cms_name == 'pligg_version' && $old_version == '201') || ($cms_name == 'pligg_version' && $old_version == '202') || ($cms_name == 'pligg_version' && $old_version == '203')) {
				echo "<li>upgrading from Pligg $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli4-p200.php');
			}elseif ($cms_name == 'kliqqi_version' && $old_version == '300') {
				echo "<li>upgrading from Kliqqi $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli4-k3005.php');
			}elseif ($cms_name == 'kliqqi_version' && $old_version == '350' || $cms_name == 'kliqqi_version' && $old_version == '352') {
				echo "<li>upgrading from Kliqqi $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli4-k350.php');
			}elseif ($cms_name == 'plikli_version' && $old_version == '400') {
				echo "<li>upgrading from Plikli $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli410-plikli400.php');
			}elseif ($cms_name == 'plikli_version' && $old_version == '410') {
				echo "<li>upgrading from Plikli $old_version to Plikli " . $lang['plikli_version'] . "</li></ul></fieldset>";
				include_once('plikli415-plikli410.php');
			}

    define("mnmmods", __DIR__.'/../modules/');
    $updMods = array('admin_language' => 'admin_language.zip', 'captcha' => 'captcha.zip');

    foreach ($updMods as $key => $value) {
        $ch = curl_init();
        $fp = fopen (mnmmods."$value", 'w+');
        $ch = curl_init("https://plikli.com/upgrade/$value");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        if(filesize(mnmmods."$value") == 0) {
            echo "<li>" . $key . " module was not downloaded! <a href=\"https://plikli.com/upgrade/$value\">Download $key</a> Module, unzip it to the modules folder.</li>";
        } else {
            $zip = new ZipArchive;
            $res = $zip->open(mnmmods."$value");
            if ($res === TRUE) {
              $zip->extractTo(mnmmods."$key/");
              $zip->close();
              echo '<li>' .$key. ' module updated!</li>';
            } else {
                echo "<li>" . $key . " module was not updated! <a href=\"https://plikli.com/upgrade/$value\">Download $key</a> Module, unzip it to the modules folder.</li>";
            }
        }
        unlink(mnmmods."$value");
	}


		echo '<fieldset><legend>Recalculating Totals, clearing the cache and creating the settings.php file</legend><ul><li>Regenerating the totals table</li>';
	totals_regenerate();

	echo '<li>Clearing /cache directory</li>';
	include_once('../internal/Smarty.class.php');
	$smarty = new Smarty;
	$smarty->config_dir= '';
	$smarty->compile_dir = "../cache";
	$smarty->template_dir = "../templates";
	$smarty->config_dir = "..";
	$smarty->clear_compiled_tpl();

	include(mnminclude.'admin_config.php');
	$config = new plikliconfig;
	$config->create_file("../settings.php");

		echo '</ul><div class="alert alert-info">' . $lang['IfNoError'] . '</div></fieldset>';

    //end of if post submit is Yes.
    }
//end of no errors

    $url = 'https://plikli.com/upgrade/cms.php';
    $userips = $_SERVER['SERVER_ADDR'];
    $fields = array(
        'status'   => 'upgrade',
        'mysqlserver'   => $_SESSION['mysqlserver'],
        'mysqlclient'   => CheckmysqlClientVersion(),
        'myphp'         => phpversion(),
        'mycms'         => $_SESSION['cms_name'],
        'myversion'     => $_SESSION['old_version'],
        'userhost'      => urlencode($my_base_url),
        'userdomain'    => urlencode($my_plikli_base),
        'userips'       => urlencode(get_ip_address()),
        'date'          => strtotime(date('Y-m-d H:i:s'))
    );

    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        return;
    } else {
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($resultStatus == 200) {

        } else {
            return;
        }
    }
    curl_close($ch);
}
else {
	echo DisplayErrors($errors);
	echo '<p>' . $lang['PleaseFix'] . '</p>';
}

echo '</div></fieldset>'; // .well

$include='footer.php'; if (file_exists($include)) { include_once($include); }
?>