<?php
/**
 * Create page title sentence
 * 
 * @since 5.0.6
 * @updated 5.0.7
 * 
 * @return string
 */
function kahuk_page_title($pn = "") {
    global $hooks, $main_smarty, $pagePrefix, $globalStory, $globalCategory, $globalGroup, $globalUser;

    if (!$pn) {
        $pn = kahuk_build_unique_page();
    }

    $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_HOME');

    switch($pn) {
        case "home-published":
            break;

        case "home-new":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_NEW');
            break;

        case "home-trending":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_TRENDING');
            break;

        case "story":
            $lang_txt = $main_smarty->get_config_vars('KAHUK_LANG_STORY');
            $page_title = trim($globalStory['link_title'], $lang_txt) . " " . $lang_txt;
            break;

        case "submit-begin":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_SUBMIT');
            break;

        case "groups":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_GROUPS');
            break;

        case "group":
            $lang_txt = $main_smarty->get_config_vars('KAHUK_LANG_GROUP');
            $page_title = trim($globalGroup['group_name'], $lang_txt) . " " . $lang_txt;
            break;

        case "user":
            $page_title = ucfirst($globalUser['user_names']) . " Profile";

            if ($pagePrefix == "settings") {
                $page_title .= " Settings";
            }

            break;

        case "category":
            $lang_txt = $main_smarty->get_config_vars('KAHUK_LANG_CATEGORY');
            $page_title = trim($globalCategory['category_name'], $lang_txt) . " " . $lang_txt;
            break;

        case "login":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_LOGIN');
            break;

        case "register":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_REGISTER');
            break;

        case "search":
            $search = sanitize_text_field(_get('search'));
            $page_title = sprintf(
                $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_SEARCH'),
                $search
            );

            break;

        case "topusers":
            $page_title = $main_smarty->get_config_vars('KAHUK_LANG_PAGE_TITLE_TOPUSERS');
            break;

        default:
            break;
    }

    return $hooks->apply_filters("page_title", $page_title, $pn);
}

/**
 * 
 */
function kahuk_current_page_title() {
    // global $hooks;

    $pn = kahuk_build_unique_page();
    static $page_title = false;

    if (!$page_title) {
        $page_title = kahuk_page_title($pn);
    }
    
    return $page_title;
}

/**
 * Build and echo page title for title bar
 * 
 * @since 5.0.6
 * 
 * @return string 
 */
function kahuk_build_page_title($pn = "", $display = true) {
    global $main_smarty, $hooks;

    $defaultArgs = [
        'divider' => "|",
        'site_name_position' => "right",
    ];

    $title_array = $hooks->apply_filters("kahuk_title_parts", $defaultArgs);

    $page_title = ($pn ? kahuk_page_title($pn) : kahuk_current_page_title());
    $site_name = $main_smarty->get_config_vars("KAHUK_LANG_SITE_NAME");

    $output = $page_title . " " . $title_array["divider"] . " " . $site_name;

    if ($title_array["site_name_position"] != "right") {
        $output = $site_name . " " . $title_array["divider"] . " " . $page_title;
    }

    $title = $hooks->apply_filters("kahuk_title", $output);

    // Send it out.
	if ( $display ) {
		echo "<title>{$title}</title>";
	} else {
		return $title;
	}
}

/**
 * Create breadcrumbs data array
 * 
 * @since 5.0.6
 * 
 * @return array
 */
