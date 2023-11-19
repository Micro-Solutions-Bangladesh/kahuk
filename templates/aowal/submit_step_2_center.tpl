{php}global $hooks;{/php}
<div class="main-content col mt-base">
    <h1 class="story-title">{#KAHUK_Visual_Submit2_Details#}</h1>

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_step2_start");{/php}

    <div class="flex flex-wrap mt-6">
        <div class="story-submit-form w-full lg:w-2/3">
            <form class="form-horizontal" action="{$page_submit_url}" 
                method="post" name="thisform" id="thisform"
                enctype="multipart/form-data"
            >
            {php}$hooks->do_action("kahuk_form_start");{/php}
            
            {if $enable_group && !empty($group)}
                <div class="form-row">
                    <strong>Story submit to "{$group.group_name}" group.</strong>
                </div>
                <input type="hidden" name="gId" value="{$group.group_id}">
            {/if}
                <div class="form-row">
                    <label for="title" class="control-label">{#KAHUK_Visual_Submit2_Title#}</label>
                    <div class="controls">
                        <input type="text" id="title" class="form-control sanitize-title disable-submit" tabindex="1" name="title"
                            value="{$submit_url_title}" size="54" maxlength="{$maxTitleLength}" />
                        <p class="help-inline">{#KAHUK_Visual_Submit2_TitleInstruct#}</p>
                    </div>
                </div>
                <div class="form-row">
                    {if $Multiple_Categories}
                        <strong>{#KAHUK_Visual_Submit2_Category#}</strong>
                    {else}
                        <label for="category">{#KAHUK_Visual_Submit2_Category#}</label>
                    {/if}
                    <div class="controls select-category{if $Multiple_Categories} multi-select-category{/if}">
                        {if $Multiple_Categories}
                            {section name=thecat loop=$submit_cat_array}
                                {$submit_cat_array[thecat].spacercount|repeat_count:'&nbsp;&nbsp;&nbsp;&nbsp;'}
                                {*Redwine: I added the $submit_cat_array[thecat].category_name as the title attribute to the input checkbox so that we are able to use it to get the category name to be added in the preview section on the submit step 2*}
                                <input type="radio" class="form-control" id="id_{$submit_cat_array[thecat].category_id}" name="category"
                                    value="{$submit_cat_array[thecat].category_id}" title="{$submit_cat_array[thecat].category_name}"
                                    {if $submit_cat_array[thecat].category_id == $submit_category}checked{/if}>
                                <label for="id_{$submit_cat_array[thecat].category_id}">{$submit_cat_array[thecat].category_name}</label>
                                <br />
                            {/section}
                            <br />
                        {else}
                            <select class="form-control category" tabindex="2" name="category" id="category">
                                <option value="">{#KAHUK_Visual_Submit2_CatInstructSelect#}</option>

                                {foreach from=$submit_cat_array item=catItem}
                                    <option value="{$catItem.category_id}" {if $catItem.category_id == $submit_category}selected{/if}>
                                        {$catItem.category_name}
                                    </option>

                                    {if $catItem.sub_items}
                                        {foreach from=$catItem.sub_items item=childLabel1}
                                            <option value="{$childLabel1.category_id}" {if $childLabel1.category_id == $submit_category}selected{/if}>
                                                - {$childLabel1.category_name}
                                            </option>

                                            {if $childLabel1.sub_items}
                                                {foreach from=$childLabel1.sub_items item=childLabel2}
                                                    <option value="{$childLabel2.category_id}" {if $childLabel2.category_id == $submit_category}selected{/if}>
                                                        -- {$childLabel2.category_name}
                                                    </option>
                                                {/foreach}
                                            {/if}
                                        {/foreach}
                                    {/if}
                                {/foreach}
                            </select>
                        {/if}
                        <p class="help-inline">{#KAHUK_Visual_Submit2_CatInstruct#}</p>
                    </div>
                </div>

                <div class="form-row">
                    <label for="typedesc">{#KAHUK_Visual_Submit2_Description#}</label>
                    <div class="controls">
                        <textarea name="summarytext" tabindex="15" rows="6" 
                            class="form-control bodytext" id="typedesc" 
                            maxlength="{$maxStoryLength}" WRAP="SOFT"
                        >{$submit_url_description}</textarea>

                        {if $allowed_html_tags eq ""}
                            <p class="help-inline">
                                <strong>{#KAHUK_Visual_Submit2_No_HTMLTagsAllowed#}
                                </strong>{*#KAHUK_Visual_Submit2_HTMLTagsAllowed#*}
                            </p>
                        {else}
                            <p class="help-inline" style="word-wrap: break-word;">
                                <strong>{#KAHUK_Visual_Submit2_HTMLTagsAllowed#}:</strong> {$allowed_html_tags|htmlspecialchars}
                            </p>
                        {/if}
                    </div>
                </div>

                {php}$hooks->do_action("kahuk_form_end");{/php}

                <div class="btn-wrap child-ml-2 ">
                    <input type="hidden" name="url" id="url" value="{$submit_url}" />
                    <input type="hidden" name="phase" value="2" />
                    <input type="hidden" name="randkey" value="{$randkey}" />
                    <input type="hidden" name="id" value="{$submit_id}" />

                    <button class="btn btn-default" tabindex="30" onclick="history.go(-1); return false;">Cancel</button>
                    <input class="btn btn-primary" tabindex="31" type="submit" value="{#KAHUK_Visual_Submit2_Continue#}" />
                </div>
            </form>
        </div><!-- /.story-submit-form -->

        <div class="hidden lg:flex lg:w-1/3 justify-center pl-base">
            {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_step2_middle");{/php}
        </div>
    </div>

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_step2_end");{/php}
</div>

{include file=$the_template"/functions/sanitize-title.tpl"}
