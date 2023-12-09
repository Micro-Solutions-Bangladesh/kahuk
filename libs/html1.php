<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Get config from language
 * 
 * @since 5.0.6
 * 
 * @return string
 */
function kahuk_language_config($name) {
	global $main_smarty;

	return $main_smarty->get_config_vars($name);
}

/**
 * find category id when given category name
 * $the_cats is set in /libs/smartyvariables.php
 * 
 */
function get_category_id($cat_name)
{
	global $the_cats;

	foreach ($the_cats as $cat) {
		if ($cat->category_name == $cat_name) {
			return $cat->category_id;
		}
	}
	return null;
}

/**
 * find category name when given category id
 * $the_cats is set in /libs/smartyvariables.php
 */
function get_category_name($cat_id)
{
	global $the_cats;

	foreach ($the_cats as $cat) {
		if ($cat->category_id == $cat_id) {
			return $cat->category_name;
		}
	}
	return null;
}

/**
 * 
 */
function category_display()
{
	global $db;
	$maincategory = $db->get_results("select * from " . table_categories . "");

	$maincategory = object_2_array($maincategory);

	foreach ($maincategory as $id => $rs) {
		$maincategory[$id]['category_safe_name'] = $rs['category_safe_name'];
		$maincategory[$id]['category_id'] = $rs['category_id'];
		$maincategory[$id]['parent'] = $rs['category_parent'];
		$maincategory[$id]['order'] = $rs['category_order'];

		$childcategory = $db->get_results("select * from " . table_categories . " where category_parent =" . $rs['category_id']);
		//echo "select * from ".table_categories." where category_parent =".$rs['category_id'];
		$childcategory = object_2_array($childcategory);
		foreach ($childcategory as $id => $rc) {
			$childcategory[$id]['category_safe_name'] = $rc['category_safe_name'];
			$childcategory[$id]['category_id'] = $rc['category_id'];
			$childcategory[$id]['parent'] = $rc['category_parent'];
		}
	}
	return $maincategory;
}

function cat_safe_name($cat_id)
{
	global $the_cats;

	foreach ($the_cats as $cat) {
		if ($cat->category_id == $cat_id) {
			return $cat->category_safe_name;
		}
	}
}


function sanitize($var, $santype = 1, $allowable_tags = '')
{
	if ($santype == 0) {
		return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
	} elseif ($santype == 1) {
		return strip_tags($var, $allowable_tags = '');
	} elseif ($santype == 2) {
		return htmlentities(strip_tags($var, $allowable_tags), ENT_QUOTES, 'UTF-8');
	} elseif ($santype == 3) {
		return addslashes(strip_tags($var, $allowable_tags));
	} elseif ($santype == 4) {
		/*** Redwine: As of PHP 5.5.0, the preg_replace() emitts an error E_DEPRECATED level when passing in the "\e" modifier
			As of PHP 7.0.0 E_WARNING is emited in this case and "\e" modifier has no effect.
		 ***/
		//return stripslashes(preg_replace('/<([^>]+)>/es', "'<'.sanitize('\\1',5).'>'",strip_tags($var, $allowable_tags)));
		// Substituting preg_replace with preg_replace_callback 
		return stripslashes(preg_replace_callback(
			'/<([^>]+)>/is',
			function ($m) {
				return '<' . sanitize($m[1], 5) . '>';
			},
			strip_tags($var, $allowable_tags)
		));
	} elseif ($santype == 5) {
		/*** Redwine: As of PHP 5.5.0, the preg_replace() emitts an error E_DEPRECATED level when passing in the "\e" modifier
			As of PHP 7.0.0 E_WARNING is emited in this case and "\e" modifier has no effect.
		 ***/
		//return preg_replace('/\son\w+\s*=/is','',$var);
		// Substituting preg_replace with preg_replace_callback 
		return preg_replace_callback(
			'/\son\w+\s*=/is',
			function ($m) {
				return '';
			},
			$var
		);
	}
}


/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $imgsize Size in large, small, defaults to 256 [ 1 - 2048 ]
 *
 * @source https://gravatar.com/site/implement/images/php/
 * 
 * @since 5.0.5
 */
function kahuk_gravatar($id_or_email, $customArgs = [])
{

	$defaults = array(
		'rating'        => 'g',
		'class'         => null,
		'img'			=> false,
		'alt'			=> '',
		'imgsize'		=> '',
		'imgSizes'		=> [
			'large' => Avatar_Large,
			'medium' => 80,
			'small' => Avatar_Small,
		],
		'default'		=> 'mp',
		'note' => '',
	);

	$args = array_merge($defaults, $customArgs);
	$output = [];
	$hasEmailError = false;

	$imgsize = $args['imgsize'];
	$default = $args['default'];

	$email = is_email(sanitize_text_field($id_or_email));

	if (is_array($email)) {
		$email = kahuk_user_email_by_id($id_or_email); // Get user email by user id

		if (!$email) {
			$hasEmailError = true;
		}
	}

	foreach ($args['imgSizes'] as $sizeName => $sizeNumber) {
		$imgUrl = 'https://www.gravatar.com/avatar/';
		$imgUrl .= md5(strtolower(trim($email)));
		$imgUrl .= "?s=" . $sizeNumber . "&d=$default&r=" . $args['rating'];

		if ($hasEmailError) {
			$imgUrl = "Error: {$email} :: " . $args['note']; // TODO Error handle
		}

		if ($args['img']) {
			$imgMarkup = '<img src="' . $imgUrl . '" alt="' . $args['alt'] . '"';

			if ($args['class']) {
				$imgMarkup .= ' class="' . $args['class'] . '"';
			}

			$imgMarkup .= ' />';

			$output[$sizeName] = $imgMarkup;
		} else {
			$output[$sizeName] = $imgUrl;
		}
	}

	if (isset($output[$imgsize])) {
		return $output[$imgsize];
	}

	return $output;
}


