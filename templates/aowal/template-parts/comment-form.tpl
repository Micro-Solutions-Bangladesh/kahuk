{php}global $hooks;{/php}
{if !$enable_comments}
    <div class="alert alert-danger">Comments are now disabled!</div>
{else}
    <form action="{$page_submit_comment}" method="post" id="commentform" name="mycomment_form">
        {php}$hooks->do_action("kahuk_form_start");{/php}
        
        <div class="form-row mb-4">
            <label for="comment_content" class="font-bold">{#KAHUK_Visual_Comment_Send#}</label>

            <textarea {if isset($TheComment)}autofocus="autofocus"{/if} name="comment_content" id="comment_content"
                class="comment-form form-control"
                rows="6">{if isset($TheComment)}{$TheComment}{/if}</textarea>
            
            <div class="flex items-center mt-2">
                <svg fill="none" class="w-5 h-5 text-gray-600 mr-1" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <p class="help-inline">
                    {if $allowed_html_tags eq ""}
                        {#KAHUK_Visual_Submit2_No_HTMLTagsAllowed#}
                    {else}
                        <strong>{#KAHUK_Visual_Submit2_HTMLTagsAllowed#}:</strong> {$allowed_html_tags|htmlspecialchars}
                    {/if}
                </p>
            </div>
        </div>

        {php}$hooks->do_action("kahuk_form_end");{/php}

        <div class="form-actions">
            <input type="hidden" name="process" value="submit-comment" />
            <input type="hidden" name="comment_id" value="" />
            <input type="hidden" name="story_id" value="{$story.link_id}" />
            <input type="hidden" name="user_id" value="{$session_user_id}" />
            <input type="hidden" name="parrent_id" value="0" />
            <input type="submit" name="submit" value="{#KAHUK_Visual_Comment_Submit#}" class="btn btn-primary" />
        </div>
    </form>
{/if}
