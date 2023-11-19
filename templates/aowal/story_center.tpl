{php}global $hooks;{/php}
<div class="main-content col mt-base">
    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_start_single_story");{/php}

    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if $session_messages  neq ''}
        <div class="mb-base">
            {include file=$the_template"/template-parts/session-messages.tpl"}
        </div>
    {/if}

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_before_single_story");{/php}
    <div class="stories">
        {$the_story}
    </div><!-- /.stories -->
    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_after_single_story");{/php}

    <hr class="star mt-12 mb-6" />

    {if $group_by_reactions}
        <div class="who-voted-wrap flex flex-wrap">
            {if $group_by_reactions.positive}
                <div class="who-liked w-full {if $group_by_reactions.negative}md:w-1/2{/if} mt-6">
                    <h3 class="section-title">{#KAHUK_Visual_Story_WhoVoted#}</h3>

                    <div class="flex flex-wrap -space-x-4 overflow-hidden">
                        {section name=index loop=$group_by_reactions.positive}
                            <a href="{$group_by_reactions.positive[index].user_profile_url}" title="{$group_by_reactions.positive[index].user_names}">
                                <img src="{$group_by_reactions.positive[index].user_gravatars.medium}" alt="{$group_by_reactions.positive[index].user_login}"
                                    class="profile-img inline-block h-16 w-16 rounded-full ring-2 ring-white mt-4" />
                            </a>
                        {/section}
                    </div>
                </div>
            {/if}

            {if $group_by_reactions.negative}
                <div class="who-disliked w-full {if $group_by_reactions.positive}md:w-1/2{/if} mt-6">
                    <h3 class="section-title">{#KAHUK_Visual_Story_Who_Downvoted_Story#}</h3>

                    <div class="flex flex-wrap -space-x-4 overflow-hidden">
                        {section name=index loop=$group_by_reactions.negative}
                            <a href="{$group_by_reactions.negative[index].user_profile_url}" title="{$group_by_reactions.negative[index].user_names}">
                                <img src="{$group_by_reactions.negative[index].user_gravatars.medium}" alt="{$group_by_reactions.negative[index].user_login}"
                                    class="profile-img inline-block h-16 w-16 rounded-full ring-2 ring-white mt-4" />
                            </a>
                        {/section}
                    </div>
                </div>
            {/if}
        </div>
    {/if}

    <hr class="star my-12" />

    <div class="comments-wrap" id="comments">
        {$comments_markup}
    </div>

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_end_single_story");{/php}
</div>
<!-- /.main-content -->

<script language="javascript">
var story_link="{$story_url}";
{literal}
    function show_comments(id){
        document.location.href=story_link+'/'+id+'#comment-'+id;
    }
    function show_replay_comment_form(id){
        document.location.href=story_link+'/reply/'+id+'#comment-reply-'+id;
    }
{/literal}
</script>