/** Update client cache when image has changed:
 * Generate the image URL based on the date and time that the file on the
 * server has changed, so that the client will request the updated version of
 * the file from the server, for the new URL, instead of relying on the
 * out-dated client-side cache. */
function latest_avatar($client_url, $server_path)
{
	clearstatcache();
	return $client_url . '?cache_timestamp=' . filemtime($server_path);
}

function do_sidebar($var_smarty, $navwhere = '')
{
	// show the categories in the sidebar
	global $db, $globals, $the_cats;

	if ($navwhere == '') {
		global $navwhere;
	}

	// fix for 'undefined index' errors
	if (!isset($navwhere['text4'])) {
		$navwhere['text4'] = '';
	} else {
		$navwhere['text4'] = htmlspecialchars($navwhere['text4']);
	}
	if (!isset($navwhere['text3'])) {
		$navwhere['text3'] = '';
	} else {
		$navwhere['text3'] = htmlspecialchars($navwhere['text3']);
	}
	if (!isset($navwhere['text2'])) {
		$navwhere['text2'] = '';
	} else {
		$navwhere['text2'] = htmlspecialchars($navwhere['text2']);
	}
	if (!isset($navwhere['text1'])) {
		$navwhere['text1'] = '';
	} else {
		$navwhere['text1'] = htmlspecialchars($navwhere['text1']);
	}
	if (!isset($navwhere['link4'])) {
		$navwhere['link4'] = '';
	}
	if (!isset($navwhere['link3'])) {
		$navwhere['link3'] = '';
	}
	if (!isset($navwhere['link2'])) {
		$navwhere['link2'] = '';
	}
	if (!isset($navwhere['link1'])) {
		$navwhere['link1'] = '';
	}
	$var_smarty->assign('navbar_where', $navwhere);

	$var_smarty->assign('body_args', '');
	// fix for 'undefined index' errors

	$_caching = $var_smarty->cache; 	// get the current cache settings
	$var_smarty->cache = true; 			// cache has to be on otherwise is_cached will always be false
	$var_smarty->cache_lifetime = -1;   // lifetime has to be set to something otherwise is_cached will always be false
	$thetpl = $var_smarty->get_template_vars('the_template') . '/categories.tpl';

	// check to see if the category sidebar module is already cached
	// if it is, use it

	if (isset($_GET['category'])) {
		$thecat = sanitize($_GET['category'], 3);
	} else {
		$thecat = '';
	}
	if (isset($var_smarty) && is_object($var_smarty) && $var_smarty->is_cached($thetpl, 'sidebar|category|' . $thecat)) {
		$var_smarty->assign('cat_array', 'x'); // this is needed. sidebar.tpl won't include the category module if cat_array doesnt have some data
	} else {
		if (isset($_GET['category'])) {
			$thecat = get_cached_category_data('category_safe_name', urlencode(sanitize($_GET['category'], 1)));
			$catID  = $thecat->category_id;
			$thecat = $thecat->category_name;
		}

		if (!empty($catID)) {
			foreach ($the_cats as $cat) {
				if ($cat->category_id == $catID && $cat->category_parent == 0) {
					$globals['category_id'] = $cat->category_id;
					$globals['category_name'] = $cat->category_name;
				}
			}
		}

		$pos = strrpos($_SERVER["SCRIPT_NAME"], "/");
		$script_name = substr($_SERVER["SCRIPT_NAME"], $pos + 1, 100);
		$script_name = str_replace(".php", "", $script_name);


		// use the 'totals' table now 
		$published_count = get_story_count('published');

		$var_smarty->assign('published_count', $published_count);
		$var_smarty->assign('category_url', getmyurl('maincategory'));
	}

	$var_smarty->cache = $_caching; // set cache back to original value

	return $var_smarty;
}





/**
 * Depricated in favour of the kahuk_pagination_test()
 */
