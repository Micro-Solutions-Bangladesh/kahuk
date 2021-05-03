{************************************
****** Submit Step 1 Template *******
*************************************}
<!-- submit_step_1_center.tpl -->
{if $Enable_Submit  neq '1'}
<div class="alert alert-danger">{$disable_Submit_message}</div>
{else}
<legend>{#KAHUK_Visual_Submit1_Header#}</legend>
<div class="submit">
	{checkActionsTpl location="tpl_kahuk_submit_step1_start"}

	<div class="row">
		<div class="col-md-6">
			<h3>{#KAHUK_Visual_Submit1_Instruct#}:</h3>

			<div class="submit_instructions">
				<ul class="instructions">
					{if #KAHUK_Visual_Submit1_Instruct_1A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_1A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_1B#}</li>{/if}
					{if #KAHUK_Visual_Submit1_Instruct_2A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_2A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_2B#}</li>{/if}
					{if #KAHUK_Visual_Submit1_Instruct_3A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_3A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_3B#}</li>{/if}
					{if #KAHUK_Visual_Submit1_Instruct_4A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_4A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_4B#}</li>{/if}
					{if #KAHUK_Visual_Submit1_Instruct_5A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_5A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_5B#}</li>{/if}
					{if #KAHUK_Visual_Submit1_Instruct_6A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_6A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_6B#}</li>{/if}
					{if #KAHUK_Visual_Submit1_Instruct_7A# ne ''}<li><strong>{#KAHUK_Visual_Submit1_Instruct_7A#}:</strong> {#KAHUK_Visual_Submit1_Instruct_7B#}</li>{/if}
				</ul>
			</div>
		</div>
		<div class="col-md-6">
			<div class="bookmarklet">
				<h3>{#KAHUK_Visual_User_Profile_Bookmarklet_Title#}</h3>
				<p>{#KAHUK_Visual_User_Profile_Bookmarklet_Title_1#} {#KAHUK_Visual_Name#}.{#KAHUK_Visual_User_Profile_Bookmarklet_Title_2#}<br />
				<br /><strong>{#KAHUK_Visual_User_Profile_IE#}:</strong> {#KAHUK_Visual_User_Profile_IE_1#}
				<br /><strong>{#KAHUK_Visual_User_Profile_Firefox#}:</strong> {#KAHUK_Visual_User_Profile_Firefox_1#}
				<br /><strong>{#KAHUK_Visual_User_Profile_Opera#}:</strong> {#KAHUK_Visual_User_Profile_Opera_1#}
				<br /><br /><strong>{#KAHUK_Visual_User_Profile_The_Bookmarklet#}: { include file=$the_template"/bookmarklet.tpl" }</strong>
				</p>
			</div>
		</div>
	</div>

	{checkActionsTpl location="tpl_kahuk_submit_step1_middle"}

	<hr />

	<div class="link-submit-form-wrapper">
		<div class="row">
			<div class="col-xs-12">
				<form action="{if isset($UrlMethod) && $UrlMethod == "2"}{$URL_submit}{else}submit.php{/if}" method="post" class="form-inline" id="thisform">
					<h3>{#KAHUK_Visual_Submit1_NewsSource#}</h3>
					<label for="url">{#KAHUK_Visual_Submit1_NewsURL#}:</label>
					<div class="row">
						<div class="col-xs-8 form-group">
							<input autofocus="autofocus" type="text" name="url" class="form-control col-md-12" id="url" placeholder="http://" />
						</div>
						<div class="col-xs-4 form-group">
							<input type="hidden" name="phase" value="1">
							<input type="hidden" name="randkey" value="{$submit_rand}">
							<input type="hidden" name="id" value="c_1">
							<input type="submit" value="{#KAHUK_Visual_Submit1_Continue#}" class="col-md-12 btn btn-primary" />
						</div>
					</div>
					{checkActionsTpl location="tpl_kahuk_submit_step1_end"}
				</form>
			</div>
		</div>

		{if $actioncode  neq ''}
		<div class="row mt-4">
			<div class="col-xs-12">
				{include file=$the_template"/actioncode.tpl"}
			</div>
		</div>
		{/if}

		{if $sessionMessages  neq ''}
		<div class="row mt-4">
			<div class="col-xs-12">
				{include file=$the_template"/session_messages.tpl"}
			</div>
		</div>
		{/if}
	</div>
</div>
{/if}
<!-- submit_step_1_center.tpl -->
