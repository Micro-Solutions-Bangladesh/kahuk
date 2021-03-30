<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);

if (!isset($_SESSION['agree'])) {
    header ("Location: plikli-cms-terms.php?action=troubleshooter");
}

if (isset($_POST['Submit'])) {
    include_once('./languages/lang_english.php');
  $dbuser = $_POST['dbuser'];
  $dbpass = $_POST['dbpass'];
  $dbname = $_POST['dbname'];
  $dbhost = $_POST['dbhost'];
  if($conn = @mysqli_connect($dbhost,$dbuser,$dbpass)) {
      echo '<div class="alert alert-success">' . $lang['ConnectionEstab'] . '</div>';
      if(mysqli_select_db($conn, $dbname)) {
          
          $sqlServerVersion = $conn->server_info;
           echo '<div class="alert alert-success">
           <p><strong>' . $lang['FoundDb'] . '</strong></p>
           <p>You have what Plikli CMS needs to function properly, MySQL Server Version '. $sqlServerVersion.'</p></div>';
          $_SESSION['mysqlserver'] = $sqlServerVersion;
      } else {
          echo '<div class="alert alert-warning">' .$lang['Error2-3']. '</div>';
      } 
  } else {
      echo '<div class="alert alert-warning">' .$lang['Error2-4']. '</div>'; 
  }
}


$page = 'troubleshooter';
define('page', $page);
$include='header.php'; if (file_exists($include)) { include_once($include); }
$include='functions.php'; if (file_exists($include)) { require_once($include); }
if (!isset($_SESSION['mysqlserver'])) {
    include_once('./languages/lang_english.php');
    echo '<fieldset><form class="form-horizontal" id="form1" name="form1" action="" method="post">
        <div class="alert alert-warning">
            Plikli CMS needs to get the MySQL Server Version in order to determine if it is suitable for the installation or upgrade. In order to do that, we need to establish a connection to your database. Please enter the required information below. 
        </div>
		<p>' . $lang['EnterMySQL'] . '</p>
		
		<div class="control-group">
			<label for="input01" class="control-label">' . $lang['DatabaseName'] . '</label>
			<div class="controls">
				<input class="form-control" name="dbname" type="text" value="" />
			</div>
		</div>

		<div class="control-group">
			<label for="input01" class="control-label">' . $lang['DatabaseUsername'] . '</label>
			<div class="controls">
				<input class="form-control" name="dbuser" type="text" value="" />
			</div>
		</div>
		  
		<div class="control-group">
			<label for="input01" class="control-label">' . $lang['DatabasePassword'] . ' (if it is password protected)</label>
			<div class="controls">
				<input class="form-control" name="dbpass" type="password" value="" />
			</div>
		</div>
		
		<div class="control-group">
			<label for="input01" class="control-label">' . $lang['DatabaseServer'] . '</label>
			<div class="controls">
				<input class="form-control" name="dbhost" type="text" value="localhost" />
			</div>
		</div>
		<br />
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" name="Submit" value="' . $lang['CheckSettings'] . '" />
			<button class="btn btn-default" onclick="history.go(-1)">Back</button>
		</div>
		
</form></fieldset><br /><hr />';
}




if (isset($_POST['language'])) {
	$selected_lang = $_POST['language'];
	foreach ($selected_lang as $sel_lang){
		if (file_exists("../languages/$sel_lang")) {
			$torename = str_replace(".default", "", $sel_lang);
			rename("../languages/$sel_lang", "../languages/$torename");
			chmod("../languages/$torename", 0777);
			
		}
	}
	//header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
//die();
}else{ 
	$selected_lang = "";
}
?>
<style type="text/css">
.popover-inner {
width:500px;
}
ul, ol{list-style-type:none;}
</style>

<script type="text/javascript" language="JavaScript"><!--
function InsertContent(tid) {
if(document.getElementById(tid).style.display == "none") {
	document.getElementById(tid).style.display = "";
	}
else {
	document.getElementById(tid).style.display = "none";
	}
}
//--></script>

<?php
if (isset($_SESSION['mysqlserver'])) {
    $mysqlServerVersion = $_SESSION['mysqlserver'];
}

$mysqlClientversion = CheckmysqlClientVersion(); 


$phpversion = phpversion();

// Tally up how many items are fulfilled.
$required = 25; // This should be the number of checks being performed
$tally = 0;
$warning_php_version = '';
$warning_mysql_Client_version = '';
$warning_mysql_Server_version = '';

if (glob("../languages/*.conf")) { $tally = $tally+1;}