function do_pages($total, $page_size, $thepage, $fetch = false)
{
	// "previous" and "next" page buttons
	global $main_smarty;

	$index_limit = 10;

	$current = get_current_page();
	$total_pages = ceil($total / $page_size);
	$start = max($current - intval($index_limit / 2), 1);
	$end = $start + $index_limit - 1;

	$output = '';

	if ($total_pages != '1') { // If there is only 1 page, don't display any pagination at all
		$query = preg_replace('(login=)', '/', str_replace('amp;', '&', sanitize($_SERVER['QUERY_STRING'], 3)));	//remove login= from query string //
		$query = preg_replace('(view=)', '/', $query);	                    //remove view= from query string //
		$query = preg_replace('(part=)', '', $query);
		$query = preg_replace('(order)', '', $query);
		$query = preg_replace('/page[=\/][0-9]+/', '', $query);  			//remove page arguments to because its hardcoded in html   //
		$query = preg_replace('/title=([^&]*)/', '/$1', $query); 	 		//main line to recompose arg to place in url //	
		$query = preg_replace('/([^&]+)=([^&]*)/', '/$1/$2/', $query); 	 		//main line to recompose arg to place in url //	
		$query = preg_replace('/&/', '', $query);							//whack any ampersands	//	

		$query = preg_replace('/search\/(.*)/', '$1' . '/', $query);
		/* Redwine: added quotes to group_story because it was assumed as a constant and generating Notice:  Use of undefined constant group_story. */
		if ($thepage != "group_story")
			$query = preg_replace('/(?<!s)category\/(.*)/', '$1' . '/', $query);
		$query = preg_replace('/\/+/', '/', $query);
		$query = preg_replace('/^\//', '', $query);
		$query = preg_replace('/\/$/', '', $query);

		$output .= '<div class="pagination-wrapper mt-6"><ul class="pagination">';

		if ($current == 1) {
			// There are no previous pages, so don't show the "previous" link.
			//$output .= '<li class="disabled"><span>&#171; '.$main_smarty->get_config_vars("KAHUK_Visual_Page_Previous"). '</span></li>';
		} else {
			$i = $current - 1;
			if (pagename == "admin_users") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">&#171; ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Previous") . '</a></li>';
			} elseif (pagename == "admin_links") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">&#171; ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Previous") . '</a></li>';
			} elseif (pagename == "index" || pagename == "published") {
				$output .= '<li><a href="' . my_kahuk_base . '/' . $query . ($i > 1 ? '/page/' . $i : '') . '">&#171; ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Previous") . '</a></li>';
			} else {
				$output .= '<li><a href="' . my_kahuk_base . '/' . pagename . '/' . $query . '/page/' . $i . '">&#171; ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Previous") . '</a></li>';
			}
		}

		if ($start > 1) {
			$i = 1;
			if (pagename == "admin_users") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">' . $i . '</a></li>';
			} elseif (pagename == "admin_links") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">' . $i . '</a></li>';
			} elseif (pagename == "index" || pagename == "published") {
				$output .= '<li><a href="' . my_kahuk_base . '/' . $query . '">' . $i . '</a></li>';
			} else {
				$output .= '<li><a href="' . my_kahuk_base . '/' . pagename . '/' . $query . '/page/' . $i . '">' . $i . '</a></li>';
			}
			$output .= '<li class="dots disabled"><a href="#" aria-disabled="true">...</a></li>';
		}
		for ($i = $start; $i <= $end && $i <= $total_pages; $i++) {
			if ($i == $current) {
				$output .= '<li class="active"><a href="#">' . $i . '</a></li>';
			} else {
				if (pagename == "admin_users") {
					$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">' . $i . '</a></li>';
				} elseif (pagename == "admin_links") {
					$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">' . $i . '</a></li>';
				} elseif (pagename == "index" || pagename == "published") {
					$output .= '<li><a href="' . my_kahuk_base . '/' . $query . ($i > 1 ? '/page/' . $i : '') . '">' . $i . '</a></li>';
				} else {
					$output .= '<li><a href="' . my_kahuk_base . '/' . pagename . '/' . $query . '/page/' . $i . '">' . $i . '</a></li>';
				}
			}
		}

		if ($total_pages > $end) {
			$i = $total_pages;
			$output .= '<li class="dots disabled"><a href="#" aria-disabled="true">...</a></li>';
			if ($pagename == "admin_users") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">' . $i . '</a></li>';
			} elseif (pagename == "admin_links") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '">' . $i . '</a></li>';
			} elseif (pagename == "index" || pagename == "published") {
				$output .= '<li><a href="' . my_kahuk_base . '/' . $query . '/page/' . $i . '">' . $i . '</a></li>';
			} else {
				$output .= '<li><a href="' . my_kahuk_base . '/' . pagename . '/' . $query . '/page/' . $i . '">' . $i . '</a></li>';
			}
		}

		if ($current < $total_pages) {
			$i = $current + 1;
			if (pagename == "admin_users") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '"> ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Next") . ' &#187;' . '</a></li>';
			} elseif (pagename == "admin_links") {
				$output .= '<li><a href="' . my_kahuk_base . '/admin/' . pagename . '.php?page=' . $i . '"> ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Next") . ' &#187;' . '</a></li>';
			} else {
				$output .= '<li><a href="' . my_kahuk_base . '/' . pagename . '/' . $query . '/page/' . $i . '"> ' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Next") . ' &#187;' . '</a></li>';
			}
		} else {
			$output .= '<li class="disabled"><a href="#" aria-disabled="true">' . $main_smarty->get_config_vars("KAHUK_Visual_Page_Next") . ' &#187;' . '</a></li>';
		}

		$output .= "</ul></div>";
		$output = str_replace("/group_story/", "/groups/", $output);
		$output = str_replace("//", "/", $output);
	}

	if ($fetch == false) {
		echo $output;
	} else {
		return $output;
	}
}

function generateHash($plainText, $salt = null)
{
	if ($salt === null) {
		$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
	} else {
		$salt = substr($salt, 0, SALT_LENGTH);
	}

	return $salt . sha1($salt . $plainText);
}

function generatePassHash($plainText)
{
	if (!function_exists('password_hash')) {
		require "password.php";
		$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);

		return 'bcrypt:' . $salt . password_hash(sha1($salt . $plainText), PASSWORD_BCRYPT);
	} else {
		$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);

		return 'bcrypt:' . $salt . password_hash(sha1($salt . $plainText), PASSWORD_BCRYPT);
	}
}

function verifyPassHash($plainText, $hashedPass)
{
	$hashTrim = substr($hashedPass, (SALT_LENGTH + 7));
	$salt = substr($hashedPass, 7, SALT_LENGTH);

	if (!function_exists('password_verify')) {
		require "password.php";

		if (password_verify(sha1($salt . $plainText), $hashTrim)) {
			return true;
		} else {
			return false;
		}
	} else {
		if (password_verify(sha1($salt . $plainText), $hashTrim)) {
			return true;
		} else {
			return false;
		}
	}
}

