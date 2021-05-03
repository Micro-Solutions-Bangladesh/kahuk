{************************************
******** User Search Results ********
 This template controls the user search results pages
*************************************}

<!-- user_search_center.tpl -->

<div class="row">
	<div class="col-md-12">
		<div class="input-group">
			<form action="{$my_kahuk_base}/user.php" method="get" {php} global $URLMethod, $my_base_url, $my_kahuk_base; if ($URLMethod==2) print "onsubmit='document.location.href=\"{$kahuk_base_url}/user/search/\"+encodeURIComponent(this.keyword.value); return false;'";{/php}>
				<span class="input-group-btn">
					<input type="hidden" name="view" value="search">
					<input type="text" name="keyword" class="form-control" placeholder="{#KAHUK_Visual_User_Search_Users#}">
					<button class="btn btn-primary" type="submit">Search Accounts</button>
				</span>
			</form>	
		</div><!-- /input-group -->
	</div><!-- /.col-md-6 -->
</div><!-- /.row -->

<hr />

{***********************************************************************************}
{if $user_view eq 'search'}
	{if $userlist}
		<h4>{#KAHUK_Visual_Search_SearchResults#} &quot;{$search}&quot;</h4>
		<table class="table table-bordered table-striped">
			<thead class="table_title">
				<tr>
					<th>{#KAHUK_Visual_Login_Username#}</th>
					<th>{#KAHUK_Visual_User_Profile_Joined#}</th>
					<th>{#KAHUK_User_Profile_Social#}</th>
					{if $Allow_Friends}<th>Add/Remove</th>{/if}
				</tr>
			</thead>
			<tbody id="user_search_body">
				{section name=nr loop=$userlist}
					<tr>
						<td>
							<img src="{$userlist[nr].Avatar}" align="absmiddle" /> 
							<a href="{$URL_user, $userlist[nr].user_login}">{$userlist[nr].user_login|capitalize}</a> {checkActionsTpl location="tpl_kahuk_search_friend_center"}</td>
						<td>
							{* {$userlist[nr].user_date} *}
							{php}
								$kahuk_date = $this->_vars['userlist'][$this->_sections['nr']['index']]['user_date'];
								echo date("F d, Y", strtotime($kahuk_date));
							{/php}
						</td>
						<td>
							{checkActionsTpl location="tpl_user_profile_social_start"}
							{if $userlist[nr].user_skype}
								<a href="callto://{$userlist[nr].user_skype}" title="Skype {$userlist[nr].user_login|capitalize}}" rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i class="fa fa-circle fa-stack-2x opacity_reset" style="color:#00aaf1;"></i><i class="fa fa-skype fa-stack-1x fa-inverse opacity_reset"></i></span></a>
							{/if}
							{if $userlist[nr].user_facebook}
								<a href="http://www.facebook.com/{$userlist[nr].user_facebook}" title="{$userlist[nr].user_login|capitalize}} on Facebook" rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i class="fa fa-circle fa-stack-2x opacity_reset" style="color:#3c5b9b;"></i><i class="fa fa-facebook fa-stack-1x fa-inverse opacity_reset"></i></span></a>
							{/if}
							{if $userlist[nr].user_twitter}
								<a href="http://twitter.com/{$userlist[nr].user_twitter}" title="{$userlist[nr].user_login|capitalize}} on Twitter" rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i class="fa fa-circle fa-stack-2x opacity_reset" style="color:#2daae1;"></i><i class="fa fa-twitter fa-stack-1x fa-inverse opacity_reset"></i></span></a>
							{/if}
							{if $userlist[nr].user_linkedin}
								<a href="http://www.linkedin.com/in/{$userlist[nr].user_linkedin}" title="{$userlist[nr].user_login|capitalize}} on LinkedIn" rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i class="fa fa-circle fa-stack-2x opacity_reset" style="color:#0173b2;"></i><i class="fa fa-linkedin fa-stack-1x fa-inverse opacity_reset"></i></span></a>
							{/if}
							{if $userlist[nr].user_googleplus}
								<a href="https://plus.google.com/{$userlist[nr].user_googleplus}" title="{$userlist[nr].user_login|capitalize}} on Google+" rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i class="fa fa-circle fa-stack-2x opacity_reset" style="color:#f63e28;"></i><i class="fa fa-google-plus fa-stack-1x fa-inverse opacity_reset"></i></span></a>
							{/if}
							{if $userlist[nr].user_pinterest}
								<a href="http://pinterest.com/{$userlist[nr].user_pinterest}/" title="{$userlist[nr].user_login|capitalize}} on Pinterest" rel="nofollow noopener noreferrer" target="_blank"><span class="fa-stack fa-lg opacity_reset"><i class="fa fa-circle fa-stack-2x opacity_reset" style="color:#cb2027;"></i><i class="fa fa-pinterest fa-stack-1x fa-inverse opacity_reset"></i></span></a>
							{/if}
							{checkActionsTpl location="tpl_user_profile_social_end"}
						</td>
						{if $user_login neq $user_logged_in && $user_authenticated eq true}
							{if $Allow_Friends}
								<td style="text-align:center;">{if $userlist[nr].status eq '' || $userlist[nr].status eq 'follower'}	
										<a href="{$userlist[nr].add_friend}" class="btn btn-success">{#KAHUK_Follow#}</a>
									{elseif $userlist[nr].status eq 'following' || $userlist[nr].status eq 'mutual'}
										<a href="{$userlist[nr].remove_friend}" class="btn btn-danger">{#KAHUK_Unfollow#}</a>
									{/if}
								</td>
							{/if}
						{else}
							<td>&nbsp;</td>
						{/if}
					</tr>
				{/section}
			</tbody>
		</table>
	{else}
		<h3>{#KAHUK_Visual_Search_NoResults#} '{$search}'</h3>
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

<!--/user_search_center.tpl -->