if ($phpversion >= 5.4 && $phpversion < 7.3) {
	$tally = $tally+1;
} elseif ($phpversion >= 7.3) {
	$warning_php_version = "You have PHP version $phpversion and Plikli is compatible with this version. However, there are still some deprecated features that must be checked before it is fully compatible with PHP $phpversion<br />It is recommended that you use PHP 7.2.18 for now. Check the cPanel under SOFTWARE -> MultiPHP Manager. if it is available and you can select the PHP version you want. Otherwise, ask your host to install EasyApache so you can have access to MultiPHP Manager.<br /><strong>You can still proceed with the installation / upgrade, however Plikli CMS will not provide technical support until all the deprecated features have been modified and tested thoroughly!</strong>";
} elseif ($phpversion < 5.4) {
	$warning_php_version = "You have PHP version $phpversion and Plikli is NOT compatible with PHP version $phpversion; as Plikli CMS uses functions that are designed for PHP 5.4+<br />Check the cPanel under SOFTWARE -> MultiPHP Manager. if it is available and you can select the PHP version you want, then set it to 5.4+. Otherwise, ask your host to install EasyApache so you can have access to MultiPHP Manager.";
}
if (version_compare($mysqlClientversion, '5.0.0', '>=')) {
	$tally = $tally+1; 
}else{
	$warning_mysql_Client_version = "You have MySQL Client Server version $mysqlClientversion it may not have the newest API to access MySQL Server. If the site experiences some issues, update the MySQL Client Server!";
}

if (version_compare($mysqlServerVersion, '5.0.3', '>=')) {
	$tally = $tally+1; 
}else{
	$warning_mysql_Server_version = "You have MySQL Server version $mysqlServerVersion <strong>Pay attention when the CMS is installing the tables in the database. If the LINKS table did not install and the installation halted, OPEN <span style=\"font-style: italic;\">/install/installtables.php and change LINE 385<br />`link_url` varchar(\$urllength) collate utf8mb4_unicode_ci NOT NULL default '',
    <br />TO<br />
    `link_url` varchar(255) collate utf8mb4_unicode_ci NOT NULL default '',
    <br /> <span style=\"text-decoration: underline;\">AND RE-INSTALL PLIKLI CMS.</span></span>
    </strong>";
}
$zip_extension = false;
if (!extension_loaded('zip')) {
    $warning_zip_extension = "You need to load the Zip extension in the PHP extensions, otherwise the installation/upgrade will fail! Check the PHP PEAR PACKAGE in your cPanel.";
} else {
    $zip_extension = true;
    $tally = $tally+1;
}

if (function_exists('curl_version')){ $tally = $tally+1; }
if (function_exists('fopen')){ $tally = $tally+1; }
if (function_exists('fwrite')){ $tally = $tally+1; }
if (file_get_contents(__FILE__)){ $tally = $tally+1; }
if (function_exists('gd_info')){ $tally = $tally+1; }
if (file_exists('../settings.php')) { $tally = $tally+1; }
if (file_exists('../libs/dbconnect.php')) { $tally = $tally+1; }
if (file_exists('../logs/bannedips.log')) { $tally = $tally+1; }
if (is_writable('../logs/bannedips.log')) { $tally = $tally+1; }
if (file_exists('../logs/domain-blacklist.log')) { $tally = $tally+1; }
if (is_writable('../logs/domain-blacklist.log')) { $tally = $tally+1; }
if (file_exists('../logs/domain-whitelist.log')) { $tally = $tally+1; }
if (is_writable('../logs/domain-whitelist.log')) { $tally = $tally+1; }
if (is_writable('../admin/backup/')) { $tally = $tally+1; }
if (is_writable('../avatars/groups_uploaded/')) { $tally = $tally+1; }
if (is_writable('../avatars/user_uploaded/')) { $tally = $tally+1; }
if (is_writable('../cache/')) { $tally = $tally+1; }
if (is_writable('../languages/')) { $tally = $tally+1; }
if (is_writable('../libs/dbconnect.php')) { $tally = $tally+1; }
if (is_writable('../settings.php')) { $tally = $tally+1; }
$percent = percent($tally,$required);

