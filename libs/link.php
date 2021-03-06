<?php
if (!defined('KAHUKPATH')) {
	die();
}

class Link
{

	public $id = 0;
	public $author = -1;
	public $username = false;
	public $randkey = 0;
	public $karma = 1;
	public $valid = true;
	public $date = false;
	public $published_date = 0;
	public $scheduled_date = 0;
	public $modified = 0;
	public $url = '';
	public $url_title = '';
	public $url_description = '';
	public $encoding = false;
	public $status = 'discard';
	public $type = '';
	public $category = 0;
	public $additional_cats = array();
	public $votes = 0;
	public $comments = 0;
	public $reports = 0;
	public $title = '';
	public $title_url = '';
	public $tags = '';
	public $content = '';
	public $link_summary = '';
	public $html = true;
	public $trackback = false;
	public $read = true;
	public $fullread = true;
	public $voted = false;
	public $link_field1 = '';
	public $link_field2 = '';
	public $link_field3 = '';
	public $link_field4 = '';
	public $link_field5 = '';
	public $link_field6 = '';
	public $link_field7 = '';
	public $link_field8 = '';
	public $link_field9 = '';
	public $link_field10 = '';
	public $link_field11 = '';
	public $link_field12 = '';
	public $link_field13 = '';
	public $link_field14 = '';
	public $link_field15 = '';
	public $link_group_id = 0;
	public $current_user_votes = 0;
	public $current_user_reports = 0;
	public $debug = false;
	public $check_saved = true; // check to see if the user has 'saved' this link. sidebarstories doesn't need to check (so don't waste time on it)
	public $get_author_info = true; // get information about the link_author. sidebarstories doesn't need this information (so don't waste time on it)
	public $check_friends = true; // see if the author is a friend of the logged in user.  sidebarstories doesn't need this information (so don't waste time on it)
	public $vote_from_this_ip = 0; // if disable multiple vote from the same ip
	public $report_from_this_ip = 0; // if disable multiple vote from the same ip
	/* Redwine: initialized a new variable $is_rtl to hold the value 0 or 1 when returned from the script that checks whether the content is left-to-right or right-to-left language.  and the uploaded_image to be used for the Open Graph Protocol*/
	public $is_rtl = 0;
	public $uploaded_image = '';
	public $og_twitter_image = '';