function kahuk_breadcrumbs() {
    global $main_smarty, $permalink, $pageCon, $pageTask, $globalStory, $globalGroup;

    $pn = kahuk_build_unique_page();
    $output = [];

    $output[] = [
        'title' => $main_smarty->get_config_vars('KAHUK_Visual_Home'),
        'url' => KAHUK_BASE_URL,
        'active' => false,
    ];

    switch($pageCon) {

        case "editgroup":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_Visual_Groups'),
                'url' => getmyurl("groups"),
            ];
    
            $output[] = [
                'title' => $globalGroup['group_name'],
                'url' => getmyurl("group_story_title", $globalGroup['group_safename']),
            ];
    
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_Visual_Group_Edit'),
                'url' => '',
            ];

            break;

        case "error-404":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_404_ERROR'),
                'url' => '',
                'active' => true,
            ];

            break;

        case "group":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_Visual_Groups'),
                'url' => getmyurl("groups"),
            ];
    
            $output[] = [
                'title' => $globalGroup['group_name'],
                'url' => '',
            ];

            break;

        case "groups":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_Visual_Groups'),
                'url' => '',
            ];

            break;

        case "home":
            if ($pn == "home-published") {
                // 
            } else if ($pn == "home-trending") {
                $output[] = [
                    'title' => 'Trending', // TODO add a language config
                    'url' => '',
                    'active' => true,
                ];
            } else if ($pn == "home-new") {
                $output[] = [
                    'title' => $main_smarty->get_config_vars('KAHUK_Visual_Kahuk_Queued'),
                    'url' => '',
                    'active' => true,
                ];
            }

            break;

        case "login":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_BREADCRUMB_LOGIN'),
                'url' => '',
                'active' => true,
            ];

            break;
            
        case "password-reset":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_BREADCRUMB_PASS_RESET'),
                'url' => '',
                'active' => true,
            ];

            break;
        
        case "register":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_BREADCRUMB_REGISTER'),
                'url' => '',
                'active' => true,
            ];

            break;

        case "search":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_SEARCH'),
                'url' => $permalink,
            ];

            $search = sanitize_text_field(_get('search'));
            $output[] = [
                'title' => $search,
                'url' => '',
            ];

            break;

        case "story":
            $output[] = [
                'title' => $globalStory['link_title'],
                'url' => '',
            ];

            break;

        
        case "submit":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_SUBMIT'),
                'url' => '',
                'active' => true,
            ];

            break;
            
        case "submit-group":
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_Visual_Groups'),
                'url' => getmyurl("groups"),
            ];
    
            $output[] = [
                'title' => $main_smarty->get_config_vars('KAHUK_LANG_BREADCRUMB_CREATE_A_GROUP'),
                'url' => '',
                'active' => true,
            ];

            break;
        
        default:
            break;
    }

    return $output;
}

/**
 * Create markup of pagination
 * 
 * @sinve 5.0.6
 * 
 * @return string markup of pagination
 */
function kahuk_pagination($argsCustom = []) {
	global $page_size;

	$defaults = [
		'total' => 0,
		'page_limit' => 0,

        'permalink' => '',

        'page_url' => '',
		'page_con' => '',
		'page_action' => '',

		'output_type' => 'ul', // could be anchor, ul
		'wrapper_cls' => 'pagination',

		'next_previous' => true,
		'next_text' => '&#8250;',
		'prev_text' => '&#8249;',

		'first_last' => true,
		'first_text' => '&#171;',
		'last_text' => '&#187;',

        'pagination_style' => 1,
        'surround_pages' => 5,
	];

	$args = array_merge($defaults, $argsCustom);

	if ($args['total'] < 2) {
		return '';
	}

    if (!$args['page_limit']>0) {
		$args['page_limit'] = $page_size;
	}

    $output = '';

    if ($args['pagination_style'] == 1) {
        $output = kahuk_pagination_1($args);
    } else {
        $output = kahuk_pagination_2($args);
    }

	return $output;
}

/**
 * Create markup for pagination style 1
 * 
 * @sinve 5.0.6
 * 
 * @return string markup of pagination
 */