if ($tally < $required ){
	echo '<div class="alert alert-warning">
		<p><strong>Warning:</strong> Your server has only met <strong>'.$tally.'</strong> of  the <strong>'.$required.'</strong> requirements to run Plikli CMS. While not all of the items on this page are required to run Plikli, we suggest that you try to comply with the suggestions made on this page. Please see the list below to discover what issues need to be addressed.</p><br />';
		if ($warning_php_version != '') {
			echo "<p>$warning_php_version</p>";
		}
		if ($warning_mysql_Client_version != '') {
			echo "<p>$warning_mysql_Client_version</p><br />";
		}
        if ($warning_mysql_Server_version != '') {
			echo "<p>$warning_mysql_Server_version</p><br />";
		}
        if ($warning_zip_extension != '') {
			echo "<p>$warning_zip_extension</p>";
		}
		echo '<div class="progress" style="margin-bottom: 9px;">
				<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">
					<span class="sr-only">'.$percent.'% Complete</span>
				</div>
			</div>';
} else {
	echo '<div class="alert alert-success">
		<p>Your server met all of the '.$required. ' requirements needed to run Plikli CMS. See the information below for a detailed report.</p><br />';
	
		echo '<div class="progress" style="margin-bottom: 9px;">
				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$percent.'%;">
					<span class="sr-only">'.$percent.'% Complete</span>
				</div>
			</div>';
}
?>
</div>
<?php
			

echo '<table class="table table-bordered table-striped">';
echo '<thead><tr><th colspan="2">Checking for files need to be renamed</th></tr></thead>';
echo '<tbody>';

// Start Language Check
$rename = " must be renamed to ";

$language_file_count = 0;
foreach (glob("../languages/*.conf") as $filename) { $language_file_count = $language_file_count+1;}
if (!glob("../languages/*.conf")) { 
	echo '<tr><td style="width:20px;" class="bad"><i class="fa fa-times"></i></td><td>No Language file has been detected! You will need to remove the .default extension from one of these language files:<ul style="margin:0px 0 5px 15px;padding:0;">';
echo '<form action="" method="post"><br />';
	getfiles("../languages"); // List language files
	echo '<input type="submit" value="submit"></form>';

	echo '</ul></td></tr>';
}else{
    echo "<tr><td style='width:20px;' class='good'><i class='fa fa-check'></i></td><td>You have renamed ";
	echo '<a id="langfiles" data-trigger="hover" data-html="true" data-content="<ul>';
	foreach (glob("../languages/*.conf") as $filename) {
		echo "<li>$filename</li>";
	}
	echo '</ul>" rel="popover" href="#" data-original-title="Renamed Language Files">'.$language_file_count.' language file(s)</a>, which are ready to be used</td></tr>'."\n";
}

$settings = '../settings.php';
$settingsdefault = '../settings.php.default';
if (file_exists($settings)) {
	echo '<tr><td class="good"><i class="fa fa-check"></i></td><td>'.$settings.'</td></tr>';
} else {
	if (file_exists($settingsdefault)) {
		rename("$settingsdefault", "$settings");
		chmod("$settings", 0666);
		echo '<tr><td class="good"><i class="fa fa-check"></i></td><td>We renamed '.$settingsdefault. ' to '.$settings. ' for you and set the CHMOD to 666!</td></tr>';
	}
}
$dbconnect = '../libs/dbconnect.php';
$dbconnectdefault = '../libs/dbconnect.php.default';
if (file_exists($dbconnect)) {
	echo '<tr><td><i class="fa fa-check"></i></td><td>'.$dbconnect.'</td></tr>';
} else {
	if (file_exists($dbconnectdefault)) {
		rename("$dbconnectdefault", "$dbconnect");
		chmod("$dbconnect", 0666);
		echo '<tr><td class="good"><i class="fa fa-check"></i></td><td>We renamed '.$dbconnectdefault. ' to '.$dbconnect. ' for you and set the CHMOD to 666!</td></tr>';
	}
}

$logs = '../logs.default';
if (is_dir($logs)) {
	rename("$logs", '../logs');
	}