	/**
	 * 
	 */
	function get($url)
	{
		$url = trim($url);

		if (CHECK_SPAM && $this->check_spam($url)) {
			$this->valid = false;
			return;
		}

		if (Validate_URL != false) {
			if ($url != 'http://' && $url != '') {
				$r = new KahukHTTPRequest($url);
				$xxx = $r->DownloadToString();

				/* Redwine: to fix the bug that wasn't getting the url of some site that do not allow fopen or fsockopen. This way, the submitted url will be the url of the link */
				if ($xxx == '') {
					$this->valid = true;
					$this->url = $url;
				}
			} else {
				$this->url = 'http://';
				$xxx = '';
			}
		} else {
			$xxx = "";
			$this->valid = true;
			$this->url = $url;

			return;
		}

		if (!($this->html = $xxx)) {
			return;
		}

		if ($xxx == "BADURL") {
			$this->valid = false;

			return;
		}

		$this->valid = true;
		$this->url = $url;

		/***********
		Redwine: added the "s" modifier to also match a new line in case the closing tag is on another line. Also added code to remove the piped site name from the title (I.e. "| sitename" or "- sitename"
		Also added the "?" to better grab the content of the title tag because we encountered a couple of sites that use non-standard html coding by having many title tag on the page.
		 ***********/
		if (preg_match('/<title>(.+?)<\/title>/si', $this->html, $matches)) {
			$this->url_title = trim($matches[1]);
			$this->url_title = preg_replace('/\|?-?~?[^|-~]*$/', '', $this->url_title);
		}

		/** 
		 * Defining the url_title
		 */
		if (!$this->url_title) {
			if (preg_match("'<meta\s+property=[\"\']og:title[\"\']\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
				$this->url_title = trim($matches[1]);
			}
		}
		/**
		 * enhanced the preg_match patern to match any quotes, single or double.
		 * Also some sites have the meta content attribute preceding the name attribute.
		 * We are also grabbing the Facebook and Twitter opengraph to make sure we find the needed meta.
		 */

		/**
		 * finding the image of the article to be used when submitting a story on Kahuk.
		 */
		if (preg_match("'<meta\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s+content=[\"\']([^<]*?)[\"\'](?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->og_twitter_image = $matches[3];
		} elseif (preg_match("'<meta\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\'](?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->og_twitter_image = $matches[3];
		} elseif (preg_match("'<meta\s+content=[\"\']([^<]*?)[\"\']\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->og_twitter_image = $matches[1];
		} elseif (preg_match("'<meta\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?content=[\"\']([^<]*?)[\"\']\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->og_twitter_image = $matches[1];
		} elseif (preg_match("'<meta\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->og_twitter_image = $matches[3];
		} elseif (preg_match("'<meta\s+content=[\"\']([^<]*?)[\"\']\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s+(?:itemprop=[\"\'][^<]*?[\"\'])?\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->og_twitter_image = $matches[1];
		}

		/**
		 * finding the description of the article to be used when submitting a story on Kahuk.
		 */
		if (preg_match("'<meta\s+name=[\"\']description[\"\']\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->url_description = $matches[1];
		} elseif (preg_match("'<meta\s+content=[\"\']([^<]*?)[\"\']\s+name=[\"\']description[\"\']\s{0,}\/?\s{0,}>'si", $this->html, $matches)) {
			$this->url_description = $matches[1];
		} elseif (preg_match("'<meta\s+(?:(name|property)=[\"\'](og:description|twitter:description)[\"\']\s+)?(?:property=[\"\'](og:description|twitter:description)[\"\'])?(?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s?/?>'si", $this->html, $matches)) {
			$this->url_description = $matches[4];
		} elseif (preg_match("'<meta\s+(?:property=[\"\'](og:description|twitter:description)[\"\']\s+)?(?:(name|property)=[\"\'](og:description|twitter:description)[\"\'])?(?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s?/?>'si", $this->html, $matches)) {
			$this->url_description = $matches[4];
		} elseif (preg_match("'<meta\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?(?:property=[\"\'](og:description|twitter:description)[\"\']\s+)?(?:(name|property)=[\"\'](og:description|twitter:description)[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s?/?>'si", $this->html, $matches)) {
			$this->url_description = $matches[4];
		} else {
			// Fall back on the first <p> tag content
			/* Redwine: If the meta description was not found, then we global main_smarty to pull the settings of Story_Content_Tags_To_Allow to use to filter for the allowed hml tags, used below. */
			global $main_smarty;
			/***** 
				Redwine: we match the first <p> element and if is found, then we need the Story_Content_Tags_To_Allow to use in case html tags are allowed and defined. Example:
				
				submitting https://www.cheatography.com/davechild/cheat-sheets/regular-expressions/ will populate this in the textarea:
				<p class="subdesc">A quick reference guide for regular expressions (regex), including symbols, ranges, grouping, assertions and some sample patterns to get you started.</p>
				
				Now assuming we have allowed these tags in the dashboard: <strong><br><font><img><p>
				then when we use the code on line 129 the textarea content will be filtered to this:
				
				<p class="subdesc">A quick reference guide for regular expressions (regex), including symbols, ranges, grouping, assertions and some sample patterns to get you started.</p>
				
				Notice that the <a> and <b> were filtered out.
			 *****/

			/**
			 * the original had just <p> to match. I changed it to also match the p tag with its attributes if any.
			 */
			if (preg_match('%(<p[^>]*>.*?</p>)%iu', $this->html, $regs)) {
				$paragraph = $regs[1];
				$allowedhtml = $main_smarty->get_template_vars('Story_Content_Tags_To_Allow');

				if ($allowedhtml == "") {
					$paragraph = strip_tags($paragraph);
				} else {
					$paragraph = close_tags(stripslashes(sanitize($paragraph, 4, html_entity_decode($allowedhtml))));
				}
			}

			// Make sure that it's over 100 characters in length
			/* Redwine: I have no idea why they used 100 below. I changed it to compare with the Minimum Story Length set in the Dashboard which is a constant minStoryLength */
			if (strlen($paragraph) > minStoryLength) {
				$this->url_description = $paragraph;
			}
		}

		/**
		 * Detect trackbacks
		 */
		if (isset($_POST['trackback']) && sanitize($_POST['trackback'], 3) != '') {
			$this->trackback = trim(sanitize($_POST['trackback'], 3));
		} elseif (
			preg_match('/trackback:ping="([^"]+)"/i', $this->html, $matches) ||
			preg_match('/trackback:ping +rdf:resource="([^>]+)"/i', $this->html, $matches) ||
			preg_match('/<trackback:ping>([^<>]+)/i', $this->html, $matches)
		) {
			$this->trackback = trim($matches[1]);
		} elseif (preg_match('/<a[^>]+rel="trackback"[^>]*>/i', $this->html, $matches)) {
			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
				$this->trackback = trim($matches2[1]);
			}
		} elseif (preg_match('/<a[^>]+href=[^>]+>trackback<\/a>/i', $this->html, $matches)) {
			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
				$this->trackback = trim($matches2[1]);
			}
		}
	}

	/**
	 * 
	 */
	function type()
	{
		if (empty($this->type)) {
			return 'normal';
		}

		return $this->type;
	}

	/**
	 * 
	 */
	function store()
	{
		global $db, $current_user;

		// DB 09/03/08
		if (!is_numeric($this->id)) {
			return false;
		}

		/////
		$this->store_basic();

		$link_url = $db->escape($this->url);
		$link_url_title = $db->escape($this->url_title);
		$link_url_description = $db->escape($this->url_description);
		$link_title = $db->escape($this->title);
		$link_title_url = $db->escape($this->title_url);

		if ($this->title != '') {
			if ($link_title_url == "") {
				$link_title_url = makeUrlFriendly($this->title, $this->id);
			}
		}

		$link_tags = preg_replace('/[^\p{L}\p{N}_\s\,]/u', '', $this->tags);
		$link_content = $db->escape($this->content);
		$link_field1 = $db->escape($this->link_field1);
		$link_field2 = $db->escape($this->link_field2);
		$link_field3 = $db->escape($this->link_field3);
		$link_field4 = $db->escape($this->link_field4);
		$link_field5 = $db->escape($this->link_field5);
		$link_field6 = $db->escape($this->link_field6);
		$link_field7 = $db->escape($this->link_field7);
		$link_field8 = $db->escape($this->link_field8);
		$link_field9 = $db->escape($this->link_field9);
		$link_field10 = $db->escape($this->link_field10);
		$link_field11 = $db->escape($this->link_field11);
		$link_field12 = $db->escape($this->link_field12);
		$link_field13 = $db->escape($this->link_field13);
		$link_field14 = $db->escape($this->link_field14);
		$link_field15 = $db->escape($this->link_field15);
		$link_group_id = $db->escape($this->link_group_id);
		$link_summary = $db->escape($this->link_summary);

		// Save old SEO URL if changed
		$old_url = $db->get_var("SELECT link_title_url FROM " . table_links . " WHERE link_id={$this->id}");

		if ($old_url && $old_url != $this->title_url) {
			$db->query("INSERT INTO " . table_old_urls . " SET old_link_id={$this->id}, old_title_url='$old_url'");
		}

		$sql = "UPDATE " . table_links . " set link_summary='$link_summary', link_title_url='$link_title_url', link_url='$link_url', link_url_title='$link_url_title', link_title='$link_title', link_content='$link_content', link_tags='$link_tags', link_field1='$link_field1', link_field2='$link_field2', link_field3='$link_field3', link_field4='$link_field4', link_field5='$link_field5', link_field6='$link_field6', link_field7='$link_field7', link_field8='$link_field8', link_field9='$link_field9', link_field10='$link_field10', link_field11='$link_field11', link_field12='$link_field12', link_field13='$link_field13', link_field14='$link_field14', link_field15='$link_field15', link_group_id = $link_group_id WHERE link_id=$this->id";

		if ($this->debug == true) {
			echo '<hr>Store:' . $sql . '<hr>';
		}

		$db->query($sql);

		$pos = strrpos($_SERVER["SCRIPT_NAME"], "/");
		$script_name = substr($_SERVER["SCRIPT_NAME"], $pos + 1, 100);
		$script_name = str_replace(".php", "", $script_name);
	}

	function store_basic()
	{
		global $db, $current_user;

		// DB 09/03/08
		if (!is_numeric($this->id)) {
			return false;
		}

		//
		if (!$this->date) {
			$this->date = time();
		}

		$link_author = $this->author;
		$link_status = $this->status;
		$link_votes = $this->votes;
		$link_comments = $this->comments;
		$link_reports = $this->reports;
		$link_karma = $this->karma;
		$link_randkey = $this->randkey;
		$link_category = $this->category;
		$link_date = $this->date;

		/* Redwine: fix to the published date defaulting to 943941600 (Tue, 30 Nov 1999 06:00:00 GMT) */
		if ($this->published_date == 0) {
			$this->published_date = $this->date;
		}

		$link_published_date = $this->published_date;
		$link_group_id = $this->link_group_id;

		$vars = array('link' => $this);
		check_actions('link_store_basic_pre_sql', $vars);

		if ($this->id === 0) {
			/* Redwine: Fixed the negative votes to discard a story as per Admin Panel -> Settings -> Voting -> Negative votes to remove submission. See https://github.com/Pligg/pligg-cms/commit/d05ccab49c8efbc7b0ba69b2ad6e96056652296b */
			$sql = "INSERT IGNORE INTO " . table_links . " (link_author, link_status, link_randkey, link_category, link_date, link_published_date, link_votes, link_karma, link_title, link_content ,link_group_id) VALUES ($link_author, '$link_status', $link_randkey, $link_category, FROM_UNIXTIME($link_date), FROM_UNIXTIME($link_published_date), $link_votes, $link_karma, '', '',$link_group_id)";

			if ($this->debug == true) {
				echo '<hr>store_basic:Insert:' . $sql . '<hr>';
			}

			$db->query($sql);
			$this->id = $db->insert_id;
		} else {
			/* Redwine: With php 5.6, we started having more errors and warnings as it is not tolerant like the previous versions. in the below UPDATE query, the link_modified=NULL was causing an error and therefore the story was discarded. It did not need updating because it is timestamp field. So i removed it from the query. */
			$sql = "UPDATE " . table_links . " set `link_reports`=$link_reports, `link_comments`=$link_comments, link_author=$link_author, link_status='$link_status', link_randkey=$link_randkey, link_category='$link_category', link_date=FROM_UNIXTIME($link_date), link_published_date=FROM_UNIXTIME($link_published_date), link_votes=$link_votes, link_karma=$link_karma, link_group_id=$link_group_id WHERE link_id=$this->id";

			if ($this->debug == true) {
				echo '<hr>store_basic:Update:' . $sql . '<hr>';
			}

			$db->query($sql);

			$db->query("DELETE FROM " . table_additional_categories . " WHERE ac_link_id={$this->id}");
		}

		foreach ($this->additional_cats as $cat)
			$db->query("INSERT INTO " . table_additional_categories . " SET ac_cat_id='" . sanitize($cat, 3) . "', ac_link_id={$this->id}");

		$vars = array('link' => $this);
		check_actions('link_store_basic_post_sql', $vars);
	}

	function read($usecache = TRUE)
	{
		global $db, $current_user, $cached_links;
		$id = $this->id;
		$this->rating = 0;

		if (!is_numeric($id)) {
			return false;
		}

		// check to see if the link is cached
		// if it is, use it
		// if not, get from mysql and save to cache

		if (isset($cached_links[$id]) && $usecache == TRUE) {
			$link = $cached_links[$id];
		} else {
			$link = $db->get_row("SELECT " . table_links . ".* FROM " . table_links . " WHERE link_id = $id");
			$cached_links[$id] = $link;
		}

		if ($link) {
			$this->author = $link->link_author;
			$this->userid = $link->link_author;
			$this->status = $link->link_status;
			$this->votes = $link->link_votes;
			$this->karma = $link->link_karma;
			$this->reports = $link->link_reports;
			$this->comments = $link->link_comments;
			$this->randkey = $link->link_randkey;
			$this->category = $link->link_category;
			$this->url = $link->link_url;
			$this->url = str_replace('&amp;', '&', $link->link_url);
			$this->url_title = $link->link_url_title;
			$this->title = $link->link_title;
			$this->title_url = $link->link_title_url;
			$this->tags = $link->link_tags;
			$this->content = $link->link_content;

			/* Redwine: a value 0 or 1 will be returned from the script that checks whether the content is left-to-right or right-to-left language. This varibale will be passed to /templates/bootstrap/link_summary.tpl and used on line 214 to apply dir="rtl" when applicable. As per http://php.net/manual/en/regexp.reference.unicode.php and https://www.w3.org/International/questions/qa-scripts#which */
			if (preg_match('/\p{Arabic}/u', $this->title) == 1 || preg_match('/\p{Hebrew}/u', $this->title) == 1 || preg_match('/\p{Nko}/u', $this->title) == 1 || preg_match('/\p{Syloti_Nagri}/u', $this->title) == 1 || preg_match('/\p{Thaana}/u', $this->title) == 1) {
				$this->is_rtl = 1;
			} else {
				$this->is_rtl = 0;
			}

			// DB 01/08/09
			$this->date = strtotime($link->link_date);
			//			$date=$link->link_date;
			//			$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			$this->published_date = strtotime($link->link_published_date);
			//			$date=$link->link_published_date;
			//			$this->published_date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			$this->modified = strtotime($link->link_modified);
			//			$date=$link->link_modified;
			//			$this->modified=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			/////
			$this->fullread = $this->read = true;
			$this->link_summary = $link->link_summary;

			$this->link_field1 = $link->link_field1;
			$this->link_field2 = $link->link_field2;
			$this->link_field3 = $link->link_field3;
			$this->link_field4 = $link->link_field4;
			$this->link_field5 = $link->link_field5;
			$this->link_field6 = $link->link_field6;
			$this->link_field7 = $link->link_field7;
			$this->link_field8 = $link->link_field8;
			$this->link_field9 = $link->link_field9;
			$this->link_field10 = $link->link_field10;
			$this->link_field11 = $link->link_field11;
			$this->link_field12 = $link->link_field12;
			$this->link_field13 = $link->link_field13;
			$this->link_field14 = $link->link_field14;
			$this->link_field15 = $link->link_field15;
			$this->link_group_id = $link->link_group_id;

			$this->additional_cats = array();

			$results = $db->get_results("SELECT ac_cat_id FROM " . table_additional_categories . " WHERE ac_link_id=$id", ARRAY_N);

			if ($results) {
				foreach ($results as $cat) {
					$this->additional_cats[] = $cat[0];
				}
			}

			return true;
		}

		$this->fullread = $this->read = false;

		return false;
	}

	/**
	 * 
	 */
	function read_basic()
	{
		global $db, $current_user;
		$this->username = false;
		$this->fullread = false;
		$this->rating = 0;
		$id = $this->id;

		if (!is_numeric($id)) {
			return false;
		}

		$link = $db->get_row("SELECT link_comments, link_author, link_status, link_randkey, link_category, link_date, link_votes, link_karma, link_published_date,link_group_id FROM " . table_links . " WHERE link_id = $id");

		if ($link) {
			$this->author = $link->link_author;
			$this->votes = $link->link_votes;
			$this->karma = $link->link_karma;
			$this->status = $link->link_status;
			$this->randkey = $link->link_randkey;
			$this->category = $link->link_category;
			// DB 01/08/09
			$this->date = strtotime($link->link_date);
			//$date=$link->link_date;
			//$this->date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");
			$this->published_date = strtotime($link->link_published_date);
			//$date=$link->link_published_date;
			//$this->published_date=$db->get_var("SELECT UNIX_TIMESTAMP('$date')");

			/////
			$this->comments = $link->link_comments;
			$this->link_group_id = $link->link_group_id;
			$this->read = true;

			$this->additional_cats = array();

			$results = $db->get_results("SELECT ac_cat_id FROM " . table_additional_categories . " WHERE ac_link_id=$id", ARRAY_N);

			if ($results) {
				foreach ($results as $cat) {
					$this->additional_cats[] = $cat[0];
				}
			}

			return true;
		}

		$this->read = false;

		return false;
	}

	function duplicates($url)
	{
		global $db;

		$link_url = $db->escape($url);
		$n = $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_url = '$link_url' AND link_status != 'discard'");

		return $n;
	}

	function duplicates_title($title)
	{
		global $db;

		$link_title = $db->escape($title);
		$n = $db->get_var("SELECT count(*) FROM " . table_links . " WHERE link_title = '$link_title' AND link_status != 'discard'");

		return $n;
	}


	function print_summary($type = 'full', $fetch = false, $link_summary_template = 'link_summary.tpl')
	{
		global $current_user, $globals, $the_template, $smarty, $ranklist, $db;

		// DB 09/03/08
		if (!is_numeric($this->id)) {
			return false;
		}

		//
		include_once(KAHUK_SMARTY_DIR . 'Smarty.class.php');

		$main_smarty = new Smarty;
		$main_smarty->compile_check = false;
		// enable caching at your own risk. this code is still experimental
		//$smarty->cache = true;
		$main_smarty->cache_lifetime = 120;

		$main_smarty->compile_dir = KAHUKPATH . "cache/";
		$main_smarty->template_dir = KAHUKPATH . "templates/";
		$main_smarty->cache_dir = KAHUKPATH . "cache/";

		$main_smarty->config_dir = "";
		// $main_smarty->assign('kahuk_language', KAHUK_LANG);
		$main_smarty->config_load(lang_loc . "/languages/lang_" . KAHUK_LANG . ".conf");

		$anonymous_can_vote = $db->get_var('SELECT var_value from ' . table_config . ' where var_name = "anonymous_vote";');
		$main_smarty->assign('anonymous_vote', $anonymous_can_vote);

		if (phpnum() == 4) {
			$main_smarty->force_compile = true;
		}

		$main_smarty = $this->fill_smarty($main_smarty, $type);

		$main_smarty->assign('use_title_as_link', use_title_as_link);
		$main_smarty->assign('link_nofollow', link_nofollow);
		$main_smarty->assign('open_in_new_window', open_in_new_window);
		$main_smarty->assign('the_template', The_Template);

		include KAHUK_LIBS_DIR . 'extra_fields_smarty.php';

		if ($fetch == false) {
			$main_smarty->display($the_template . '/' . $link_summary_template, 'story' . $this->id . "|" . $current_user->user_id . "|" . $type);
		} else {
			return $main_smarty->fetch($the_template . '/' . $link_summary_template, 'story' . $this->id . "|" . $current_user->user_id . "|" . $type);
		}
	}


	/**
	 * 
	 */
	function fill_smarty($smarty, $type = 'full')
	{

		static $link_index = 0;

		$link_index = $this->id;
		global $current_user, $globals, $the_template, $db, $ranklist;

		if (!$ranklist) {
			$users = $db->get_results("SELECT user_karma, COUNT(*) FROM " . table_users . " WHERE user_level NOT IN ('Spammer') AND user_karma>0 GROUP BY user_karma ORDER BY user_karma DESC", ARRAY_N);
			$ranklist = array();
			$rank = 1;

			if ($users) {
				foreach ($users as $dbuser) {
					$ranklist[$dbuser[0]] = $rank;
					$rank += $dbuser[1];
				}
			}
		}

		// DB 08/04/08
		if (!is_numeric($this->id)) {
			return false;
		}

		/////
		$smarty->assign('link_id', $this->id);

		if (!$this->read) {
			return $smarty;
		}

		$url = str_replace('&amp;', '&', htmlspecialchars($this->url));
		$url_short = txt_shorter($url);

		if ($this->url == "http://" || $this->url == '') {
			$url_short = "http://";
		} else {
			$parsed = parse_url($this->url);
			if (isset($parsed['scheme'])) {
				$url_short = $parsed['scheme'] . "://" . $parsed['host'];
			}
		}

		$title_short = utf8_wordwrap($this->title, 30, " ", 1);

		$smarty->assign('viewtype', $type);
		$smarty->assign('No_URL_Name', No_URL_Name);

		if (track_outgoing == true && $url_short != "http://") {
			if (track_outgoing_method == "id") {
				$smarty->assign('url', getmyurl("out", ($this->id)));
			}

			if (track_outgoing_method == "title") {
				$smarty->assign('url', getmyurl("outtitle", urlencode($this->title_url)));
			}

			if (track_outgoing_method == "url") {
				$smarty->assign('url', getmyurl("outurl", ($url)));
			}
		} else {
			$smarty->assign('url', ($url));
		}
		// DB 11/12/08
		if ($url_short == "http://" || $url_short == "://") {
			$smarty->assign('enc_url', urlencode(my_base_url . $this->get_internal_url()));
		} else {
			$smarty->assign('enc_url', urlencode($url));
		}

		//
		$smarty->assign('url_short', $url_short);
		$smarty->assign('title_short', $title_short);
		$smarty->assign('title_url', urlencode($this->title_url));
		$smarty->assign('enc_title_short', urlencode($title_short));
		$smarty->assign('story_url', $this->get_internal_url());
		$previd = $db->get_var("SELECT link_title_url FROM " . table_links . " WHERE link_status = 'published' AND link_id < $this->id ORDER BY link_id DESC LIMIT 1");
		$nextid = $db->get_var("SELECT link_title_url FROM " . table_links . " WHERE link_status = 'published' AND link_id > $this->id ORDER BY link_id ASC  LIMIT 1");
		$smarty->assign('story_prev_url', getmyurl("storytitle", $previd));
		$smarty->assign('story_next_url', getmyurl("storytitle", $nextid));
		$smarty->assign('story_edit_url', getmyurl("editlink", $this->id));
		$smarty->assign('story_admin_url', getmyurl("admin_modify", $this->id));
		$smarty->assign('story_comment_count', $this->comments());
		$smarty->assign('story_status', $this->status);
		$smarty->assign('story_karma', $this->karma);
		$smarty->assign('is_rtl', $this->is_rtl);
		$smarty->assign('og_twitter_image', $this->og_twitter_image);

		/* Redwine: modifications to reinstate the read more feature, done to the if $type == summary code. 
			If we remove the conditional statement on line 557 below, then we won't get the read more functionality! */
		$smarty->assign('maxSummaryLength', StorySummary_ContentTruncate);

		if ($type == "summary") {
			$smarty->assign('story_content', $this->truncate_content());
		}

		if ($type == "full") {
			$smarty->assign('story_content', $this->content);
		}

		if ($this->get_author_info == true) {
			$smarty->assign('link_submitter', $this->username());
			$smarty->assign('submitter_profile_url', getmyurl('user', $this->username));
			if (array_search($this->userkarma, $ranklist)) {
				$smarty->assign('submitter_rank', $ranklist[$this->userkarma]);
			}
			$smarty->assign('user_extra_fields', $this->extra_field);
		}

		$smarty->assign('link_submit_time', $this->date);
		$smarty->assign('link_submit_timeago', txt_time_diff($this->date));
		$smarty->assign('link_submit_date', date('F, d Y g:i A', $this->date));
		$smarty->assign('link_published_time', $this->published_date);
		$smarty->assign('link_published_timeago', txt_time_diff($this->published_date));
		$smarty->assign('link_category', $this->category_name());

		if (Multiple_Categories) {
			$cats = array();

			foreach ($this->additional_cats as $cat) {
				$url = $this->category_safe_name($cat);

				if ($this->status == "published") {
					$url = getmyurl("maincategory", $url);
				}

				if ($this->status == "new") {
					$url = getmyurl("newcategory", $url);
				}

				if ($this->status == "discard") {
					$url = getmyurl("discardedcategory", $url);
				}

				$cats[$url] = $this->category_name($cat);
			}

			$smarty->assign('link_additional_cats', $cats);
		}

		//assign category id to smarty, so we can use it in the templates. Needed for category colors!
		$smarty->assign('category_id', $this->category);

		global $URLMethod; {
			$catvar = $this->category_safe_name();
		}

		$smarty->assign('Voting_Method', Voting_Method);
		$this->votecount = $this->countvotes();

		if (Voting_Method == 2) {
			if (!$this->rating) {
				$this->rating = $this->rating($this->id) / 2;
			}

			$smarty->assign('link_rating', $this->rating);
			$smarty->assign('link_rating_width', $this->rating * 25);

			$current_user_id = $current_user->user_id;
			$jsLink = "vote($current_user_id, $this->id, $link_index, '" . md5($current_user_id . $this->randkey) . "', ";

			for ($stars = 1; $stars <= 5; $stars++) {
				$smarty->assign("link_shakebox_javascript_vote_{$stars}star", $jsLink . ($stars * 2) . ')');
			}

			$smarty->assign('vote_count', $this->votecount);

			if ($this->votes($current_user_id) > 0) {
				$smarty->assign('star_class', "-noh");
			} else {
				$smarty->assign('star_class', "");
			}
		}


		$smarty->assign('get_group_membered', $this->get_group_membered());
		$smarty->assign('get_group_shared_membered', $this->get_group_shared_membered());

		if ($this->status == "published") {
			$smarty->assign('category_url', getmyurl("maincategory", $catvar));
		}

		if ($this->status == "new") {
			$smarty->assign('category_url', getmyurl("newcategory", $catvar));
		}

		if ($this->status == "discard") {
			$smarty->assign('category_url', getmyurl("discardedcategory", $catvar));
		}

		$smarty->assign('trackback_url', get_trackback($this->id));
		$smarty->assign('user_logged_in', $current_user->user_login);
		$smarty->assign('randmd5', md5($current_user->user_id . $this->randkey));
		$smarty->assign('user_id', $this->author);
		$smarty->assign('current_user_id', $current_user->user_id);

		if (Enable_Extra_Fields) {
			$main_smarty = $smarty;
			include KAHUK_LIBS_DIR . 'extra_fields_smarty.php';
			$smarty = $main_smarty;

			$smarty->assign('link_field1', $this->link_field1);
			$smarty->assign('link_field2', $this->link_field2);
			$smarty->assign('link_field3', $this->link_field3);
			$smarty->assign('link_field4', $this->link_field4);
			$smarty->assign('link_field5', $this->link_field5);
			$smarty->assign('link_field6', $this->link_field6);
			$smarty->assign('link_field7', $this->link_field7);
			$smarty->assign('link_field8', $this->link_field8);
			$smarty->assign('link_field9', $this->link_field9);
			$smarty->assign('link_field10', $this->link_field10);
			$smarty->assign('link_field11', $this->link_field11);
			$smarty->assign('link_field12', $this->link_field12);
			$smarty->assign('link_field13', $this->link_field13);
			$smarty->assign('link_field14', $this->link_field14);
			$smarty->assign('link_field15', $this->link_field15);
		}

		$smarty->assign('link_group_id', $this->link_group_id);
		$smarty->assign('instpath', my_base_url . my_kahuk_base . "/");
		$smarty->assign('UseAvatars', do_we_use_avatars());
		$smarty->assign('Avatar', $avatars = get_avatar('all', "", "", "", $this->userid));
		$smarty->assign('Avatar_ImgSrc', $avatars['large']);
		$smarty->assign('Avatar_ImgSrcs', $avatars['small']);
		/* Redwine: Roles and permissions and Groups fixes */
		// Get the Group creator/Admin/Moderator to use the assigned permissions, when $this->link_group_id is greater than 0 
		$is_gr_Creator = 0;
		$is_gr_Admin = 0;
		$is_gr_Moderator = 0;
		if ($this->link_group_id > 0) {
			$g_creator = $db->get_row("SELECT group_creator FROM " . table_groups . " WHERE group_id =" . $this->link_group_id);
			if ($g_creator->group_creator == $current_user->user_id) {
				$is_gr_Creator = 1;
			}
			$ismember = $db->get_row("SELECT member_role FROM " . table_group_member . " WHERE member_group_id =" . $this->link_group_id . " AND member_user_id =" . $current_user->user_id . " AND member_status ='active'");
			if (!empty($ismember)) {
				if ($ismember->member_role != "") {
					if ($ismember->member_role == "admin") {
						$is_gr_Admin = 1;
					} elseif ($ismember->member_role == "moderator") {
						$is_gr_Moderator = 1;
					}
				}
			}
		} else {
			//Redwine: we want to find out if the user is a group admin to use it in the group unshare, because we want to allow group admins and group link sharer to unshare it.

			$ismember = $db->get_var("SELECT  distinct t1.member_role
			FROM `" . table_group_member . "` t1
			LEFT JOIN `" . table_group_shared . "` t2
			ON t1.member_group_id=t2.share_group_id WHERE t1.member_group_id IN (SELECT `share_group_id` from `" . table_group_shared . "` where `share_link_id` = " . $this->id . ")");

			if (!empty($ismember)) {
				if ($ismember != "") {
					if ($ismember == "admin") {
						$is_gr_Admin = 1;
					}
				}
			}
		}
		//Rediwne: find if the link was shared by a group member
		$link_sharer = $db->get_var("SELECT `share_user_id` FROM `" . table_group_shared . "` WHERE `share_link_id` = " . $this->id . " AND `share_user_id` = " . $current_user->user_id);
		$smarty->assign('is_gr_Creator', $is_gr_Creator);
		$smarty->assign('is_gr_Admin', $is_gr_Admin);
		$smarty->assign('is_gr_Moderator', $is_gr_Moderator);
		$smarty->assign('is_link_sharer', $link_sharer);
		/*Redwine: Roles and permissions and Groups fixes. We need the user_level to determine the site wide Admin & Moderators to give access according to their permissions */
		global $main_smarty;
		$smarty->assign('isAdmin', $main_smarty->get_template_vars('isAdmin'));
		$smarty->assign('isModerator', $main_smarty->get_template_vars('isModerator'));

		if ($this->check_friends == true) {
			// For Friends //
			include_once(KAHUK_LIBS_DIR . 'friend.php');
			$friend = new Friend;
			// make sure we're logged in and we didnt submit the link.
			if ($current_user->user_id > 0 && $current_user->user_login != $this->username()) {
				$friend_md5 = friend_MD5($current_user->user_login, $this->username());
				$smarty->assign('FriendMD5', $friend_md5);

				$isfriend = $friend->get_friend_status($this->author);
				if (!$isfriend) {
					$friend_text = 'add to';
					$friend_url = 'addfriend';
				} else {
					$friend_text = 'remove from';
					$friend_url = 'removefriend';
				}

				$smarty->assign('Friend_Text', $friend_text);
				$smarty->assign('user_add_remove', getmyurl('user', $this->username(), $friend_url));
			}

			$smarty->assign('Allow_Friends', Allow_Friends);
			$smarty->assign('Use_New_Story_Layout', Use_New_Story_Layout);
			// --- //
		}
		/* Redwine: commented the block from line 639 to 647 because it is related to block 649 to 663 that was commented and therefore obsolete, and generating a notice. */
		/*if($current_user->user_id != '')
		{
			$vars = array('author_id' => $this->author,'link_id' => $this->id);
			check_actions('friends_activity_function', $vars);
			if($vars['value'] == true){
				$smarty->assign('friendvoted', 1);
			}	
		}*/
		/*
		//for friends voting activity
		include_once(KAHUK_LIBS_DIR.'friend.php');
		$friend = new Friend;
		$sql = 'SELECT ' . table_votes . '.*, ' . table_users . '.user_id FROM ' . table_votes . ' INNER JOIN ' . table_users . ' ON ' . table_votes . '.vote_user_id = ' . table_users . '.user_id WHERE (((' . table_votes . '.vote_value)>0) AND ((' . table_votes . '.vote_link_id)='.$this->id.') AND (' . table_votes . '.vote_type= "links"));';
		$voters = $db->get_results($sql);
		$voters = object_2_array($voters);
		foreach($voters as $key => $val)
		{
			$voteduserid = $val['user_id'];
			if($voteduserid == $friend->get_friend_status($this->author))
			{
				$friendvoted = 1;
			}
			$smarty->assign('friendvoted', $friendvoted);
		}*/
		if ($this->check_saved == true) {
			global $cached_saved_links;
			if (isset($cached_saved_links[$this->id])) {
				$smarty->assign('link_mine', $cached_saved_links[$this->id]);
			} else {
				$smarty->assign('link_mine', $db->get_row("SELECT * FROM " . table_saved_links . " WHERE saved_user_id=$current_user->user_id AND saved_link_id=$this->id LIMIT 1;"));
			}
		}
		$smarty->assign('user_url_saved', getmyurl('user2', $current_user->user_login, 'saved'));

		$smarty->assign('user_add_links_private', getmyurl('user_add_links_private', $this->id));
		$smarty->assign('user_add_links_public', getmyurl('user_add_links_public', $this->id));

		$smarty->assign('group_story_links_publish', getmyurl('group_story_links_publish', $this->id));
		$smarty->assign('group_story_links_new', getmyurl('group_story_links_new', $this->id));
		$smarty->assign('group_story_links_discard', getmyurl('group_story_links_discard', $this->id));
		$smarty->assign('link_id', $this->id);
		$smarty->assign('user_url_add_links', getmyurl('user_add_links', $this->id));
		$smarty->assign('user_url_remove_links', getmyurl('user_remove_links', $this->id));

		$smarty->assign('link_shakebox_index', $link_index);
		$smarty->assign('link_shakebox_votes', $this->votes);
		$smarty->assign('link_shakebox_showbury', $this->reports);


		$this->get_current_user_votes($current_user->user_id);
		if (votes_per_ip > 0) {
			$smarty->assign('vote_from_this_ip', $this->vote_from_this_ip);
			$smarty->assign('report_from_this_ip', $this->report_from_this_ip);
		}

		$smarty->assign('link_shakebox_currentuser_votes', $this->current_user_votes);
		$smarty->assign('link_shakebox_currentuser_reports', $this->current_user_reports);


		if ($this->reports == -1) {
			// reporting was added to the svn and some people started using it
			// so in upgrade if someone already has the reports field, we set it to
			// -1. Then when we read() we check if -1. if it still is, update the count
			// from the votes table and store it into the link_reports field so we
			// don't have to look at the votes table again.

			$this->reports = $this->count_all_votes("<0");
			$this->store_basic();
			$smarty->assign('link_shakebox_reports', $this->reports);
		}

		$jslink = "vote($current_user->user_id,$this->id,$link_index," . "'" . md5($current_user->user_id . $this->randkey) . "',10)";
		$jsreportlink = "vote($current_user->user_id,$this->id,$link_index," . "'" . md5($current_user->user_id . $this->randkey) . "',-10)";
		$smarty->assign('link_shakebox_javascript_vote', $jslink);

		$jsunvote = "unvote($current_user->user_id,$this->id,$link_index," . "'" . md5($current_user->user_id . $this->randkey) . "',10)";
		$smarty->assign('link_shakebox_javascript_unvote', $jsunvote);

		$jsunbury = "unvote($current_user->user_id,$this->id,$link_index," . "'" . md5($current_user->user_id . $this->randkey) . "',-10)";
		$smarty->assign('link_shakebox_javascript_unbury', $jsunbury);

		$smarty->assign('link_shakebox_javascript_report', $jsreportlink);
		if (!defined('alltagtext')) {
			// for pages like index, this ->display was being called for each story
			// which was sometimes 15+ times per page. this way it's just called once
			$smarty->display('blank.tpl'); //this is just to load the lang file so we can pull from it in php
			define('alltagtext', $smarty->get_config_vars('KAHUK_Visual_Tags_All_Tags'));
		}
		$alltagtext = alltagtext;

		$smarty->assign('enable_group', enable_group);
		$smarty->assign('pagename', pagename);
		$smarty->assign('my_base_url', my_base_url);
		$smarty->assign('my_kahuk_base', my_kahuk_base);
		$smarty->assign('Default_Gravatar_Large', Default_Gravatar_Large);

		//$link_index++;
		$vars['smarty'] = $smarty;
		check_actions('lib_link_summary_fill_smarty', $vars);
		/*** Redwine: Assigning the return value from upload_main.php  to uploaded_image variable to be used in the Open Graph Protocol ***/
		$this->uploaded_image = $vars['smarty']->_vars['uploaded_image'];
		/*** Redwine: populating the session and assigning it to smarty. ***/
		$_SESSION['uploaded_image'] = $this->uploaded_image;
		$smarty->assign('uploaded_image', $this->uploaded_image);

		return $smarty;
	}


	//sharing membered group list
	function get_group_membered()
	{
		global $db, $main_smarty, $rows, $current_user;
		$current_userid = $current_user->user_id;
		/***********
		Redwine: 1- modified the query to exclude members that are banned or flagged and inactive.
		2- to also accurately get what a group member can share. the modifications will only pull the groups where a group member can share a story that has not been shared by the user or any other group member; a story will not be shared twice to the same group.
		 ***********/
		if ($current_userid)
			$this->group_membered = $db->get_results("SELECT  DISTINCT t1.group_id,t1.group_name FROM `" . table_groups . "` t1 LEFT JOIN `" . table_group_member . "` t2 ON t2.member_group_id=t1.group_id and t2.member_user_id = $current_userid left join   `" . table_group_shared . "` t3 on t3.share_user_id = t2.member_user_id WHERE t2.member_user_id = $current_userid AND t2.member_role !='banned' AND t2.member_role !='flagged' AND t2.member_status !='inactive' AND t1.group_id NOT IN (SELECT `share_group_id` from `" . table_group_shared . "` where `share_link_id` = " . $this->id . ")");

		$output = '';
		/* Redwine: added !empty to eliminate the Undefined property: Link::$group_membered. */
		if (!empty($this->group_membered)) {
			if ($this->group_membered != NULL) {
				foreach ($this->group_membered as $results)
					$output .= "<a class='group_member_share' href='" . my_base_url . my_kahuk_base . "/group_share.php?link_id=" . $this->id . "&group_id=" . $results->group_id . "&user_id=" . $current_user->user_id . "' >" . $results->group_name . "</a><br />";
			}
		}

		return $output;
	}
	/*Redwine: I created get_group_shared_membered function to allow a group member who shared a story to unshare it. Group admins also have the same privilege to unshare a story shared by group members.*/
	function get_group_shared_membered()
	{
		global $db, $main_smarty, $rows, $current_user;
		$current_userid = $current_user->user_id;
		if (!isset($this->group_shared_membered) && $current_userid)
			$this->group_shared_membered = $db->get_results("SELECT DISTINCT t1.group_id,t1.group_name FROM `" . table_groups . "` t1 LEFT JOIN `" . table_group_member . "` t2 ON t2.member_group_id=t1.group_id AND t2.member_user_id = $current_userid LEFT JOIN `" . table_group_shared . "` t3 ON t3.share_group_id = t1.group_id WHERE t2.member_user_id = $current_userid AND t2.member_role !='banned' AND t2.member_role !='flagged' AND t2.member_status !='inactive' AND group_status = 'Enable' AND `share_user_id` = $current_userid AND `share_link_id` = " . $this->id);

		$output = '';
		if (!empty($this->group_shared_membered))
			foreach ($this->group_shared_membered as $results)
				$output .= "<a class='group_member_share' href='" . my_base_url . my_kahuk_base . "/group_share.php?link_id=" . $this->id . "&group_id=" . $results->group_id . "&user_id=" . $current_user->user_id . "&action=unshare'>" . $results->group_name . "</a><br />";

		return $output;
	}
	//--------------------------------------
	/* Redwine: modifications to reinstate the read more feature, done to the truncate_content function */
	function truncate_content()
	{
		if (utf8_strlen($this->content) > StorySummary_ContentTruncate) {
			if (!use_title_as_link) {
				$url_read = $this->get_internal_url();
			} else {
				$url_read = $this->url;
			}
			global $main_smarty;
			$readmore = "<div class=\"read_more_article\" storyid=\"" . $this->id . "\" ><a href=" . $url_read . "> " . $main_smarty->get_config_vars('KAHUK_Visual_Read_More') . "</a></div>";
			$content = truncate_strings_html($this->content, StorySummary_ContentTruncate, $break = " ", '');
			$content .= $readmore;
			return $content;
		}
		return $this->content;
	}

	function print_shake_box($smarty)
	{
		global $current_user;
	}

	function rating($linkid)
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		$vote->link = $linkid;
		return $vote->rating();
	}

	function countvotes()
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		$vote->link = $this->id;
		return $vote->anycount();
	}

	function count_all_votes($value = "> 0")
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		$vote->link = $this->id;
		return $vote->count_all($value);
	}

	function votes($user, $value = "> 0")
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		$vote->user = $user;
		$vote->link = $this->id;
		return $vote->count($value);
	}

	function reports($user)
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		$vote->user = $user;
		$vote->link = $this->id;
		return $vote->reports();
	}

	// DB 11/10/08
	function votes_from_ip($ip = '')
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		if ($ip)
			$vote->ip = $ip;
		else {
			$vote->ip = check_ip_behind_proxy();
		}
		$vote->link = $this->id;
		return $vote->count();
	}

	function reports_from_ip($ip = '')
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		if ($ip)
			$vote->ip = $ip;
		else {
			$vote->ip = check_ip_behind_proxy();
		}
		$vote->link = $this->id;
		return $vote->reports();
	}
	/////

