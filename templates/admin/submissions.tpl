<!-- submissions.tpl -->
{literal}
<script type="text/javascript" language="javascript">
function submit_list_form(){
	
	if($(".enabled_disable:checked").length==0) {
		alert("Please select news");
		return false;
	}
	
	val_action=$("#admin_action").val();
	
	if(val_action=="spam"){
		var usernames ="";
		$('.enabled_disable:checked').each(function(i){
			usernames += $(this).attr("usernameval")+", ";
		});
		if(confirm("CAUTION: Using killspam will change the user's status to sapmmer and deletes all his activity (submissions, coments, groups, etc and blacklists all the domains of the stories submitted! If you want to mark A PARTICULAR STORY AS SPAM, use the gear that appears on the right side of the story and select \"Change the status\" and on the Change Status page, click on Ban this url\r\rAre you sure that you want to killspam these users: "+usernames)){
        
		} else {
			return false;
		}
	}
	
	document.getElementById("user_list_form").submit();
	
	//for(x in document.getElementById("user_list_form"))
	//alert(x);
	//alert(document.getElementById("user_list_form"));
}

$(function(){
	// add multiple select / deselect functionality
	$("#selectall_user_ed").click(function () {
		  $('.enabled_disable').attr('checked', this.checked);
	});
	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".enabled_disable").click(function(){
		if($(".enabled_disable").length == $(".enabled_disable:checked").length) {
			$("#selectall_user_ed").attr("checked", "checked");
		} else {
			$("#selectall_user_ed").removeAttr("checked");
		}
	});
});

function set_admin_action(acc){
	$("#admin_action").val(acc);
	submit_list_form(); 
}

