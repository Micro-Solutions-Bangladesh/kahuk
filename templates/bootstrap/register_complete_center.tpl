{************************************
******* Registration Complete *******
*************************************}
<!-- register_complete_center.tpl -->
{checkActionsTpl location="tpl_kahuk_register_complete_start"}
<p>
{* Redwine: The below code is to check if the SMTP testing settings are true. If true, it will display the email message sent to users. *}
{if $allow_smtp_testing eq '1'  && $smtp_fake_email eq '1' && $validationEmail neq ''}
	<span style="font-style: italic;">You are viewing the email message for the purpose of testing SMTP email sending.<br />
	{$validationEmail}
	</span>
	<br /><hr />
{/if}
{* Redwine: END SMTP testing settings. *}
</p>
<p>
	{#KAHUK_Visual_Register_Thankyou#|sprintf:$get.user}
	{#KAHUK_Visual_Register_Noemail#}
</p>

{assign var="email" value=#KAHUK_PassEmail_From#}
<ul>
	{#KAHUK_Visual_Register_ToDo#|sprintf:$email}
</ul>
{checkActionsTpl location="tpl_kahuk_register_complete_end"}
<!--/register_complete_center.tpl -->