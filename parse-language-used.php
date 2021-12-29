<?php
header('Content-Type: text/html; charset=utf-8');
include_once('internal/Smarty.class.php');
global $main_smarty;
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');


$unusedLanguage = file('C:/Users/George/Desktop/Misc-CMS/Kahuk-language-files/unused_lang_definitions.txt');
$lines = file('C:/Users/George/Desktop/Misc-CMS/Kahuk-language-files/language-constants-without-description.txt');
$clean = array_diff($lines, $unusedLanguage);

//To be used to replace the empty definitions found on LINE 27
$masterDefinitions = file('C:/Users/George/Desktop/Misc-CMS/Kahuk-language-files/latest/lang_english.conf');

$fp = fopen('C:/Users/George/Desktop/Misc-CMS/Kahuk-language-files/latest/lang_' . $language . '.conf', 'w');
$tempLine = '';
$langConstant = '';
foreach ($clean as $line) {
    $pos = strpos($line, 'KAHUK_');
    if ($pos !== false) {
        $line = trim($line);
        $langConstant = mysql_real_escape_string($main_smarty->get_config_vars($line));
        if ($langConstant == '' && $pos !== false) {
            foreach ($masterDefinitions as $master) {
                $mPos = strpos($master, $line);
                if ($mPos !== false) {
                    $langConstant = str_replace($line . ' = "', '', $master);
                    $langConstant = trim($langConstant);
                    $tempLine = $line . ' = "' . $langConstant;
                    goto B;
                }
            }
        } else {
            $tempLine = $line . ' = "' . $langConstant . '"';
        }


        B:
        fwrite($fp, $tempLine . "\n");
        echo $line . ' = "' . htmlspecialchars($langConstant) . '"<br />';
    } else {
        echo htmlspecialchars($line) . "<br />";
        fwrite($fp, $line);
    }
}
fclose($fp);
