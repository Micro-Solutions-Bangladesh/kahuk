<div class="main-content col mt-base">
    <h1 class="story-title max-w-screen-md mx-auto">{#KAHUK_Visual_EditStory_Header#}</h1>

    <form action="" method="post" id="thisform" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{$story.link_id}" />
        {$hidden_token_edit_link}

        <div class="story-submit-form max-w-screen-md mt-6 mx-auto">
            <p>
                <label for="url" class="font-bold">{#KAHUK_Visual_Submit2_NewsURL#}: </label> ( <a
                    href="{$story.link_url}" target="_blank">{$story.link_url}</a> )
            </p>
            {if $user_role eq "admin"}
                <div class="form-row">
                    <input type="text" name="url" id="url" class="form-control" value="{$story.link_url}" placeholder="https://">
                </div>
            {/if}

            {if $user_role eq "admin"}
                <div class="form-row">
                    <p>
                        <label for="url" class="font-bold">{#KAHUK_LANG_STORY_SlUG#}: </label> ( {$story.link_title_url} )
                    </p>
                    <input type="text" name="link_title_url" id="link_title_url" class="form-control" value="{$story.link_title_url}">
                    <input type="hidden" name="link_title_url_hidden" value="{$story.link_title_url}">
                </div>
            {/if}

            <div class="form-row">
                <p>
                    <label for="link_title" class="font-bold">{#KAHUK_Visual_Submit2_Title#}: </label> ( {$story.link_title|htmlentities} )
                </p>

                <input type="text" id="link_title" class="form-control sanitize-title" name="link_title" value="{$story.link_title|htmlentities}"
                    maxlength="{$maxTitleLength}" />

                <span class="help-inline">{#KAHUK_Visual_Submit2_TitleInstruct#}</span>
            </div>


            {if $user_role eq "admin"}
                <div class="form-row">
                    <p>
                        <label for="author" class="font-bold">{#KAHUK_Visual_Change_Link_Submitted_By#}:</label> ({$story_author.user_names})
                    </p>
                    <input type="text" name="author" id="author" class="form-control sanitize-title" value="{$story_author.user_id}">
                </div>
            {/if}

            {if $enable_group && $group_list neq ''}
                <div class="form-row">
                    {*
                        Roles and permissions and Groups fixes. 
                        To add the ability to submit to a group or to change from one group to another 
                    *}
                    <label class="font-bold">{#KAHUK_Visual_Group_Submit_story#}</label>
                    {$group_list}
                </div>
            {/if}

            <div class="form-row">
                <label for="link_content" class="font-bold">{#KAHUK_Visual_Submit2_Description#}</label>
                <textarea name="bodytext" rows="10" id="bodytext" class="form-control" WRAP="SOFT">{$story.link_content}</textarea>
                <span class="help-inline">{#KAHUK_Visual_Submit2_DescInstruct#}</span>
                {if $allowed_html_tags neq ""}
                    <span class="help-inline mt-2">
                        {#KAHUK_Visual_Submit2_HTMLTagsAllowed#}: {$allowed_html_tags|htmlspecialchars}
                    </span>
                {/if}
            </div>

            <div class="form-row">
                {if $SubmitSummary_Allow_Edit eq 1}
                    <label for="summarytext" class="font-bold">{#KAHUK_Visual_Submit2_Summary#}</label>
                    
                    <textarea name="summarytext" rows="5" id="summarytext" class="form-control"
                        WRAP="SOFT">{$story.link_summary|kahuk_autop}</textarea>
                    
                    <span class="help-inline">
                        {#KAHUK_Visual_Submit2_SummaryInstruct#}
                        {#KAHUK_Visual_Submit2_SummaryLimit#}
                        {$MAX_NUMBER_OF_WORD_STORY_DESC}
                        {#KAHUK_Visual_Submit2_SummaryLimitCharacters#}
                    </span>
                {/if}
            </div>

            <div class="form-row">
                <label for="category" class="font-bold">{#KAHUK_Visual_Submit2_Category#}: </label>


                <select class="form-control" name="category" id="category">
                    <option value="">{#KAHUK_Visual_Submit2_CatInstructSelect#}</option>

                    {foreach from=$submit_cat_array item=catItem}
                        <option value="{$catItem.category_id}" {if $catItem.category_id == $story.link_category}selected{/if}>
                            {$catItem.category_name}
                        </option>

                        {if $catItem.sub_items}
                            {foreach from=$catItem.sub_items item=childLabel1}
                                <option value="{$childLabel1.category_id}" {if $childLabel1.category_id == $story.link_category}selected{/if}>
                                    - {$childLabel1.category_name}
                                </option>

                                {if $childLabel1.sub_items}
                                    {foreach from=$childLabel1.sub_items item=childLabel2}
                                        <option value="{$childLabel2.category_id}" {if $childLabel2.category_id == $story.link_category}selected{/if}>
                                            -- {$childLabel2.category_name}
                                        </option>
                                    {/foreach}
                                {/if}
                            {/foreach}
                        {/if}
                    {/foreach}
                </select>

                <span class="help-inline">{#KAHUK_Visual_Submit2_CatInstruct#}</span>
            </div>

            {$warning_message}

            <div class="btn-wrap">
                <input type="submit" value="{#KAHUK_Visual_Submit2_Continue#}" class="btn btn-primary" />
            </div>
        </div>
    </form>
</div>

{include file=$the_template"/functions/sanitize-title.tpl"}