	function get_current_user_votes($user)
	{
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');

		$vote = new Vote;
		$vote->type = 'links';
		$vote->user = $user;
		$vote->link = $this->id;
		$results = $vote->user_list_all_votes();

		$votes = 0;
		$reports = 0;

		if (is_array($results)) {
			foreach ($results as $row) {
				if (isset($row->vote_value)) {
					if ($row->vote_value > 0) {
						$votes = $votes + 1;
					}
					if ($row->vote_value < 0) {
						$reports = $reports + 1;
					}
				}
			}
		}

		$this->current_user_votes = $votes;
		$this->current_user_reports = $reports;

		if (votes_per_ip > 0 && $user == 0) {
			$ac_vote_from_IP = $this->votes_from_ip();
			if ($ac_vote_from_IP <= 1)
				$ac_vote_from_IP = 0;

			$ac_report_from_IP = $this->reports_from_ip();
			if ($ac_report_from_IP <= 1)
				$ac_report_from_IP = 0;

			$this->vote_from_this_ip = $ac_vote_from_IP;
			$this->report_from_this_ip = $ac_report_from_IP;
		}
	}

	function remove_vote($user = 0, $value = 10)
	{

		$vote = new Vote;
		$vote->type = 'links';
		$vote->user = $user;
		$vote->link = $this->id;
		$vote->value = $value;
		$vote->remove();

		$vote = new Vote;
		$vote->type = 'links';
		$vote->link = $this->id;
		if (Voting_Method == 1) {
			$this->votes = $vote->count();
			$this->reports = $this->count_all_votes("<0");
		} elseif (Voting_Method == 2) {
			$this->votes = $vote->rating();
			$this->votecount = $vote->count();
			$this->reports = $this->count_all_votes("<0");
		} elseif (Voting_Method == 3) {
			$this->votes = $vote->count();
			$this->votecount = $vote->count();
			$this->karma = $vote->karma();
			$this->reports = $this->count_all_votes("<0");
		}
		$this->store_basic();

		$vars = array('link' => $this);
		check_actions('link_remove_vote_post', $vars);
	}