$file = '../logs/bannedips.log';
if (file_exists($file)) {
	if (!is_writable($file)) {
		chmod("$file", 0666);
}
	echo '<tr><td class="good"><i class="fa fa-check"></i></td><td>'.$file.'</td></tr>';
}
$file='../logs/domain-blacklist.log';
if (file_exists($file)) {
	if (!is_writable($file)) {
		chmod("$file", 0666);
	}
	echo '<tr><td class="good"><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>';
}
$file='../logs/domain-whitelist.log';
if (file_exists($file)) {
	if (!is_writable($file)) {
		chmod("$file", 0666);
	}
	if (is_writable($file)) { echo '<tr><td class="good"><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }
}
echo '</tbody></table>';




/* This causes a conflict if there is no lang_english.conf language file. */
/*
include_once('../config.php');
if ($URLMethod == 2 && !file_exists('../.htaccess')) { echo '<tr><td><i class="fa fa-times"></i></td><td>URL Method 2 is enabled in your Admin Panel, but the file .htaccess does not exist! Please rename the file "htaccess.default" to ".htaccess"</td></tr>'; }
if ((!$my_base_url) || ($my_base_url == '')) { echo '<tr><td><i class="fa fa-times"></i></td><td>Your Base URL is not set - Visit <a href = "../admin/admin_config.php?page=Location%20Installed">Admin > Config > Location Installed</a> to change your settings. You can also temporarily change the value from ../settings.php if you aren\'t able to access the Admin Panel.</td></tr>'; }
*/

echo '<table class="table table-bordered table-striped">';
echo '<thead><tr><th colspan="2">Checking <a id="chmod" data-trigger="hover" data-html="true" data-content="<span style=\'font-weight:normal;\'>CHMOD represents the read, write, and execute permissions given to files and directories. Plikli CMS requires that certain files and directories are given a CHMOD status of 0777, allowing Plikli to have access to make changes to files. Any lines that return as an error represent files that need to be updated to CHMOD 0777.<span>" rel="popover" href="http://en.wikipedia.org/wiki/Chmod" data-original-title="CHMOD">CHMOD Settings</a></th></tr></thead>';
echo '<tbody>';

$file='../admin/backup/';
if (!is_writable($file)) { echo '<tr><td style="width:20px;"><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this directory to 777.</span></td></tr>'; }
if (is_writable($file)) { echo '<tr><td style="width:20px;"><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }

$file='../avatars/groups_uploaded/';
if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this directory to 777.</span></td></tr>'; }
if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }

$file='../avatars/user_uploaded/';
if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this directory to 777.</span></td></tr>'; }
if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }

$file='../cache/';
if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this directory to 777.</span></td></tr>'; }
if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }

$file='../languages/';
if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this directory and all contained files to 777.</span></td></tr>'; }
if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }

/*foreach (glob("../languages/*.conf") as $filename) {
	if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$filename.' is not writable! Please chmod this file to 777.</span></td></tr>'; }
	if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$filename.'</span></td></tr>'; }*/


$file='../logs/bannedips.log';
if (file_exists($file)) {
	if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this file to 666.</span></td></tr>'; }
	if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }
}

$file='../logs/domain-blacklist.log';
if (file_exists($file)) {
	if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this file to 666.</span></td></tr>'; }
	if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }
}

$file='../logs/domain-whitelist.log';
if (file_exists($file)) {
	if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this file to 666.</span></td></tr>'; }
	if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }
}

$file='../libs/dbconnect.php';
if (file_exists($file)) {
	if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this file to 666.</span></td></tr>'; }
	if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }
}

$file='../settings.php';
if (file_exists($file)) {
	if (!is_writable($file)) { echo '<tr><td><i class="fa fa-times"></i></td><td>'.$file.' is not writable! Please chmod this file to 666.</span></td></tr>'; }
	if (is_writable($file)) { echo '<tr><td><i class="fa fa-check"></i></td><td>'.$file.'</span></td></tr>'; }
}

echo '<table class="table table-bordered table-striped">';
echo '<thead><tr><th colspan="2">Checking Server Settings</th></tr></thead>';
echo '<tbody>';

// PHP
echo '<tr><td>';
if ($phpversion >= 5.4 && $phpversion < 7.3) {
	echo '<i class="fa fa-check"></i>';
}else{
	echo '<i class="fa fa-times"></i>';

}
echo '</td><td><a id="phpversion" data-trigger="hover" data-content="Plikli has been tested on PHP version 5.4+. We have designed the content management system based on PHP 5.4+ technologies, so certain problems may occur when using older versions of PHP. We recommended that your server runs a minimum of PHP 5.4." rel="popover" href="http://us3.php.net/tut.php" data-original-title="PHP Version">PHP Version ('.$phpversion.')</a></td>';
echo '</tr>';

echo '<tr><td>';

if (version_compare($mysqlServerVersion, '5.0.3', '>=')) {
	echo '<i class="fa fa-check"></i>';
}else{
	echo '<i class="fa fa-times"></i>';
}
echo '</td><td><a id="mysqlServerVersion" data-trigger="hover" data-content="Plikli has been tested on MySQL versions 4 and 5, during that process we have discovered that bugs will occasionally pop up if you are running MySQL 4. For this reason we recommend that you use a server with MySQL Server 5.0.3 or later to run a Plikli CMS website. MySQL 5.0.3 has been available for some time now and we hope that most major web hosts now support it. It offers features that are not built into MySQL 4, which we may have used when writing code for Plikli CMS." rel="popover" href="http://dev.mysql.com/doc/" data-original-title="MySQL Server Version">MySQL Server Version ('.$mysqlServerVersion.')</a></td>';
echo '</tr>';

