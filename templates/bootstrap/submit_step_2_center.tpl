{************************************
****** Submit Step 2 Template *******
*************************************}
<!-- submit_step_2_center.tpl -->
<div class="submit_page">
	<legend>{#KAHUK_Visual_Submit2_Details#}</legend>
	{checkActionsTpl location="tpl_kahuk_submit_step2_start"}
	<form class="form-horizontal" action="{$URL_submit}" method="post" name="thisform" id="thisform" enctype="multipart/form-data">
		<div class="col-md-6 submit_step_2_left">
			<div class="control-group">
				<label for="title" class="control-label">{#KAHUK_Visual_Submit2_Title#}</label>
				<div class="controls">
					<input type="text" id="title" class="form-control title col-md-4" tabindex="1" name="title" value="{$submit_url_title}" size="54" maxlength="{$maxTitleLength}" />
					<p class="help-inline">{#KAHUK_Visual_Submit2_TitleInstruct#}</p>
				</div>
			</div>
			<div class="control-group">
				{if $Multiple_Categories}
					<strong>{#KAHUK_Visual_Submit2_Category#}</strong>
				{else}
					<label for="category">{#KAHUK_Visual_Submit2_Category#}</label>
				{/if}
				<div class="controls select-category{if $Multiple_Categories} multi-select-category{/if}">
					{if $Multiple_Categories}
						{section name=thecat loop=$submit_cat_array}
							{$submit_cat_array[thecat].spacercount|repeat_count:'&nbsp;&nbsp;&nbsp;&nbsp;'}
								{*Redwine: I added the $submit_cat_array[thecat].name as the title attribute to the input checkbox so that we are able to use it to get the category name to be added in the preview section on the submit step 2*}
								 <input type="radio" class="form-control" id="id_{$submit_cat_array[thecat].auto_id}" name="category" value="{$submit_cat_array[thecat].auto_id}" title="{$submit_cat_array[thecat].name}" {if $submit_cat_array[thecat].auto_id == $submit_category  || in_array($submit_cat_array[thecat].auto_id,$submit_additional_cats)}checked{/if}>
								 <label for="id_{$submit_cat_array[thecat].auto_id}">{$submit_cat_array[thecat].name}</label>
								 <br />							
						{/section}
						<br />
					{else}
						<select class="form-control category" tabindex="2" name="category" id="category">
							<option value="">{#KAHUK_Visual_Submit2_CatInstructSelect#}</option>
							{section name=thecat loop=$submit_cat_array}
								<option value="{$submit_cat_array[thecat].auto_id}" {if $submit_cat_array[thecat].auto_id == $submit_category  || in_array($cat_array[thecat].auto_id,$submit_additional_cats)}selected{/if}>
									{if $submit_cat_array[thecat].spacercount lt $submit_lastspacer}{$submit_cat_array[thecat].spacerdiff|repeat_count:''}{/if}
									{if $submit_cat_array[thecat].spacercount gt $submit_lastspacer}{/if}
									{$submit_cat_array[thecat].spacercount|repeat_count:'&nbsp;&nbsp;&nbsp;'}
									{$submit_cat_array[thecat].name}
									&nbsp;&nbsp;&nbsp;
									{assign var=submit_lastspacer value=$submit_cat_array[thecat].spacercount}
								</option>
							{/section}
						</select>
					{/if}
					<p class="help-inline">{#KAHUK_Visual_Submit2_CatInstruct#}</p>
				</div>
			</div>
			{if $enable_group && $output neq ''}
				<div class="control-group">
					<label for="link_group_id" class="control-label">{#KAHUK_Visual_Group_Submit_story#}</label>
					<div class="controls">
						{$output}
					</div>
				</div>
			{/if}
			{checkActionsTpl location="tpl_header_admin_main_comment_subscription"}


			{checkActionsTpl location="tpl_kahuk_submit_step2_middle"}
			<div class="control-group">
				<label for="bodytext" class="control-label">{#KAHUK_Visual_Submit2_Description#}</label>
				<div class="controls">
{*Redwine: to display before the the content textarea what are the html tags allowed if set in Admin Panel; otherwise, it displays NO HTML*}
					{ if $Story_Content_Tags_To_Allow eq ""}
						<p class="help-inline"><strong>{#KAHUK_Visual_Submit2_No_HTMLTagsAllowed#} </strong>{*#KAHUK_Visual_Submit2_HTMLTagsAllowed#*}</p>
					{else}
							<p class="help-inline" style="word-wrap: break-word;"><strong>{#KAHUK_Visual_Submit2_HTMLTagsAllowed#}:</strong> {$Story_Content_Tags_To_Allow}</p>
					{/if }
					<textarea name="summarytext" tabindex="15" rows="6" class="form-control bodytext col-md-4" id="typedesc" maxlength="{$maxStoryLength}" WRAP="SOFT">{$submit_url_description}</textarea>
					<br />
				</div>
			</div>

			<button type="button" id="btn-desc">desc</button>

			{checkActionsTpl location="submit_step_2_pre_extrafields"}
			{include file=$tpl_extra_fields.".tpl"}
			{if isset($register_step_1_extra)}
				{$register_step_1_extra}
			{/if}<br clear="all" />
			<div class="form-actions">            
				<input type="hidden" name="url" id="url" value="{$submit_url}" />
				<input type="hidden" name="phase" value="2" />
				<input type="hidden" name="randkey" value="{$randkey}" />
				<input type="hidden" name="id" value="{$submit_id}" />
				
				{if $Allow_Draft eq 1}
					<div class="draft-scheduled">
				<input type="checkbox" id="draft" name="draft" value="draft" />
						<label for="draft">{#KAHUK_Visual_Submit2_Draft#}</label><br />
					</div><br />
				{/if}
				{if $Allow_Scheduled eq 1}
					<div class="draft-scheduled">
						<label for="calendar" class="control-label">{#KAHUK_Visual_Submit2_Draft_Scheduled#}</label>
						<div class="input-group" id="date-picker">
							<input type="text" id="calendar" class="form-control datepicker" name="date_schedule" data-provide="datepicker" data-date-format="yyyy-mm-dd">
							<input type="hidden" name="timezone" id="timezone" value="" />
						</div><br />
						<div id="wrong-date" class="alert-danger" style="display:none">scheduled date cannot be lesser than or equal to the current date. Change the date or press &nbsp;&nbsp;<input type="button" value="OK to continue" onclick="getValue();" />
						</div>
					</div><br />
				{/if}
				{* Redwine: I added ; return false; to the onlclick event to fix the bug in Firefox that was taking the user back to an error message...when clicking the cancel the submission button. https://github.com/redwinefireplace/kahuk-cms/commit/bcc2b717bd8196c32126380706bf2f5ae4ff9b90 *}
				<button class="btn btn-default" tabindex="30" onclick="history.go(-1); return false;">Cancel</button>
				{checkActionsTpl location="tpl_kahuk_submit_step2_end"}<br /><br />
				<input class="btn btn-primary" tabindex="31" type="submit" value="{#KAHUK_Visual_Submit2_Continue#}" />
			</div>
		</div>
		{* START STORY PREVIEW *}
		<div class="col-md-6 submit_step_2_right" id="dockcontent">
			{checkActionsTpl location="tpl_kahuk_submit_preview_start"}
				{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_shakebox_index" *}
			<div class="stories" id="xnews-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}">
				{checkActionsTpl location="tpl_kahuk_story_start"}
				<div class="headline">
					{if $Voting_Method eq 2}
						<h4 id="ls_title-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}">
							<ul class='star-rating{$star_class}' id="xvotes-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}">
								<li class="current-rating" style="width: {$link_rating_width}px;" id="xvote-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}"></li>
								<span id="mnmc-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}" {if $link_shakebox_currentuser_votes ne 0}style="display: none;"{/if}>
									<li><a href="javascript:{$link_shakebox_javascript_vote_1star}" class='one-star'>1</a></li>
									<li><a href="javascript:{$link_shakebox_javascript_vote_2star}" class='two-stars'>2</a></li>
									<li><a href="javascript:{$link_shakebox_javascript_vote_3star}" class='three-stars'>3</a></li>
									<li><a href="javascript:{$link_shakebox_javascript_vote_4star}" class='four-stars'>4</a></li>
									<li><a href="javascript:{$link_shakebox_javascript_vote_5star}" class='five-stars'>5</a></li>
								</span>
								<span id="mnmd-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}" {if $link_shakebox_currentuser_votes eq 0}style="display: none;"{/if}>
									<li class='one-star-noh'>1</li>
									<li class='two-stars-noh'>2</li>
									<li class='three-stars-noh'>3</li>
									<li class='four-stars-noh'>4</li>
									<li class='five-stars-noh'>5</li>
								</span>
							</ul>
						</h4>
					{else}
						<div class="votebox votebox-published">
							<div class="vote">
								{checkActionsTpl location="tpl_kahuk_story_votebox_start"}
								<div class="votenumber">1</div>
								{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_shakebox_index" *}
								<div id="xvote-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}" class="votebutton">
									<!-- Already Voted -->
									<a class="btn btn-success"><i class="fa fa-white fa-thumbs-up"></i></a>
									<!-- Bury It -->
									{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_id *}
									<a class="btn btn-default linkVote_{if !empty($link_id)}{$link_id}{/if}"><i class="fa fa-thumbs-down"></i></a>
								</div><!-- /.votebutton -->
								{checkActionsTpl location="tpl_kahuk_story_votebox_end"}
							</div><!-- /.vote -->
						</div><!-- /.votebox -->
					{/if}
					{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_shakebox_index" *}
					<div class="title" id="title-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}">
						<h2>
							{checkActionsTpl location="tpl_kahuk_story_title_start"}
							<span class="story_title">&nbsp;</span>
							{checkActionsTpl location="tpl_kahuk_story_title_end"}
						</h2>
						<span class="subtext">
							{php}
								global $main_smarty, $current_user;
								if ($current_user->user_id > 0 && $current_user->authenticated) {
									$login=$current_user->user_login;
								}
								// Read the users information from the database
								$user=new User();
								$user->username = $login;
								// Assign smarty variables to use in the template.
								$main_smarty->assign('link_submitter', $user->username);
								$main_smarty->assign('submitter_profile_url', getmyurl('user2', $login, 'profile'));
								$main_smarty->assign('Avatar_ImgSrc', get_avatar('small', $user->avatar_source, $user->username));
							{/php}
							{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_shakebox_index" *}
							{if $UseAvatars neq "0"}<span id="ls_avatar-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}"><img src="{$Avatar_ImgSrc}" width="16px" height="16px" alt="" title="Avatar" /></span>{else}<i class="fa fa-user"></i>{/if}
							<a href="{$submitter_profile_url}">{$link_submitter}</a> 
							<i class="fa fa-time"></i>
							Being Submitted Now
							{if $url_short neq "http://" && $url_short neq "://"}
								<i class="fa fa-globe"></i>
								{* Redwine: temporary solution to eliminate the warnings "Undefined index: story_status" *}
								<a href="{$url}" {if $open_in_new_window eq true} target="_blank" rel="noopener noreferrer"{/if}  {if !empty($story_status) && $story_status neq "published"}rel="nofollow noopener noreferrer"{/if}>{$url_short}</a>
							{/if}
						</span>
					</div><!-- /.title -->
				</div> <!-- /.headline -->
				<div class="storycontent">
					{checkActionsTpl location="tpl_link_summary_pre_story_content"}
					{if $pagename eq "story"}{checkActionsTpl location="tpl_kahuk_story_body_start_full"}{else}{checkActionsTpl location="tpl_kahuk_story_body_start"}{/if}
					{*if $viewtype neq "short"*}
						<span class="news-body-text" id="ls_contents-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}">
							<span class="bodytext">&nbsp;</span>
							<div class="clearboth"></div> 
						</span>
						{checkActionsTpl location="tpl_kahuk_story_body_end"}
					{*/if*}
				</div><!-- /.storycontent -->
				<div class="storyfooter">
					<div class="story-tools-left">
						{checkActionsTpl location="tpl_kahuk_story_tools_start"}
						{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_shakebox_index" *}
						<span id="ls_comments_url-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}">
							<i class="fa fa-comment"></i> <span id="linksummaryDiscuss"><a class="comments">{#KAHUK_MiscWords_Discuss#}</a>&nbsp;</span>
						</span> 
						<i class="fa fa-star"></i> <span id="linksummarySaveLink"><a id="add" class="favorite" >{#KAHUK_MiscWords_Save_Links_Save#}</a></span>&nbsp;
						{* Redwine: temporary solution to eliminate the warnings "Undefined index: link_shakebox_index" *}
						<span id="stories-{if !empty($link_shakebox_index)}{$link_shakebox_index}{/if}" class="label label-success" style="display:none;line-height:1em;">{#KAHUK_MiscWords_Save_Links_Success#}</span>
						{if $enable_group eq "true" && $user_logged_in}
							<i class="fa fa-group"></i> <span class="group_sharing"><a>{#KAHUK_Visual_Group_Share#}</a></span>
						{/if}
						{checkActionsTpl location="tpl_kahuk_story_tools_end"}
					</div>
					<div class="story-tools-right">
						{*Redwine: I changed the class below from category to prev_category because when the javascript runs, it was applying the result to the class category thus affecting the select option from which we select the category and render it empty execpt from the selected category and the impossibility to change the category of we choose to!*}
						<i class="fa fa-folder"></i> <a><span class="prev_category">&nbsp;</span></a>	
					 </div><!-- /.story-tools-right -->
					 <div style="clear:both;"></div>
				</div><!-- /.storyfooter -->
				{checkActionsTpl location="tpl_link_summary_end"}
			</div><!-- /.stories -->
			{checkActionsTpl location="tpl_kahuk_story_end"}
		</div>
		{* END STORY PREVIEW *}
	</form>
	{checkActionsTpl location="tpl_kahuk_submit_step2_after_form"}
	{if $og_twitter_image neq ""}
		<div class="display_img_to_upload">{#KAHUK_Visual_Display_IMG_To_Upload#}</div>
		<img src="{$og_twitter_image}" style="max-width:550px"/>
	{/if}
</div>
{literal}
	<script type="text/javascript">
		//var dock0=new dockit("dockcontent", 0);
	</script>
	
	
	<script type="text/javascript">
	
$( document ).ready(function() {
	// Rediwne: This part deals with the content textarea.
	var allowedHTML = "{/literal}{$Story_Content_Tags_To_Allow}{literal}";
	allowedHTML = allowedHTML.replace(/&lt;/gi, '');
	allowedHTML = allowedHTML.replace(/&gt;/gi, ',');
	var lastCharcomma = allowedHTML.slice(-1);
	if (lastCharcomma == ",") { 
		allowedHTML = allowedHTML.slice(0, -1);
	}
	splitallowedHTML = allowedHTML.split(',');
	var arrayLength = splitallowedHTML.length;
	var mywhiteList = {};
	for (var i = 0; i < arrayLength; i++) {
		mywhiteList[splitallowedHTML[i]] = [];
	}  
	var options = {
	stripIgnoreTag: true,
	allowCommentTag: false,
	whiteList: 
		mywhiteList
	
	};
	$('#btn-desc').click(function() {
		var html = $('#typedesc').val();
		var s = Date.now();
		var out = filterXSS(html,options);
		$('#bodytext').val(out);
		// The preview area
		$(".bodytext").html(out);
	});
	$('#btn-desc').click();

	var lastType = Date.now();
	var lastHTML = $('#typedesc').val().trim();
	$('#typedesc').keypress(function() {
	  lastType = Date.now();
	});
	setInterval(function() {
	  var now = Date.now();
	  if (now - lastType > 50) {
		var html = $('#typedesc').val().trim();
		if (html !== lastHTML) {
		  lastHTML = html;
		  $('#btn-desc').click();
		}
	  }
	}, 50);
	// End process content textarea.	
		
	// Redwine: Added the below to prevent pressing the enter key which was taking the user back to the url submit page.
	$('#title').on('keyup keypress', function(e) {
		  var keyCode = e.keyCode || e.which;
		  if (keyCode === 13) { 
			e.preventDefault();
			return false;
		  }
		});

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
	
	
	// Redwine: End code deals with the Title, Tags input fields
	// Redwine: the following code deals with the Title, Tags, Category in the preview area
	$(function() {
		$("#title").on('change keyup', function() {
			var title=$(this).val();
			$(".story_title").html(title);
			return false;
		});
		/*** Redwine: to capture the selected categories when multiple categories is set to true in Admin Panel***/
		var category = "";
		$( 'input[name="category[]"]' ).on( "click", function() {
			category += $(this).attr("title") + " ";
			$(".prev_category").html(category);
		});
		/*** Redwine: to capture the selected categories when multiple categories is set to false in Admin Panel***/
		$('select[name="category"').change(function() {
			var option = $('select[name="category"').find(":selected").text();
			$(".prev_category").html(option);
		});

	});
	});
	</script>
	
<script type="text/javascript">
$(document).ready(function(){
	var zone = jstz.determine().name();
	$("#timezone").val(zone);

//making sure that the draft is unchecked if the scheduled date is set
$('#draft').change(function() {
	if($(this).is(":checked") && $('#calendar').val() != '' && $('#calendar').val() != undefined) {
		$('#draft').attr('checked', false);
	}
}); 	

$('#calendar').datepicker({
     onSelect: function(d,i){
          if(d !== i.lastVal){
              $(this).change();
          }
     }
});
	$('#calendar').change(function() {
	  var d1 = new Date();
	  var d2 = new Date($('#calendar').val());
	  if (d2.getTime() <= d1.getTime()) { 
		$("#wrong-date").css("display", "block");
		
		
	  }else{
		  $('#wrong-date').css("display", "none");
		  $('#draft').attr('checked', false);
	  }
	});
});
function getValue(){
			$('#calendar').val('');
			$('#wrong-date').css("display", "none");
		}
</script>
{/literal}
<!--/submit_step_2_center.tpl -->