	/**
	 * use kahuk_insert_vote function instead of this function
	 * depricated
	 */
	function insert_vote($user = 0, $value = 10)
	{
		global $anon_karma;
		require_once(KAHUK_LIBS_DIR . 'class-votes.php');
		if ($value > 10) {
			$value = 10;
		}
		$vote = new Vote;
		$vote->type = 'links';
		$vote->user = $user;
		$vote->link = $this->id;
		$vote->value = $value;
		//		if($value<10) {$vote->value=($anon_karma/10)*$value;}
		if ($user > 0) {
			$dbuser = new User($user);
			if ($dbuser->id > 0)
				$vote->karma = $dbuser->karma;
		} elseif (!anonymous_vote) {
			return;
		} else {
			$vote->karma = $anon_karma;
		}
		if ($vote->insert()) {
			$vote = new Vote;
			$vote->type = 'links';
			$vote->link = $this->id;
			if (Voting_Method == 1) {
				$this->votes = $vote->count();
				$this->reports = $this->count_all_votes("<0");
			} elseif (Voting_Method == 2) {
				$this->votes = $vote->rating();
				$this->votecount = $vote->count();
				$this->reports = $this->count_all_votes("<0");
			} elseif (Voting_Method == 3) {
				$this->votes = $vote->count();
				$this->votecount = $vote->count();
				$this->karma = $vote->karma();
				$this->reports = $this->count_all_votes("<0");
			}
			$this->store_basic();
			$this->check_should_publish();

			/* Redwine: fix to some bugs in the Karma system. This code was to update the user_karma with the value of "Voted on an article" when the auto vote is set to true upon story submission. It was causing double update of the user_karma upon voting. We provisioned a new code in /module/karma_main.php in the karma_do_submit3 function. https://github.com/Pligg/pligg-cms/commit/737770202d22ec938465fe66e52f2ae7cdcf5240 */
			//$vars = array('vote' => $this);
			//check_actions('link_insert_vote_post', $vars);		

			return true;
		}
		return false;
	}

