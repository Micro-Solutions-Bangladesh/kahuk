{literal}
<script type="text/javascript">
	$(function () {
		$("[rel='tooltip']").tooltip();
	});
</script>

<script type="text/javascript" language="javascript">
function submit_list_form(){	
	if($(".enabled_disable:checked").length==0) {
		alert("Please select users");
		return false;
	}
	
	val_action=$("#admin_action").val();
	
	if(val_action==3){
		var usernames ="";
		$('.enabled_disable:checked').each(function(i){
			usernames += $(this).attr("usernameval")+", ";
		});
		if(confirm("CAUTION: Using killspam will change the user's status to sapmmer and deletes all his activity (submissions, coments, groups, etc and blacklists all the domains of the stories submitted! If you want to mark A PARTICULAR COMMENT, use the gear that appears on the right side of the comment and select \"Delete\" OR from the Comments admin page, select Moderated or Discard\r\rAre you sure that you want to killspam these users: "+usernames)){
        
		} else {
			return false;
		}
	}
	
	document.getElementById("user_list_form").submit();
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
	if(acc==1){
		$("#selected_action").addClass("fa fa-check-square-o");
		$("#selected_action").removeClass("fa fa-ban");
		$("#selected_action").removeClass("fa fa-fa-square-o");
		$("#selected_action").removeClass("fa fa-trash-o");
		$("#admin_action").val(1);
	}
	if(acc==2){
		$("#selected_action").addClass("fa fa-fa-square-o");
		$("#selected_action").removeClass("fa fa-check-square-o");
		$("#selected_action").removeClass("fa fa-ban");
		$("#selected_action").removeClass("fa fa-trash-o");
		$("#admin_action").val(2);
	}
	if(acc==3){
		$("#selected_action").addClass("fa fa-ban");
		$("#selected_action").removeClass("fa fa-fa-square-o");
		$("#selected_action").removeClass("fa fa-check-square-o");
		$("#selected_action").removeClass("fa fa-trash-o");
		$("#admin_action").val(3);
	}
	if(acc==4){
		$("#selected_action").addClass("fa fa-trash-o");
		$("#selected_action").removeClass("fa fa-fa-square-o");
		$("#selected_action").removeClass("fa fa-check-square-o");
		$("#selected_action").removeClass("fa fa-ban");
		$("#admin_action").val(4);
	}
	submit_list_form(); 
}

