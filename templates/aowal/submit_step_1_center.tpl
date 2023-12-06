{php}global $hooks;{/php}
<div class="main-content col mt-base">
    {if $Enable_Submit  neq '1'}
        <div class="alert alert-danger">{$disable_Submit_message}</div>
    {else}
        <h1 class="story-title">{#KAHUK_Visual_Submit1_Header#}</h1>

        {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_step1_start");{/php}

        <div class="row mt-8">
            <div class="col">
                <h3 class="section-title mb-4">{#KAHUK_Visual_Submit1_Instruct#}:</h3>

                <div class="submit_instructions">
                    <ul class="instructions">
                        {if #KAHUK_Visual_Submit1_Instruct_1A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_1A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_1B#}
                            </li>
                        {/if}
                        {if #KAHUK_Visual_Submit1_Instruct_2A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_2A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_2B#}
                            </li>
                        {/if}
                        {if #KAHUK_Visual_Submit1_Instruct_3A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_3A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_3B#}
                            </li>
                        {/if}
                        {if #KAHUK_Visual_Submit1_Instruct_4A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_4A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_4B#}
                            </li>
                        {/if}
                        {if #KAHUK_Visual_Submit1_Instruct_5A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_5A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_5B#}
                            </li>
                        {/if}
                        {if #KAHUK_Visual_Submit1_Instruct_6A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_6A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_6B#}
                            </li>
                        {/if}
                        {if #KAHUK_Visual_Submit1_Instruct_7A# ne ''}
                            <li>
                                <strong>{#KAHUK_Visual_Submit1_Instruct_7A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_7B#}
                            </li>
                        {/if}
                    </ul>
                </div>
            </div>

            <div class="col">
                <div class="bookmarklet">
                    <h3 class="section-title mb-4">{#KAHUK_Visual_User_Profile_Bookmarklet_Title#}</h3>

                    <p>{#KAHUK_Visual_User_Profile_Bookmarklet_Title_1#}
                        {#KAHUK_Visual_Name#}.{#KAHUK_Visual_User_Profile_Bookmarklet_Title_2#}
                    </p>
                    <ul>
                        <li><strong>{#KAHUK_Visual_User_Profile_IE#}:</strong> {#KAHUK_Visual_User_Profile_IE_1#}</li>
                        <li><strong>{#KAHUK_Visual_User_Profile_Firefox#}:</strong> {#KAHUK_Visual_User_Profile_Firefox_1#}
                        </li>
                        <li><strong>{#KAHUK_Visual_User_Profile_Opera#}:</strong> {#KAHUK_Visual_User_Profile_Opera_1#}</li>
                    </ul>
                    <p><strong>{#KAHUK_Visual_User_Profile_The_Bookmarklet#}:
                            {include file=$the_template"/template-parts/bookmarklet.tpl"}</strong></p>
                </div>
            </div>
        </div>

        {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_step1_middle");{/php}

        {if $session_messages  neq ''}
            <div class="mb-base">
                {include file=$the_template"/template-parts/session-messages.tpl"}
            </div>
        {/if}

        <div class="link-submit-form-wrapper mt-8">
            {if $actioncode neq ''}
                <div class="row mt-8">
                    <div class="col-12">
                        {include file=$the_template"/template-parts/actioncode.tpl"}
                    </div>
                </div>
            {/if}

            {if $sessionMessages neq ''}
                <div class="row mt-4">
                    <div class="col-12">
                        {include file=$the_template"/template-parts/session-messages.tpl"}
                    </div>
                </div>
            {/if}

            <div class="max-w-screen-md mx-auto mt-8">
                <form action="{$page_submit_url}" method="GET"
                    class="form-inline" id="thisform"
                >
                    <label for="url" class="font-bold">{#KAHUK_Visual_Submit1_NewsURL#}:</label>

                    <fieldset class="group-fields">
                        <input type="text" name="url" id="url" class="form-control" placeholder="https://...">
                        <input type="submit" value="{#KAHUK_Visual_Submit1_Continue#}" class="btn btn-primary" />
                    </fieldset>

                    {if $gId gt 0}
                        <input type="hidden" name="gId" value="{$gId}">
                    {/if}

                    <input type="hidden" name="phase" value="1">
                    {* <input type="hidden" name="randkey" value="{$submit_rand}"> *}
                    <input type="hidden" name="id" value="c_1">
                </form>
            </div>
        </div>

        {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_submit_step1_end");{/php}
    {/if}
</div>