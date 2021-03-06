<?php
function str_ends_with($haystack, $needle)
{
	return ( substr ($haystack, -strlen ($needle) ) === $needle) || $needle === '';
}

/* If the URL is too verbose (specifying index.php or page 1), then, of course
 * we just want the main page, which defaults to page 1 anyway. */
$url = parse_url($_SERVER['REQUEST_URI']);
if (strpos($_SERVER['REQUEST_URI'],'index.php') !== false || ( isset ($_GET['page']) && $_GET['page'] == 1)) {
	header("HTTP/1.1 301 Moved Permanently");
	$_SERVER['QUERY_STRING'] = str_replace('page=1','',$_SERVER['QUERY_STRING']);
	header ("Location: ./".($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
	exit;
} elseif (str_ends_with($url['path'], '/page/1') || str_ends_with($url['path'], '/page/1/')) {
	header("HTTP/1.1 301 Moved Permanently");
	header ("Location: ../".($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
	exit;
}

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'search.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

// module system hook
$vars = '';
check_actions('index_top', $vars);

// find the name of the current category
if (isset($_REQUEST['category'])) {
	
	$thecat = get_cached_category_data('category_safe_name', sanitize($_REQUEST['category'], 1));
	
	$main_smarty->assign('request_category_name', $thecat->category_name);
	$catID = $thecat->category_id;
	$thecat = $thecat->category_name;

	if ( !$thecat ) {
		kahuk_redirect_404();
	}
}

// start a new search
$search = new Search();

// check for some get/post
if (isset($_REQUEST['from'])) {$search->newerthan = sanitize($_REQUEST['from'], 3);}
unset($_REQUEST['search']);
unset($_POST['search']);
unset($_GET['search']);
if (isset($_REQUEST['search'])) {
	$search->searchTerm = sanitize($_REQUEST['search'], 3);
}
if (isset($_REQUEST['search'])) {
	$search->filterToStatus = "all";
}
if (!isset($_REQUEST['search'])) {
	$search->orderBy = "link_published_date DESC, link_date DESC";
}
if (isset($_REQUEST['tag'])) {
	$search->searchTerm = sanitize($_REQUEST['search'], 3); $search->isTag = true;
}
if (isset($thecat)) {
	$search->category = $catID;
}

// figure out what "page" of the results we're on
$search->offset = (get_current_page()-1)*$page_size;

// pagesize set in the admin panel
$search->pagesize = $page_size;

// since this is index, we only want to view "published" stories
$search->filterToStatus = "published";

// this is for the tabs on the top that filter
if (isset($_GET['part'])) {
	$search->setmek = $db->escape($_GET['part']);
}

$search->do_setmek();	

// do the search
$search->doSearch($search->pagesize);

$linksum_count = $search->countsql;
$linksum_sql = $search->sql;

if (isset($_REQUEST['category'])) {
	$category_data = get_cached_category_data('category_safe_name', sanitize($_REQUEST['category'], 1));
	$main_smarty->assign('meta_description', $category_data->category_desc);
	$main_smarty->assign('meta_keywords', $category_data->category_keywords);

	// breadcrumbs and page title for the category we're looking at
	$main_smarty->assign('title', ''.$main_smarty->get_config_vars('KAHUK_Visual_Published_News').' - ' . $thecat . '');
	$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Published_News');
	$navwhere['link1'] = getmyurl('root', '');
	$navwhere['text2'] = $thecat;
	$main_smarty->assign('navbar_where', $navwhere);
	$main_smarty->assign('pretitle', $thecat );
	$main_smarty->assign('posttitle', $main_smarty->get_config_vars('KAHUK_Visual_Published_News'));
	$main_smarty->assign('page_header', $thecat . $main_smarty->get_config_vars('KAHUK_Visual_Published_News'));
	// pagename	
	define('pagename', 'published'); 
	$main_smarty->assign('pagename', pagename);
} else {
	// breadcrumbs and page title
	$navwhere['show'] = 'yes';
	$navwhere['text1'] = $main_smarty->get_config_vars('KAHUK_Visual_Published_News');
	$navwhere['link1'] = getmyurl('root', '');
	$main_smarty->assign('navbar_where', $navwhere);
	$main_smarty->assign('posttitle', $main_smarty->get_config_vars('KAHUK_Visual_Home_Title'));
	$main_smarty->assign('page_header', $main_smarty->get_config_vars('KAHUK_Visual_Published_News'));
	// pagename	
	define('pagename', 'index'); 
	$main_smarty->assign('pagename', pagename);
}

//  make sure my_base_url is set
if ($my_base_url == '') {
	echo '<div style="text-align:center;"><span class=error>ERROR: my_base_url is not set. Please correct this using the <a href = "/admin/admin_config.php?page=Location%20Installed">admin panel</a>. Then refresh this page.</span></div>';
}

// sidebar
$main_smarty = do_sidebar($main_smarty);

// misc smarty

//Redwine: checking if SMTP testing is true to display the message body that is sent to the user for password recovery.
if (isset($_SESSION['recoveryConfirmation'])) {
	$main_smarty->assign('recoveryConfirmation', $_SESSION['recoveryConfirmation']);
	unset($_SESSION['recoveryConfirmation']);
}
if (isset($from_text)) {
	$main_smarty->assign('from_text', $from_text);
}

if (isset($search->setmek)) {
	$main_smarty->assign('setmeka', $search->setmek);
} else {
	$main_smarty->assign('setmeka', '');
}
if (!empty($category_data)) {
	$main_smarty->assign('URL_rss_page', getmyurl('rsspage', $category_data->category_safe_name, ''));
}
$fetch_link_summary = true;
include('./libs/link_summary.php'); // this is the code that show the links / stories

//For Infinit scrolling and continue reading option 
if (Auto_scroll==2 || Auto_scroll==3) {
   $main_smarty->assign("scrollpageSize", $page_size);
 
} else {
   $main_smarty->assign('link_pagination', do_pages($rows, $page_size, "published", true));
}
// show the template
$main_smarty->assign('tpl_center', $the_template . '/index_center');
$main_smarty->display($the_template . '/kahuk.tpl');