function validate_all_user_action(){
	if($("#admin_action").val()==""){
		alert("select user list");
		return false;
	}
}
</script>
{/literal}
<legend>{#KAHUK_Visual_AdminPanel_User_Manage#}</legend>

{include file=$the_template"/template-parts/session-messages.tpl"}

{if $moderated_users_count > 0}
	<div class="alert alert-warning">
		There {if $moderated_users_count eq "1"}is{else}are{/if} <strong>{$moderated_users_count} {if $moderated_users_count eq "1"}user{else}users{/if}</strong> awaiting moderation.<br />
		<a href="admin_users.php?filter=disabled">Click here to review the {if $moderated_users_count eq "1"}acccount{else}accounts{/if}.</a>
	</div>
{/if}

<form action="{$kahuk_base_url}/admin/admin_users.php" method="get" >
	<div class="row management-tools">
		<div class="col-md-3">
			<div class="btn-group pull">
				<a class="btn btn-default dropdown-toggle" href="#" data-toggle="dropdown">
					<i id="selected_action"></i>
					{#KAHUK_Visual_AdminPanel_Apply_Changes#}
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li>
						<a onclick="set_admin_action('1')" href="#">
							<i class="fa fa-check-square-o"></i>
							{#KAHUK_Visual_User_Profile_Enabled#}
						</a>
					</li>
{* Redwine: Roles and permissions and Groups fixes *}
					{if $amIadmin eq '1'}
					<li>
						<a onclick="set_admin_action('2')" href="#">
							<i class="fa fa-square-o"></i>
							{#KAHUK_Visual_User_Profile_Disabled#}
						</a>
					</li>
					<li>
						<a onclick="set_admin_action('3')" href="#">
							<i class="fa fa-ban"></i>
							{#KAHUK_Visual_KillSpam#}
						</a>
					</li>
					<li>
						<a onclick="set_admin_action('4')" href="#">
							<i class="fa fa-trash-o"></i>
							{#KAHUK_LANG_DELETE#}
						</a>
					</li>
{* Redwine: Roles and permissions and Groups fixes *}
					{/if}
				</ul>
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-append">
				<input type="hidden" name="mode" value="search">
				{if isset($templatelite.get.keyword) && $templatelite.get.keyword neq ""}
					{assign var=searchboxtext value=$templatelite.get.keyword|sanitize:2}
				{else}
					{assign var=searchboxtext value=#KAHUK_Visual_Search_SearchDefaultText#}			
				{/if}
				<input type="text" class="form-control search-query" name="keyword" placeholder="{$searchboxtext}">
				<button type="submit" class="btn btn-primary">{#KAHUK_Visual_Search_Go#}</button>
			</div>
		</div>			
		<div class="col-md-3">
			<select name="filter" class="form-control" id="user_filter" onchange="this.form.submit()">
				<option value=""> -- User Level -- </option>
				<option value="admin" {if $templatelite.get.filter == "admin"} selected="selected" {/if}>Admin</option>
				<option value="moderator" {if $templatelite.get.filter == "moderator"} selected="selected" {/if}>Moderator</option>
				<option value="normal" {if $templatelite.get.filter == "normal"} selected="selected" {/if}>Normal</option>
				<option value="spammer" {if $templatelite.get.filter == "spammer"} selected="selected" {/if}>Spammer</option>
			</select>
		</div>
		<div class="col-md-3">
			<select name="pagesize" class="form-control" id="user_sort" onchange="this.form.submit()">
				<option value="15" {if isset($pagesize) && $pagesize == 15}selected{/if}>Show 15</option>
				<option value="30" {if isset($pagesize) && $pagesize == 30}selected{/if}>Show 30</option>
				<option value="50" {if isset($pagesize) && $pagesize == 50}selected{/if}>Show 50</option>
				<option value="100" {if isset($pagesize) && $pagesize == 100}selected{/if}>Show 100</option>
				<option value="200" {if isset($pagesize) && $pagesize == 200}selected{/if}>Show 200</option>
			</select>
		</div>
	</div>
</form>

{if isset($usererror)}
	<span class="error">{$usererror}</span><br/>
{/if}

<form name="user_list_form" id="user_list_form" action="{$kahuk_base_url}/admin/admin_users.php" method="post" onsubmit="return validate_all_user_action()">
	<input type="hidden" name="frmsubmit" value="userlist" />	
	<input type="hidden" name="admin_acction"  value="" id="admin_action"/>
	{$hidden_token_admin_users_list}
	<table class="table table-bordered table-bordered table-condensed tablesorter" id="tablesorter-userTable">
		<thead>
			<tr>
				<th style="text-align:center;vertical-align:middle;"><input type='checkbox' id="selectall_user_ed" name="all1"></th>
				
				<th style="min-width:50px;text-align:center;">ID</th>
				<th style="min-width:95px;">{#KAHUK_Visual_Login_Username#}</th>
				<th style="min-width:75px;text-align:center;">{#KAHUK_Visual_View_User_Level#}</th>
				<th>{#KAHUK_Visual_View_User_Email#}</th>
				<th style="min-width:85px">{#KAHUK_Visual_User_Profile_Joined#}</th>
				<th style="min-width:50px;text-align:center;">Status</th>
				
			</tr>
		</thead>
		<tbody>
			{section name=nr loop=$userlist}
				<tr class="{if $userlist[nr].user_status eq 'disable'}tr_moderated {/if}">
					<td style="text-align:center;vertical-align:middle;">
						{if $userlist[nr].user_level neq 'admin'}      
							<input type="checkbox" name="enabled[{$userlist[nr].user_id}]" class="enabled_disable"  value="1" usernameval="{$userlist[nr].user_login}"/>
						{/if} 
					</td>
					
					<td style="text-align:center;vertical-align:middle;">{$userlist[nr].user_id}</td>

					<td class="flex gap-2">
						<img src="{$userlist[nr].Avatar.small}" style="height:40px;width:40px;" />
						<a href="?mode=view&user={$userlist[nr].user_id}">
							{$userlist[nr].user_login}<br>{$userlist[nr].user_karma}
						</a>
					</td>

					<td style="text-align:center;vertical-align:middle;">{$userlist[nr].user_level}</td>
					<td style="vertical-align:middle;">
						{* Display email field only to Site Admins *}
						{if $amIadmin eq '1'}
							{if $userlist[nr].user_level eq 'unverified'}
								<a data-toggle="modal" href="{$kahuk_base_url}/admin/admin_user_validate.php?id={$userlist[nr].user_id}" title="{#KAHUK_Visual_AdminPanel_Unconfirmed_Email#}">
									<i class="fa fa-warning unconfirmed-email" rel="tooltip" data-placement="left" data-toggle="tooltip" data-original-title="{#KAHUK_Visual_AdminPanel_Unconfirmed_Email#}"></i>
								</a>
							{else}
								<i class="fa fa-check confirmed-email" title="{#KAHUK_Visual_AdminPanel_Confirmed_Email#}" alt="{#KAHUK_Visual_AdminPanel_Confirmed_Email#}"></i>							
							{/if}
							<a href="mailto:{$userlist[nr].user_email}" target="_blank" rel="noopener noreferrer">{$userlist[nr].user_email|truncate:25:"...":true}</a>
						{/if}
					</td>
					<td>{$userlist[nr].user_date}</td>
					<td style="text-align:center;vertical-align:middle;">
						{if $userlist[nr].user_level eq 'spammer'}
							<i class="fa fa-ban" title="{#KAHUK_Visual_AdminPanel_Spam#}"></i> {#KAHUK_Visual_AdminPanel_Spam#}
						{elseif $userlist[nr].user_status eq 'enable'}
							<i class="fa fa-check-square-o" title="{#KAHUK_Visual_User_Profile_Enabled#}"></i> {#KAHUK_Visual_User_Profile_Enabled#}
						{else}
							<i class="fa fa-square-o" title="{#KAHUK_Visual_User_Profile_Disabled#}"></i> {#KAHUK_Visual_User_Profile_Disabled#}
						{/if}
					</td>
					
				</tr>
			{/section}
		</tbody>
	</table>
</form>
{* Roles and permissions and Groups fixes. Only Site Admins can create users accounts *}
{if $isAdmin eq '1'}
{include file="/admin/user_create.tpl"}
<div style="float:right;margin:8px 2px 0 0;">
	<a class="btn btn-success"  href="#createUserForm-modal" data-toggle="modal" title="{#KAHUK_Visual_AdminPanel_New_User#}">{#KAHUK_Visual_AdminPanel_New_User#}</a>
</div>
{* Roles and permissions and Groups fixes. Only Site Admins can create users accounts *}
{/if}
<div style="clear:both;"></div>

<SCRIPT>
{literal}
function check_all(elem) {
	for (var i=0; i< document.bulk.length; i++) 
		if (document.bulk[i].id == "killspam")
			document.bulk[i].checked = elem.checked;
}
{/literal}
</SCRIPT>
{* End: users.tpl *}
