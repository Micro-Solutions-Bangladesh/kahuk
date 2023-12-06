{php}global $hooks;{/php}
<div class="main-content col mt-base">

    <nav class="breadcrumbs">
        {include file=$the_template"/template-parts/breadcrumb.tpl"}
    </nav>

    {if $session_messages  neq ''}
        <div class="mb-base">
            {include file=$the_template"/template-parts/session-messages.tpl"}
        </div>
    {/if}
    
    {if $errorMsg ne ""}
        <div class="alert alert-warning">
            {$errorMsg}
        </div>
    {/if}

    {php}$hooks->do_action("snippet_action_tpl", "tpl_login_top");{/php}

    <div class="row">
        <div class="col-12 md:col mt-base">
            <h1 class="story-title">{#KAHUK_Visual_Login_Login#}</h1>
            <p>{#KAHUK_Visual_Login_Have_Account#}</p>

            <form action="{$page_login_url}" method="post">
                <div class="form-row">
                    <label for="username" class="font-bold">{#KAHUK_Visual_Login_Username#}/{#KAHUK_Visual_Register_Email#}:</label>
                    <input autofocus="autofocus" type="text" id="username" name="username" class="form-control"
                        value="{if isset($login_username)}{$login_username}{/if}" tabindex="1" />
                </div>

                <div class="form-row">
                    <label for="password-field" class="font-bold">{#KAHUK_Visual_Login_Password#}:</label>
                    <input type="password" name="password" id="password-field" class="form-control" tabindex="2" />
                </div>

                <div class="login-remember form-row">
                    <input type="checkbox" class="col-sm-offset-2" name="persistent" tabindex="3" />
                    {#KAHUK_Visual_Login_Remember#}
                </div>
                    
                <div class="btn-wrap">
                    <input type="hidden" name="processlogin" value="1" />
                    <input type="hidden" name="redirectto" value="{if isset($get.redirectto)}{$get.redirectto}{/if}" />

                    <input type="submit" value="{#KAHUK_Visual_Login_LoginButton#}" id="login_submit"
                        class="btn btn-primary" tabindex="4" />
                </div>
            </form>
        </div>

        <hr class="star my-6 md:hidden" />

        <div class="col-12 md:col mt-base">
            <form action="{$page_pass_reset_url}" id="thisform2" method="post">
                <h2 class="section-title">{#KAHUK_Visual_Login_ForgottenPassword#}</h2>
                <p>{#KAHUK_Visual_Login_EmailChangePass#}</p>

                <div class="form-row">
                    <label for="password-field" class="font-bold">{#KAHUK_Visual_Register_Email#}:</label>
                    <input type="text" name="email" class="form-control" size="25" tabindex="5" id="forgot-email" value="" />
                </div>

                <div class="btn-wrap">
                    <input type="submit" value="Submit" class="btn btn-primary" tabindex="6" />
                    <input type="hidden" name="action" value="reset" />
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-screen-md mx-auto mt-base-double">
        <h2 class="section-title">{#KAHUK_Visual_Login_NewUsers#}</h2>
        <p>
            {#KAHUK_Visual_Login_NewUsersA#}<a href="{$page_register_url}" class="btn btn-success btn-xs" tabindex="7">{#KAHUK_Visual_Login_NewUsersB#}</a>{#KAHUK_Visual_Login_NewUsersC#}
        </p>
    </div>

    {php}$hooks->do_action("snippet_action_tpl", "tpl_login_bottom");{/php}
</div>

{literal}
    <script>
        if ($('#waitTime').length) {
            var sec = $('#waitTime').text()
            var timer = setInterval(function() {
                $('#waitTime').text(--sec);
                $('#login_submit').prop('disabled', true);
                if (sec == 0) {
                    $('#login_submit').prop('disabled', false);
                    clearInterval(timer);
                }
            }, 1000);
        }
    </script>
{/literal}