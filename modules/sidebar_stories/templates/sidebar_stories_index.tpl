{php}

include_once('internal/Smarty.class.php');
if(!isset($main_smarty)){$main_smarty = new Smarty;}
// If we're calling this page through another page like index.php, $main_smarty will already be set
// If we're calling this page directly, set main_smarty

include_once('config.php');
include_once(KAHUK_LIBS_DIR.'link.php');
include_once(KAHUK_LIBS_DIR.'search.php');
include_once(KAHUK_LIBS_DIR.'smartyvariables.php');
include_once(KAHUK_LIBS_DIR.'sidebarstories.php');

global $the_template, $main_smarty;

	// for filterTo you can use "published", "new" or "all"
	// to change the way the links look, make a copy of the /modules/sidebar_stories/templates/sidebar_stories.tpl to /templates/<your template>/modules/sidebar_stories/sidebar_stories.tpl

	$ss = new SidebarStories();
	$ss->orderBy = "link_date DESC"; // newest on top.
	$ss->pagesize = 5; // the number of items to show in the box.
	$ss->TitleLengthLimit = 60;
	
	if(pagename == "index"){
		$ss->filterToStatus = "new";
		$ss->header = $main_smarty->get_config_vars("KAHUK_Visual_Kahuk_Queued");
		$ss->link = getmyurl("new");
	}
	elseif(pagename == "new"){
		$ss->filterToStatus = "published";
		$ss->header = $main_smarty->get_config_vars("KAHUK_Visual_Published_News");
		$ss->link = my_base_url.my_kahuk_base;
	}
	else{
		$ss->filterToStatus = "published";
		$ss->header = $main_smarty->get_config_vars("KAHUK_Visual_Published_News");
		$ss->link = my_base_url.my_kahuk_base;
	}
	
	$ss->template = $my_base_url . './modules/sidebar_stories/templates/sidebar_stories.tpl';
	$main_smarty->assign('ss_toggle_id', 'ssstories');
	$main_smarty->assign('ss_body', $ss->show(true));
	$main_smarty->assign('ss_header', $ss->header);	
	$main_smarty->display($my_base_url . './modules/sidebar_stories/templates/sidebar_stories_wrapper.tpl');
{/php}