	function check_should_publish()
	{

		$votes = $this->category_votes();
		// $votes must be explicitly cast to (int) to compare accurately
		if (!is_numeric($votes))
			$votes = (int) votes_to_publish;
		else
			$votes = (int) $votes;

		if (Voting_Method == 1) {
			// check to see if we should change the status to publish
			if ($this->status == 'new' && $this->votes >= $votes) {
				$now = time();
				$diff = $now - $this->date;
				$days = intval($diff / 86400);
				if ($days <= days_to_publish) {
					$this->publish();
				}
			}
		} elseif (Voting_Method == 2) {
			if ($this->status == 'new' && $this->votes >= (rating_to_publish * 2) && $this->votecount >= $votes) {
				$now = time();
				$diff = $now - $this->date;
				$days = intval($diff / 86400);
				if ($days <= days_to_publish + 1000) {
					$this->publish();
				}
			}
		} elseif (Voting_Method == 3) {
			$karma = $this->category_karma();
			if (!is_numeric($karma))
				$karma = karma_to_publish;
			if ($this->status == 'new' && $this->karma >= $karma && $this->votecount >= $votes) {
				$now = time();
				$diff = $now - $this->date;
				$days = intval($diff / 86400);
				if ($days <= days_to_publish) {

					$this->publish();
				}
			}
		}
		/* Redwine: Fixed the negative votes to discard a story as per Admin Panel -> Settings -> Voting -> Negative votes to remove submission. See https://github.com/Pligg/pligg-cms/commit/d05ccab49c8efbc7b0ba69b2ad6e96056652296b */
		/*
		if(($this->status == 'new' || $this->status == 'discard') && buries_to_spam>0 && $this->reports>=buries_to_spam) {
			$this->status='discard';
			$this->store_basic();

			$vars = array('link_id' => $this->id);
			check_actions('story_spam', $vars);
		}
		*/
	}

