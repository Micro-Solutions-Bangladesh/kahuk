<?php

include_once('internal/Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(KAHUK_LIBS_DIR.'link.php');
include(KAHUK_LIBS_DIR.'smartyvariables.php');

// sidebar
$main_smarty = do_sidebar($main_smarty);

// breadcrumbs and page title
$navwhere['text1'] = 'Feeds';
$navwhere['link1'] = getmyurl('about', $dblang);
$main_smarty->assign('navbar_where', $navwhere);

// pagename
define('pagename', 'rssfeeds'); 
$main_smarty->assign('pagename', pagename);

// show the template
$main_smarty->assign('tpl_center', $the_template . '/rss_feeds_center');
$main_smarty->display($the_template . '/kahuk.tpl');