function kahuk_pagination_1($args = []) {
    $surround_pages = $args['surround_pages'];

	$total = $args['total'];
	$page_limit = $args['page_limit'];

	$current = get_page_number();
    $total_pages = 1;

    if ($page_limit) {
        $total_pages = ceil($total / $page_limit);
    }

	// $start = max($current - intval($page_limit / 2), 1);

	$pageNavs = [];

    if ($current>1) {
        // Navigate to the very first page
        $args['page_num'] = 1;

        if ($args['permalink']) {
            $href = add_query_arg(['page' => $args['page_num']], $args['permalink']);
        } else {
            $href = kahuk_create_permalink($args);
        }

        $pageNavs[] = sprintf(
            "<li class=\"page-item first-item\">%s</li>",
            sprintf(
                "<a href=\"%s\">%s</a>", 
                $href, 
                $args['first_text']
            )
        );

        // Navigate to the previous page
        $args['page_num'] = ($current - 1);
        // $href = kahuk_create_permalink($args);
        if ($args['permalink']) {
            $href = add_query_arg(['page' => $args['page_num']], $args['permalink']);
        } else {
            $href = kahuk_create_permalink($args);
        }

        $pageNavs[] = sprintf(
            "<li class=\"page-item previous-item\">%s</li>",
            sprintf(
                "<a href=\"%s\">%s</a>", 
                $href, 
                $args['prev_text']
            )
        );
    }


    // Creating nav pages for before the current page
    $expectedFirstNav = ($current - $surround_pages);

    for($i = $expectedFirstNav; $i < $current; $i++) {
        if ($i > 0) {
            $args['page_num'] = $i;
            // $href = kahuk_create_permalink($args);
            if ($args['permalink']) {
                $href = add_query_arg(['page' => $args['page_num']], $args['permalink']);
            } else {
                $href = kahuk_create_permalink($args);
            }

            $pageNavs[] = sprintf(
                "<li class=\"page-item\">%s</li>",
                sprintf(
                    "<a href=\"%s\">%s</a>", 
                    $href, 
                    $i
                )
            );
        }
    }

    // Current page
    $pageNavs[] = sprintf(
        "<li class=\"page-item active\">%s</li>",
        sprintf(
            "<a href=\"%s\">%s</a>", 
            '#', 
            $current
        )
    );

    // Creating nav pages for after the current page
    $expectedLastNav = ($current + $surround_pages);

    if ($expectedLastNav > $total_pages) {
        $expectedLastNav = $total_pages;
    }

    for($i = ($current + 1); $i <= $expectedLastNav; $i++) {
        $args['page_num'] = $i;
        // $href = kahuk_create_permalink($args);
        if ($args['permalink']) {
            $href = add_query_arg(['page' => $args['page_num']], $args['permalink']);
        } else {
            $href = kahuk_create_permalink($args);
        }

        $pageNavs[] = sprintf(
            "<li class=\"page-item\">%s</li>",
            sprintf(
                "<a href=\"%s\">%s</a>", 
                $href, 
                $i
            )
        );
    }


    if (($total_pages>2) && ($total_pages>$current)) {
        // Navigate to the next page
        $args['page_num'] = ($current + 1);
        // $href = kahuk_create_permalink($args);
        if ($args['permalink']) {
            $href = add_query_arg(['page' => $args['page_num']], $args['permalink']);
        } else {
            $href = kahuk_create_permalink($args);
        }

        $pageNavs[] = sprintf(
            "<li class=\"page-item next-item\">%s</li>",
            sprintf(
                "<a href=\"%s\">%s</a>", 
                $href, 
                $args['next_text']
            )
        );

        // Navigate to the very last page
        $args['page_num'] = $total_pages;
        // $href = kahuk_create_permalink($args);
        if ($args['permalink']) {
            $href = add_query_arg(['page' => $args['page_num']], $args['permalink']);
        } else {
            $href = kahuk_create_permalink($args);
        }

        $pageNavs[] = sprintf(
            "<li class=\"page-item last-item\">%s</li>",
            sprintf(
                "<a href=\"%s\">%s</a>", 
                $href, 
                $args['last_text']
            )
        );
    }

    //
    $output = sprintf(
        "<ul class=\"%s\">%s</ul>",
        $args['wrapper_cls'],
        implode("", $pageNavs)
    );

    return $output;
}


/**
 * Create markup for pagination style 2
 * 
 * @sinve 5.0.6
 * 
 * @return string markup of pagination
 */