	function category_votes()
	{
		// $the_cats is set in /libs/smartyvariables.php

		global $dblang, $the_cats, $main_smarty;

		foreach ($the_cats as $cat) {
			if ($cat->category_id == $this->category)
				return $cat->category_votes;
		}

		return $main_smarty->get_config_vars('KAHUK_Visual_Submit3Errors_NoCategory');
	}

	function category_karma()
	{
		global $dblang, $the_cats, $main_smarty;

		foreach ($the_cats as $cat) {
			if ($cat->category_id == $this->category)
				return $cat->category_karma;
		}

		return $main_smarty->get_config_vars('KAHUK_Visual_Submit3Errors_NoCategory');
	}

	function category_name($id = 0)
	{
		// $the_cats is set in /libs/smartyvariables.php

		global $dblang, $the_cats, $main_smarty;
		if (!$id) $id = $this->category;

		foreach ($the_cats as $cat) {
			if ($cat->category_id == $id)
			//			if($cat->category_id == $this->category && $cat->category_lang == $dblang)
			{
				return $cat->category_name;
			}
		}

		return $main_smarty->get_config_vars('KAHUK_Visual_Submit3Errors_NoCategory');
	}

	function category_safe_name($id = 0)
	{
		// $the_cats is set in /libs/smartyvariables.php

		global $dblang, $the_cats;
		if (!$id) $id = $this->category;

		foreach ($the_cats as $cat) {
			if ($cat->category_id == $id && $cat->category_lang == $dblang) {
				return $cat->category_safe_name;
			}
		}
	}

