<?php

if (!function_exists('random_bytes')) {
	require_once "/lib/random.php";
	echo "from users functions<br />";
}
	try {
    $string = random_bytes(32);
} catch (TypeError $e) {
    // Well, it's an integer, so this IS unexpected.
    die("An unexpected error has occurred"); 
} catch (Error $e) {
    // This is also unexpected because 32 is a reasonable integer.
    die("An unexpected error has occurred");
} catch (Exception $e) {
    // If you get this message, the CSPRNG failed hard.
    die("Could not generate a random string. Is our OS secure?");
}
var_dump(bin2hex($string));
$string1 = random_bytes(64);
echo "<br /><br />";
$authenticator = bin2hex($string1);
$saltedlogin = hash('sha256', $authenticator);
echo "<br /><br />authenticator => ";
var_dump($authenticator);
echo "<br /><br />saltedlogin => ";
var_dump($saltedlogin);