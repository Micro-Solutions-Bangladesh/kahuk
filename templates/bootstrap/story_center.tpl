{************************************
****** Story Wrapper Template *******
*************************************}
<!-- story_center.tpl -->
{* Redwine: The below code is to check if the SMTP testing settings are true. If true, it will display the email message sent to users. *}
{if $allow_smtp_testing eq '1'  && $smtp_fake_email eq '1' && $notifyStatus neq ''}
	<span style="font-style: italic;">You are viewing the email message for the purpose of testing SMTP email sending.<br />
	{$notifyStatus}
	</span>
	<br /><hr />
{/if}


{* Redwine: END SMTP testing settings. *}
{* Redwine: Spam Trigger Module was not working as intended. Fix provided by modifying 8 files.">Spam Trigger Module was not working as intended. https://github.com/Pligg/pligg-cms/commit/2faf855793814f82d7c61a8745a93998c13967e0 *}
{checkActionsTpl location="tpl_kahuk_content_start"}
{$the_story}
<ul class="nav nav-tabs" id="storytabs">
	<li class="active"><a data-toggle="tab" href="#comments"><i class="fa fa-comments"></i> {#KAHUK_Visual_Story_Comments#}</a></li>
	{if count($voter) neq 0}<li><a data-toggle="tab" href="#who_voted"><i class="fa fa-thumbs-up"></i> {#KAHUK_Visual_Story_Who_Upvoted#}</a></li>{/if}
	{if count($downvoter) neq 0}<li><a data-toggle="tab" href="#who_downvoted"><i class="fa fa-thumbs-down"></i> {#KAHUK_Visual_Story_Who_Downvoted#}</a></li>{/if}
	{checkActionsTpl location="tpl_kahuk_story_tab_end"}
</ul>
<script language="javascript">
var story_link="{$story_url}";
{literal}

	$(function () {
		$('#storytabs a[href="#who_voted"]').tab('show');
		$('#storytabs a[href="#who_downvoted"]').tab('show');
		$('#storytabs a[href="#related"]').tab('show');
		$('#storytabs a[href="#comments"]').tab('show');
	});

{/literal}

{if $urlmethod==2}
    {literal}
        function show_comments(id){
			document.location.href=story_link+'/'+id+'#comment-'+id;
        }
        function show_replay_comment_form(id){
           document.location.href=story_link+'/reply/'+id+'#comment-reply-'+id;
        }
    {/literal}
{else}
    {literal}
        function show_comments(id){
			document.location.href=story_link+'&comment_id='+id+'#comment-'+id;
        }
        function show_replay_comment_form(id){
           document.location.href=story_link+'&comment_id='+id+'&reply=1#comment-reply-'+id;
        }
    {/literal}
{/if}
</script>

<div id="tabbed" class="tab-content">

	<div class="tab-pane fade active in" id="comments" >
		{checkActionsTpl location="tpl_kahuk_story_comments_start"}
		<h3>{#KAHUK_Visual_Story_Comments#}</h3>
		<a name="comments" href="#comments"></a>
		<ol class="media-list comment-list">
			{checkActionsTpl location="tpl_kahuk_story_comments_individual_start"}
				{$the_comments}
			{checkActionsTpl location="tpl_kahuk_story_comments_individual_end"}
			{if $user_authenticated neq ""}
				{include file=$the_template."/comment_form.tpl"}
			{else}
				{checkActionsTpl location="anonymous_comment_form_start"}
				<div align="center" class="login_to_comment">
					<br />
					<h3><a href="{$login_url}">{#KAHUK_Visual_Story_LoginToComment#}</a> {#KAHUK_Visual_Story_Register#} <a href="{$register_url}">{#KAHUK_Visual_Story_RegisterHere#}</a>.</h3>
				</div>
				{checkActionsTpl location="anonymous_comment_form_end"}
			{/if}
		</ol>
		{checkActionsTpl location="tpl_kahuk_story_comments_end"}
	</div>
	
	{if count($voter) neq 0}
		<div class="tab-pane fade" id="who_voted">
			<h3>{#KAHUK_Visual_Story_WhoVoted#}</h3>
			{checkActionsTpl location="tpl_kahuk_story_who_voted_start"}
			<div class="whovotedwrapper whoupvoted">
				<ul>			
					{section name=upvote loop=$voter}
						<li>
							{if $UseAvatars neq "0"}
								<a href="{$URL_user, $voter[upvote].user_login}" rel="tooltip" title="{$voter[upvote].user_login}" class="avatar-tooltip"><img class="img-thumbnail" src="{$voter[upvote].Avatar_ImgSrc}" alt="" align="top" title="" /></a>
							{else}
								<a href="{$URL_user, $voter[upvote].user_login}">{$voter[upvote].user_login}</a>
							{/if}
						</li>
					{/section}
				</ul>
			</div>
			{checkActionsTpl location="tpl_kahuk_story_who_voted_end"}
		</div>
	{/if}
	{if count($downvoter) neq 0}
		<div class="tab-pane fade" id="who_downvoted">
			<h3>{#KAHUK_Visual_Story_Who_Downvoted_Story#}</h3>
			{checkActionsTpl location="tpl_kahuk_story_who_downvoted_start"}
			<div class="whovotedwrapper whodownvoted">
				<ul>
					{section name=downvote loop=$downvoter}
						<li>
							{if $UseAvatars neq "0"}
								<a href="{$URL_user, $downvoter[downvote].user_login}" rel="tooltip" title="{$downvoter[downvote].user_login}" class="avatar-tooltip"><img class="img-thumbnail" src="{$downvoter[downvote].Avatar_ImgSrc}" alt="" align="top" title="" /></a>
							{/if}
							{if $UseAvatars eq "0"}<a href="{$URL_user, $downvoter[downvote].user_login}">{$downvoter[downvote].user_login}</a>{/if}
						</li>
					{/section}
				</ul>
			</div>
			<div style="clear:both;"></div>
			{checkActionsTpl location="tpl_kahuk_story_who_downvoted_end"}
		</div>
	{/if}
	{checkActionsTpl location="tpl_kahuk_story_tab_end_content"}
</div>
<!--/story_center.tpl -->
