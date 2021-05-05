<?php

$GonxAdmin["dbhost"] = DB_HOST;
$GonxAdmin["dbname"] = DB_NAME;
$GonxAdmin["dbuser"] = DB_USER;
$GonxAdmin["dbpass"] = DB_PASSWORD;
$GonxAdmin["dbtype"] = "mysql";
$GonxAdmin["compression"] = array("bz2","zlib");
$GonxAdmin["compression_default"] = "zlib";
$GonxAdmin["login"] = "admin";
$GonxAdmin["pass"] = "pass";
$GonxAdmin["locale"] = "en";
$GonxAdmin["pagedisplay"] = 10;
//$GonxAdmin["mysqldump"] = "D:/mysql/bin/mysqldump.exe";


require_once("libs/db.class.php");
require_once("libs/gonxtabs.class.php");
require_once("libs/backup.class.php");
require_once("libs/locale.class.php");	// Localisation class