function urlsafe_base64_encode($data)
{
	return strtr(base64_encode($data), ['+' => '-', '/' => '_', '=' => '']);
}

function urlsafe_base64_decode($data, $strict = false)
{
	return base64_decode(strtr($data, '-_', '+/'), $strict);
}

function create_token()
{
	//mcrypt_create_iv (PHP 4, PHP 5, PHP 7 < 7.2.0, PECL mcrypt >= 1.0.0)
	//bin2hex (PHP 4, PHP 5, PHP 7)
	//openssl_random_pseudo_bytes (PHP 5 >= 5.3.0, PHP 7)
	/*if (function_exists('mcrypt_create_iv')) {
		$created_token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
	} else {
		$created_token = bin2hex(openssl_random_pseudo_bytes(32));
	}
	return $created_token;*/
	if (!function_exists('random_bytes')) {
		require_once "random_compat/lib/random.php";
	}
	try {
		$string = random_bytes(64);
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

	//$string1 = random_bytes(64);
	$authenticator = bin2hex($string);
	//$saltedlogin = hash('sha256', $authenticator);
	return $authenticator;
}

function check_valid($token, $name)
{
	defined('USE_MB_STRING') or define('USE_MB_STRING', function_exists('mb_strlen'));
	/**
	 * hash_equals — Timing attack safe string comparison
	 *
	 * Arguments are null by default, so an appropriate warning can be triggered
	 *
	 * @param string $known_string
	 * @param string $user_string
	 *
	 * @link http://php.net/manual/en/function.hash-equals.php
	 *
	 * @return boolean
	 */
	$score_valid = 0;
	if (!function_exists('hash_equals')) {
		defined('USE_MB_STRING') or define('USE_MB_STRING', function_exists('mb_strlen'));
		/**
		 * hash_equals — Timing attack safe string comparison
		 *
		 * Arguments are null by default, so an appropriate warning can be triggered
		 *
		 * @param string $known_string
		 * @param string $user_string
		 *
		 * @link http://php.net/manual/en/function.hash-equals.php
		 *
		 * @return boolean
		 */
		function hash_equals($token = null, $name = null)
		{
			$argc = func_num_args();
			// Check the number of arguments
			if ($argc < 2) {
				//trigger_error(sprintf('hash_equals() expects exactly 2 parameters, %d given', $argc), E_USER_WARNING);
				return null;
			}

			// Check $known_string type
			if (!is_string($token)) {
				//trigger_error(sprintf('hash_equals(): Expected known_string to be a string, %s given', strtolower(gettype($token))), E_USER_WARNING);
				return false;
			}

			// Check $user_string type
			if (!is_string($name)) {
				//trigger_error(sprintf('hash_equals(): Expected user_string to be a string, %s given', strtolower(gettype($name))), E_USER_WARNING);
				return false;
			}

			// Ensures raw binary string length returned
			$strlen = function ($string) {
				if (USE_MB_STRING) {
					return mb_strlen($string, '8bit');
				}
				return strlen($string);
			};

			// Compare string lengths
			if (($length = $strlen($token)) !== $strlen($name)) {
				return false;
			}
			$diff = 0;
			// Calculate differences
			for ($i = 0; $i < $length; $i++) {
				$diff |= ord($token[$i]) ^ ord($name[$i]);
			}
			return $diff === 0;
		}
	}
	/***************************************************************
	Redwine: calling hash_equals function, the built-in or the user's created one expects a return of 1 (equal = true) or 0 (equal = false).
	 ***************************************************************/
	$score_valid = hash_equals($token, $name);

	return $score_valid;
}

function truncate_strings_html($string, $limit, $break = " ", $pad = "")
{
	// return with no change if string is shorter than $limit
	if (mb_strlen($string, 'UTF-8') <= $limit) {
		return $string;
	}

	// is $break present between $limit and the end of the string?
	if (false !== ($breakpoint = mb_strpos($string, $break, $limit, "UTF-8"))) {
		if ($breakpoint < mb_strlen($string, 'UTF-8') - 1) {
			// $string = substr($string, 0, $breakpoint) . $pad;
			$string = mb_substr($string, 0, $breakpoint, "UTF-8") . $pad;
		}
	}

	#put all opened tags into an array	
	preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $string, $result);
	$openedtags = $result[1];
	#put all closed tags into an array
	preg_match_all("#</([a-z]+)>#iU", $string, $result);

	$closedtags = $result[1];
	$len_opened = count($openedtags);

	# all tags are closed
	if (count($closedtags) == $len_opened) {
		return $string;
	}

	$openedtags = array_reverse($openedtags);
	# close tags
	for ($i = 0; $i < $len_opened; $i++) {
		if (!in_array($openedtags[$i], $closedtags)) {
			$string .= "</" . $openedtags[$i] . ">";
		} else {
			unset($closedtags[array_search($openedtags[$i], $closedtags)]);
		}
	}
	return $string;
}

function getmyFullurl($x, $var1 = "", $var2 = "", $var3 = "")
{
	return my_base_url . getmyurl($x, $var1, $var2, $var3);
}

