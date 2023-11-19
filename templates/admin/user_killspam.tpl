<!--user_killspam.tpl -->
<legend>{#KAHUK_Visual_Breadcrumb_User_Killspam#}</legend>

<p>{#KAHUK_Visual_View_Killspam_Step1#}</p>
<form name="token" method="post">
<p>
	<a class="btn btn-danger" href="{$kahuk_base_url}/admin/admin_users.php?mode=yeskillspam&user={$user}&id={$id}{$uri_token_admin_users_killspam}">{#KAHUK_Visual_Ban_Link_Yes#}</a>
	<a class="btn btn-default" href="javascript:history.back()">{#KAHUK_Visual_Ban_Link_No#}</a>
</p>
        {$hidden_token_admin_users_killspam}
    

</form>
<!--/user_killspam.tpl -->