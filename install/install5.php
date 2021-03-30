<?php
if (!$step) { header('Location: ./install.php'); die(); }
if ($_POST['language'] == 'arabic') {
	$site_direction = "rtl";
}else{
	$site_direction = "ltr";
}
if ($_POST['language'])
    $language = addslashes(strip_tags($_POST['language']));
if($language == 'arabic'){
	include_once('./languages/lang_arabic.php');
}elseif($language == 'catalan'){
	include_once('./languages/lang_catalan.php');
}elseif($language == 'chinese_simplified'){
	include_once('./languages/lang_chinese_simplified.php');
}elseif($language == 'french'){
	include_once('./languages/lang_french.php');
}elseif($language == 'german'){
	include_once('./languages/lang_german.php');
}elseif($language == 'italian'){
	include_once('./languages/lang_italian.php');
}elseif($language == 'russian'){
	include_once('./languages/lang_russian.php');
}elseif($language == 'thai'){
	include_once('./languages/lang_thai.php');
} elseif($language == 'spanish'){
	include_once('./languages/lang_spanish.php');
} elseif($language == 'italian'){
	include_once('./languages/lang_italian.php');
} elseif($language == 'portuguese'){
	include_once('./languages/lang_portuguese.php');
} elseif($language == 'swedish'){
	include_once('./languages/lang_swedish.php');
} else {
	include_once('./languages/lang_english.php');
}

echo '<div class="instructions">';
$file='../config.php';
if (!file_exists($file)) { $errors[]="$file " . $lang['NotFound'] ; }
elseif (filesize($file) <= 0) { $errors[]="$file " . $lang['ZeroBytes'] ; }

$file='../settings.php';
if (!file_exists($file)) { $errors[]="$file " . $lang['SettingsNotFound'] ; }
elseif (filesize($file) <= 0) { $errors[]="$file " . $lang['ZeroBytes'] ; }
elseif (!is_writable($file)) { $errors[]="$file " . $lang['NotEditable'] ; }

define("mnminclude", dirname(__FILE__).'/../libs/');
include_once mnminclude.'db.php';
include_once mnminclude.'html1.php';

// Check user input here
if (!$_POST['adminlogin'] || !$_POST['adminpassword'] || !$_POST['adminemail']) {
	$errors[] = $lang['Error5-1'];
} elseif ($_POST['adminpassword'] != $_POST['adminpassword2']) {
    $errors[] = $lang['Error5-2'];
}
	
if (!$errors) {
	include_once( '../config.php' );
	include_once( '../libs/admin_config.php' );
	
//	echo "Adding the Admin user account...<br />";
	$userip=$db->escape($_SERVER['REMOTE_ADDR']);
	$saltedpass=generatePassHash($_POST['adminpassword']);
	$sql = "INSERT INTO `" . table_users . "` (`user_id`, `user_login`, `user_level`, `user_modification`, `user_date`, `user_pass`, `user_email`, `user_names`, `user_karma`, `user_url`, `user_lastlogin`, `user_ip`, `user_lastip`, `last_reset_request`, `user_enabled`) VALUES (1, '".$db->escape($_POST['adminlogin'])."', 'admin', now(), now(), '$saltedpass', '".$db->escape($_POST['adminemail'])."', '', '10.00', 'https://www.plikli.com', now(), '0', '0', now(), '1');";
	$db->query( $sql );

	// If user specified a site title, change language files.
	if (isset($_POST['sitetitle']) && $_POST['sitetitle'] != ''){
		// Change the value for PLIKLI_Visual_Name in the language files
		$replacement = 'PLIKLI_Visual_Name = "'.strip_tags($_POST['sitetitle']).'"';
		if (glob("../languages/*.conf")) {
			foreach (glob("../languages/*.conf") as $filename) {
				$filedata = file_get_contents($filename);
				$filedata = preg_replace('/PLIKLI_Visual_Name = \"(.*)\"/iu',$replacement,$filedata);
				// print $filedata;
				
				// Write the changes to the language files
				$lang_file = fopen($filename, "w");
				fwrite($lang_file, $filedata);
				fclose($lang_file);
			}
		}
	}
	
	$approvedips = '../logs/approvedips.log';
	if (file_exists($approvedips)) {
		$user_ip = get_ip_address();
		if ($user_ip){
			$filedata = "$user_ip\n";
			
			// Write to the approvedips log file
			$ip_file = fopen($approvedips, "w");
			fwrite($ip_file, $filedata);
			fclose($ip_file);
			
		}
	}
	
	
	// Output success message
	$output = '<div class="jumbotron" style="padding:14px 25px;direction:'.$site_direction.'">
		<h2>' . $lang['InstallSuccess'] . '</h2>
		<p style="font-size:1.2em;">' . $lang['InstallSuccessMessage'] . '</p>
	</div></fieldset>';
	
	$output .='<p><strong></strong></p>
	<br /><fieldset style="direction:'.$site_direction.'"><legend>' . $lang['WhatToDo'] . '</legend>
	<div class="donext" style="direction:'.$site_direction.'"><ol>
		' . $lang['WhatToDoList'] . '
	</ol></div>';

	if ($_POST['sitetitle'] != ''){
		// Change the site title (PLIKLI_Visual_Name) in the language file
	}
    
    $url = 'https://plikli.com/upgrade/cms.php';
    $userips = $_SERVER['SERVER_ADDR'];
    $fields = array(
        'status'   => 'install',
        'mysqlserver'   => $_SESSION['mysqlserver'],
        'mysqlclient'   => CheckmysqlClientVersion(),
        'myphp'         => phpversion(),
        'mycms'         => 'Plikli',
        'myversion'     => $lang['plikli_version'],
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

if (isset($errors)) {
	$output=DisplayErrors($errors);
	$output.='<p>' . $lang['Errors'] . '</p>';
}

echo $output;

echo '</ul></div></fieldset><br />';

echo '</div>';

?>