function getmyurl($x, $var1 = "", $var2 = "", $var3 = "")
{
	$var1 = sanitize($var1, 1);
	$var2 = sanitize($var2, 1);
	$var3 = sanitize($var3, 1);

	$ret = '';

	if ($x == "storyURL") {
		// var 1 = category_safe_name
		// var 2 = title_url
		// var 3 = story id
		return getmyurl("storycattitle", $var1, $var2);

		// if (enable_friendly_urls == true) {
		// 	return getmyurl("storycattitle", $var1, $var2);
		// } else {
		// 	return getmyurl("story", $var3);
		// }
	}

	if ($x == "maincategory") $ret = "/" . $var1;
	elseif ($x == "newcategory") $ret = "/new/" . $var1;
	elseif ($x == "discardedcategory") $ret = "/discarded/" . $var1 . "/";

	elseif ($x == "editlink") $ret = "/story/" . $var1 . "/edit/";
	elseif ($x == "edit") $ret = "/story/" . $var1 . "/editcomment/" . $var2 . "/";
	elseif ($x == "user") $ret = "/user/" . $var1 . ($var1 ? '/' : '');
	elseif ($x == "user_friends") $ret = "/user/" . $var1 . "/" . $var2 . "/";
	elseif ($x == "user_add_remove") $ret = "/user/" . $var2 . "/" . $var1 . "/";
	elseif ($x == "user_add_links") $ret = "/user/add/link/" . $var1 . "/";
	elseif ($x == "user_remove_links") $ret = "/user/remove/link/" . $var1 . "/";
	elseif ($x == "user_inbox") $ret = "/inbox/";
	elseif ($x == "userblank") $ret = "/user/";
	elseif ($x == "user2") $ret = "/user/" . $var1 . "/" . $var2 . "/";
	elseif ($x == "index") $ret = "/";
	elseif ($x == "index_sort") $ret = "/" . $var1 . ($var2 ? '/' . $var2 : '') . "/";
	elseif ($x == "new_sort") $ret = "/new/" . $var1 . ($var2 ? '/' . $var2 : '') . "/";
	elseif ($x == "search") $ret = "/search" . ($var1 ? '/' . $var1 : '') . "/";
	// elseif ($x == "advancedsearch") $ret = "/advanced-search/";
	elseif ($x == "search_url") $ret = "/search/" . urlencode(str_replace('/', '|', $var1)) . "/";
	elseif ($x == "admin_login") $ret = "/admin/admin_login.php?return=" . urlencode($var1);
	elseif ($x == "login") $ret = "/login.php?return=" . urlencode($var1);
	elseif ($x == "logout") $ret = "/login.php?op=logout&return=" . my_kahuk_base;
	elseif ($x == "register") $ret = "/register/";
	elseif ($x == "submit") $ret = "/submit/";
	elseif ($x == "story") $ret = "/story/" . $var1 . "/";
	elseif ($x == "storytitle") $ret = "/story/" . $var1 . "/";
	elseif ($x == "storycattitle") $ret = "/" . $var1 . "/" . $var2 . "/";

	elseif ($x == "out") $ret = "/out/" . $var1 . "/";
	elseif ($x == "outtitle") $ret = "/out/" . $var1 . "/";
	elseif ($x == "outurl") $ret = "/out/" . $var1 . "/";
	elseif ($x == "root") $ret = "/";
	elseif ($x == "new") $ret = "/new/";
	elseif ($x == "topusers") $ret = "/topusers/";
	elseif ($x == "user_edit") $ret = "/user/$var1/edit/";
	elseif ($x == "userNoVar") $ret = "/user/";
	elseif ($x == "loginNoVar") $ret = "/login/";
	elseif ($x == "rssTime") $ret = "/rss.php?time=" . $var1;
	elseif ($x == "about") $ret = "/about/" . $var1 . "/";
	elseif ($x == "rss") $ret = "/rss/";
	elseif ($x == "rssuser") $ret = "/user/$var1/rss/";
	elseif ($x == "rssnew") $ret = "/new/rss/";
	elseif ($x == "rssall") $ret = "/rss/" . $var1 . "/";
	elseif ($x == "rsscategory") $ret = "/rss/category/" . $var1;
	elseif ($x == "rsscategorynew") $ret = "/rss/category/new/" . $var1;
	elseif ($x == "rsssearch") $ret = "/search/" . $var1 . "/rss/";
	elseif ($x == "rsspage") $ret = ($var2 ? "/$var2" : '') . ($var1 ? "/$var1" : '') . ($var3 ? "/group/$var3" : '') . "/rss/";
	elseif ($x == "rssgroup") $ret = "/group/$var1" . ($var2 ? "/$var2" : '') . "/rss/";

	elseif ($x == "storyrss") $ret = "/$var2/$var1/rss/";
	elseif ($x == "page") $ret = "/static/" . $var1 . "/";
	elseif ($x == "editcomment") $ret = "/story/" . $var2 . "/editcomment/" . $var1 . "/";

	elseif ($x == "admin") $ret = "/admin/";
	elseif ($x == "admin_modify") $ret = "/story/" . $var1 . "/modify/main/";
	elseif ($x == "admin_modify_do") $ret = "/story/" . $var1 . "/modify/do" . $var2 . "/";
	elseif ($x == "admin_modify_edo") $ret = "/story/" . $var1 . "/modify/edo" . $var2 . "/";
	elseif ($x == "admin_discard") $ret = "/story/" . $var1 . "/modify/discard/";
	elseif ($x == "admin_new") $ret = "/story/" . $var1 . "/modify/new/";
	elseif ($x == "admin_published") $ret = "/story/" . $var1 . "/modify/published/";

	elseif ($x == "groups") $ret = "/groups/";
	elseif ($x == "submit_groups") $ret = "/groups/submit/";
	elseif ($x == "group_story") $ret = "/groups/id/" . $var1 . "/";
	elseif ($x == "group_story_title") $ret = "/groups/" . $var1 . "/";
	elseif ($x == "group_story2") $ret = "/groups/" . $var1 . "/" . $var2 . ($var3 ? "/$var3/" : '');
	elseif ($x == "join_group") $ret = "/groups/join/" . $var1 . "/privacy/" . $var2 . "/";
	elseif ($x == "unjoin_group") $ret = "/groups/unjoin/" . $var1 . "/privacy/" . $var2 . "/";
	elseif ($x == "join_group_withdraw") $ret = "/groups/withdraw/" . $var1 . "/user_id/" . $var2 . "/";
	elseif ($x == "group_admin") $ret = "/groups/member/admin/id/" . $var1 . "/role/admin/userid/" . $var3 . "/";
	elseif ($x == "group_normal") $ret = "/groups/member/normal/id/" . $var1 . "/role/normal/userid/" . $var3 . "/";
	elseif ($x == "group_moderator") $ret = "/groups/member/moderator/" . $var1 . "/role/moderator/userid/" . $var3 . "/";
	elseif ($x == "group_flagged") $ret = "/groups/member/flagged/" . $var1 . "/role/flagged/userid/" . $var3 . "/";
	elseif ($x == "group_banned") $ret = "/groups/member/banned/id/" . $var1 . "/role/banned/userid/" . $var3 . "/";
	elseif ($x == "group_avatar") $ret = "/group_avatar/" . $var1 . "/";
	elseif ($x == "group_sort") $ret = "/groups/" . $var1 . ($var2 ? "/$var2" : '') . "/";
	elseif ($x == "editgroup") $ret = "/groups/edit/" . $var1 . "/";
	elseif ($x == "deletegroup") $ret = "/groups/delete/" . $var1 . "/";
	elseif ($x == "group_story_links_publish") $ret = "/join_group/action/published/link/" . $var1 . "/";
	elseif ($x == "group_story_links_new") $ret = "/join_group/action/new/link/" . $var1 . "/";
	elseif ($x == "group_story_links_discard") $ret = "/join_group/action/discard/link/" . $var1 . "/";
	elseif ($x == "admin_categories_tasks") $ret = "/admin_categories_tasks/action/" . $var1 . "/";

	return my_kahuk_base . preg_replace('/\/+/', '/', $ret);
}