	function category_safe_names()
	{
		$cats = array($this->category_safe_name());
		foreach ($this->additional_cats as $cat)
			$cats[] = $this->category_safe_name($cat);
		sort($cats, SORT_STRING);

		return join(',', $cats);
	}


	function publish()
	{
		if (!$this->read) $this->read_basic();
		$this->published_date = time();

		totals_adjust_count($this->status, -1);
		totals_adjust_count('published', 1);

		$this->status = 'published';
		$this->store_basic();

		$vars = array('link_id' => $this->id);
		check_actions('link_published', $vars);
	}

	function username()
	{
		$user = new User;
		$user->id = $this->author;
		$user->read();

		$this->username = $user->username;
		$this->userkarma = $user->karma;
		$this->extra_field = $user->extra_field;

		return $user->username;
	}


	function recalc_comments()
	{
		global $db;

		// DB 08/04/08
		if (!is_numeric($this->id)) {
			return false;
		}
		/////
		$this->comments = $db->get_var("SELECT count(*) FROM " . table_comments . " WHERE comment_status='published' AND comment_link_id = $this->id");
		/* Redwine:added the return value of the link_comment column, otherwise it was not storing it */
		return $this->comments;
	}


	function comments()
	{
		global $db;

		if (summarize_mysql == 1) {
			return $this->comments;
		} else {
			// DB 08/04/08
			if (!is_numeric($this->id)) {
				return false;
			}
			/////
			return $db->get_var("SELECT count(*) FROM " . table_comments . " WHERE comment_status='published' AND comment_link_id = $this->id");
		}
	}

