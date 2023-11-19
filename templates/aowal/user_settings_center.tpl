{php}global $hooks;{/php}
<div class="main-content col mt-base">
    <nav class="breadcrumbs ">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if $action_messages neq ''}
        <div class="mb-base">
            {include file=$the_template"/template-parts/action-messages.tpl"}
        </div>
    {/if}

    {if $session_messages neq ''}
        <div class="mb-base">
            {include file=$the_template"/template-parts/session-messages.tpl"}
        </div>
    {/if}

    {php}$hooks->do_action("snippet_action_tpl", "tpl_kahuk_profile_info_start");{/php}

    <form action="" method="post" id="thisform" name="thisform" role="form"
        {if $validate_password eq '1'}onsubmit="return checkBreachedPassword();" {/if}
    >
        <div class="grid gap-base grid-cols-1 md:grid-cols-2">
            <table class="table col-bordered ">
                <thead class="table_title">
                    <tr>
                        <th colspan="2">{#KAHUK_Visual_Profile_ModifyProfile#}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <label for="user_login" accesskey="1">{#KAHUK_Visual_Register_Username#}:</label>
                        </td>
                        <td>
                            <strong>{$user.user_login}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="user_email" accesskey="1">{#KAHUK_Visual_Profile_Email#}:</label>
                        </td>
                        <td>
                            {if $session_user_level == "admin"}
                                <input type="text" class="form-control" name="user_email" id="user_email" 
                                    tabindex="3" value="{$user.user_email}"
                                >
                            {else}
                                <strong>{$user.user_email}</strong>
                            {/if}
                            <div class="help-inline clear-both mt-2">{#KAHUK_Visual_Profile_OnlyAdmins#}</div>
                        </td>
                    </tr>

                    {* TODO require verification
                    <tr>
                        <td><label for="name" accesskey="1">{#KAHUK_Visual_User_Profile_PublicEmail#}:</label></td>
                        <td><input type="text" class="form-control" name="public_email" id="public_email"
                                tabindex="5" value="{$user.public_email}"></td>
                    </tr> *}

                    <tr>
                        <td><label for="user_url" accesskey="1">{#KAHUK_Visual_User_Profile_Homepage#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_url" id="user_url" 
                                tabindex="7" value="{$user.user_url}"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td><label for="user_names" accesskey="1">{#KAHUK_Visual_Profile_RealName#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_names" id="user_names" 
                                tabindex="1" value="{$user.user_names}"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td><label for="user_occupation" accesskey="1">{#KAHUK_Visual_Profile_Occupation#}:</label></td>
                        <td>
                        <input type="text" class="form-control" name="user_occupation" id="user_occupation" tabindex="11"
                                value="{$user.user_occupation}">
                                </td>
                    </tr>
                    <tr>
                        <td><label for="user_location" accesskey="1">{#KAHUK_Visual_Profile_Location#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_location" id="user_location" 
                                tabindex="9" value="{$user.user_location}"
                            >
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table col-bordered">
                <thead class="table_title">
                    <tr>
                        <th colspan="2">{#KAHUK_User_Profile_Social#}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><label for="user_skype" accesskey="1">{#KAHUK_Visual_User_Profile_Skype#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_skype" id="user_skype" 
                                tabindex="10" value="{$user.user_skype}"
                            >
                        </td>
                    </tr>

                    <tr>
                        <td><label for="user_facebook" accesskey="1">{#KAHUK_User_Profile_Facebook#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_facebook" id="user_facebook" 
                                tabindex="2" value="{$user.user_facebook}"
                                placeholder="https://facebook.com/.../"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td><label for="user_twitter" accesskey="1">{#KAHUK_User_Profile_Twitter#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_twitter" id="user_twitter" 
                                tabindex="4" value="{$user.user_twitter}"
                                placeholder="https://twitter.com/.../"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td><label for="user_linkedin" accesskey="1">{#KAHUK_User_Profile_Linkedin#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_linkedin" id="user_linkedin" 
                                tabindex="6" value="{$user.user_linkedin}"
                                placeholder="https://linkedin.com/in/.../"
                            >
                        </td>
                    </tr>
                    {* todo delete user_googleplus columns *}
                    <tr>
                        <td><label for="user_pinterest" accesskey="1">{#KAHUK_User_Profile_Pinterest#}:</label></td>
                        <td>
                            <input type="text" class="form-control" name="user_pinterest" 
                                id="user_pinterest" tabindex="12" value="{$user.user_pinterest}"
                                placeholder="https://pinterest.com/.../"
                            >
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table col-bordered">
                <thead class="table_title">
                    <tr>
                        <th colspan="2">{#KAHUK_Visual_Profile_ChangePass#}</th>
                    </tr>
                </thead>
                <tbody>
                    {* <tr>
                        <td><label for="oldpassword">{#KAHUK_Visual_Profile_OldPass#}:</label></td>
                        <td>
                            <input type="password" class="form-control" id="oldpassword" 
                                name="oldpassword" size="25" tabindex="13"
                            >
                        </td>
                    </tr> *}
                    <tr>
                        <td><label for="newpassword">{#KAHUK_Visual_Profile_NewPass#}:</label></td>
                        <td>
                            <input type="password" class="form-control" id="newpassword" 
                                name="newpassword" size="25" tabindex="14"
                            >
                            <span class="reg_userpasscheckitvalue"></span></td>
                    </tr>
                    <tr>
                        <td><label for="verify">{#KAHUK_Visual_Profile_VerifyNewPass#}:</label></td>
                        <td>
                            <input type="password" class="form-control" id="verify" 
                                name="newpassword2" size="25" tabindex="15"
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="brn-wrap mt-base">
            {$hidden_token_user_settings}

            {* <input type="hidden" name="process" value="1"> *}
            <input type="hidden" name="action" value="save-user-settings">
            <input type="hidden" name="user_id" value="{$user_id}">
            <input type="submit" name="save_profile" value="{#KAHUK_Visual_Profile_Save#}"
                class="btn btn-primary profile_settings_save" tabindex="16"
            >
        </div>
    </form>
</div>

{* Delted the JS scripts for checkBreachedPassword() *}
