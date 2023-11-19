{php}global $hooks;{/php}
<fieldset>{* todo: upgrade *}
    {if $submit_error eq 'cantedit'}
        <div class="alert alert-danger">
            <p>You cannot edit link after {$edit_time} minutes!</p>
            <br />
            <form id="thisform">
                <input type="button" onclick="gPageIsOkToExit=true; document.location.href='{$toredirect}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'invalidurl'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit2Errors_InvalidURL#}: {$submit_url}</p>
            <br />
            <form id="thisform">
                <input type="button" onclick="javascript:gPageIsOkToExit=true;window.history.go(-1);"
                    value="{#KAHUK_Visual_Submit2Errors_Back#}" class="btn btn-primary">
            </form>
        </div>
    {/if}
    {if $submit_error eq 'dupeurl'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit2Errors_DupeArticleURL#}: {$submit_url}</p>
            <p>{#KAHUK_Visual_Submit2Errors_DupeArticleURL_Instruct#}</p>
            <p><a href="{$submit_search}"><strong>{#KAHUK_Visual_Submit2Errors_DupeArticleURL_Instruct2#}</strong></a></p>
            <br style="clear: both;" /><br style="clear: both;" />
            <form id="thisform">
                <input type="button" onclick="javascript:gPageIsOkToExit=true;window.history.go(-1);"
                    value="{#KAHUK_Visual_Submit2Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_error_2");{/php}

    {if $submit_error eq 'badkey'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_BadKey#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'hashistory'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_HasHistory#}: {$submit_error_history}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'urlintitle'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_URLInTitle#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'incomplete'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_Incomplete#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'long_title'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_Long_Title#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'long_content'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_Long_Content#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'long_tags'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_Long_Tags#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}

    {if $submit_error eq 'short_tags'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Submit3Errors_Short_Tags#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}

    {if $submit_error eq 'long_summary'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_Long_Summary#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}
    {if $submit_error eq 'nocategory'}
        <div class="alert alert-danger">
            <p>{#KAHUK_Visual_Submit3Errors_NoCategory#}</p>
            <br />
            <form id="thisform">
                <input type="button"
                    onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php?id={$link_id}';"
                    value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-primary" />
            </form>
        </div>
    {/if}

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_error_3");{/php}
</fieldset>