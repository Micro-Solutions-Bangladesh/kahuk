<!-- comments.tpl -->
{literal}
<script type="text/javascript" language="javascript">
function submit_list_form(){
	
	if($(".enabled_disable:checked").length==0) {
		alert("Please select comments");
		return false;
	}
	
	val_action=$("#admin_action").val();
	
	if(val_action=="spam"){
		var usernames ="";
		$('.enabled_disable:checked').each(function(i){
			usernames += $(this).attr("usernameval")+", ";
		});
		if(confirm("Are you sure that you want to killspam these users: "+usernames)){
        
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
		{$templatelite.get.user|sanitize:2}'s {#PLIGG_Visual_Story_Comments#}
	{else}
		{#PLIGG_Visual_AdminPanel_Comments_Legend#}
	{/if}
</legend>
{if $moderated_comments_count neq "0"}
	<div class="alert alert-warning">
		There {if $moderated_comments_count eq "1"}is{else}are{/if} <strong>{$moderated_comments_count} {if $moderated_comments_count eq "1"}comment{else}comments{/if}</strong> awaiting moderation.<br />
		<a href="admin_comments.php?filter=moderated">Click here to review {if $moderated_comments_count eq "1"}it{else}them{/if}.</a>
	</div>
{/if}

<div class="row management-tools">
	<div class="col-md-3">
		<div class="btn-group pull">
			<a class="btn btn-default dropdown-toggle" href="#" data-toggle="dropdown">
				<i id="selected_action"></i> {#PLIGG_Visual_AdminPanel_Apply_Changes#} <span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a onclick="set_admin_action('published')" href="#"><i class="fa fa-check-square-o"></i>{#PLIGG_Visual_AdminPanel_Publish#}</a></li>
				<li><a onclick="set_admin_action('moderated')" href="#"><i class="fa fa-exclamation-triangle"></i>{#PLIGG_Visual_AdminPanel_Moderated#}</a></li>
				<li><a onclick="set_admin_action('discard')" href="#"><i class="fa fa-trash-o"></i>{#PLIGG_Visual_AdminPanel_Discard#}</a></li>
				<li><a onclick="set_admin_action('spam')" href="#"><i class="fa fa-ban"></i>{#PLIGG_Visual_AdminPanel_Spam#}</a></li>
			</ul>
		</div>
    </div>
	
	<form action="{$my_base_url}{$my_pligg_base}/admin/admin_comments.php" method="get" class="form-search search-bar">
		<div class="col-md-3">
			<div class="input-append">
				<input type="hidden" name="mode" value="search">
				{if isset($templatelite.get.keyword) && $templatelite.get.keyword neq ""}
					{assign var=searchboxtext value=$templatelite.get.keyword|sanitize:2}
				{else}
					{assign var=searchboxtext value=#PLIGG_Visual_Search_SearchDefaultText#}			
				{/if}
				<input type="hidden" name="user" value="{$templatelite.get.user|sanitize:2}">
				<input type="text" class="form-control search-query" name="keyword" placeholder="{$searchboxtext}">
				<button type="submit" class="btn btn-primary">{#PLIGG_Visual_Search_Go#}</button>
			</div>
		</div>
		<div class="col-md-3">
			<select name="filter" class="form-control" id="comment_filter" onchange="this.form.submit()">
				<option value="all" {if $templatelite.get.filter == "all"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_All_Comments#}</option>
				<option value="published" {if $templatelite.get.filter == "published"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Published#}</option>
				<option value="moderated" {if $templatelite.get.filter == "moderated"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Moderated#}</option>
				<option value="discard" {if $templatelite.get.filter == "discard"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Discarded#}</option>
				<option value="spam" {if $templatelite.get.filter == "spam"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Spam#}</option>
				<option value="all">   ---   </option>
				<option value="today" {if $templatelite.get.filter == "today"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Today#}</option>
				<option value="yesterday" {if $templatelite.get.filter == "yesterday"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Yesterday#}</option>
				<option value="week" {if $templatelite.get.filter == "week"} selected="selected" {/if}>{#PLIGG_Visual_AdminPanel_Week#}</option>
			</select>
		</div>
		<div class="col-md-3">
			<select name="pagesize" class="form-control" id="comment_sort" onchange="this.form.submit()">
				<option value="15" {if isset($pagesize) && $pagesize == 15}selected{/if}>Show 15</option>
				<option value="30" {if isset($pagesize) && $pagesize == 30}selected{/if}>Show 30</option>
				<option value="50" {if isset($pagesize) && $pagesize == 50}selected{/if}>Show 50</option>
				<option value="100" {if isset($pagesize) && $pagesize == 100}selected{/if}>Show 100</option>
				<option value="200" {if isset($pagesize) && $pagesize == 200}selected{/if}>Show 200</option>
			</select>
		</div>
	</form>
</div>

<form name="bulk_moderate" id="user_list_form" action="{$my_base_url}{$my_pligg_base}/admin/admin_comments.php?action=bulkmod&page={$templatelite.get.page|sanitize:2}" method="post">
	<input type="hidden" name="admin_acction"  value="" id="admin_action"/>
	{$hidden_token_comments_edit}
	<table class="table table-bordered table-condensed" width="100%" >
		<thead>
			<tr>
				<th style="text-align:center;vertical-align:middle;"><input type='checkbox' id="selectall_user_ed" name="all1" ></th>
				{checkActionsTpl location="tpl_pligg_admin_comments_th_start"}
				<th style="width:125px;">{#PLIGG_Visual_View_Links_Author#}</th>
				<th>{#PLIGG_MiscWords_Comment#}</th>
				<th>{#PLIGG_Visual_User_NewsSent#}</th>
				<th nowrap style="text-align:center;">{#PLIGG_Visual_View_Links_Status#}</th>
				{checkActionsTpl location="tpl_pligg_admin_comments_th_end"}
			</tr>
		</thead>
		{if isset($template_comments)}
			{section name=id loop=$template_comments}
			<tr {if $template_comments[id].comment_status=='moderated'}class="tr_moderated"{/if}>
				<td style="text-align:center;"><input type="checkbox" name="comment[{$template_comments[id].comment_id}]" class="enabled_disable"  value="1" usernameval="{$template_comments[id].comment_author}"/></td>
				{checkActionsTpl location="tpl_pligg_admin_comments_td_start"}
				<td><a href="{$my_base_url}{$my_pligg_base}/admin/admin_users.php?mode=view&user={$template_comments[id].comment_author}" title="{$template_comments[id].comment_author}'s Profile" id="comment-{$template_comments[id].comment_id}-author">{$template_comments[id].comment_author}</a></td>
				<td style="text-align:justify;">
					<a href="{$my_base_url}{$my_pligg_base}/edit.php?id={$template_comments[id].comment_link_id}&commentid={$template_comments[id].comment_id}"><i class="fa fa-edit" title="{#PLIGG_Visual_AdminPanel_Page_Edit#}" alt="{#PLIGG_Visual_AdminPanel_Page_Edit#}"></i></a>
					<a href="{$my_base_url}{$my_pligg_base}/story.php?id={$template_comments[id].comment_link_id}#c{$template_comments[id].comment_id}" title="{$template_comments[id].comment_content_long|truncate:50:"...":true}">{$template_comments[id].comment_content}</a>
					<input type='hidden' name='old[{$template_comments[id].comment_id}]' id="comment-{$template_comments[id].comment_id}-old" value='{$template_comments[id].comment_status}'>
				</td>
				<td width="240px">{$template_comments[id].comment_date}</td>
				<td style="text-align:center;vertical-align:middle;text-transform:capitalize;">{$template_comments[id].comment_status}</td>
				{checkActionsTpl location="tpl_pligg_admin_comments_td_end"}
			</tr>
			{/section}
		{/if}		
	</table>
</form>

<div style="float:right;margin:8px 2px 0 0;">
	<a data-toggle="modal" class="btn btn-danger" href="{$my_base_url}{$my_pligg_base}/admin/admin_delete_comments.php"><i class="fa fa-trash-o fa-white"></i> {#PLIGG_Visual_AdminPanel_Delete_Comments#}</a> 
</div>

<div style="clear:both;"> </div>
</form>

<SCRIPT>
var confirmation = "{#PLIGG_Visual_AdminPanel_Confirm_Killspam#}\n";
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
		if (document.bulk_moderate[i].value == "moderated") {
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
		if ((document.bulk_moderate[i].value == "new")||(document.bulk_moderate[i].value == "discard")||(document.bulk_moderate[i].value == "spam")|| (document.bulk_moderate[i].value == "publish")){
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
<!--/comments.tpl -->