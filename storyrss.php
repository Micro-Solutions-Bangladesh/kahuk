<?php
//
// Story RSS feed
// Usage:
// 	storyrss.php?id=story_id&rows=10&time=3600
// or
// 	storyrss.php?title=Story_title&rows=100&time=86600
//

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

$requestID = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0; 

if(isset($_GET['title']) && sanitize($_GET['title'], 3) != ''){$requestTitle = sanitize(sanitize($_GET['title'], 3),4);}
if(isset($requestTitle)){$requestID = $db->get_var($sql = "SELECT link_id FROM " . table_links . " WHERE `link_title_url` = '".$db->escape($requestTitle)."';"); }

$rows = isset($_GET['rows']) && is_numeric($_GET['rows']) ? $_GET['rows'] : 40;
$time = isset($_GET['time']) && is_numeric($_GET['time']) ? $_GET['time'] : 0;

if(is_numeric($requestID)) {
	$id = $requestID;
	$link = new Link;
	$link->id=$requestID;
	if(!$link->read() || ($link->status=='spam' && !checklevel('admin') && !checklevel('moderator'))){

		// check for redirects
		include(KAHUK_LIBS_DIR.'redirector.php');
		$x = new redirector($_SERVER['REQUEST_URI']);

		kahuk_redirect_404();
	}

	do_rss_header($link);

	// get all parent comments
	$sql = "SELECT * FROM " . table_comments . "
			LEFT JOIN " . table_users . " ON comment_user_id=user_id 
			WHERE comment_status='published' AND comment_link_id=$link->id";
	if($time > 0) {
     		$from = time()-$time;
		$sql .= " AND comment_date > FROM_UNIXTIME($from)";
        } 
	$sql .= " ORDER BY comment_date DESC ";
	if($rows > 0) {
		$sql .= " LIMIT 0,$rows";
	}

	$comments = $db->get_results($sql);
	if ($comments) {
		require_once(KAHUK_LIBS_DIR.'comment.php');
		$comment = new Comment;
		foreach($comments as $dbcomment) {
			$comment->id=$dbcomment->comment_id;
			$cached_comments[$dbcomment->comment_id] = $dbcomment;
			$comment->read();

			echo "<item>\n";
			echo "	<title><![CDATA[".$main_smarty->get_config_vars('KAHUK_MiscWords_Comment')." #".$comment->id."]]></title>\n";
			echo "	<link>".getmyFullurl("storyURL", $link->category_safe_names($link->category), urlencode($link->title_url), $link->id)."#c".$comment->id."</link>\n";
			$vars = array('link' => $link);
			check_actions('rss_add_data', $vars);
			echo '	<source url="'.getmyFullurl("storyURL", $link->category_safe_names($link->category), $link->title_url, $link->id).'"><![CDATA['. $link->title .']]></source>';
			echo "\n	<description><![CDATA[" . $comment->content . "]]></description>\n";
			if (!empty($comment->date))
				echo "	<pubDate>".date('D, d M Y H:i:s T', $comment->date-misc_timezone*3600)."</pubDate>\n";
			else 
				echo "	<pubDate>".date('D, d M Y H:i:s T', time()-misc_timezone*3600)."</pubDate>\n";
			echo "	<author>" . $dbcomment->user_login . "</author>\n";
			echo "	<votes>".$comment->votes."</votes>\n";
			echo "	<guid isPermaLink='false'>".$comment->id."</guid>\n";

			// module system hook
			$vars = array('item' => $comment);
			check_actions('comment_rss_item', $vars);

			echo "</item>\n\n";
 		} 
	}

	do_rss_footer();

} else {

	// check for redirects
	include(KAHUK_LIBS_DIR.'redirector.php');
	$x = new redirector($_SERVER['REQUEST_URI']);
	
	kahuk_redirect_404();
}

function do_rss_header($link) {
	global $last_modified, $dblang, $main_smarty;
#	header('Content-type: text/xml; charset=utf-8', true);
	echo '<?xml version="1.0" encoding="utf-8"?'.'>' . "\n";
	echo '<rss version="2.0" '."\n";
	echo 'xmlns:content="http://purl.org/rss/1.0/modules/content/"'."\n";
	echo 'xmlns:wfw="http://wellformedweb.org/CommentAPI/"'."\n";
	echo 'xmlns:dc="http://purl.org/dc/elements/1.1/"'."\n";
	echo '>'. "\n";
	echo '<channel>'."\n";
	echo '<title>'.htmlspecialchars($main_smarty->get_config_vars("KAHUK_Visual_Name"))." - ".$link->title.'</title>'."\n";
	echo "<link>".getmyFullurl("storyURL", $link->category_safe_names($link->category), urlencode($link->title_url), $link->id)."</link>\n";
	echo '<description>'.strip_tags($link->truncate_content()).'</description>'."\n";

	if (!empty($link->date))
		echo '<pubDate>'.date('D, d M Y H:i:s T', $link->date-misc_timezone*3600).'</pubDate>'."\n";
	else 
		echo "<pubDate>".date('D, d M Y H:i:s T', time()-misc_timezone*3600)."</pubDate>\n";
	echo '<language>'.$dblang.'</language>'."\n";
}

function do_rss_footer() {
	echo "</channel>\n</rss>\n";
}

function onlyreadables($string) {
  for ($i=0;$i<strlen($string);$i++) {
   $chr = $string{$i};
   $ord = ord($chr);
   if ($ord<32 or $ord>126) {
     $chr = "~";
     $string{$i} = $chr;
   }
  }
  return str_replace("~", "", $string);
}
