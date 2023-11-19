<div class="main-content col mt-base">
    {if ($session_user_id eq $group.group_creator) || ($session_user_level == "admin")}
        {if $session_messages  neq ''}
            <div class="mb-base">
                {include file=$the_template"/template-parts/session-messages.tpl"}
            </div>
        {/if}

        <fieldset>
            {* Group Avatar Form *}
            <form method="POST" class="form-horizontal" enctype="multipart/form-data" name="image_upload_form"
                action="{$permalink}">

                <div class="form-row">
                    <label >{#KAHUK_Visual_Profile_UploadAvatar2#}</label>
                    <div class="controls">
                        <img src="{$group.meta.avatar_image}" alt="Group Avatar" class="img-thumbnail" />
                        {$hidden_token_edit_group}
                        <input type="file" name="image_file" size="20">
                        <input type="hidden" name="avatar" value="uploaded" />
                        <input type="hidden" name="avatarsource" value="useruploaded">
                        <input type="hidden" name="action" value="groupavatar">
                        <button type="submit" value="{#KAHUK_Visual_Group_Avatar_Upload#}" class="btn btn-primary">
                            <i class="fa fa-white fa-picture-o"></i> {#KAHUK_Visual_Group_Avatar_Upload#}
                        </button>
                    </div>
                </div>
            </form>

            {* Main Group Edit Form *}
            <form method="POST" class="form-horizontal" enctype="multipart/form-data" name="image_upload_form"
                action="{$permalink}">
                {$hidden_token_edit_group}

                <ul class="form-row">
                    <li class="story-meta flex flex-wrap items-center text-sm">
                        <strong>{#KAHUK_Visual_Submit_Group_Creator#}:</strong>
                        <a href="{$group.meta.creator_profile.user_profile_url}" title="{$group.meta.creator_profile.user_names} [id: {$group.group_creator}]">
                            <img src="{$group.meta.creator_profile.user_gravatars.medium}" class="w-12 h-12 ml-2 rounded-full" />
                        </a>
                    </li>
                    <li>
                        <strong>Submit time:</strong> {$group.meta.submit_timeago} ago
                    </li>
                    <li>
                        <strong>Active members:</strong> {$group.meta.count_active_members}
                    </li>
                </ul>

                <div class="form-row">
                    <label for="group_name">{#KAHUK_Visual_Submit_Group_Title#}:</label>
                    <div class="controls">
                        <input type="text" name="group_name" id="group_name" class="form-control col-md-7"
                            value="{$group.group_name}" />
                    </div>
                </div>

                <div class="form-row">
                    <label for="group_safename">Group Slug:</label>
                    <div class="controls">
                        <input type="text" name="group_safename" id="group_safename" class="form-control col-md-7"
                            value="{$group.group_safename}" />
                    </div>
                </div>

                <div class="form-row">
                    <label class="control-label">{#KAHUK_Visual_Submit_Group_Description#}:</label>
                    <div class="controls">
                        <textarea type="text" name="group_description" rows="4" class="form-control col-md-7"
                            id="group_description">{$group.group_description}</textarea>
                    </div>
                </div>

                <div class="form-row">
                    <label class="control-label">{#KAHUK_Visual_Submit_Group_Privacy#}:</label>
                    <div class="controls">
                        <select name="group_privacy" class="form-control"
                            onchange="document.getElementById('group_email').style.display=this.selectedIndex==0 ? 'none' : 'block';">
                            <option {if $group.group_privacy eq 'public'}SELECTED{/if} value="public">
                                {#KAHUK_Visual_Submit_Group_Public#}</option>
                            <option {if $group.group_privacy eq 'private'}SELECTED{/if} value="private">
                                {#KAHUK_Visual_Submit_Group_Private#}</option>
                            <option {if $group.group_privacy eq 'restricted'}SELECTED{/if} value="restricted">
                                {#KAHUK_Visual_Submit_Group_Restricted#}</option>
                        </select>
                    </div>
                </div>

                {if $session_user_level eq "admin"}
                    <div class="form-row">
                        <label for="group_status">Group Status</label>
                        <select name="group_status" id="group_status" class="form-control" required>
                            <option value="enable" {if $group.group_status eq 'enable'}selected{/if}>Enable</option>
                            <option value="pending" {if $group.group_status eq 'pending'}selected{/if}>Pending</option>
                            <option value="disable" {if $group.group_status eq 'disable'}selected{/if}>Disable</option>
                        </select>
                    </div>
                {/if}

                <div class="btn-wrap child-ml-2">
                    <input type="hidden" name="action" value="groupedit">
                    <input type="submit" value="{#KAHUK_Visual_Group_Edit#}" class="btn btn-primary" />
                    <input type="button" onclick="history.go(-1)" value="{#KAHUK_Visual_View_User_Edit_Cancel#}" class="" />
                </div>
            </form>
        </fieldset>
    {else}
        {#KAHUK_Visual_Group_Admin_Error#}
    {/if}
</div>