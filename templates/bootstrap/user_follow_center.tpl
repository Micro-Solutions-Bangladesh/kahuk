{************************************
******* User Follow Template ********
 This template controls the user friend/follower pages
*************************************}
{include file=$the_template"/user_navigation.tpl"}

<!-- user_follow_center.tpl -->
{***********************************************************************************}
{if $user_view eq 'removefriend'}
	<div class="alert alert-warning">
		<button class="close" data-dismiss="alert">&times;</button>
		{#KAHUK_Visual_User_Profile_Friend_Removed#}
	</div>
{/if}
{***********************************************************************************}
{if $user_view eq 'addfriend'}
	<div class="alert alert-success">
		<button class="close" data-dismiss="alert">&times;</button>
		{#KAHUK_Visual_User_Profile_Friend_Added#}
	</div>
{/if}
{***********************************************************************************}
{if $user_view eq 'removefriend' || $user_view eq 'addfriend'}
	<div id="profile_container" style="position: relative;">
		<div class="row">
			{checkActionsTpl location="tpl_kahuk_profile_info_start"}
			{checkActionsTpl location="tpl_kahuk_profile_info_middle"}
			<div id="stats" class="col-md-6">
				<table class="table table-bordered table-striped vertical-align">
					<thead class="table_title">
						<tr>
							<th colspan="2">{#KAHUK_Visual_User_Profile_User_Stats#}</th>
						</tr>
					</thead>
					<tbody>
						{if $user_karma > "0.00"}
							<tr>
								<td><strong>{#KAHUK_Visual_Rank#}:</strong></td>
								<td>{$user_rank}</td>
							</tr>
							<tr>
								<td><strong>{#KAHUK_Visual_User_Profile_KarmaPoints#}:</strong></td>
								<td>{$user_karma|number_format:"0"}</td>
							</tr>
						{/if}
						<tr>
							<td><strong>{#KAHUK_Visual_User_Profile_Joined#}:</strong></td>
							<td width="120px">
								{*	{$user_joined}	*}
								{php}
									$kahuk_date = $this->_vars['user_joined'];
									echo date("F d, Y", strtotime($kahuk_date));
								{/php}
							</td>	
						</tr>
						<tr>
							<td><strong>{#KAHUK_Visual_User_Profile_Total_Links#}:</strong></td>
							<td>{$user_total_links}</td>
						</tr>
						<tr>
							<td><strong>{#KAHUK_Visual_User_Profile_Published_Links#}:</strong></td>
							<td>{$user_published_links}</td>
						</tr>
						<tr>
							<td><strong>{#KAHUK_Visual_User_Profile_Total_Comments#}:</strong></td>
							<td>{$user_total_comments}</td>
						</tr>
						<tr>
							<td><strong>{#KAHUK_Visual_User_Profile_Total_Votes#}:</strong></td>
							<td>{$user_total_votes}</td>
						</tr>
						{*
						<tr>
							<td><strong>{#KAHUK_Visual_User_Profile_Published_Votes#}:</strong></td>
							<td>{$user_published_votes}</td>
						</tr>
						*}
					</tbody>
				</table>
			</div>
			{if $enable_group eq "true"}
				<div id="groups" class="col-md-6">
					<table class="table table-bordered table-striped vertical-align">
						<thead class="table_title">
							<tr>
								<th>Group Name</th>
								<th style="width:60px;text-align:center;">Members</th>
							</tr>
						<tbody>
							{if empty($group_display)}
								<tr>
									<td colspan="2">
										{#Kahuk_Profile_No_Membership#}
									</td>
								</tr>
							{else}
								{$group_display}
							{/if}
						</tbody>
					</table>
				</div>
			{/if}
			{if $Allow_Friends neq "0"}
				<div id="following" class="col-md-6">
					<table class="table table-bordered table-striped vertical-align">
						<thead class="table_title">
							<tr>
								<th>{#KAHUK_Visual_Login_Username#}</th>
								{checkActionsTpl location="tpl_kahuk_profile_friend_th"}
								{if $user_authenticated eq true}<th>{#KAHUK_Visual_User_Profile_Add_Friend#} / {#KAHUK_Visual_User_Profile_Remove_Friend#}</th>{/if}
							</tr>
						</thead>
						<tbody>
							{if !empty($following)}
								{foreach from=$following item=myfriend}
									{php}
										$this->_vars['friend_avatar'] = get_avatar('small', $this->_vars['myfriend']['user_avatar_source'], $this->_vars['myfriend']['user_login'], $this->_vars['myfriend']['user_email']);
										$this->_vars['profileURL'] = getmyurl('user2', $this->_vars['myfriend']['user_login'], 'profile');
									    $this->_vars['user_url_remove'] = getmyurl('user_friends', $this->_vars['myfriend']['user_login'], 'removefriend');
										$this->_vars['user_url_add'] = getmyurl('user_friends', $this->_vars['myfriend']['user_login'], 'addfriend');
									{/php}
									<tr>
										<td>
											<a href="{$profileURL}">
												<img src="{$friend_avatar}" align="absmiddle" />
												{$myfriend.user_login}
											</a>
										</td>
										{if $user_login eq $user_logged_in}
											<td>
												<a href="{$my_kahuk_base}/module.php?module=simple_messaging&view=compose&return={$templatelite.server.REQUEST_URI|urlencode}&to={$myfriend.user_login}">
													<span class="btn btn-default"><i class="fa fa-envelope"></i></span>
												</a>
											</td>
										{else}
											<td>&nbsp;</td>
										{/if}
										{checkActionsTpl location="tpl_kahuk_profile_friend_td"}
										{if $user_authenticated eq true && $myfriend.following>0}
											<td>
												<a href="{$user_url_remove}" class="btn btn-danger">{#KAHUK_Visual_User_Profile_Remove_Friend#}</a>
											</td>
										{else}
											<td>
												<a href="{$user_url_add}" class="btn btn-success">{#KAHUK_Visual_User_Profile_Add_Friend#}</a>
											</td>
										{/if}
									</tr>
								{/foreach}
							{else}
								<tr>
									<td colspan="3">
										{$user_username|capitalize} {#KAHUK_Visual_User_Profile_No_Friends#}
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			{/if}
			{checkActionsTpl location="tpl_kahuk_profile_info_end"}
			<div style="clear:both;"> </div>
			{checkActionsTpl location="tpl_kahuk_profile_tab_insert"}
		</div>
	</div>
{/if}
{***********************************************************************************}
{if $user_view eq 'following'}
	<legend>{#KAHUK_Visual_User_Profile_People#} {$user_username|capitalize} {#KAHUK_Visual_User_Profile_Is_Following#}</legend>
	{if $following}
	  	<table class="table table-bordered table-condensed table-striped vertical-align">
			<thead>
				<th>{#KAHUK_Visual_User_Profile_Username#}</th>
				{if $user_authenticated eq true}<th>{#KAHUK_Visual_User_Profile_Add_Friend#} / {#KAHUK_Visual_User_Profile_Remove_Friend#}</th>{/if}
			</thead>
			<tbody>
				{foreach from=$following item=myfriend}
					{php}
						$this->_vars['friend_avatar'] = get_avatar('small', $this->_vars['myfriend']['user_avatar_source'], $this->_vars['myfriend']['user_login'], $this->_vars['myfriend']['user_email']);
						$this->_vars['profileURL'] = getmyurl('user2', $this->_vars['myfriend']['user_login'], 'profile');
					    $this->_vars['user_url_remove'] = getmyurl('user_friends', $this->_vars['myfriend']['user_login'], 'removefriend');
						$this->_vars['user_url_add'] = getmyurl('user_friends', $this->_vars['myfriend']['user_login'], 'addfriend');
					{/php}
					<tr>
						<td>
							<img src="{$friend_avatar}" align="absmiddle" /> <a href="{$profileURL}">{$myfriend.user_login}</a> {checkActionsTpl location="tpl_kahuk_profile_friend_following"}
						</td>
						{if $user_authenticated eq true && $myfriend.following>0}
							<td>
								<a href="{$user_url_remove}" class="btn btn-danger">{#KAHUK_Visual_User_Profile_Remove_Friend#}</a>
							</td>
						{elseif $user_authenticated neq true}
						
						{else}
							<td>
								<a href="{$user_url_add}" class="btn btn-success">{#KAHUK_Visual_User_Profile_Add_Friend#}</a>
							</td>
						{/if}
					</tr>
				{/foreach}
			<tbody>
		</table>
	{else}
		<br /><br />
		<h3 style="text-align:center;">{$user_username|capitalize} {#KAHUK_Visual_User_Profile_No_Friends#}</h3>
	{/if}
{/if}
{***********************************************************************************}
{if $user_view eq 'followers'}
	<legend>{#KAHUK_Visual_User_Profile_Viewing_Friends_2a#} {$user_username|capitalize}</legend>
	{if $follower}
	  	<table class="table table-bordered table-condensed table-striped vertical-align">
			<thead>
				<tr>
					<th>{#KAHUK_Visual_User_Profile_Username#}</th>
					{if check_for_enabled_module('simple_messaging',2.0)}
						<th>{#KAHUK_Visual_User_Profile_Message#}</th>
					{/if}
					{if $user_authenticated eq true}
						<th>{#KAHUK_Visual_User_Profile_Add_Friend#} / {#KAHUK_Visual_User_Profile_Remove_Friend#}</th>
					{/if}
				</tr>
			</thead>
			<tbody>
				{foreach from=$follower item=myfriend}
					{php}
						$this->_vars['friend_avatar'] = get_avatar('small', $this->_vars['myfriend']['user_avatar_source'], $this->_vars['myfriend']['user_login'], $this->_vars['myfriend']['user_email']);
						$this->_vars['profileURL'] = getmyurl('user2', $this->_vars['myfriend']['user_login'], 'profile');
						$this->_vars['addURL'] = getmyurl('user_friends', $this->_vars['myfriend']['user_login'], 'addfriend');
					    $this->_vars['user_url_remove'] = getmyurl('user_friends', $this->_vars['myfriend']['user_login'], 'removefriend');
					{/php}
					<tr>
						<td><img src="{$friend_avatar}" align="absmiddle" /> <a href="{$profileURL}">{$myfriend.user_login}</a></td>
						{if check_for_enabled_module('simple_messaging',2.0) && !empty($myfriend.is_friend)}
							<td><a href="{$my_kahuk_base}/module.php?module=simple_messaging&view=compose&to={$myfriend.user_login}&return={$templatelite.server.REQUEST_URI|urlencode}"><span class="btn btn-sm btn-default"><i class="fa fa-envelope"></i></span></a></td>
						{else}
							<td></td>
						{/if}
						{if $user_authenticated eq true}
							{if !empty($myfriend.is_friend) && $myfriend.is_friend>0}
								<td><a class="btn btn-sm btn-danger" href="{$user_url_remove}">{#KAHUK_Visual_User_Profile_Remove_Friend#}</a></td>
							{else}
								<td><a class="btn btn-sm btn-success" href="{$addURL}">{#KAHUK_Visual_User_Profile_Add_Friend#}</a></td>
							{/if}
						{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		<h4>{#KAHUK_Visual_User_Profile_No_Friends_2#} {$user_username|capitalize}</h4>
	{/if}
{/if}

{***********************************************************************************}

{if isset($user_page)}
	{$user_page}
{/if}

{if isset($user_pagination)}
	{checkActionsTpl location="tpl_kahuk_pagination_start"}
	{$user_pagination}
	{checkActionsTpl location="tpl_kahuk_pagination_end"}
{/if}

{checkActionsTpl location="tpl_kahuk_profile_end"}
<!--/user_follow_center.tpl -->