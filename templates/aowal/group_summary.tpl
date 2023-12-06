{php}global $hooks;{/php}
<div class="group group-{$group.group_id} group-{$group.group_status} row mb-6">
    <div class="col-auto">
        <a href="{$group.meta.permalink}" title="{$group.group_name} Avatar">
            <img src="{$group.meta.avatar_image}" alt="Group Avatar" class="group-img"
                width="{$group.meta.avatar_size_with}px" />
        </a>
    </div>
    <div class="col">
        {if $pagename eq 'group'}
            <h1 class="story-title">{$group.group_name}</h1>
        {else}
            <h2 class="story-title">
                <a href="{$group.meta.permalink}">{$group.group_name}</a>
            </h2>
        {/if}

        <div class="story-meta flex flex-wrap items-center text-sm">
            {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_group_list_start");{/php}
            <a class="author-name" href="{$group.meta.creator_profile.user_profile_url}" title="{#KAHUK_Visual_Group_Created_By#} {$group.meta.creator_profile.user_names}" itemprop="author name" itemtype="http://schema.org/Person">
                <i class="icon icon-user"></i> {$group.meta.creator_profile.user_login}
            </a>

            {* <span>{#KAHUK_Visual_Group_Created_On#} {$group.group_date|date_format:'%Y-%m-%d %H:%M:%S'}</span> *}
            <span>
                <i class="icon icon-clock"></i> {$group.meta.submit_timeago} ago
            </span>

            <span>
                <i class="icon icon-users"></i> {$group.meta.count_active_members} {if $group.meta.count_active_members lt 2}Member{else}{#KAHUK_Visual_Group_Member#}{/if}
            </span>
        </div>

        <div class="user-text mt-4">
            {$group.group_description|kahuk_autop}
        </div>

        {if $pagename eq 'group'}
            <div class="group-footer child-ml-2 flex flex-wrap items-center mt-4">
                {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_group_list_end");{/php}

                {if $user_authenticated}
                    {* Add Approve/Enable buttons for Admin/Moderator *}
                    {if ($session_user_level == 'admin') || ($session_user_level == 'moderator')}
                        {if ($group.group_status eq 'pending')}
                            <a href="{$group_approve_url}">
                                <i class="icon icon-check"></i> Approve
                            </a>
                        {elseif ($group.group_status eq 'disable')}
                            {if ($session_user_level == 'admin')}
                                <a href="{$group_approve_url}">
                                    <i class="icon icon-check"></i> Enable
                                </a>
                            {else}
                                <a href="#" class="skip-action">
                                    <i class="icon icon-check"></i> Enable
                                </a>
                            {/if}
                        {/if}
                    {/if}

                    {* Add Edit button for Admins/Moderators *}
                    {if ($session_user_membership && ($session_user_membership.member_role == "admin")) || ($session_user_level == 'admin') || ($session_user_level == 'moderator')}
                        <a href="{$group_edit_url}">
                            <i class="icon icon-pencil-alt"></i> {#KAHUK_Visual_Group_Text_edit#}
                        </a>
                    {/if}

                    {* Add Join/Unjoin button for loggedin users *}
                    {if ($group.group_status eq 'enable')}
                        {if $session_user_membership}{* User is a member of the group *}
                            {if $session_user_membership.member_status eq 'active'}
                                <a href="{$unjoin_group_url}" title="{#KAHUK_Visual_Group_Unjoin#}">
                                    <i class="icon icon-sign-out-alt"></i> {#KAHUK_Visual_Group_Unjoin#}
                                </a>
                            {elseif $session_user_membership.member_status eq 'requested'}
                                <a href="#" class="skip-action" title="Group join requested">
                                    <i class="icon icon-sign-in-alt"></i> Requested
                                </a>
                                <a href="{$join_group_withdraw}" title="{#KAHUK_Visual_Group_Withdraw_Request#}">
                                    <i class="icon icon-sign-out-alt"></i> {#KAHUK_Visual_Group_Withdraw_Request#}
                                </a>
                            {elseif $session_user_membership.member_status eq 'banned' || $session_user_membership.member_status eq 'inactive'}
                                {* Join/Unjoin/Withdraw - No button to display for inactive and, banned user *}
                            {/if}
                        {else}
                            <a href="{$join_group_url}" title="{#KAHUK_Visual_Group_Join#}" {if $user_groups_total gt $max_groups_to_join}class="max-group-reached"{/if}>
                                <i class="icon icon-sign-in-alt"></i> {#KAHUK_Visual_Group_Join#}
                            </a>
                        {/if}
                    {/if}                    

                    {if ($session_user_level == 'admin')}
                        <form method="POST" class="m-0" action="{$group_delete_url}">
                            {$hidden_token_delete_group}
                            <input type="hidden" name="action" value="groupdelete">
                            <button type="submit" onclick="return confirm('{#KAHUK_Visual_Group_Delete_Confirm#}')">
                                <i class="icon icon-trash-alt"></i> {#KAHUK_Visual_Group_Text_Delete#}
                            </button>
                        </form>
                    {/if}

                    {if !empty($session_user_membership)}
                        <a href="{$group_submit_url}" title="{#KAHUK_LANG_SUBMIT_A_Story#}">
                            <i class="icon icon-plus"></i> {#KAHUK_LANG_SUBMIT_A_Story#}
                        </a>
                    {/if}
                {else}
                    <a href="{$permalink}" data-bs-toggle="modal" data-bs-target="#LoginModal" title="{#KAHUK_Visual_Group_Join#}">
                        <i class="icon icon-sign-in-alt"></i> {#KAHUK_Visual_Group_Join#}
                    </a>
                    <a href="{$group_submit_url}" data-bs-toggle="modal" data-bs-target="#LoginModal" title="{#KAHUK_LANG_SUBMIT_A_Story#}">
                        <i class="icon icon-plus"></i> {#KAHUK_LANG_SUBMIT_A_Story#}
                    </a>
                {/if}
            </div>
        {/if}
    </div>
</div>
