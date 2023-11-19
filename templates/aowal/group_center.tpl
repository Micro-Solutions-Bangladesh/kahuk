<div class="main-content col mt-base">
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if $session_messages  neq ''}
		<div class="mb-base">
            {include file=$the_template"/template-parts/session-messages.tpl"}
		</div>
    {/if}

    {if $enable_group eq "true"}
        {include file=$the_template."/group_summary.tpl"}

        <div class="tabs-wrapper">
            <ul class="tabs-header list-caret list-caret-active dark-combination " role="tablist">
                <li class="tabs-item {if $groupview eq "trending"}active{/if}" role="presentation">
                    <a href="{$group_view_trending}" class="tabs-link">
                        <span>{#KAHUK_Visual_Group_Trending#}</span>
                    </a>
                </li>
                <li class="tabs-item {if $groupview eq "published"}active{/if}" role="presentation">
                    <a href="{$group_view_published}" class="tabs-link {if $groupview eq "published"}active{/if}">
                        <span>{#KAHUK_Visual_Group_Published#}</span>
                    </a>
                </li>
                <li class="tabs-item {if $groupview eq "shared"}active{/if}" role="presentation">
                    <a href="{$group_view_shared}" class="tabs-link">
                        <span>{#KAHUK_Visual_Group_Shared#}</span>
                    </a>
                </li>
                <li class="tabs-item {if $groupview eq "members"}active{/if}" role="presentation">
                    <a href="{$group_view_members}" class="tabs-link">
                        <span class="active">{#KAHUK_Visual_Group_Member#}</span>
                    </a>
                </li>
            </ul>

            <div class="tabs-content">
            {if $group_privacy eq "private" && ($session_user_level neq 'admin' && $session_user_level neq 'moderator' && $user_role_group eq 'admin' && $user_role_group eq 'moderator' && $is_group_member eq 0 && ($is_member_active eq "inactive" || $is_member_active eq ""))}
                {#KAHUK_Visual_Submit_Group_Private#} {#KAHUK_Visual_Group#}
            {else}
                {if $groupview eq "trending"}
                    {if !empty($groupViewContent)}
                        {$groupViewContent}
                        <div class="pagination-wrapper mt-6">
                            {$pagination}
                        </div>
                    {else}
                        <p><strong>{#KAHUK_Visual_Group_No_Trending_Stories#}</strong></p>
                    {/if}
                {elseif $groupview eq "published"}
                    {if !empty($groupViewContent)}
                        {$groupViewContent}
                        <div class="pagination-wrapper mt-6">
                            {$pagination}
                        </div>
                    {else}
                        <strong>{#KAHUK_Visual_Group_No_Published_Stories#}</strong>
                    {/if}
                {elseif $groupview eq "shared"}
                    {if !empty($groupViewContent)}
                        {$groupViewContent}
                        <div class="pagination-wrapper mt-6">
                            {$pagination}
                        </div>
                    {else}
                        <strong>{#KAHUK_Visual_Group_No_Shared_Stories#}</strong>
                    {/if}
                {elseif $groupview eq "members"}
                    {if !empty($groupViewContent)}
                        <div class="flex flex-wrap -space-x-4 overflow-hidden">
                            {$groupViewContent}
                        </div>
                    {else}
                        <strong>Currently no members found in the group!</strong>
                    {/if}
                {/if}
            {/if}
            </div>
        </div>
    {/if}
</div>