<div class="main-content col mt-base"> {* todo: upgrade *}
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>


    <div class="user-profile flex flex-col items-center">
        <div class="gravatar">
            <img src="{$user.user_gravatars.medium}" class="w-48 rounded-full" />
        </div>

        <a class="name text-xl font-medium mt-base-half" href="{$user.user_profile_url}">{$user.user_names}</a>

        <div class="following-followers following-followers-{$user.user_id} child-ml-2 flex flex-wrap items-center mt-4">
            <a href="{$url_user_followers}" class="followers hover:underline"><strong>{$followers.count}</strong> <span>{#KAHUK_LANG_FOLLOWERS#}</span></a>
            <a href="{$url_user_following}" class="following hover:underline"><strong>{$following.count}</strong> <span>{#KAHUK_LANG_FOLLOWING#}</span></a>

            {if $user_authenticated}
                {if $user.user_id eq $session_user_id}

                {else}
                    {if ($friendship_type eq 'mutual') || ($friendship_type eq 'following')}
                        <button class="follow-unfollow capitalize" data-do="unfollow" data-user="{$user.user_login}">{#KAHUK_LANG_UNFOLLOW#}</button>
                    {else}
                        <button class="follow-unfollow capitalize" data-do="follow" data-user="{$user.user_login}">{#KAHUK_LANG_FOLLOW#}</button>
                    {/if}

                    {if ($friendship_type eq 'mutual') || ($friendship_type eq 'follower')}
                        <button class="badge pop-follower capitalize">{#KAHUK_LANG_FOLLOWS_YOU#}</button>
                    {/if}
                    
                {/if}
            {else}
                <button class="follow-unfollow capitalize" {$login_special_attribute}>{#KAHUK_LANG_FOLLOW#}</button>
            {/if}
        </div>
        {* todo <div class="social mb-4"></div> *}
    </div>

    <div class="mt-8">
        <div class="row">
            <div id="stats" class="author-{$user.user_id} col-12 md:col-6 my-4">
                <h3 class="block-header">{#KAHUK_Visual_User_Profile_User_Stats#}</h3>

                <table class="table header-center col-bordered">
                    <tbody>
                        {if $user.user_karma > "0.00"}
                            <tr>
                                <td><strong>{#KAHUK_Visual_Rank#}:</strong></td>
                                <td>{$user.user_rank}</td>
                            </tr>
                            <tr>
                                <td><strong>{#KAHUK_Visual_User_Profile_KarmaPoints#}:</strong></td>
                                <td><span class="author-karma  author-karma-{$user.user_id}">{$user.user_karma|number_format:"0"}</span></td>
                            </tr>
                        {/if}
                        <tr>
                            <td><strong>{#KAHUK_Visual_User_Profile_Joined#}:</strong></td>
                            <td width="120px">
                                {$user.user_date_formatted}<br>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{#KAHUK_Visual_User_Profile_Total_Links#}:</strong></td>
                            <td>{$user_stories_total}</td>
                        </tr>
                        <tr>
                            <td><strong>{#KAHUK_Visual_User_Profile_Published_Links#}:</strong></td>
                            <td>{$user_stories_published_total}</td>
                        </tr>
                        <tr>
                            <td><strong>{#KAHUK_Visual_User_Profile_Total_Comments#}:</strong></td>
                            <td>{$user_total_comments}</td>
                        </tr>
                        <tr>
                            <td><strong>{#KAHUK_Visual_User_Profile_Total_Votes#}:</strong></td>
                            <td>{$user_total_votes}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            

            {if $enable_group eq "true"}
                <div id="groups" class="col-12 md:col-6 my-4">
                    <table class="table header-center col-bordered">
                        <thead class="table_title">
                            <tr>
                                <th>{#KAHUK_Visual_AdminPanel_Group_Name#}</th>
                                {if !empty($group_display)}<th style="width:60px;text-align:center;">
                                    {#KAHUK_Visual_Group_Member#}</th>{/if}
                            </tr>
                        <tbody>
                            {if empty($group_display)}
                                <tr>
                                    <td colspan="2">
                                        {#Kahuk_Profile_No_Membership#}
                                    </td>
                                </tr>
                            {else}
                                {$group_display}
                            {/if}
                        </tbody>
                    </table>
                </div>
            {/if}

            <div id="following" class="col-12 md:col-6 my-4">
                <h3 class="block-header">{#KAHUK_LANG_FOLLOWING#}</h3>
                <div class="box-content py-base-half">
                    {if $following.count gt 0}
                        <div class="flex flex-wrap -space-x-4 overflow-hidden">
                        {foreach from=$following.users item=row}
                            <a href="{$row.user_profile_url}" title="{$row.user_names}">
                                <img src="{$row.user_gravatars.medium}" alt="{$row.user_login}"
                                    class="profile-img inline-block h-16 w-16 rounded-full ring-2 ring-white mt-4" />
                            </a>
                        {/foreach}
                        </div>
                    {else}
                        <p class="text-center mb-0">No Following!</p>
                    {/if}
                </div>
            </div>

            <div id="followers" class="col-12 md:col-6 my-4">
                <h3 class="block-header">{#KAHUK_LANG_FOLLOWERS#}</h3>
                <div class="box-content py-base-half">
                    {if $followers.count gt 0}
                        <div class="flex flex-wrap -space-x-4 overflow-hidden">
                        {foreach from=$followers.users item=row}
                            <a href="{$row.user_profile_url}" title="{$row.user_names}">
                                <img src="{$row.user_gravatars.medium}" alt="{$row.user_login}"
                                    class="profile-img inline-block h-16 w-16 rounded-full ring-2 ring-white mt-4" />
                            </a>
                        {/foreach}
                        </div>
                    {else}
                        <p class="text-center mb-0">No Follower!</p>
                    {/if}
                </div>
            </div>

            <div class="col-12 my-4">
                <h3 class="block-header">{#KAHUK_LANG_STORIES#}</h3>

                <div class="stories mt-base">
                    {$stories_markup}
                </div><!-- /.stories -->

                {if $pagination neq ''}
                    <div class="pagination-wrapper mt-6">
                        {$pagination}
                    </div>
                {/if}
            </div>
        </div>
    </div>





    {* {if isset($user_page)}
        {$user_page}
        {if $user_page eq ''}
            <div class="jumbotron" style="padding:15px 25px;">
                <p style="padding:0;margin:0;font-size:1.1em;">{#KAHUK_User_Profile_No_Content#}</p>
            </div>
        {/if}
    {/if}

    {if isset($user_pagination) && $user_page neq ''}
        {$user_pagination}
    {/if}

    {checkActionsTpl location="tpl_kahuk_profile_end"} *}

</div>