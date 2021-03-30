<?php

//old functions - no longer used, kept just incase for now...
function DoesExist ( $canContinue, $file, $mode, $desc )
{
	@chmod( $file, $mode );
	$good = file_exists( $file ) ? 1 : 0;
	Message ( $desc.' does exist.: ', $good );
	return ( $canContinue && $good );
}

function isWriteable ( $canContinue, $file, $mode, $desc )
{
	@chmod( $file, $mode );
	$good = is_writable( $file ) ? 1 : 0;
	Message ( $desc.' is writable: ', $good );
	return ( $canContinue && $good );
}

function Message( $message, $good )
{
	if ( $good ){
		$yesno = '<b><font color="green">Yes</font></b>';
	} else {
		$yesno = '<b><font color="red">No</font></b>';
	}
	echo '<tr><td class="normal">'. $message .'</td><td>'. $yesno .'</td></tr>';
}
//end old functions

function DisplayErrors($errors) {
	$output = '';
	foreach ($errors as $error) {
		$output.="<div class='alert alert-error'><strong>Error:</strong> $error</div>\n";
	}
	return $output;
}

function checkfortable($table)
{
	global $handle;
	$result = $handle->query("SHOW TABLES LIKE '".$table."';");
	$numRows = $result->num_rows;
	if ($numRows < 1) {
		return false;
	}else{
	return true;
	}
}

function check_lang_conf($version) {
	$file = '../languages/lang_english.conf';
	$data=file_get_contents($file);
	$lines=explode("\n",$data);
	foreach ($lines as $line) {
		if (preg_match("#\/\/<VERSION>$version<\/VERSION>#",$line)) { return TRUE; }
	}
}

function percent($num_amount, $num_total) {
	$count1 = $num_amount / $num_total;
	$count2 = $count1 * 100;
	$count = number_format($count2, 0);
	return $count;
}

function getfiles($dirname=".") {
	$pattern="\.default$";
	$files = array();
	if($handle = opendir($dirname)) {
	   while(false !== ($file = readdir($handle))){
			if(preg_match('/'.$pattern.'/i', $file)){
				echo "<li><input type=\"checkbox\" name=\"language[]\" id=\"color\" value=\"$file\">$file</li>";
			}
	   }
		closedir($handle);
	}
	return($files);
}

function CheckmysqlServerVersion() {
	global $handle;
	$theMySqlVersion = $handle->server_info;
	return $theMySqlVersion;
}

function CheckmysqlClientVersion() {
	global $handle;
	$theMySqlClientVersion = mysqli_get_client_info();
    $pattern = '/[^0-9-.]/i';
    $replacement = '';
    $theMySqlClientVersion = preg_replace($pattern, $replacement, $theMySqlClientVersion); 
    if (strpos($theMySqlClientVersion, '-') > 0){ 
        $theMySqlClientVersion = strstr($theMySqlClientVersion, '-', true);
    }else{
        $theMySqlClientVersion = $theMySqlClientVersion;
    }
	return $theMySqlClientVersion;
}

function CheckIfFileExists ($file, $default) {
	if (!file_exists($file)) {
		if (file_exists($default)) {
			rename("$default", "$file");
			chmod("$file", 0666);
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}
	// Add user IP address to approved IP list, so they are never blocked for bad logins
	function get_ip_address() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						return $ip;
					}
				}
			}
		}
	}
?>