/**
 * 
 */
function SetSmartyURLs($main_smarty) {
	$main_smarty->assign('URL_search', getmyurl("search"));
	$main_smarty->assign('URL_advancedsearch', getmyurl("advancedsearch"));
	$main_smarty->assign('URL_maincategory', getmyurl("maincategory"));
	$main_smarty->assign('URL_newcategory', getmyurl("newcategory"));
	$main_smarty->assign('URL_category', getmyurl("category"));

	$main_smarty->assign('URL_user', getmyurl("user"));
	// $main_smarty->assign('URL_userNoVar', getmyurl("userNoVar"));
	$main_smarty->assign('URL_user_inbox', getmyurl("user_inbox", "inbox"));
	$main_smarty->assign('URL_user_add_remove', getmyurl("user_add_remove"));
	// $main_smarty->assign('URL_profile', getmyurl("user_edit"));
	$main_smarty->assign('URL_story', getmyurl("story"));
	$main_smarty->assign('URL_storytitle', getmyurl("storytitle"));

	if (isset($_GET['category']) && sanitize($_GET['category'], 1) != '' && strpos($_SERVER['PHP_SELF'], "index.php") === false && strpos($_SERVER['PHP_SELF'], "story.php") === false) {
		$main_smarty->assign('URL_base', getmyurl("maincategory", sanitize(sanitize($_GET['category'], 1), 2)));
	} else {
		$main_smarty->assign('URL_base', getmyurl("index"));
	}

	$main_smarty->assign('URL_rss', getmyurl("rss"));
	$main_smarty->assign('URL_rsscategory', getmyurl("rsscategory"));
	$main_smarty->assign('URL_rsscategorynew', getmyurl("rsscategorynew"));
	$main_smarty->assign('URL_rssnew', getmyurl("rssnew", "new"));
	$main_smarty->assign('URL_rssall', getmyurl("rssall", "all"));
	$main_smarty->assign('URL_rsssearch', getmyurl("rsssearch"));

	$main_smarty->assign('URL_admin_users', getmyurl("admin_users"));
	$main_smarty->assign('URL_admin_language', getmyurl("admin_language"));
	$main_smarty->assign('URL_admin_categories', getmyurl("admin_categories"));
	$main_smarty->assign('URL_admin_backup', getmyurl("admin_backup"));

	$main_smarty->assign('URL_admin_config', getmyurl("admin_config"));
	$main_smarty->assign('URL_admin_rss', getmyurl("admin_rss"));

	$main_smarty->assign('URL_template', getmyurl("template"));


	$main_smarty->assign('URL_join_group', getmyurl("join_group"));
	$main_smarty->assign('unjoin_group', getmyurl("unjoin_group"));
	return $main_smarty;
}

function friend_MD5($userA, $userB)
{
	$user = new User();
	$user->username = $userA;
	if (!$user->read()) {
		echo "a-" . $userA . "error 2";
		die;
	}
	$userAdata = $user->username . $user->date;

	$user = new User();
	$user->username = $userB;
	if (!$user->read()) {
		echo "b-" . $userB . "error 2";
		die;
	}
	$userBdata = $user->username . $user->date;

	$themd5 = md5($userAdata . $userBdata);
	return $themd5;
}

