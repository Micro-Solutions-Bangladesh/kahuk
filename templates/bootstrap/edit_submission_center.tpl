{************************************
********* Edit Submission **********
*************************************}
<!-- edit_submission_center.tpl -->
{literal}
	<script type="text/javascript">
	function SetState(obj_checkbox, obj_textarea) {
		if(obj_checkbox.checked)
			{ obj_textarea.disabled = false;
		}
		else {
			obj_textarea.disabled = true;
		}
	}
	function textCounter(field, countfield, maxlimit) {
		if (field.value.length > maxlimit) // if too long...trim it!
				field.value = field.value.substring(0, maxlimit);
				// otherwise, update 'characters left' counter
		else
				countfield.value = maxlimit - field.value.length;
	}
	function counter(text) {
		text.form.text_num.value = text.value.length;
	}
	</script>
{/literal}

{checkActionsTpl location="tpl_kahuk_submit_step2_start"}
<form action="" method="post" id="thisform" enctype="multipart/form-data" >
	<input type="hidden" name="id" value="{$submit_id}" />
	{$hidden_token_edit_link}
	<legend>{#KAHUK_Visual_EditStory_Header#}: {$submit_title}</legend>
	<strong>{#KAHUK_Visual_Submit2_NewsURL#}: </strong>
	&nbsp;<a href="{$submit_url}">{$submit_url}</a>
	{if $isAdmin eq 1}
		<br />
		<input type="text" name="url" id="url" class="form-control col-md-6" {if $submit_url neq "http://" && $submit_url neq ""} value ="{$submit_url}"{else} placeholder="http://"{/if}>
	{/if}
	<br />
	{* 
		<strong for="url_title" accesskey="2">{#KAHUK_Visual_Submit2_URLTitle#}: </strong>{$submit_url_title} 
		<hr />
	*}
	<strong>{#KAHUK_Visual_Submit2_Title#}: </strong> 
	<span class="field-description">{#KAHUK_Visual_Submit2_TitleInstruct#}</span>
	<br />
	<input type="text" id="title" class="form-control col-md-6" name="title" value="{$submit_title}" maxlength="{$maxTitleLength}" />
	<br />
	{if $isAdmin eq 1 or $isModerator eq 1}
		<strong>{#KAHUK_Visual_Change_Link_Submitted_By#}: </strong>
		<br />
        <select name="author" id="author" class="form-control col-md-6">
			{section name=nr loop=$userdata}
				<option value="{$userdata[nr].user_id}" {if $userdata[nr].user_id eq $author} selected {/if} >{$userdata[nr].user_login}</option>
			{/section}      
        </select>
        
		{*
			<input type="text" id="author" class="form-control col-md-6" name="author" value="{$author}"  />
		*}
		
		<br />
	{/if}

{* Redwine: Roles and permissions and Groups fixes. To add the ability to submit to a group or to change from one group to another *}
	{if $enable_group && !empty($output)}
		<div class="control-group">
			<label for="input01" class="control-label">{#KAHUK_Visual_Group_Submit_story#}</label>
			<div class="controls">
				{$output}
			</div>
		</div>
	{/if}

	<strong>{#KAHUK_Visual_Submit2_Description#}: </strong>
	<span class="field-description">{#KAHUK_Visual_Submit2_DescInstruct#}</span>
	<br />
	<textarea name="bodytext" rows="10" id="bodytext" class="form-control" WRAP="SOFT">{$submit_content}</textarea>
	{if $Story_Content_Tags_To_Allow neq ""}
		<span class="help-inline">
			{#KAHUK_Visual_Submit2_HTMLTagsAllowed#}: {$Story_Content_Tags_To_Allow}
		</span>
	{/if}
	<br />
	{if $SubmitSummary_Allow_Edit eq 1}
		<br />
		<strong>{#KAHUK_Visual_Submit2_Summary#}: </strong>
		<span class="field-description">
			{#KAHUK_Visual_Submit2_SummaryInstruct#}
			{#KAHUK_Visual_Submit2_SummaryLimit#}
			{$StorySummary_ContentTruncate}
			{#KAHUK_Visual_Submit2_SummaryLimitCharacters#}
		</span>
		<br />
		<textarea name="summarytext" rows="5" id="summarytext" class="form-control" WRAP="SOFT">{$submit_summary}</textarea>
		<br />
	{/if}
	<br />
	<strong>{#KAHUK_Visual_Submit2_Category#}: </strong>
	<span class="field-description">{#KAHUK_Visual_Submit2_CatInstruct#}</span>
	<br />
	{if $Multiple_Categories}
        {* Redwine: to apply the proper CSS Class depending on whether the multi categories is set or not *}
		<div class="controls select-category{if $Multiple_Categories} multi-select-category{/if}">
		{section name=thecat loop=$submit_cat_array}
			{$submit_cat_array[thecat].spacercount|repeat_count:'&nbsp;&nbsp;&nbsp;&nbsp;'}
				<input type="checkbox" class="form-control" name="category[]" value="{$submit_cat_array[thecat].auto_id}" {if $submit_cat_array[thecat].auto_id == $submit_category  || in_array($submit_cat_array[thecat].auto_id,$submit_additional_cats)}checked{/if}> {$submit_cat_array[thecat].name}<br />
                
				{*Redwine: I added the code below to save an array of the riginal categories used for the article, to accurately get the difference if the categories were edited by the Admin, to include in the notification email in editlink.php*}
                {if $submit_cat_array[thecat].auto_id == $submit_category  || in_array($submit_cat_array[thecat].auto_id,$submit_additional_cats)}
				    <input type="hidden" name="additionalCats[]" value="{$submit_cat_array[thecat].auto_id}">
				{/if}
		{/section}
        {* Redwine: to apply the proper CSS Class depending on whether the multi categories is set or not *}
		</div>
	{else}
		<select class="form-control"name="category">
			{section name=thecat loop=$submit_cat_array}
				<option value = "{$submit_cat_array[thecat].auto_id}"{if $submit_cat_array[thecat].auto_id eq $submit_category} selected{/if}>
				{if $submit_cat_array[thecat].spacercount lt $lastspacer}{$submit_cat_array[thecat].spacerdiff|repeat_count:''}{/if}
				{if $submit_cat_array[thecat].spacercount gt $lastspacer}{/if}
				{$submit_cat_array[thecat].spacercount|repeat_count:'&nbsp;&nbsp;&nbsp;'}
				{$submit_cat_array[thecat].name} 
				&nbsp;&nbsp;&nbsp;       
				{assign var=lastspacer value=$submit_cat_array[thecat].spacercount}					
				</option>
			{/section}
		</select>
	{/if}
	<br />
	{if $canIhaveAccess eq 1}
		<strong>{#KAHUK_Visual_EditStory_Notify#}: </strong>
		<br />
		&nbsp; <input type="checkbox" name="notify" value="yes"> {#KAHUK_Visual_EditStory_NotifyText#}
		<ul class="notify_option_list">
			<li><input type="radio" name="reason" value="typo" onclick="notify.checked = 'true';">{#KAHUK_Visual_EditStory_Reason_typo#}</li>
			<li><input type="radio" name="reason" value="category" onclick="notify.checked = 'true';">{#KAHUK_Visual_EditStory_Reason_category#}</li>
			
			<li><input type="radio" name="reason" value="foul" onclick="notify.checked = 'true';">{#KAHUK_Visual_EditStory_Reason_foul#}</li>
			<li>
				<input type="radio" name="reason" value="other" onclick="notify.checked = 'true';">{#KAHUK_Visual_EditStory_Reason_other#} 
				<input type="text" name="otherreason" class="form-control" size="50">
			</li>
		</ul>
	{/if}
	{checkActionsTpl location="submit_step_2_pre_extrafields"}
	{include file=$tpl_extra_fields.".tpl"}
	<br />

			{$warning_message}
			<ul class="notify_option_list alert alert-warning">
				<li><input type="radio" id="rd_{$submit_stat}" name="change_status" value="{$submit_stat}" checked /> <label for="rd_{$submit_stat}">{#KAHUK_Visual_Submit2_Edit_Keep_As_Status#$submit_stat}</label></li>
				{if $submit_stat eq 'draft' || $submit_stat eq 'scheduled'}
					<li><input type="radio" id="rd_tonew" name="change_status" value="to_new" /> <label for="rd_tonew">{#KAHUK_Visual_Submit2_Edit_Draft_Post#}</li>
				{/if}
				{if $submit_stat eq 'new' || $submit_stat eq 'published'}
					{if $Allow_Draft eq 1}
					<li><input type="radio" id="rd_todraft" name="change_status" value="to_draft" title="WARNING: &#10;if your article is published, when you change it to draft &#10;and want to re-post it later, &#10;it might not go back to 'published' page; &#10;it dependds on the settings in the dashboard!" /> <label for="rd_todraft" title="WARNING: &#10;if your article is published, when you change it to draft &#10;and want to re-post it later, &#10;it might not go back to 'published' page; &#10;it dependds on the settings in the dashboard!">{#KAHUK_Visual_Submit2_Edit_Draft#}</label></li>
					{/if}
				{/if}
				<li><input type="radio" id="rd_discard" name="change_status" value="discard" /> <label for="rd_discard">{#KAHUK_Visual_Submit2_Edit_Draft_Discard#}</li>
			</ul>
			</br />

	<input type="submit" value="{#KAHUK_Visual_Submit2_Continue#}" class="btn btn-primary" />
</form>
{checkActionsTpl location="tpl_kahuk_submit_step2_after_form"}

 {literal}
<script type="text/javascript">
$( document ).ready(function() {
	/*
	I used [`~!@#$%^&*()|+=?;:'",.<>\{\}\[\]\\\/] versus [^\w\s-_] because JavaScript does not work well with UTF-8
	and does not recognize the word boundaries in utf8. 
	*/
	// Redwine: the following code deals with the Title, Tags input fields
	$('#title').keyup(function() {
		var yourInput = $(this).val();
		re = /[`~!@#$%^&*()|+=?;:'",.<>\{\}\[\]\\\/]/gi;
		var isSplChar = re.test(yourInput);
		if(isSplChar) {
			var no_spl_char = yourInput.replace(re, '');
			$(this).val(no_spl_char);
		}
	});
	$('#title').bind("paste", function() {
		setTimeout(function() { 
		  //get the value of the input text
		  var data= $( '#title' ).val() ;
		  //replace the special characters to '' 
		  var dataFull = data.replace(/[`~!@#$%^&*()|+=?;:'",.<>\{\}\[\]\\\/]/gi, '');
		  //set the new value of the input text without special characters
		  $( '#title' ).val(dataFull);
		});
	});
});
</script>
{/literal}
<!--/edit_submission_center.tpl -->