function validate_all_user_action(){
	if($("#admin_action").val()==""){
		alert("select news list");
		return false;
	}
}
</script>
{/literal}
<legend>
	{if $templatelite.get.user}
		{$templatelite.get.user|sanitize:2}'s {#KAHUK_Visual_TopUsers_TH_News#}
	{else}
		{#KAHUK_Visual_AdminPanel_Links#}
	{/if}
</legend>
<div class="alert alert-warning expires-warning">{#KAHUK_Visual_Page_Expires#}</div>
{if $moderated_submissions_count neq 0}
	<div class="alert alert-warning">
		There {if $moderated_submissions_count eq 1}is{else}are{/if} <strong>{$moderated_submissions_count} {if $moderated_submissions_count eq 1}story{else}stories{/if}</strong> awaiting moderation.<br />
		<a href="admin_links.php?filter=moderated">Click here to review {if $moderated_submissions_count eq 1}it{else}them{/if}.</a>
	</div>
{/if}
{if $spam_submissions_count neq 0}
	<div class="alert alert-warning">
		There {if $spam_submissions_count eq 1}is{else}are{/if} <strong>{$spam_submissions_count} Spam {if $spam_submissions_count eq 1}story{else}stories{/if}</strong> awaiting review.<br />
		<a href="admin_links.php?filter=spam">Click here to review {if $spam_submissions_count eq 1}it{else}them{/if}.</a>
	</div>
{/if}

<div class="row management-tools">
	<div class="col-md-3">
		<div class="btn-group pull">
			<a class="btn btn-default dropdown-toggle" href="#" data-toggle="dropdown">
				<i id="selected_action"></i> {#KAHUK_Visual_AdminPanel_Apply_Changes#} <span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a onclick="set_admin_action('published')" href="#"><i class="fa fa-level-up"></i>{#KAHUK_Visual_AdminPanel_Publish#}</a></li>
				<li><a onclick="set_admin_action('new')" href="#"><i class="fa fa-level-down"></i>{#KAHUK_Visual_AdminPanel_New#}</a></li>
				<li><a onclick="set_admin_action('discard')" href="#"><i class="fa fa-trash-o"></i>{#KAHUK_Visual_AdminPanel_Discard#}</a></li>
				<li><a onclick="set_admin_action('moderated')" href="#"><i class="fa fa-ban"></i>{#KAHUK_Visual_AdminPanel_Moderated#}</a></li>
				<li><a onclick="set_admin_action('spam')" href="#"><i class="fa fa-ban"></i>{#KAHUK_Visual_AdminPanel_Spam#}</a></li>
			</ul>
		</div>
	</div>
	<form action="{$kahuk_base_url}/admin/admin_links.php" method="get" class="form-search search-bar">
		<div class="col-md-3">
			<div class="input-append">
				<input type="hidden" name="mode" value="search">
				{if isset($templatelite.get.keyword) && $templatelite.get.keyword neq ""}
					{assign var=searchboxtext value=$templatelite.get.keyword|sanitize:2}
				{else}
					{assign var=searchboxtext value=#KAHUK_Visual_Search_SearchDefaultText#}			
				{/if}
				<input type="hidden" name="user" value="{$templatelite.get.user|sanitize:2}">
				<input type="text" class="form-control search-query" name="keyword" placeholder="{$searchboxtext}">
				<button type="submit" class="btn btn-primary search-button">{#KAHUK_Visual_Search_Go#}</button>
			</div>
		</div>
		<div class="col-md-3">
			<select name="filter" class="form-control" id="submission_filter" onchange="this.form.submit()">
				<option value="all" {if $templatelite.get.filter == "all"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_All#}</option>
				<option value="published" {if $templatelite.get.filter == "published"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Published#}</option>
				<option value="new" {if $templatelite.get.filter == "new"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_New#}</option>
				<option value="draft" {if $templatelite.get.filter == "draft"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Draft#}</option>
				<option value="scheduled" {if $templatelite.get.filter == "scheduled"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Scheduled#}</option>
				<option value="discard" {if $templatelite.get.filter == "discard"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Discarded#}</option>
				<option value="moderated" {if $templatelite.get.filter == "moderated"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Moderated#}</option>
				<option value="spam" {if $templatelite.get.filter == "spam"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Spam#}</option>
				<option value="all">   ---   </option>
				<option value="today" {if $templatelite.get.filter == "today"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Today#}</option>
				<option value="yesterday" {if $templatelite.get.filter == "yesterday"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Yesterday#}</option>
				<option value="week" {if $templatelite.get.filter == "week"} selected="selected" {/if}>{#KAHUK_Visual_AdminPanel_Week#}</option>
			</select>
		</div>
		<div class="col-md-3">
			<select name="pagesize" class="form-control" id="submission_sort" onchange="this.form.submit()">
				<option value="15" {if isset($pagesize) && $pagesize == 15}selected{/if}>Show 15</option>
				<option value="30" {if isset($pagesize) && $pagesize == 30}selected{/if}>Show 30</option>
				<option value="50" {if isset($pagesize) && $pagesize == 50}selected{/if}>Show 50</option>
				<option value="100" {if isset($pagesize) && $pagesize == 100}selected{/if}>Show 100</option>
				<option value="200" {if isset($pagesize) && $pagesize == 200}selected{/if}>Show 200</option>
			</select>
		</div>
	</form>
</div>

<form name="bulk_moderate" id="user_list_form" action="{$kahuk_base_url}/admin/admin_links.php?action=bulkmod&page={$templatelite.get.page|sanitize:2}" method="post" onsubmit="return validate_all_user_action()">
	<input type="hidden" name="admin_acction"  value="" id="admin_action"/>
	{$hidden_token_admin_links_edit}
	<table class="table table-bordered table-condensed">
		<tr>
			<th style="text-align:center;vertical-align:middle;"><input type='checkbox' id="selectall_user_ed" name="all1"></th>
			{checkActionsTpl location="tpl_kahuk_admin_submissions_th_start"}
			<th>{#KAHUK_Visual_View_Links_Author#}</th>
			<th>{#KAHUK_Visual_View_Links_New_Window#}</th>
			<th nowrap style="text-align:center;">{#KAHUK_Visual_AdminPanel_Group_Date#}</th>
			<th nowrap style="text-align:center;">{#KAHUK_Visual_View_Links_Status#}</th>
			{checkActionsTpl location="tpl_kahuk_admin_submissions_th_end"}
		</tr>
		{section name=id loop=$template_stories}
		<tr {if $template_stories[id].link_status=='moderated'}class="tr_moderated"{/if}>
			<td style="text-align:center;vertical-align:middle;">
				{if $userlist[nr].user_level neq 'admin'}      
					<input type="checkbox" name="link[{$template_stories[id].link_id}]" class="enabled_disable"  value="1" usernameval="{$template_stories[id].link_author}"/>
				{/if} 
			</td>
			{checkActionsTpl location="tpl_kahuk_admin_submissions_td_start"}
			<td><a href="{$kahuk_base_url}/admin/admin_users.php?mode=view&user={$template_stories[id].link_author}" title="{$template_stories[id].link_author}'s Articles" id="link-{$template_stories[id].link_id}-author">{$template_stories[id].link_author}</a> {checkActionsTpl location="tpl_kahuk_admin_links_extra"}</td>
			<td>
				<a href='{$my_kahuk_base}/editlink.php?id={$template_stories[id].link_id}'><i class="fa fa-edit" title="{#KAHUK_Visual_AdminPanel_Page_Edit#}" alt="{#KAHUK_Visual_AdminPanel_Page_Edit#}"></i></a>
				<a href="{$kahuk_base_url}/story.php?title={$template_stories[id].link_title_url}" title="{$template_stories[id].link_title|truncate:50:"...":true}" >{$template_stories[id].link_title}</a>
			</td>
			<td style="text-align:center;vertical-align:middle;">{$template_stories[id].link_date}</td>
			<td style="text-align:center;vertical-align:middle;">
				{if $template_stories[id].link_status=='new'} 
					{#KAHUK_Visual_AdminPanel_New#}
				{elseif $template_stories[id].link_status=='published'}
					{#KAHUK_Visual_AdminPanel_Published#}
				{elseif $template_stories[id].link_status=='discard'}
					{#KAHUK_Visual_AdminPanel_Discarded#}
				{elseif $template_stories[id].link_status=='spam'} 
					{#KAHUK_Visual_AdminPanel_Spam#}
				{elseif $template_stories[id].link_status=='draft'} 
					{#KAHUK_Visual_AdminPanel_Draft#}
				{elseif $template_stories[id].link_status=='scheduled'} 
					{#KAHUK_Visual_AdminPanel_Scheduled#}	
				{elseif $template_stories[id].link_status=='moderated'} 
					{#KAHUK_Visual_AdminPanel_Moderated#}
				{/if}
			</td>
			{checkActionsTpl location="tpl_kahuk_admin_submissions_td_end"}
		</tr>	
		{/section}
	</table>
	<div style="clear:both;"> </div>
</form>
<div style="float:right;margin-top:6px;">
	<a data-toggle="modal" href="{$kahuk_base_url}/admin/admin_delete_stories.php" class="btn btn-danger"><i class="fa fa-trash-o fa-white"></i> {#KAHUK_Visual_AdminPanel_Delete_Stories#}</a>
</div>
<div style="clear:both;"> </div>
<SCRIPT>
var confirmation = "{#KAHUK_Visual_AdminPanel_Confirm_Killspam#}\n";
{literal}
function mark_all_publish() {
	document.bulk_moderate.all1.checked=1;
	document.bulk_moderate.all2.checked=0;
	document.bulk_moderate.all3.checked=0;
	document.bulk_moderate.all4.checked=0;
	for (var i=0; i< document.bulk_moderate.length; i++) {
		if (document.bulk_moderate[i].value == "published") {
			document.bulk_moderate[i].checked = true;
		}
	}
}
function mark_all_discard() {
	document.bulk_moderate.all1.checked=0;
	document.bulk_moderate.all2.checked=0;
	document.bulk_moderate.all3.checked=0;
	document.bulk_moderate.all4.checked=1;
	for (var i=0; i< document.bulk_moderate.length; i++) {
		if (document.bulk_moderate[i].value == "discard") {
			document.bulk_moderate[i].checked = true;
		}
	}
}
function mark_all_new() {
	document.bulk_moderate.all1.checked=0;
	document.bulk_moderate.all2.checked=1;
	document.bulk_moderate.all3.checked=0;
	document.bulk_moderate.all4.checked=0;
	for (var i=0; i< document.bulk_moderate.length; i++) {
		if (document.bulk_moderate[i].value == "new") {
			document.bulk_moderate[i].checked = true;
		}
	}
}
function mark_all_spam() {
	document.bulk_moderate.all1.checked=0;
	document.bulk_moderate.all2.checked=0;
	document.bulk_moderate.all3.checked=1;
	document.bulk_moderate.all4.checked=0;
	for (var i=0; i< document.bulk_moderate.length; i++) {
		if (document.bulk_moderate[i].value == "spam") {
			document.bulk_moderate[i].checked = true;
		}
	}
}
function uncheck_all() {
	document.bulk_moderate.all1.checked=0;
	document.bulk_moderate.all2.checked=0;
	document.bulk_moderate.all3.checked=0;
	document.bulk_moderate.all4.checked=0;
	for (var i=0; i< document.bulk_moderate.length; i++) {
		if ((document.bulk_moderate[i].value == "new")||(document.bulk_moderate[i].value == "discard")||(document.bulk_moderate[i].value == "spam")|| (document.bulk_moderate[i].value == "published")){
			document.bulk_moderate[i].checked = false;
		}
	}
}
function confirm_spam() {
    var checks = document.getElementsByTagName('INPUT');
    var authors = new Array();
    for (var i=0; i<checks.length; i++)
     	if (checks[i].type=="radio" && checks[i].checked && checks[i].value=="spam")
        {
            old = document.getElementById(checks[i].id+'-old');
            if (old.value!='spam')
            {
                author = document.getElementById(checks[i].id+'-author');
                authors[author.innerHTML] = 1;
            }
        }

    var message = "";
    for (name in authors)
	if (authors[name]==1)
            message += name+"\n";
    if (message.length > 0)
        	return confirm(confirmation+message);
    return 1;
}
</SCRIPT>
{/literal}
<!--/submissions.tpl -->