{************************************ user_navigation.tpl *************************************}

{checkActionsTpl location="tpl_kahuk_profile_start"}

<div class="row user_navigation_top">
    {checkActionsTpl location="tpl_kahuk_profile_begin"}

    <div class="col-md-12 user_navigation">
        <h1 class="user_name">
			<div class="img-thumbnail avatar_thumb">
				<img class="profile_avatar" src="{$Avatar.large}" alt="Avatar" />
			</div>

            {checkActionsTpl location="tpl_kahuk_profile_username_start"}
			{$user_username|capitalize}
			{checkActionsTpl location="tpl_kahuk_profile_username_end"}
        </h1>

        {checkActionsTpl location="tpl_kahuk_profile_userdetails_start"}
        <div class="user_details">
            {if $user_publicemail ne ""}
                <i class="fa fa-envelope-o"></i>
                {php}
                // Method to try to trick automated email address collection bots
                global $main_smarty;
                $full_email = $this->_vars['user_publicemail'];
                list($email_start,$_) = explode('@',$full_email); $email_domain = ''.$_;
                $main_smarty->assign('email_start', $email_start);
                $main_smarty->assign('email_domain', $email_domain);
                {/php}
                <script type="text/javascript">
                    <!--
                var string1 = "{$email_start}";
                var string2 = "@";
                var string3 = "{$email_domain}";
                //  document.write(string4);
                document.write("<a href=" + "mail" + "to:" + string1 + string2 + string3 +
                    " id='user_email'>Email</a> | ");
                //
                -->
                </script>
            {/if}

            {if ($user_url neq '' && $user_karma > "20") || ($user_url neq '' && $user_login eq $user_logged_in)}
                <i class="fa fa-home"></i>
                <a href="{$user_url}" target="_blank" rel="nofollow noopener noreferrer" id="user_website">{$user_url}</a>
                <br />
            {/if}

            {checkActionsTpl location="tpl_user_profile_details_start"}

            <span class="user_description_text">
                {if $user_names != ""}
                    {$user_names}
                    {if $user_occupation != "" || $user_location != ""} is {/if}
                {/if}
                {if $user_occupation != ""}
                    {if $user_names != ""} a {/if}
                    {$user_occupation}
                {/if}
                {if $user_location != ""}
                    {if $user_occupation != "" || $user_names != ""}
                        from
                    {elseif $user_location != ""}
                        From
                    {/if}
                    {$user_location}
                {/if}
            </span>

            {checkActionsTpl location="tpl_user_profile_details_end"}
        </div>

        {checkActionsTpl location="tpl_kahuk_profile_userdetails_end"}

        <div class="user_social">
            {checkActionsTpl location="tpl_user_profile_social_start"}

            {if $user_skype}
                <a href="callto://{$user_skype}" title="Skype {$user_username|capitalize}"
                    rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i
                            class="fa fa-circle fa-stack-2x opacity_reset" style="color:#00aaf1;"></i><i
                            class="fa fa-skype fa-stack-1x fa-inverse opacity_reset"></i></span></a>
            {/if}
            {if $user_facebook}
                <a href="http://www.facebook.com/{$user_facebook}" title="{$user_username|capitalize} on Facebook"
                    rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i
                            class="fa fa-circle fa-stack-2x opacity_reset" style="color:#3c5b9b;"></i><i
                            class="fa fa-facebook fa-stack-1x fa-inverse opacity_reset"></i></span></a>
            {/if}
            {if $user_twitter}
                <a href="http://twitter.com/{$user_twitter}" title="{$user_username|capitalize} on Twitter"
                    rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i
                            class="fa fa-circle fa-stack-2x opacity_reset" style="color:#2daae1;"></i><i
                            class="fa fa-twitter fa-stack-1x fa-inverse opacity_reset"></i></span></a>
            {/if}
            {if $user_linkedin}
                <a href="http://www.linkedin.com/in/{$user_linkedin}" title="{$user_username|capitalize} on LinkedIn"
                    rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i
                            class="fa fa-circle fa-stack-2x opacity_reset" style="color:#0173b2;"></i><i
                            class="fa fa-linkedin fa-stack-1x fa-inverse opacity_reset"></i></span></a>
            {/if}
            {if $user_googleplus}
                <a href="https://plus.google.com/{$user_googleplus}" title="{$user_username|capitalize} on Google+"
                    rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i
                            class="fa fa-circle fa-stack-2x opacity_reset" style="color:#f63e28;"></i><i
                            class="fa fa-google-plus fa-stack-1x fa-inverse opacity_reset"></i></span></a>
            {/if}
            {if $user_pinterest}
                <a href="http://pinterest.com/{$user_pinterest}/" title="{$user_username|capitalize} on Pinterest"
                    rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i
                            class="fa fa-circle fa-stack-2x opacity_reset" style="color:#cb2027;"></i><i
                            class="fa fa-pinterest fa-stack-1x fa-inverse opacity_reset"></i></span></a>
            {/if}

            {checkActionsTpl location="tpl_user_profile_social_end"}
        </div>

        {checkActionsTpl location="tpl_show_extra_profile"}

        {if $Allow_Friends neq "0"}
            <div class="btn-group user_followers">
                <a class="btn btn-default btn-sm" href="{$user_url_friends}"><i class="fa fa-user"></i> {$user_following}
                    {#KAHUK_Visual_User_Profile_View_Friends#}</a>
                <a class="btn btn-default btn-sm" href="{$user_url_friends2}"><i class="fa fa-user"></i> {$user_followers}
                    {#KAHUK_Visual_User_Profile_Your_Friends#}</a>
                {if ($is_friend eq "mutual") && $user_login neq $user_logged_in}
                    {if check_for_enabled_module('simple_messaging',2.0) && $is_friend && $user_login neq $user_logged_in}
                        <a class="btn btn-default btn-sm"
                            href="{$kahuk_base_url}/module.php?module=simple_messaging&view=compose&to={$username}&return={$my_kahuk_base}%2Fuser.php%3Flogin%3D{$user_logged_in}%26view%3Dfollowers"><i
                                class="fa fa-envelope"></i> Send Message</a>
                    {/if}
                {/if}
                {* To fix a bug that was displaying the wrong button (follow/unfollow) under the users avatar and beside the following and followers buttons in the user profile page. See the picture http://mymonalisasmile.com/upload/follow-unfollow-buttons.jpg*}
                {if ($is_friend eq "following" || $is_friend eq "mutual") && $user_login neq $user_logged_in}
                    <a href="{$user_url_remove}" class="btn btn-sm btn-danger">{#KAHUK_Unfollow#} {$user_login|capitalize} </a>
                    {if $user_authenticated eq true}
                        {checkActionsTpl location="tpl_user_center"}
                    {/if}
                    {* To fix a bug that was displaying the wrong button (follow/unfollow) under the users avatar and beside the following and followers buttons in the user profile page. See the picture http://mymonalisasmile.com/upload/follow-unfollow-buttons.jpg*}
                {elseif ($is_friend eq "" || $is_friend eq "follower") && $user_login neq $user_logged_in && $user_authenticated neq false}
                    <a class="btn btn-sm btn-success" href="{$user_url_add}">{#KAHUK_Visual_User_Profile_Add_Friend#}
                        {$user_login|capitalize}</a>
                {/if}
                <a class="btn btn-primary btn-sm" href="#user_search_modal" data-toggle="modal"><i
                        class="fa fa-search white_text"></i></a>

                {* User search modal *}
                <div class="modal fade" id="user_search_modal">
                    <div class="modal-dialog">
                        <div class="modal-content user_search">
                            <form action="{$my_kahuk_base}/user.php" class="form-inline" role="form" method="get" {php}
                                global $URLMethod, $my_base_url, $my_kahuk_base; if ($URLMethod==2)
                                print "onsubmit='document.location.href=\"
                            {$kahuk_base_url}/user/search/\"+encodeURIComponent(this.keyword.value); return
                            false;'";{/php}>
                                <input type="hidden" name="view" value="search">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                    <h4 class="modal-title">{#KAHUK_Visual_User_Search_Users#}</h4>
                                </div>
                                <div class="modal-body">
                                    <input type="text" name="keyword" class="form-control" autofocus="autofocus"
                                        placeholder="{#KAHUK_Visual_User_Search_Users#}">
                                    <button type="submit" class="btn btn-primary">{#KAHUK_Visual_Search_Go#}</button>
                                </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>
        {/if}
    </div>

    <div class="clearfix"></div>
</div>

{checkActionsTpl location="tpl_user_center_just_below_header"}

<ul class="nav nav-tabs" id="profiletabs">
    {checkActionsTpl location="tpl_kahuk_profile_sort_start"}

    <li {if $user_view eq 'profile' || $user_view eq 'removefriend' || $user_view eq 'addfriend'}class="active" {/if}><a
            {if $user_view eq 'profile'}data-toggle="tab" href="#personal_info" 
            {else}href="{$user_url_personal_data2}"
            {/if}>{#KAHUK_Visual_User_PersonalData#}</a></li>
    {if $user_login eq $user_logged_in || isset($isadmin) && $isadmin eq 1}
        <li {if $pagename eq 'user_edit'}class="active" {/if}><a href="{$URL_Profile2}">{#KAHUK_Visual_User_Setting#}</a>
        </li>
    {/if}

    {checkActionsTpl location="tpl_kahuk_profile_sort_middle"}

    <li {if $user_view eq 'history'}class="active" {/if}><a
            href="{$user_url_news_sent2}">{#KAHUK_Visual_User_NewsSent#}</a></li>
    <li {if $user_view eq 'published'}class="active" {/if}><a
            href="{$user_url_news_published2}">{#KAHUK_Visual_User_NewsPublished#}</a></li>
    <li {if $user_view eq 'new'}class="active" {/if}><a
            href="{$user_url_news_unpublished2}">{#KAHUK_Visual_User_NewsUnPublished#}</a></li>
    {if $user_login eq $user_logged_in}
        <li {if $user_view eq 'draft'}class="active" {/if}><a href="{$user_url_draft2}">{#KAHUK_Visual_User_NewsDraft#}</a>
        </li>
        <li {if $user_view eq 'scheduled'}class="active" {/if}><a
                href="{$user_url_scheduled2}">{#KAHUK_Visual_User_NewsScheduled#}</a></li>
    {/if}
    <li {if $user_view eq 'commented'}class="active" {/if}><a
            href="{$user_url_commented2}">{#KAHUK_Visual_User_NewsCommented#}</a></li>
    <li {if $user_view eq 'upvoted'}class="active" {/if}><a
            href="{$user_url_news_upvoted2}">{#KAHUK_Visual_UpVoted#}</a></li>
    <li {if $user_view eq 'downvoted'}class="active" {/if}><a
            href="{$user_url_news_downvoted2}">{#KAHUK_Visual_DownVoted#}</a></li>
    {* to only show the saved links tab to the user owner of the profile. *}
    {if $user_login eq $user_logged_in}
        <li {if $user_view eq 'saved'}class="active" {/if}><a href="{$user_url_saved2}">{#KAHUK_Visual_User_NewsSaved#}</a>
        </li>
    {/if}

    {checkActionsTpl location="tpl_kahuk_profile_sort_end"}
</ul>

{checkActionsTpl location="tpl_kahuk_profile_end"}
<!--/user_navigation.tpl -->