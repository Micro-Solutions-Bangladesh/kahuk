<!-- user_password_reset.tpl -->
<h1>{#PLIKLI_Visual_View_User_Reset_Pass2#}</h1>
	{* Redwine: The below code is to check if the SMTP testing settings are true. If true, it will display the email message sent to users. *}
	{if $allow_smtp_testing eq '1'  && $smtp_fake_email eq '1' && $adminResetPassword neq ''}
	<span style="font-style: italic;">You are viewing the email message for the purpose of testing SMTP email sending.<br />
	{$adminResetPassword}
	</span>
	<br /><hr />
	{/if}
	{* Redwine: END SMTP testing settings. *}
<table style="border:0">
	<tr><td>{#PLIKLI_Visual_View_User_Reset_Pass4#} </td></tr>
	<tr><td><input type=button onclick="window.history.go(-1)" value="{#PLIKLI_Visual_View_Links_Back_To_User#}" class="btn btn-primary"></td></tr>
</table>
<!--/user_password_reset.tpl -->