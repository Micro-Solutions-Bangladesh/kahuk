<legend>Comments</legend>

<form name="todo_a_good_name" id="todo_a_good_id" action="{$permalink}" method="post">
	{$hidden_token_comments_edit}

	<div class="flex gap-8 mb-8 justify-between">
		<div class="flex gap-4">
			<div class="col-span-9">
				{$comment_statuses_markup}
			</div>
			<input type="submit" name="submit" value="Apply" class="btn btn-primary">
		</div>

		<div class="col-span-3">
			{$comment_pagesizes_markup}
		</div>
	</div>

	<table class="table table-bordered table-condensed" width="100%">
		<tr>
			<th style="text-align:center;vertical-align:middle;">
				<input type='checkbox' id="checkAll" name="checkAll" >
			</th>
			
			<th style="width:125px;">{#KAHUK_Visual_View_Links_Author#}</th>
			<th>{#KAHUK_MiscWords_Comment#}</th>
			<th>{#KAHUK_Visual_User_NewsSent#}</th>
			<th nowrap style="text-align:center;">{#KAHUK_Visual_View_Links_Status#}</th>
		</tr>
		
		{if isset($data_items)}
			{section name=id loop=$data_items}
			<tr {if $data_items[id].comment_status=='moderated'}class="tr_moderated"{/if}>
				<td style="text-align:center;">
					<input type="checkbox" name="comment[{$data_items[id].comment_id}]" class="enabled_disable"  value="1" usernameval="{$data_items[id].comment_author}"/>
				</td>
				
				<td>
					<a href="" title="{$data_items[id].user_email}'s Profile">
						{$data_items[id].user_login}
					</a>
				</td>
				<td style="text-align:justify;">
					<div class="flex flex-col gap-4">
						<div>
							{$data_items[id].comment_content}
						</div>
						<div class="flex gap-4">
						<a href="{$kahuk_base_url}/story.php?id={$data_items[id].comment_link_id}#c{$data_items[id].comment_id}" title="{$data_items[id].comment_content_long|truncate:50:"...":true}">
							View Story
						</a>
							<a href="{$kahuk_base_url}/edit.php?id={$data_items[id].comment_link_id}&commentid={$data_items[id].comment_id}">
								Edit Comment
							</a>
						</div>
					</div>
				</td>
				<td width="160px">{$data_items[id].comment_date}</td>
				<td width="120px" style="text-transform:capitalize;">
					{$data_items[id].comment_status}
				</td>
			</tr>
			{/section}
		{/if}		
	</table>
</form>
<!--/comments.tpl -->

{$pagination}

{literal}
<script>
(function($) {
	//
	$("#checkAll").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	//
	$('#tool_pagesize').change( function() {
		{/literal}const redirectTo = '{$page_url}';{literal}
		location.href = redirectTo + '?pagesize=' + $(this).val();;
	});
})(jQuery);
</script>
{/literal}
