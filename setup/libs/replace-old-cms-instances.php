<?php
/**
 * Replacing plikli instances and output the result
 * 
 * @since 5.0.0
 */
function _kahuk_replace_old_cms_instances( $sql, $tablename ) {
    global $kahukDB;

    mysqli_query( $kahukDB, $sql );

    $msg = sprintf( "Replace/update data in %s, %d rows affected. %s", $tablename, $kahukDB->affected_rows, $sql );
    
    _kahuk_messages_markup( [ $msg ] );
}



/**
 * Replacing plikli instances with kahuk in trackback
 * 
 * @since 5.0.0
 */
function kahuk_replace_old_cms_instances() {
    // global $kahukDB;

    /**
     * Table config
     */
    //
    $sql = "UPDATE `" . table_config . "` SET `var_value` = 'bootstrap' WHERE `var_name` = '\$thetemp';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $sql = "UPDATE `" . table_config . "` set `var_value` = 'kahuk.com', `var_defaultvalue` = 'kahuk.com', `var_optiontext` = 'kahuk.com' WHERE `var_name` = '\$trackbackURL';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $sql = "UPDATE `" . table_config . "` SET `var_desc` = REPLACE(`var_desc`, 'plikli', 'kahuk') WHERE `var_name` = '\$my_base_url';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $sql = "UPDATE `" . table_config . "` SET `var_name` = '\$my_kahuk_base', `var_title` = 'Kahuk Base Folder', `var_desc` = REPLACE(`var_desc`, 'plikli', 'kahuk') WHERE `var_name` = '\$my_plikli_base';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $sql = "UPDATE `" . table_config . "` SET `var_desc` = REPLACE(`var_desc`, 'Plikli', 'Kahuk') WHERE `var_name` = '\$USER_SPAM_RULESET';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $sql = "UPDATE `" . table_config . "` SET `var_desc` = 'Allow users to change Kahuk language<br /><strong>When SET to 1, you have to rename the language file that you want to allow in /languages/ folder.</strong> Ex: <span style=\"font-style:italic;color:#004dff\">RENAME lang_italian.conf.sample</span> to <span style=\"font-style:italic;color:#004dff\">lang_italian.conf</span>' WHERE `var_name` = 'user_language';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $urlMethodValue = ( SEO_FRIENDLY_URL ) ? 2 : 1;

    $sql = "UPDATE `" . table_config . "` SET `var_value` = ". $urlMethodValue . " WHERE `var_name` = '\$URLMethod';";
    _kahuk_replace_old_cms_instances( $sql, table_config );


    // We will use SALT_LENGTH constant from kahuk-configs file
    $sql = "DELETE FROM `" . table_config . "` WHERE `var_name` = 'SALT_LENGTH';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    // TODO
    // we will use my_kahuk_base constant instead of $my_kahuk_base var
    // $sql = "DELETE FROM `" . table_config . "` WHERE `var_name` = '\$my_kahuk_base';";
    // _kahuk_replace_old_cms_instances( $sql, table_config );

    // TODO
    // we will use my_base_url constant instead of $my_base_url var
    // $sql = "DELETE FROM `" . table_config . "` WHERE `var_name` = '\$my_base_url';";
    // _kahuk_replace_old_cms_instances( $sql, table_config );


    //
    $var_title = 'Display What is Kahuk in the sidebar?';
    $var_desc = 'Set it to false if you do not want it to display.<br /><strong>If you want it to display but with your own content, Keep it set to true and edit the language file where the entry is KAHUK_Visual_What_Is_Kahuk and KAHUK_Visual_What_Is_Kahuk_Text under the Sidebar section.<br /><a href="../admin/module.php?module=admin_language" target="_blank" rel="noopener noreferrer">Modify Language</a>';
    $var_name = 'what_is_kahuk';

    $sql = "UPDATE `" . table_config . "` SET `var_desc` = '" . $var_desc . "', `var_name` = '" . $var_name . "', `var_title` = '" . $var_title . "'  WHERE `var_name` = 'what_is_plikli';";
    _kahuk_replace_old_cms_instances( $sql, table_config );

    //
    $var_defaultvalue = 'kahuk_life_';
    $var_desc = 'Table prefix. Ex: kahuk_ makes the users table become kahuk_users. Note: changing this will not automatically rename your tables!';

    $sql = "UPDATE `" . table_config . "` SET `var_defaultvalue` = '" . $var_defaultvalue . "', `var_desc` = '" . $var_desc . "'  WHERE `var_name` = 'table_prefix';";
    _kahuk_replace_old_cms_instances( $sql, table_config );


    /**
     * Table misc_data
     */

    //
    $sql = "UPDATE `" . table_misc_data . "` SET `name` = 'kahuk_version', `data` = '5.0.5'  WHERE `name` = 'plikli_version';";
    _kahuk_replace_old_cms_instances( $sql, table_misc_data );

    //
    $sql = "UPDATE `" . table_misc_data . "` SET `data` = 'https://kahuk.com/mods/version-update.txt'  WHERE `name` = 'modules_update_url';";
    _kahuk_replace_old_cms_instances( $sql, table_misc_data );

    //
    $sql = "UPDATE `" . table_misc_data . "` SET `name` = 'kahuk_update'  WHERE `name` = 'plikli_update';";
    _kahuk_replace_old_cms_instances( $sql, table_misc_data );

    //
    $sql = "UPDATE `" . table_misc_data . "` SET `name` = 'kahuk_update_url', `data` = 'https://kahuk.com/upgrade-cms/'  WHERE `name` = 'plikli_update_url';";
    _kahuk_replace_old_cms_instances( $sql, table_misc_data );

    //
    $sql = "UPDATE `" . table_misc_data . "` SET `data` = 'tpl_kahuk_story_who_voted_start'  WHERE `name` = 'upload_fileplace';";
    _kahuk_replace_old_cms_instances( $sql, table_misc_data );

    //
    $sql = "UPDATE `" . table_misc_data . "` SET `data` = 'tpl_kahuk_profile_tab_insert'  WHERE `name` = 'status_place';";
    _kahuk_replace_old_cms_instances( $sql, table_misc_data );


    /**
     * Table widgets
     */

    //
    $sql = "UPDATE `" . table_widgets . "` SET `name` = 'Kahuk CMS', `folder` = 'kahuk_cms'  WHERE `folder` = 'plikli_cms';";
    _kahuk_replace_old_cms_instances( $sql, table_widgets );

    //
    $sql = "UPDATE `" . table_widgets . "` SET `name` = 'Kahuk News', `folder` = 'kahuk_news'  WHERE `folder` = 'plikli_news';";
    _kahuk_replace_old_cms_instances( $sql, table_widgets );

    /**
     * 
     */

    $tbl_snippets = TABLE_PREFIX . 'snippets';

    $tbl_snippets_exist = kahuk_table_exist( $tbl_snippets );

    if ( $tbl_snippets_exist ) {
        $sql = "UPDATE `" . $tbl_snippets . "` SET `snippet_location` = REPLACE(`snippet_location`, 'plikli', 'kahuk'), `snippet_status` = 0;";
        _kahuk_replace_old_cms_instances( $sql, $tbl_snippets );
    } else {
        // TODO message table snippets not exist to update
    }
    
}
