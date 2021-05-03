{config_load file=captcha_lang_conf}
{if $submit_error eq 'register_captcha_error'}

	<div class="alert alert-error">
		{#KAHUK_Captcha_Incorrect#}
	</div>
	
	<br/>
	
	<form id="thisform">
        {config_load file=captcha_kahuk_lang_conf}
		<input type="button" onclick="gPageIsOkToExit=true; document.location.href='{$kahuk_base_url}/{$pagename}.php';" value="{#KAHUK_Visual_Submit3Errors_Back#}" class="btn btn-default" />
	</form>
	
{/if}
{config_load file=captcha_kahuk_lang_conf}
