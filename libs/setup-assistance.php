<?php
if (!defined("KAHUKPATH")) {
    die(".");
}

/**
 * Create site base path
 * 
 * @since 6.0.5
 * 
 * @return string "/" or "/folder/" or "/folder/folder2/"
 */
function kahuk_base_path() {
    // echo "<pre>" . $_SERVER["SCRIPT_NAME"] . "</pre>";

	// $lastSlash = strrpos($_SERVER["SCRIPT_NAME"], "/");
	$lastSlash = "/";
    // echo "<pre>lastSlash: " . $lastSlash . "</pre>";

	if (defined("KAHUK_INSTALLING") && KAHUK_INSTALLING) {
		// $lastSlash = strrpos($_SERVER["SCRIPT_NAME"], "/setup");
		$lastSlash = "/setup";
	}

    if (defined("KAHUK_UPGRADE") && KAHUK_UPGRADE) {
		// $lastSlash = strrpos($_SERVER["SCRIPT_NAME"], "/upgrades");
		$lastSlash = "/upgrades";
	}

	$lastSlashPos = strrpos($_SERVER["SCRIPT_NAME"], $lastSlash);

	$path = substr($_SERVER["SCRIPT_NAME"], 0, $lastSlashPos);
    // echo "<pre>path: " . $path . "</pre>";

    // echo "<pre>base path: " . $path . "/</pre>";
    // echo "<pre>KAHUKPATH: " . KAHUKPATH . "</pre>";

	return $path . "/";
}

/**
 * Create /.htaccess file
 * 
 * @since 6.0.5
 * 
 * @return array messages
 */
function kahuk_create_htaccess_file() {
	$messagesArray = [];

	// $path = defined("my_kahuk_base") ? my_kahuk_base : kahuk_base_path();
	// $site_base = $path . "/";

    $site_base = kahuk_base_path();

	$sample_content = file_get_contents(KAHUKPATH . "htaccess.sample");

	$new_content = str_replace("__my_kahuk_base__", $site_base, $sample_content);

	//
	$path_to_file = KAHUKPATH . ".htaccess";

	if ($handle = fopen($path_to_file, "w")) {
		if (fwrite($handle, $new_content)) {
			fclose($handle);

			$messagesArray = [
				"status" => "success",
				"message" => "<code>{$path_to_file}</code> file has been created!",
			];
		}

		chmod($path_to_file, 0644);
	} else {
		$messagesArray = [
			"status" => "warning",
			"message" => "Fail to create <code>{$path_to_file}</code>.",
		];
	}

	return $messagesArray;
}