	function evaluate_formulas()
	{
		global $db;
		/* Redwine: Fixed the negative votes to discard a story as per Admin Panel -> Settings -> Voting -> Negative votes to remove submission. See https://github.com/Pligg/pligg-cms/commit/d05ccab49c8efbc7b0ba69b2ad6e96056652296b */
		if (buries_to_spam == 1) {
			$res = $db->get_results("select * from " . table_formulas . " where type = 'report' and enabled = 1;");
			if (!$res) return;
			foreach ($res as $formula) {
				$reports = $this->count_all_votes("< 0");
				$votes = $this->count_all_votes("> 0");
				$from = $this->date;
				$now = time();
				$diff = $now - $from;
				$hours = ($diff / 3600);
				$hours_since_submit = intval($hours * 100) / 100;

				$evalthis = 'if (' . $formula->formula . '){return "1";}else{return "0";}';
				if (eval($evalthis) == 1 && $this->status != 'spam') {
					totals_adjust_count($this->status, -1);
					totals_adjust_count('discard', 1);

					$this->status = 'discard';
					$this->store_basic();

					$vars = array('link_id' => $this->id);
					check_actions('story_discard', $vars);
				}
			}
		}
	}

	function return_formula_system_version()
	{
		// 0.1 original
		// 0.2 added hours_since_submit

		return 0.2;
	}

	function adjust_comment($value)
	{
		$this->comments = $this->comments + $value;
	}

	function verify_ownership($authorid)
	{
		global $db;

		// DB 09/03/08
		if (!is_numeric($this->id)) {
			return false;
		}
		if (!is_numeric($authorid)) {
			return false;
		}
		/////
		$sql = 'SELECT `link_id` from `' . table_links . '` WHERE `link_id` = ' . $this->id . ' AND `link_author` = ' . $authorid . ' ORDER BY `link_date` DESC LIMIT 1;';
		if ($db->get_var($sql)) {
			return true;
		} else {
			return false;
		}
	}

	function get_internal_url()
	{
		// returns the internal (comments page) url	
		if ($this->title_url == "") {
			return getmyurl("story", $this->id);
		} else {
			return getmyurl("storyURL", $this->category_safe_names(), urlencode($this->title_url), $this->id);
		}
	}

	function check_spam($text)
	{
		global $MAIN_SPAM_RULESET;
		global $USER_SPAM_RULESET;

		$regex_url   = "/(http:\/\/|https:\/\/|ftp:\/\/|www\.)([^\/\"<\s]*)/im";
		$mk_regex_array = array();
		preg_match_all($regex_url, $text, $mk_regex_array);

		for ($cnt = 0; $cnt < count($mk_regex_array[2]); $cnt++) {
			$test_domain = rtrim($mk_regex_array[2][$cnt], "\\");
			if (strlen($test_domain) > 3) {
				$domain_to_test = $test_domain . ".multi.surbl.org";
				if (strstr(gethostbyname($domain_to_test), '127.0.0')) {
					$this->logSpam("surbl rejected $test_domain");
					return true;
				}
			}
		}
		$retVal = $this->check_spam_rules($MAIN_SPAM_RULESET, strtoupper($text));
		if (!$retVal) {
			$retVal = $this->check_spam_rules($USER_SPAM_RULESET, strtoupper($text));
		}

		return $retVal;
	}

	#####################################
	# check a file of local rules
	# . . the rules are written in a regex format for php
	#     . . or one entry per line eg: bigtimespammer.com on one line
	####################

	function check_spam_rules($ruleFile, $text)
	{
		if (!file_exists($ruleFile)) {
			echo $ruleFile . " does not exist\n";
			return false;
		}
		$handle = fopen($ruleFile, "r");
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			$splitbuffer = explode("####", $buffer);
			// Parse domain name from a line
			$expression = parse_url(trim($splitbuffer[0]), PHP_URL_HOST);
			if (!$expression) $expression = trim($splitbuffer[0]);
			// Make it regexp compatible
			$expression = str_replace('.', '\.', $expression);
			// Check $text against http://<domain>
			if (strlen($expression) > 0 && preg_match("/\/\/([^\.]+\.)*$expression(\/|$)/i", $text)) {
				$this->logSpam("$ruleFile violation: $expression");
				return true;
			}
		}
		fclose($handle);
		return false;
	}


	// log date, time, IP address and rule which triggered the spam	
	function logSpam($message)
	{
		global $SPAM_LOG_BOOK;

		$ip = "127.0.0.0";
		if (!empty($_SERVER["REMOTE_ADDR"])) {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		$date = date('M-d-Y');
		$timestamp = time();

		$message = $date . "\t" . $timestamp . "\t" . $ip . "\t" . $message . "\n";

		$file = fopen($SPAM_LOG_BOOK, "a");
		fwrite($file, $message);
		fclose($file);
	}
}
class KahukHTTPRequest
{
	var $_fp;        // HTTP socket
	var $_url;        // full URL
	var $_host;        // HTTP host
	var $_protocol;    // protocol (HTTP/HTTPS)
	var $_uri;        // request URI
	var $_port;        // port

	// scan url
	function _scan_url()
	{
		$req = $this->_url;

		$pos = strpos($req, '://');
		$this->_protocol = strtolower(substr($req, 0, $pos));

		$req = substr($req, $pos + 3);
		$pos = strpos($req, '/');
		if ($pos === false)
			$pos = strlen($req);
		$host = substr($req, 0, $pos);

		if (strpos($host, ':') !== false) {
			list($this->_host, $this->_port) = explode(':', $host);
		} else {
			$this->_host = $host;
			$this->_port = ($this->_protocol == 'https') ? 443 : 80;
		}

		$this->_uri = substr($req, $pos);
		if ($this->_uri == '')
			$this->_uri = '/';
	}

	// constructor
	function __construct($url)
	{
		$this->_url = $url;
		$this->_scan_url();
	}

	// download URL to string
	function DownloadToString()
	{
		$crlf = "\r\n";

		// generate request
		$req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf
			.    'Host: ' . $this->_host . $crlf
			.    $crlf;

		// fetch
		$this->_fp = fsockopen(($this->_protocol == 'https' ? 'tls://' : '') . $this->_host, $this->_port, $errno, $errstr, 20);
		$this->_fp = fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port, $errno, $errstr, 20);
		if (!$this->_fp)
			return ("BADURL");
		fwrite($this->_fp, $req);
		$response = '';
		while (is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
			$response .= fread($this->_fp, 1024);
		fclose($this->_fp);
		if (!strstr($response, 'HTTP/'))
			return ("BADURL");

		// split header and body
		$pos = strpos($response, $crlf . $crlf);
		if ($pos === false)
			return ($response);
		$header = substr($response, 0, $pos);
		$body = substr($response, $pos + 2 * strlen($crlf));

		// parse headers
		$headers = array();
		$lines = explode($crlf, $header);
		foreach ($lines as $line)
			if (($pos = strpos($line, ':')) !== false)
				$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));

		// redirection?
		if (isset($headers['location'])) {
			$http = new KahukHTTPRequest($headers['location']);
			return ($http->DownloadToString($http));
		} else {
			if (extension_loaded('iconv') && preg_match('/charset=(.+)$/', $headers['content-type'], $m))
				$body = iconv($m[1], "UTF-8", $body);

			return ($body);
		}
	}
}
