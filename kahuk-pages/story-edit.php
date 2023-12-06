<?php
$story = kahuk_get_story();

if (!$story) {
    kahuk_redirect_404();
    exit;
}

check_referrer();

include(KAHUK_LIBS_DIR . 'page-submit.php');

$theid = sanitize_number((sanitize_text_field(_get('id'))));

if (
    $story['user_login'] == $session_user_login || 
    in_array($session_user_level, ['admin', 'moderator'])
) {
    $story_url = kahuk_permalink_story($story);

    // Redirect the user if the story no more accepting edit
    if (
        !in_array($session_user_level, ['admin', 'moderator']) && 
        limit_time_to_edit != 0 && 
        (time() - $link->date) / 60 > edit_time_limit
    ) {
        kahuk_set_session_message(
            'Time expired to edit the story!',
            'error'
        );

        kahuk_redirect($story_url);
    }

    //
    include(KAHUK_LIBS_DIR . 'csrf.php');
    $CSRF = new csrf();

    //
    $story_id = sanitize_number(sanitize_text_field(_post('id')));

    if (!empty($story_id) && ($story_id == $story['link_id'])) {
        /**
         * if TOKEN is empty, no need to continue, just display the invalid token error.
         */
        if (empty($_POST['token'])) {
            $CSRF->show_invalid_error(1);
            exit;
        }

        /**
         * if valid TOKEN, proceed. A valid integer must be equal to 2.
         */
        if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'edit_link') == 2) {
            if (isset($_POST["notify"])) {
                // TODO
            }

            $args = [];

            

            /**
             * Admin role can update link_url and link_title_url
             */
            if ($session_user_level == 'admin') {
                $story_url_src = esc_url(_post('url'));

                // $args['link_url'] = $story_url_src;

                $story_check = kahuk_check_unique_story($story_url_src, kahuk_create_slug_story(_post('link_title')), [$story_id]);

                $link_title_url = kahuk_create_slug_story(_post('link_title_url'));
                $link_title_url_hidden = kahuk_create_slug_story(_post('link_title_url_hidden'));

                if ($link_title_url != $link_title_url_hidden) {
                    $storyCountBySlug = kahuk_count_story_by_slug( $link_title_url, ['not_id' => [$story_id]] );

                    if ( MAX_NUMBER_OF_DUPLICATE_STORY_TITLE <= $storyCountBySlug ) {
                        kahuk_set_session_message(
                            'Failed to save story, slug is duplicate!',
                            'notice'
                        );

                        kahuk_redirect($story_url);
                        die();
                    } else {
                        $args['link_title_url'] = $link_title_url;
                    }                    
                }                
            }


            $args['link_title'] = sanitize_text_field(_post('link_title'));

            $args['link_category'] = sanitize_number(sanitize_text_field($_POST['category']));

            $args['link_content'] = kahuk_kses($_POST['bodytext'], $allowed_html_tags);

            if (empty($args['link_content'])) {
                kahuk_set_session_message(
                    'Summary text required some more text!',
                    'error'
                );
        
                kahuk_redirect($story_url);
                die();
            }

            $args['link_summary'] = kahuk_link_summary($args['link_content']);

            //
            $link_group_id = sanitize_number(sanitize_text_field(_post('link_group_id')));

            if ($link_group_id) {
                $args['link_group_id'] = $link_group_id;
            }

            /**
             * Update story
             */
            $globalStories->update($args, $story_id);
            $story = kahuk_get_story($story_id);

            /**
             * Story slug may be changed, so we redirect
             */
            $story_url = kahuk_permalink_story($story);
            $story_url_edit = add_query_arg(['prefix' => 'edit'], $story_url);
            kahuk_redirect($story_url_edit);
            die();
        }
    }

} else {
    // TODO Add a session message for INVALID ACCESS
    kahuk_redirect_404();
    exit;
}

$CSRF->create('edit_link', true, true);

$main_smarty->assign('story', $story);

$story_author = $globalUsersObj->get_user_profile(['user_login' => $story['user_login']]);
$main_smarty->assign('story_author', $story_author);

// 
$group_list = '';

if (enable_group == "true" && $current_user->user_id == $story_author['user_id']) {
    $sql = "
        SELECT group_id,group_name FROM " . table_groups . " 
            LEFT JOIN " . table_group_member . " ON member_group_id=group_id
        WHERE member_user_id = $current_user->user_id 
            AND group_status = 'enable' 
            AND member_status='active' 
            AND (member_role != 'banned' && member_role != 'flagged') 
            ORDER BY group_name ASC";

    $group_membered = $db->get_results($sql);

    if ($group_membered) {
        $group_list .= "<select name='link_group_id' tabindex='3' class='form-control submit_group_select'>";
        $group_list .= "<option value = ''>" . $main_smarty->get_config_vars('KAHUK_Visual_Group_Select_Group') . "</option>";

        foreach ($group_membered as $results) {
            // To select the current group that the story is submitted to
            if ($results->group_id == $story['link_group_id']) {
                $group_list .= "<option value = \"" . $results->group_id . "\" selected>" . $results->group_name . "</option>";
            } else {
                $group_list .= "<option value = \"" . $results->group_id . "\">" . $results->group_name . "</option>";
            }
        }

        $group_list .= "</select>";
    }
}

$main_smarty->assign('group_list', $group_list);

$main_smarty->assign('submit_cat_array', $globalCategoriesObj->get_items());

//
$main_smarty->assign('tpl_center', $the_template . '/edit_submission_center');