function kahuk_pagination_2($args = []) {
    $first_last = $args['first_last'];
	$output_type = $args['output_type'];
	$total = $args['total'];
	$page_limit = $args['page_limit'];

	$current = get_page_number();
	$total_pages = ceil($total / $page_limit);
	$start = max($current - intval($page_limit / 2), 1);

	// $end = $start + $page_limit - 1;
	$end = $start + $page_limit;

	$output = '';

	if ($total_pages > 1) { // If there is only 1 page, don't display any pagination at all
        // Very Fist Page Item
        if ($first_last) {
            if (1 < $current) {
                $args['page_num'] = 1;
                $href = kahuk_create_permalink($args);

                if ($output_type == 'achor') {
                    $output .= sprintf("<a href=\"%s\" class=\"page-item first-item\">%s</a>", $href, $args['first_text']);
                } else {
                    $achor = sprintf("<a href=\"%s\">%s</a>", $href, $args['first_text']);
                    $output .= sprintf("<li class=\"page-item first-item\">%s</li>", $achor);
                }
            }
        }

		// Previous Page
		if ($current == 1) {
			// There are no previous pages, so don't show or disable the "previous" link.
            if ($output_type == 'achor') {
				$output .= sprintf("<a href=\"%s\" class=\"page-item previous-item disabled\"aria-disabled=\"true\">%s</a>", '#', $args['prev_text']);
			} else {
                $output .= sprintf("<li class=\"page-item previous-item disabled\" aria-disabled=\"true\"><a href=\"%s\">%s</a></li>", '#', $args['prev_text']);
			}
		} else {
			$i = $current - 1;

			$args['page_num'] = $i;
			$href = kahuk_create_permalink($args);

			if ($output_type == 'achor') {
				$output .= sprintf("<a href=\"%s\" class=\"page-item previous-item\">%s</a>", $href, $args['prev_text']);
			} else {
				$achor = sprintf("<a href=\"%s\">%s</a>", $href, $args['prev_text']);
				$output .= sprintf("<li class=\"page-item previous-item\">%s</li>", $achor);
			}
		}

		//
		if ($start > 1) {
			$i = 1;

			$args['page_num'] = $i;
			$href = kahuk_create_permalink($args);

            if ($output_type == 'achor') {
				$output .= '<a href="' . $href . '" class="page-item">' . $i . '</a>';
                $output .= '<a href="#" class="page-item" aria-disabled="true">...</a>';
			} else {
				$output .= '<li class="page-item"><a href="' . $href . '">' . $i . '</a></li>';
			    $output .= '<li class="page-item dots disabled"><a href="#" aria-disabled="true">...</a></li>';
			}			
		}

		//
		for ($i = $start; $i <= $end && $i <= $total_pages; $i++) {
			if ($i == $current) {
                if ($output_type == 'achor') {
                    $output .= '<a href="#" class="page-item">' . $i . '</a>';
                } else {
                    $output .= '<li class="page-item active"><a href="#">' . $i . '</a></li>';
                }
			} else {
				$args['page_num'] = $i;
				$href = kahuk_create_permalink($args);

                if ($output_type == 'achor') {
                    $output .= '<a href="' . $href . '" class="page-item">' . $i . '</a>';
                } else {
                    $output .= '<li class="page-item"><a href="' . $href . '">' . $i . '</a></li>';
                }				
			}
		}

		//
		if ($total_pages > $end) {
			$i = $total_pages;

			$args['page_num'] = $i;
			$href = kahuk_create_permalink($args);

            if ($output_type == 'achor') {
                $output .= '<a href="#" class="page-item" aria-disabled="true">...</a>';
			    $output .= '<a href="' . $href . '" class="page-item">' . $i . '</a>';
            } else {
                $output .= '<li class="page-item dots disabled"><a href="#" aria-disabled="true">...</a></li>';
			    $output .= '<li class="page-item"><a href="' . $href . '">' . $i . '</a></li>';
            }
		}
		
		//
		if ($current < $total_pages) {
			$i = $current + 1;

			$args['page_num'] = $i;
			$href = kahuk_create_permalink($args);

            if ($output_type == 'achor') {
                $output .= '<a href="' . $href . '" class="page-item"> ' . $args['next_text'] . '</a>';
            } else {
                $output .= '<li class="page-item"><a href="' . $href . '"> ' . $args['next_text'] . '</a></li>';
            }
		} else {
            if ($output_type == 'achor') {
                $output .= '<a href="#" class="page-item next-item" aria-disabled="true">' . $args['next_text'] . '</a>';
            } else {
                $output .= '<li class="page-item next-item disabled"><a href="#" aria-disabled="true">' . $args['next_text'] . '</a></li>';
            }
		}

		// Very Last Page Item
		if ($args['first_last']) {
			if ($total_pages > $current) {
				$args['page_num'] = $total_pages;
				$href = kahuk_create_permalink($args);

				if ($output_type == 'achor') {
					$output .= sprintf("<a href=\"%s\" class=\"page-item last-item\">%s</a>", $href, $args['last_text']);
				} else {
					$achor = sprintf("<a href=\"%s\">%s</a>", $href, $args['last_text']);
					$output .= sprintf("<li class=\"page-item last-item\">%s</li>", $achor);
				}
			}
		}
	}

    //
	$markup = '';

	if ($output_type == 'achor') {
		$markup = sprintf("<div class=\"%s\">%s</div>", $args['wrapper_cls'], $output);
	} else {
        $markup = sprintf("<ul class=\"%s\">%s</ul>", $args['wrapper_cls'], $output);
    }

	return $markup;
}
