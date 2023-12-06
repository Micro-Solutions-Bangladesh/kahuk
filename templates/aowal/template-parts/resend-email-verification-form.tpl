{* Let user submit email address to ask new verification code to verify email address*}
<p>{#KAHUK_Visual_Resend_Email#}</p>
<form action="{$page_login_url}" method="post">
    <fieldset class="group-fields max-w-screen-md">
        <input type="email" name="email" class="form-control" placeholder="email@example.com">
        <button type="submit" class="btn">
            <span class="visually-hidden">Email</span><i class="icon icon-envelope-o"></i>
        </button>
    </fieldset>
    <input type='hidden' name='processlogin' value='5'/>
</form>