<?php
/**
 * Create the url of the website root
 * 
 * @since 5.0.6
 * 
 * @return string url
 */
function kahuk_root_url($relativePath = false) {
    $output = KAHUK_BASE_URL;

    if ($relativePath) {
        $output = my_kahuk_base;
    }

    return $output . "/";
}

/**
 * Create the url with website root and provided path
 * 
 * @since 5.0.6
 * 
 * @return string url
 */
function kahuk_create_url($path, $args = [], $relativePath = false) {
    $root_path_url = kahuk_root_url($relativePath);
    $output = $root_path_url . $path;

    if (!empty($args)) {
        $output = add_query_arg($args, $output);
    }

    return $output;
}

/**
 * Create the url with website root and provided path
 * 
 * @since 5.0.7
 * 
 * @return string url
 */
function kahuk_create_plugin_url($path, $args = []) {
    $root_path_url = kahuk_root_url();
    $output = $root_path_url . KAHUKDIR_PLUGINS . "/" . $path;

    if (!empty($args)) {
        $output = add_query_arg($args, $output);
    }

    return $output;
}

/**
 * Create URL for Ajax request
 * 
 * @since 5.0.7
 * 
 * @return string URL
 */
function kahuk_url_ajax( $params = [] ) {
	return kahuk_create_url("kahuk-ajax/", $params);
}

/**
 * Process permalink from $_SERVER
 * 
 * @since 5.0.6
 * 
 * @return string url
 */
function kahuk_get_permalink() {
    $uri = str_replace("--" . my_kahuk_base . "/", '', "--" . $_SERVER['REQUEST_URI']);

    // Remove 'redirectto' parameter from query string
    $output = preg_replace('~(\?|&)redirectto=[^&]*~', '$1', $uri);

    return kahuk_create_url($output);
}

/**
 * Create permalink with pagination
 * 
 * @since 5.0.6
 * 
 * @return string URL
 */
function kahuk_create_permalink($argsCustom = []) {
    global $pageCon, $pagePrefix, $pageTask;

    $defaults = [
        'page_url' => '',
        'permalink' => false,

		'page_num' => 0,
        'page_con' => '',
		'page_prefix' => '',
		'page_task' => '',

        'vars' => [],
	];

	$args = array_merge($defaults, $argsCustom);
    $output = '';
    $page_num = $args['page_num'];

    if ($args['permalink']) {
        $output = kahuk_get_permalink();
        // echo "<pre>{$output} :: </pre>";
        $output = add_query_arg(['page' => $page_num], $output);
        // echo "<pre>{$output}</pre>";
    } elseif ($args['page_url']) {
        $output = $args['page_url'] . "page/{$page_num}";
    } else {
        $output = KAHUK_BASE_URL . '/';

        $page_con = (($args['page_con'] != '') ? $args['page_con'] : $pageCon);
        $page_prefix = (($args['page_prefix'] != '') ? $args['page_prefix'] : $pagePrefix);
        $page_task = (($args['page_task'] != '') ? $args['page_task'] : $pageTask);

        $page_slug = get_page_slug();

        // echo "<pre>page_con: {$page_con}, page_prefix: {$page_prefix}, page_task: {$page_task}, page_slug: {$page_slug}</pre>";

        switch ($page_con) {
            case 'home':
                if (in_array($page_task, ['published', 'new', 'trending'])) {
                    $output .=  "{$page_task}/";

                    if ($page_num > 1) {
                        $output .=  "page/{$page_num}";
                    }
                }

                //
                $duration = _get('duration');

                if (in_array($duration, ['day', 'week', 'month', 'year'])) {
                    $output = add_query_arg(['duration' => $duration], $output);
                }

                break;

            case 'category':
                $output =  kahuk_category_link($page_slug);

                if ($page_num > 1) {
                    $output .=  "page/{$page_num}";
                }

                break;


            default:
                $output .=  "{$page_con}/";
                
                if (!empty($page_prefix)) {
                    $output .=  "{$page_prefix}/";
                }

                if ($page_num > 1) {
                    $output .=  "page/{$page_num}";
                }

                break;
        }
    }

    return $output;
}


/**
 * 
 */
function kahuk_permalink_group($group_or_slug = '', $args = []) {
    global $pageNum;
    $slug = '';

    if (is_array($group_or_slug) && isset($group_or_slug['group_safename'])) {
        $slug = $group_or_slug['group_safename'];
    } else {
        $slug = $group_or_slug;
    }

    $path = "groups/";

    // Creating permalink for groups listing
    if (empty($slug)) {
        if ($pageNum>1) {
            $path .= "page/{$pageNum}/";
        }
    } else {
        $path .= "{$slug}/";
    }

    $output = kahuk_create_url($path, $args);

    return $output;
}


/**
 * Create single story page url
 * 
 * @since 5.0.6
 * 
 * @return string URL
 */
function kahuk_permalink_story($story_or_id_or_slug) {
    if (is_numeric($story_or_id_or_slug)) {
        $slug = kahuk_get_story_slug_by_id($story_or_id_or_slug);
    } else if (is_array($story_or_id_or_slug) && isset($story_or_id_or_slug['link_title_url'])) {
        $slug = $story_or_id_or_slug['link_title_url'];
    } else {
        $slug = $story_or_id_or_slug;
    }

    return kahuk_create_url("story/" . $slug . "/");
}

/**
 * Create category page url
 * 
 * @since 5.0.6
 * 
 * @return string URL
 */
function kahuk_category_link($cat_slug) {
	return KAHUK_BASE_URL . "/category/" . $cat_slug . "/";
}

/**
 * Create url for the login page
 * 
 * @since 5.0.6
 * 
 * @return string URL
 */
function kahuk_url_login($redirectto = '') {
    $output = kahuk_create_url('login/');

    if (!empty($redirectto)) {
        $output = kahuk_create_url('login/', ['redirectto' => $redirectto]);
    }

	return $output;
}

/**
 * Create url for the logout action
 * 
 * @since 5.0.6
 * 
 * @return string URL
 */
function kahuk_url_logout() {
	return kahuk_create_url('login/', ['task' => 'logout']);
}

/**
 * Create url for password reset action page
 * 
 * @since 5.0.6
 * 
 * @return string URL
 */
function kahuk_url_pass_reset($isEcho = true) {
    return kahuk_create_url('password-reset/');
}

/**
 * Create url for the user profile
 * 
 * @since 5.0.7
 * 
 * @return string URL
 */
function kahuk_url_user_profile($user_login, $args = []) {
	return kahuk_create_url("user/{$user_login}", $args);
}
