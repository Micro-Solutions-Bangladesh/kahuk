<div class="main-content col mt-base">
    {if $enable_group eq "true" && $group_allow eq 1}
        <nav class="breadcrumbs">
            {include file=$the_template"/template-parts/breadcrumb.tpl"}
        </nav>

        <h1 class="story-title">Create a new group</h1>

        {if $session_messages  neq ''}
            <div class="mb-base">
                {include file=$the_template"/template-parts/session-messages.tpl"}
            </div>
        {/if}

        <fieldset>
            {if !empty($error)}
                <div class="alert alert-danger">
                    <button class="close" data-dismiss="alert">&times;</button>
                    {$error}
                </div>
            {/if}

            <form action="{$page_group_submit_url}" method="post" name="thisform" id="thisform" class="form-horizontal"
                enctype="multipart/form-data">
                {$hidden_token_submit_group}

                <div class="form-row">
                    <label for="group_title">{#KAHUK_Visual_Submit_Group_Title#}:</label>
                    <div class="controls">
                        <input type="text" id="group_title" class="form-control col-md-7" name="group_title"
                            value="{if !empty($templatelite.post.group_title)}{$templatelite.post.group_title|escape:"html"}{/if}" />

                        <p class="help-inline">{#KAHUK_Visual_Group_Submit_TitleInstruction#}</p>
                    </div>
                </div>
                
                <div class="form-row">
                    <label for="group_description">{#KAHUK_Visual_Submit_Group_Description#}:</label>
                    <div class="controls">
                        <textarea name="group_description" rows="3" class="form-control"
                            id="group_description">{if !empty($templatelite.post.group_description)}{$templatelite.post.group_description|escape:"html"}{/if}</textarea>

                        <p class="help-inline">{#KAHUK_Visual_Group_Submit_DescriptionInstruction#}</p>
                    </div>
                </div>

                <div class="form-row">
                    <label for="group_privacy">{#KAHUK_Visual_Submit_Group_Privacy#}:</label>
                    <div class="controls">
                        <select name="group_privacy" id="group_privacy" class="form-control"
                            onchange="document.getElementById('group_email').style.display=this.selectedIndex==0 ? 'none' : 'block';">
                            <option value="public"
                                {if !empty($templatelite.post.group_privacy) && $templatelite.post.group_privacy=='public'}selected{/if}>
                                {#KAHUK_Visual_Submit_Group_Public#}</option>
                            <option value="private"
                                {if !empty($templatelite.post.group_privacy) && $templatelite.post.group_privacy=='private'}selected{/if}>
                                {#KAHUK_Visual_Submit_Group_Private#}</option>
                            <option value="restricted"
                                {if !empty($templatelite.post.group_privacy) && $templatelite.post.group_privacy=='restricted'}selected{/if}>
                                {#KAHUK_Visual_Submit_Group_Restricted#}</option>
                        </select>

                        {#KAHUK_Visual_Group_Submit_PrivacyInstruction#}
                    </div>
                </div>

                <div class="form-row">
                    <label for="group_mailer">{#KAHUK_Visual_Submit_Group_Mail_Friends#}:</label>
                    <div class="controls">
                        <input type="text" id="group_mailer" class="form-control col-md-7" name="group_mailer"
                            value="{if !empty($templatelite.post.group_mailer)}{$templatelite.post.group_mailer|escape:"html"}{/if}">

                        <p class="help-inline">{#KAHUK_Visual_Group_Submit_Mail_Friends_Desc#}</p>
                    </div>
                </div>

                <div class="btn-wrap child-ml-2">
                    <input type="hidden" name="action" value="submit_group" />

                    <input type="submit" value="{#KAHUK_Visual_Submit_Group_create#}" class="btn btn-primary" />
                    <input type="button" onclick="history.go(-1)" value="{#KAHUK_Visual_View_User_Edit_Cancel#}"
                        class="btn btn-default" />
                </div>
            </form>
        </fieldset>
    {else}
        {#KAHUK_Visual_Group_Disabled#}
    {/if}
</div>