function totals_regenerate()
{
	global $db, $cached_totals;

	$name = 'new';
	$count = $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_status='$name';");
	$db->query("UPDATE `" . table_totals . "` set `total` = $count where `name` = '$name';");
	$cached_totals[$name] = $count;

	$name = 'published';
	$count = $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_status='$name';");
	$db->query("UPDATE `" . table_totals . "` set `total` = $count where `name` = '$name';");
	$cached_totals[$name] = $count;

	if (caching == 1) {
		// this is to clear the cache and reload it for settings-from-db.php
		$db->cache_dir = KAHUKPATH . 'cache';
		$db->use_disk_cache = true;
		$db->cache_queries = true;
		$db->cache_timeout = 0;
		$totals = $db->get_results("SELECT * FROM `" . table_totals . "`");
		$db->cache_queries = false;
	}
	return true;
}

function totals_adjust_count($name, $adjust)
{
	global $db, $cached_totals;

	$name = $db->escape($name);
	$db->query('UPDATE ' . table_totals . ' SET total=total+' . $adjust . ' WHERE name="' . $name . '"');
	$cached_totals[$name] = $db->get_var('SELECT total FROM ' . table_totals . ' WHERE name="' . $name . '"');

	if (caching == 1) {
		// this is to clear the cache and reload it for settings-from-db.php
		$db->cache_dir = KAHUKPATH . 'cache';
		$db->use_disk_cache = true;
		$db->cache_queries = true;
		$db->cache_timeout = 0;
		$totals = $db->get_results("SELECT * FROM `" . table_totals . "`");
		$db->cache_queries = false;
	}

	return true;
}

function get_story_count($name)
{
	global $db, $cached_totals;

	$name = $db->escape($name);
	if (summarize_mysql == 1) {
		if (isset($cached_totals[$name])) {
			return $cached_totals[$name];
		} else {
			if (caching == 1) {
				$db->cache_dir = KAHUKPATH . 'cache';
				$db->use_disk_cache = true;
				$db->cache_queries = true;
			}
			$totals = $db->get_results("SELECT * FROM `" . table_totals . "`");

			$db->cache_queries = false;

			foreach ($totals as $total) {
				$cached_totals[$total->name] = $total->total;
			}
			return $cached_totals[$name];
		}
	} else {
		return $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_status='$name';");
	}
}

function close_tags($html)
{
	$single_tags = array('meta', 'img', 'br', 'link', 'area');

	// Close HTML tags
	$html = preg_replace('/<[^>]*$/is', '', $html);

	if (preg_match_all('/<([a-z]+)(?: .*)?(?<![\/|\/ ])>/iU', $html, $m)) {
		$opened_tags = $m[1];
	} else {
		return $html;
	}
	if (preg_match_all('/<\/([a-z]+)>/iU', $html, $m)) {
		$closed_tags = $m[1];
	} else {
		$closed_tags = array();
	}
	if (count($closed_tags) == count($opened_tags)) {
		return $html;
	}
	for ($i = count($opened_tags) - 1; $i >= 0; $i--) {
		if (!in_array($opened_tags[$i], $single_tags)) {
			if (!in_array($opened_tags[$i], $closed_tags)) {
				$html .= '</' . $opened_tags[$i] . '>';
			}
		}
	}
	return $html;
}

//
// CSFR/XSFR protection
// updated
//
function check_referrer($post_url = false)
{
	global $xsfr_first_page, $_GET, $_POST;

	if (sizeof($_GET) > 0 || sizeof($_POST) > 0) {
		if (isset($_SERVER['HTTP_REFERER'])) {
			$_SERVER['HTTP_REFERER'] = sanitize($_SERVER['HTTP_REFERER'], 3);

			// update checks if HTTP_REFERER and posted url are the same!
			if ($post_url) {
				if (strpos($_SERVER['HTTP_REFERER'], $post_url) !== false) {
					return true;
				}
			}

			if (strpos(preg_replace('/^.+:\/\/(www\.)?/', '', $_SERVER['HTTP_REFERER']) . '/', preg_replace('/^.+:\/\/(www\.)?/', '', my_base_url)) !== 0) {
				unset($_SESSION['xsfr']);
				die("Wrong Referrer '{$_SERVER['HTTP_REFERER']}'");
			}
		} elseif ($xsfr_first_page) {
			unset($_SESSION['xsfr']);

			kahuk_set_session_message(
				"Security code expired!",
				'notice'
			);

			kahuk_redirect(kahuk_root_url());
			die('Wrong security code');
		}
	}
}

$english_language = array();

function translate($str)
{
	global $main_smarty, $english_language;

	if (KAHUK_LANG == 'english') {
		return $str;
	}
	if (sizeof($english_language) == 0) {
		$path = dirname(__FILE__);
		if (strrpos($path, '/')) {
			$path = substr($path, 0, strrpos($path, '/'));
		} elseif (strrpos($path, '\\')) {
			$path = substr($path, 0, strrpos($path, '\\'));
			if (!file_exists($path . '/languages/lang_english.conf')) {
				return $str;
			}
		}
		$strings = parse_ini_file($path .  '/languages/lang_english.conf');
		foreach ($strings as $key => $value) {
			$english_language[strtoupper(str_replace('&quot;', '"', $value))] = $main_smarty->get_config_vars($key);
		}
	}
	if ($translation = $english_language[strtoupper(str_replace("\r\n", "\\n", $str))]) {
		return $translation;
	} else {
		return $str;
	}
}

function detect_encoding($string)
{
	static $list = array('utf-8');
	foreach ($list as $item) {
		$sample = iconv($item, $item, $string);
		if (md5($sample) == md5($string)) {
			return $item;
		}
	}
	return null;
}