echo '<tr><td>';

if (version_compare($mysqlClientversion, '5.0.0', '>=')) {
	echo '<i class="fa fa-check"></i>';
}else{
	echo '<i class="fa fa-times"></i>';
}
echo '</td><td><a id="mysqlClientversion" data-trigger="hover" data-content="Plikli has been tested on MySQL versions 4 and 5, during that process we have discovered that bugs will occasionally pop up if you are running MySQL 4. For this reason we recommend that you use a server with MySQL 5.0.3 or later to run a Plikli CMS website. MySQL 5.0.3 has been available for some time now and we hope that most major web hosts now support it. It offers features that are not built into MySQL 4, which we may have used when writing code for Plikli CMS." rel="popover" href="http://dev.mysql.com/doc/" data-original-title="MySQL Client API version">MySQL Client API version ('.$mysqlClientversion.')</a></td>';
echo '</tr>';

echo '<tr><td style="width:20px;">', function_exists('curl_version') ? '<i class="fa fa-check"></i></td>' : '<i class="fa fa-times"></i></td>';
	echo '<td><a id="curlwarning" data-trigger="hover" data-content="cURL is a PHP library that allows Plikli to connect to external websites." rel="popover" href="http://php.net/manual/en/book.curl.php" data-original-title="cURL PHP Extension">cURL</a></td></tr>';

echo '<tr><td>', function_exists('fopen') ? '<i class="fa fa-check"></i></td>' : '<i class="fa fa-times"></i></td>';
	echo '<td><a id="fopenwarning" data-trigger="hover" data-content="The fopen function for PHP allows us to create, read, and manipulate local files." rel="popover" href="http://www.w3schools.com/php/func_filesystem_fopen.asp" data-original-title="fopen PHP Function">fopen</a></td></tr>';

echo '<tr><td>', function_exists('fwrite') ? '<i class="fa fa-check"></i></td>' : '<i class="fa fa-times"></i></td>';
	echo '<td><a id="fwritewarning" data-trigger="hover" data-content="The fwrite function is used in conjunction with the fopen function. It allows PHP to write to an opened file." rel="popover" href="http://www.w3schools.com/php/func_filesystem_fwrite.asp" data-original-title="fwrite PHP Function">fwrite</td></tr>';
	
echo '<tr><td>', file_get_contents(__FILE__) ? '<i class="fa fa-check"></i></td>' : '<i class="fa fa-times"></i></td>';
	echo '<td><a id="fgetwarning" data-trigger="hover" data-content="The file_get_contents() function for PHP reads a file into a string." rel="popover" href="http://www.w3schools.com/php/func_filesystem_file_get_contents.asp" data-original-title="fgetwarning PHP Function">file_get_contents</a></td></tr>';
	
echo '<tr><td>', function_exists('gd_info') ? '<i class="fa fa-check"></i></td>' : '<i class="fa fa-times"></i></td>';
	echo '<td><a id="gdwarning" data-trigger="hover" data-content="The GD Graphics Library is a graphics software library for dynamically manipulating images. Any images handled by Plikli, like user avatar or group images, use GD to manipulate the file." rel="popover" href="http://php.net/manual/en/book.image.php" data-original-title="GD Graphics Library">GD Graphics Library</a></td></tr>';
    
echo '<tr><td>', $zip_extension === true ? '<i class="fa fa-check"></i></td>' : '<i class="fa fa-times"></i></td>';
	echo '<td><a id="zipwarning" data-trigger="hover" data-content="You need to load the Zip extension in the PHP extensions, otherwise the installation/upgrade will fail! Check the PHP PEAR PACKAGE in your cPanel." rel="popover"  data-original-title="ZipArchive Class">ZipArchive Class</a></td></tr>';
	
echo '</tbody></table>';

echo '<div class="jumbotron" style="padding:25px 10px;"><p style="text-align:center">Please continue to the <a href="./install.php">Installation Page</a>, the <a href="./upgrade.php">Upgrade Page</a>, or the <a href="../readme.html">Plikli Readme</a>.</p></div>';

?>

<?php $include='footer.php'; if (file_exists($include)) { include_once($include); } ?>

<script>  
$(function ()  
{ 
	$("#langfiles").popover();
	$("#chmod").popover();
	$("#phpversion").popover();
	$("#mysqlClientversion").popover();
    $("#mysqlServerVersion").popover();
	$("#curlwarning").popover();
	$("#fopenwarning").popover();
	$("#fwritewarning").popover();
	$("#fgetwarning").popover();
	$("#gdwarning").popover();
    $("#zipwarning").popover();
});
</script>