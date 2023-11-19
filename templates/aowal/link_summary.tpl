{php}global $hooks;{/php}
{php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_story_start");{/php}
<article itemtype="http://schema.org/Article" 
    class="story-{$story.link_id} story article" 
    id="story-{$story.link_id}" data-id="{$story.link_id}" 
    data-title="{$story.link_title|htmlentities}" 
    data-status="{$story.link_status}"
>
    <div class="story-content">
        {if $pagename eq "story"} {* Single Story Page *}
            <h1 class="story-title" {if $story.is_rtl}dir="rtl"{else}dir="ltr"{/if}>
                {$story.link_title|htmlentities}
            </h1>
        {else} {* Story Listing Page *}
            <h2 class="story-title" {if $story.is_rtl}dir="rtl"{else}dir="ltr"{/if}>
                <a title="{$story.link_title|htmlentities}" href="{$story.story_url}">
                    {$story.link_title}
                </a>
            </h2>
        {/if}

        <div class="story-meta flex flex-wrap items-center text-sm">
            <a class="author-name" href="{$story.author_url}" title="{$story.user_login} (Karma #{$story.user_karma})" itemprop="author name" itemtype="http://schema.org/Person">
                <i class="icon icon-user"></i> {$story.user_login}
            </a>

            <span itemprop="datePublished">
                <i class="icon icon-clock"></i> {$story.submit_timeago} ago
            </span>

            <span itemprop="story-karma">
                <i class="icon icon-bolt"></i> <span class="story-karma story-karma-{$story.link_id}">{$story.link_karma}</span>
            </span>

            <span itemprop="tags">
                <i class="icon icon-tags"></i> <a href="{$story.category_primary.url}">{$story.category_primary.category_name}</a>
            </span>

            {if $user_authenticated}
                {if $story.user_acted.saved_story}
                    <button class="fork" data-do="unfork">
                        <i class="icon icon-heart-1"></i> Forked
                    </button>
                {else}
                    <button class="fork" data-do="fork">
                        <i class="icon icon-heart-o"></i> Fork
                    </button>
                {/if}
            {else}
                <button class="fork require-login" data-do="fork"
                    data-bs-toggle="modal" data-bs-target="#LoginModal" 
                >
                    <i class="icon icon-heart-o"></i> Fork
                </button>
            {/if}

            {if ($pagename eq "story")}
                <span class="story-link-out">
                    <i class="icon icon-globe"></i> 
                    <a href="{$story.link_url}" 
                        {if $open_in_new_window eq true}target="_blank" rel="noopener noreferrer"{/if}
                        {if $pagename eq "story" && $link_nofollow eq "1" && $story.link_status neq 'published'}
                            rel="nofollow"
                        {elseif $pagename eq "stories"}
                            rel="nofollow"
                        {/if}
                        {if $open_in_new_window eq true}
                            target="_blank" rel="noopener noreferrer"
                        {/if}
                    >
                        {$story.story_url_domain}
                    </a>
                </span>
            {/if}

            <span class="story-comments-count {if $story.comment_count gt 0}has-comments{/if}">
                <i class="icon icon-comments-o"></i> 
                {if $story.comment_count gt 0}
                    <a href="{$story.story_url}#comments">{$story.comment_count} {#KAHUK_MiscWords_Comment#}</a>
                {else}
                    <a href="{$story.story_url}#discuss">{#KAHUK_MiscWords_Discuss#}</a>
                {/if}
            </span>

            {if $enable_group && $user_authenticated}
                {if $story.meta.shareable_groups neq ''}
                    <div class="group-share-tool">
                        <button class="dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="icon icon-users"></i> 
                            <span class="visually-inline">{#KAHUK_Visual_Group_Share#}</span>
                        </button>
                        <ul class="dropdown-menu">
                            {$story.meta.shareable_groups}
                        </ul>
                    </div>
                {/if}
            {/if}

            {if $user_authenticated && ($session_user_level == "admin" || $session_user_level == "moderator" || ($story.link_author == $session_user_id))}
                <div class="admin-links">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="icon icon-cogs"></i>
                        <span class="visually-hidden">Story settings</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{$story.story_edit}">
                                <i class="icon icon-pencil-alt"></i> {#KAHUK_Visual_LS_Admin_Edit#}
                            </a>
                        </li>

                        {if $session_user_level == "admin"}
                            <li>
                                <a class="dropdown-item" href="{$kahuk_base_url}/admin/admin_users.php?mode=view&user={$story.user_login}">
                                    <i class="icon icon-user-cog"></i> {#KAHUK_Visual_Comment_Manage_User#} {$story.user_login}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="{$kahuk_base_url}/admin/admin_users.php?mode=killspam&user={$story.user_login}">
                                    <i class="icon icon-ban"></i> {#KAHUK_Visual_View_User_Killspam#}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{$kahuk_base_url}/delete.php?link_id={$story.link_id}"
                                    title="This function deletes the story,&#10;all its votes and comments&#10;and removes it from&#10;the groups and saved stories!">
                                    <i class="icon icon-trash-alt"></i> {#KAHUK_Visual_AdminPanel_Discard#}
                                </a>
                            </li>
                        {/if}
                    </ul>
                </div><!-- /.admin-links -->
            {/if}
        </div><!-- /.story-meta -->

        {if $viewtype neq "short"}
            <div class="user-text mt-4">
                {if $show_content neq 'FALSE'}
                    {$story.link_content|kahuk_autop}
                {/if}
            </div>
        {/if}
    </div>

    {php}
        $reaction_positive_cls = "reaction-positive btn-reaction";
        $reaction_negative_cls = "reaction-negative btn-reaction";
    {/php}
    <div id="xreaction-{$story.link_id}" 
        class="flex gap-2" 
        itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating"
    >
        {if !$user_authenticated}
            <button class="{php} echo $reaction_positive_cls; {/php} require-login" data-bs-toggle="modal" 
                data-bs-target="#LoginModal"
            >
                <div class="visually-hidden">Like</div>
                <i class="icon icon-thumbs-up"></i>
                <span class="counter">{$story.reactions_count.positive}</span>
            </button>

            <button class="{php} echo $reaction_negative_cls; {/php} require-login" data-bs-toggle="modal" 
                data-bs-target="#LoginModal"
            >
                <div class="visually-hidden">Dislike</div>
                <i class="icon icon-thumbs-down"></i>
                <span class="counter">{$story.reactions_count.negative}</span>
            </button>
        {else}
            {php}
                global $story;

                if ($story['user_acted']['did_react']) {
                    if ($story['user_acted']['vote_reaction'] == "positive") {
                        $reaction_positive_cls .= " btn-active";
                    } else if ($story['user_acted']['vote_reaction'] == "negative") {
                        $reaction_negative_cls .= " btn-active";
                    }
                }
            {/php}
            <button class="{php}echo $reaction_positive_cls;{/php}" 
                data-do="reaction" data-reaction="positive"
            >
                <div class="visually-hidden">Like</div>
                <i class="icon icon-thumbs-up"></i>
                <span class="counter">{$story.reactions_count.positive}</span>
            </button>

            <button class="{php}echo $reaction_negative_cls;{/php}" 
                data-do="reaction" data-reaction="negative"
            >
                <div class="visually-hidden">Dislike</div>
                <i class="icon icon-thumbs-down"></i>
                <span class="counter">{$story.reactions_count.negative}</span>
            </button>
        {/if}
    </div><!-- / #xreaction-{$story.link_id} -->
</article>
{php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_story_end");{/php}