function js_urldecode($str)
{
	$str = rawurldecode($str);
	$utf8 = is_utf8($str);

	preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U", $str, $r);
	$ar = $r[0];
	foreach ($ar as $k => $v) {
		if (substr($v, 0, 2) == "%u") {
			$ar[$k] = c2UTF8(intval(substr($v, -4), 16));
		} elseif (substr($v, 0, 3) == "&#x") {
			$ar[$k] = c2UTF8(intval(substr($v, 3, -1), 16));
		} elseif (substr($v, 0, 2) == "&#") {
			$ar[$k] = c2UTF8(intval(substr($v, 2, -1), 16));
		} elseif ($utf8) {
			continue;
		} elseif (function_exists('mb_convert_encoding')) {
			$ar[$k] = mb_convert_encoding($v, 'UTF-8', 'ASCII');
		} elseif (function_exists('iconv')) {
			$ar[$k] = iconv(iconv_get_encoding('input_encoding'), 'UTF-8', $v);
		}
	}
	return join("", $ar);
}

function c2UTF8($i)
{
	//0x00000000 - 0x0000007F	00000000 00000000 00000000 0zzzzzzz	0zzzzzzz
	//0x00000080 - 0x000007FF	00000000 00000000 00000yyy yyzzzzzz	110yyyyy 10zzzzzz
	//0x00000800 - 0x0000FFFF	00000000 00000000 xxxxyyyy yyzzzzzz	1110xxxx 10yyyyyy 10zzzzzz
	if ($i < 128) {
		return chr($i);
	} elseif ($i < 2048) {
		return chr(floor($i / 64) + 192) . chr($i % 64 + 128);
	} else {
		return chr(floor($i / 64 / 64) + 224) . chr(floor($i / 64) % 64 + 128) . chr($i % 64 + 128);
	}
}

$approved_ips = $static_ips = '';
function ban_ip($ip, $ip2)
{
	global $static_ips;

	$filename = KAHUKPATH . '/logs/bannedips.log';
	if (is_writable($filename)) {
		if (!$handle = fopen($filename, 'a')) {
			return "Cannot open file ($filename)";
		}
		if (!is_ip_approved($ip)) {
			if (!is_ip_banned($ip) && fwrite($handle, "$ip\n") === FALSE)
				return "Cannot write to file ($filename)";
			else
				$static_ips[] = "$ip\n";
		}
		if ($ip2 && !is_ip_approved($ip2))
			if (!is_ip_banned($ip2) && fwrite($handle, "$ip2\n") === FALSE)
				return "Cannot write to file ($filename)";
			else
				$static_ips[] = "$ip2\n";
		fclose($handle);
	} else
		return "The file $filename is not writable";
	return '';
}

function is_ip_banned($ip)
{
	global $static_ips;
	$filename = KAHUKPATH . '/logs/bannedips.log';
	if (!is_array($static_ips))
		$static_ips = file($filename);
	return in_array("$ip\n", $static_ips);
}

function is_ip_approved($ip)
{
	global $approved_ips;
	$filename = KAHUKPATH . '/logs/approvedips.log';
	if (!is_array($approved_ips))
		$approved_ips = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	return in_array($ip, $approved_ips);
}

if (!function_exists('error')) {
	function error($mess)
	{
		header('Content-Type: text/plain; charset=UTF-8');
		echo "ERROR: $mess";
		die;
	}
}

define('_is_utf8_split', 5000);
function is_utf8($string)
{ // v1.01 
	if (strlen($string) > _is_utf8_split) {
		// Based on: http://mobile-website.mobi/php-utf8-vs-iso-8859-1-59 
		for ($i = 0, $s = _is_utf8_split, $j = ceil(strlen($string) / _is_utf8_split); $i < $j; $i++, $s += _is_utf8_split) {
			if (is_utf8(substr($string, $s, _is_utf8_split)))
				return true;
		}
		return false;
	} else {
		// From http://w3.org/International/questions/qa-forms-utf-8.html 
		return preg_match('%^(?: 
                [\x09\x0A\x0D\x20-\x7E]            # ASCII 
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte 
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs 
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte 
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates 
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3 
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15 
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16 
        )*$%xs', $string);
	}
}
// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------

// to use this function to totally remove a directory, write:
// recursive_remove_directory('path/to/directory/to/delete');

// to use this function to empty a directory, write:
// recursive_remove_directory('path/to/full_directory',TRUE);

function recursive_remove_directory($directory, $empty = TRUE)
{
	// if the path has a slash at the end we remove it here
	if (substr($directory, -1) == '../cache') {
		$directory = substr($directory, 0, -1);
	}

	// if the path is not valid or is not a directory ...
	if (!file_exists($directory) || !is_dir($directory)) {
		// ... we return false and exit the function
		return FALSE;
		// ... if the path is not readable
	} elseif (!is_readable($directory)) {
		// ... we return false and exit the function
		return FALSE;
		// ... else if the path is readable
	} else {
		// we open the directory
		$handle = opendir($directory);
		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle))) {
			//print $item."\n";

			// if the filepointer is not the current directory
			// or the parent directory
			if ($item != '.' && $item != '..' && $item != '.htaccess' && $item != 'index.html') {
				// we build the new path to delete
				$path = $directory . '/' . $item;

				// if the new path is a directory
				if (is_dir($path)) {
					// we call this function with the new path
					recursive_remove_directory($path);

					// if the new path is a file
				} else {
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if ($empty == FALSE) {
			// try to delete the now empty directory
			if (!rmdir($directory)) {
				// return false if not possible
				return FALSE;
			}
		}


		// return success
		return TRUE;
	}
}
// ------------------------------------------------------------



