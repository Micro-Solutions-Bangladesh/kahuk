<?php
force_authentication();

//
include(KAHUK_LIBS_DIR . 'kahuk-manage-spam.php');
include(KAHUK_LIBS_DIR . 'class-kahuk-http-request.php');
include(KAHUK_LIBS_DIR . 'smartyvariables.php');

include(KAHUK_LIBS_DIR . 'page-submit.php');

$submit_error_code = '';
$phase = 0; //sanitize_number(_post("phase", 0));
$the_template_center = $the_template . '/submit_step_1_center';

$urlFound = esc_url(_request('url'));
$is_story_title = isset($_POST['title']);

if ($urlFound) {
	if ($is_story_title) {
		$phase = 2;
	} else {
		$phase = 1;

		/**
		 * Check the referrer URL is same as the URL submited
		 */
		check_referrer($urlFound);
	}
}

/**
 * Check if the url contain action code to display any redirection message
 * If action-code is not empty then we will by force make the phase 0
 */
$actioncode = _get('actioncode', '');

if (!empty($actioncode)) {
	$main_smarty->assign('actioncode', sanitize_text_field($actioncode));
}

/**
 * 
 */
if ($phase == 1) {
    do_submit1();
} else if ($phase == 2) {
    do_submit2();
} else {
    do_submit0();
}

//
$main_smarty->assign('submit_error', $submit_error_code);
$main_smarty->assign('tpl_center', $the_template_center);

/**
 * Very first page to collect story URL from visitor
 */
function do_submit0()
{
	global $main_smarty, $the_template, $the_template_center;

	$the_template_center = $the_template . '/submit_step_1_center';

	$gId = sanitize_number(_request("gId", '0'));
	$main_smarty->assign('gId', $gId);
}


/**
 * Lets validate the URL and collect story
 */
function do_submit1()
{
	global $globalCategoriesObj, $globalGroup, $main_smarty, $the_template, $urlFound, $the_template_center, $submit_error_code;

	include(KAHUK_LIBS_DIR . 'kahuk-link.php');
	$kahukLink = kahuk_link_init($urlFound);

	if ($kahukLink->errors->has_errors()) {
		do_submit0();
		$submit_error_code = $kahukLink->errors->get_error_code();
		return;
	}

	$randkey = intval(rand(10000, 10000000)) + intval(microtime());
	kahuk_create_url_records($randkey, $kahukLink->url);

	$main_smarty->assign('randkey', $randkey);
	$main_smarty->assign('submit_url', $kahukLink->url);
	$data = parse_url($kahukLink->url);
	$main_smarty->assign('url', $kahukLink->url);
	$main_smarty->assign('url_short', 'http://' . $data['host']); // TODO

	$main_smarty->assign('MAX_NUMBER_OF_WORD_STORY_DESC', MAX_NUMBER_OF_WORD_STORY_DESC);
	$main_smarty->assign('submit_url_description', $kahukLink->url_description);
	$main_smarty->assign('submit_trackback', $kahukLink->trackback);

	//
	$submit_url_title = str_replace('"', "&#034;", $kahukLink->url_title);
	$main_smarty->assign('submit_url_title', $submit_url_title);

	//
	$main_smarty->assign('submit_cat_array', $globalCategoriesObj->get_items());

	//
	$featured_img = (empty($kahukLink->linkImages)) ? "" : $kahukLink->linkImages[0]['url']; // Get the first image
	$main_smarty->assign('og_twitter_image', $featured_img);

	

	$the_template_center = $the_template . '/submit_step_2_center';

	$gId = sanitize_number(_request("gId", '0'));
	// 
	if (("true" == enable_group) && ($gId > 0)) {
		$main_smarty->assign('group', $globalGroup);
	}
}


/**
 * Save Story Detail
 */
function do_submit2()
{
	global $hooks, $page_submit_url_begin, $urlFound;

	$randkey = _post('randkey');
	$hasRecord = kahuk_has_url_in_record($randkey, $urlFound);

	if (!$hasRecord) {
		kahuk_set_session_message(
			sprintf('Session expired for the url: %s !', $urlFound),
			'warning'
		);

		kahuk_redirect($page_submit_url_begin);
		return;
	}

	// Verify submit via action hook
	$verify = $hooks->apply_filters(
		"submit_verify",
		['data' => [], 'success' => true]
	);

	if (isset($verify["success"]) && ($verify["success"] == false)) {
		kahuk_set_session_message(
			$verify["message"],
			"warning"
		);

		kahuk_redirect($page_submit_url_begin);
		return;
	}

	// Check Summary For the Link
	$link_content = kahuk_kses($_POST['summarytext'], allowed_html_tags);

	$link_summary = kahuk_link_summary($link_content);

	if (empty($link_content)) {
		kahuk_set_session_message(
			'Summary text required some more text!',
			'error'
		);

		kahuk_redirect($page_submit_url_begin);
	}

	// Check for the Link Title
	$story_title = sanitize_text_field($_POST['title']);
	$story_slug = kahuk_create_slug_story($story_title); // START TODO

	if (empty($story_title) || empty($story_slug)) {
		kahuk_set_session_message(
			'Please write a appropriate title!',
			'error'
		);

		kahuk_redirect($page_submit_url_begin);
	}

	if (kahuk_word_count($story_title) < MIN_NUMBER_OF_WORD_STORY_TITLE) {
		kahuk_set_session_message(
			'Please write a minimum of ' . MIN_NUMBER_OF_WORD_STORY_TITLE . ' words for the story title.',
			'error'
		);

		kahuk_redirect($page_submit_url_begin);
	}

	//
	$story_check = kahuk_check_unique_story($urlFound, $story_slug);

	if (false == $story_check['status']) {
		kahuk_set_session_message(
			$story_check['message'],
			'error'
		);

		kahuk_redirect($page_submit_url_begin);
	}

	$story_slug_unique = $story_check['story_slug'];

	// Store Link
	$linksData = [];
	$linksData['link_status'] = kahuk_story_status_default();
	$linksData['link_randkey'] = $randkey; // TODO Delete
	$linksData['link_category'] = sanitize_number($_POST['category']);
	$linksData['link_url'] = $urlFound;
	$linksData['link_title'] = $story_title;
	$linksData['link_title_url'] = $story_slug_unique;
	$linksData['link_content'] = $link_content;
	$linksData['link_summary'] = $link_summary;

	$linksData['link_group_id'] = sanitize_number(_request("gId", '0'));

	$linkId = kahuk_insert_story($linksData); //
	$newPost = [];

	if (0 < $linkId) {
		$newPost = kahuk_get_story_by_id($linkId);
	}

	//
	if (!$newPost) {
		kahuk_set_session_message(
			sprintf('Got an unexpected error while submit (%s), we will fix it!', $urlFound),
			'error'
		);

		kahuk_redirect(KAHUK_BASE_URL);
	}

	//
    $story_url = kahuk_permalink_story($newPost);
	kahuk_redirect($story_url);
}
