<?php
header('Content-Type: text/html; charset=utf-8');
include_once('internal/Smarty.class.php');
global $main_smarty;
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'smartyvariables.php');


$lines = file('C:/Users/George/Desktop/Misc-CMS/Plikli-language-files/lang.txt');

// Loop through our array, show HTML source as HTML source; and line numbers too.
//foreach ($lines as $line_num => $line) {
//    echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";
//}
$fp = fopen('C:/Users/George/Desktop/Misc-CMS/Plikli-language-files/lang_english.conf', 'w');
$tempLine = '';
$langConstant = '';
foreach ($lines as $line_num => $line) {
	$pos = strpos($line, 'PLIKLI_');
	if ($pos !== false) {
		$line = trim($line);
		$langConstant = $main_smarty->get_config_vars($line);
		if ($langConstant == '') {
			$langConstant = 'EMPTY';
		}
		$tempLine = $line . ' = "' .$langConstant.'"';
		fwrite($fp, $tempLine ."\n");
		echo $line . ' = "' . htmlspecialchars($langConstant) . '"<br />';
	}else{
		echo htmlspecialchars($line)."<br />";
		fwrite($fp, $line);
	}
}
fclose($fp);