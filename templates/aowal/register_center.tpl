{php}global $hooks;{/php}
<div class="main-content col mt-base">
    {if $allow_registration neq '1'}
        <div class="alert alert-error">Registration is temporarily suspended!</div>
    {else}
        {* TODO: work for the $validate_password feature *}

        <nav class="breadcrumbs">
            {include file=$the_template"/template-parts/breadcrumb.tpl"}
        </nav>

        {if $session_messages  neq ''}
            <div class="mb-base">
                {include file=$the_template"/template-parts/session-messages.tpl"}
            </div>
        {/if}

        <div class="row">
            <div class="col-12 md:col mt-base">
                <h2 class="section-title mb-4">{#KAHUK_Visual_Register_Description_Title#}</h2>
                <p>{#KAHUK_Visual_Register_Description_Paragraph#}</p>
                <ul class="list-disc pl-base">
                    {#KAHUK_Visual_Register_Description_Points#}
                </ul>
            </div>

            <div class="form-wrapper col-12 md:col mt-base">
                {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_register_start");{/php}
                
                <form action="{$page_register_url}" class="form-horizontal" method="post" id="thisform" name="thisform">
                    {php}$hooks->do_action("kahuk_form_start");{/php}
                
                    <div class="form-row">
                        <label for="reg_username">{#KAHUK_Visual_Register_Username#}</label>
                        <div class="w-full">
                            <input type="text" id="reg_username" name="reg_username" value="{$reg_username}" size="25"
                                class="form-control"    
                                minlength="5" maxlength="32" tabindex="10" 
                                autofocus="autofocus" required />
                            <p class="help-inline">A username can only contain alphanumeric characters and underscores.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="reg_email">{#KAHUK_Visual_Register_Email#}</label>
                        <div class="w-full">
                            <input type="email" id="reg_email" name="reg_email"
                                class="form-control reg_email" 
                                value="{$reg_email}" tabindex="12" 
                                maxlength="128" required />
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="reg_password">{#KAHUK_Visual_Register_Password#}</label>
                        <div class="w-full">
                            <input type="password" id="reg_password" name="reg_password"
                                class="form-control reg_password" 
                                value="" size="25" tabindex="14" minlength="7" maxlength="20" />
                            <p class="help-inline">{#KAHUK_Visual_Register_FiveChar#}</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="reg_verify">{#KAHUK_Visual_Register_Verify_Password#}</label>
                        <div class="w-full">
                            <input type="password" class="form-control" id="reg_verify" name="reg_password2"
                                value="" size="25" tabindex="15" />
                        </div>
                    </div>

                    {php}$hooks->do_action("kahuk_form_end");{/php}

                    <div class="btn-wrap mt-8">
                        <input type="hidden" name="action" value="create-account" />
                        <input type="submit" name="submit" value="{#KAHUK_Visual_Register_Create_User#}"
                            class="btn btn-primary reg_submit" tabindex="16" />
                    </div>
                </form>
            </div><!-- /.form-wrapper -->
            {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_register_end");{/php}
        </div>
    {/if}
</div><!-- /.main-content -->