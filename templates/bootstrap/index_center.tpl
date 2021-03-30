{************************************
***** Published Pages Template ******
*************************************}
<!-- index_center.tpl -->
	{* Redwine: The below code is to check if the SMTP testing settings are true. If true, it will display the email message sent to users. *}
	{if $allow_smtp_testing eq '1'  && $smtp_fake_email eq '1' && $recoveryConfirmation neq ''}
		<span style="font-style: italic;">You are viewing the email message for the purpose of testing SMTP email sending.<br />
		{$recoveryConfirmation}
		</span>
		<br /><hr />
	{/if}
	{* Redwine: END SMTP testing settings. *}
{if !$link_summary_output && $pagename == 'index' && count($templatelite.get) == 0}
	{* Welcome message for new installations *}
	<div class="well blank_index">
		{* Redwine: I added the hard coded text to the language files and replaced it with its language constant here, thus making it available and compliant with language changing *}
		<h2>{#PLIKLI_Visual_Welcome_to#} {#PLIKLI_Visual_Name#}</h2>
		<p style="font-size:1.0em;">{#PLIKLI_Visual_Welcome_to_Message#}</p>
		<p><a href="submit.php" class="btn btn-primary">{#PLIKLI_Visual_Welcome_to_Submit_First_Story#}</a></p>
	</div>
{else}
	{$link_summary_output}
{/if}
{checkActionsTpl location="tpl_plikli_pagination_start"}
{if $link_summary_output neq ''}
{$link_pagination}
{/if}
{checkActionsTpl location="tpl_plikli_pagination_end"}
<!--/index_center.tpl -->