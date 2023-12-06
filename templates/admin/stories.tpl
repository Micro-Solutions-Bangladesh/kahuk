<legend>Stories</legend>

{include file=$the_template"/template-parts/session-messages.tpl"}

<form name="formSearch" id="formSearch" action="{$permalink}" method="post">
	{$hidden_token_stories_edit}	

	<div class="flex gap-8 mb-8 justify-between">
		<div class="flex gap-4">
			<div class="col-span-9">
				{$tool_process_markup}
			</div>
		</div>

		<div class="">
			<input type="text" name="tool_keyword" value="{$tool_keyword|htmlentities}" class="form-control" placeholder="Search Term">
		</div>

		<div class="">
			{$tool_status_markup}
		</div>

		<div>
			<button data-task="tool_process" class="process-btn btn btn-primary">Apply</button>
		</div>

		<div class="">
			{$pagesizes_markup}
		</div>
	</div>

	<div id="cbwrap"></div>
</form>

<form name="formListing" id="formListing" action="">
	<table class="table table-bordered table-condensed" width="100%">
		<tr>
			<th style="text-align:center;vertical-align:middle;">
				<input type='checkbox' id="checkAll" name="checkAll" >
			</th>
			<th>Author</th>
			<th>Title</th>
			<th style="width:125px; text-align:center;" nowrap>Date Created</th>
			<th nowrap style="text-align:center;">Status</th>
		</tr>
		
		{if isset($data_items)}
			{section name=id loop=$data_items}
			<tr>
				<td style="text-align:center;">
					<input type="checkbox" data-id="{$data_items[id].link_id}" name="cb_[{$data_items[id].link_id}]" class="cbi enabled_disable" value="1"/>
				</td>
				<td style="width:140px;">
					<a href="{$page_url}?tool_keyword=author:{$data_items[id].user_login}" title="{$data_items[id].user_email}'s Profile">
						{$data_items[id].user_login}
					</a>
				</td>
				<td style="text-align:justify;">
					<div class="flex flex-col gap-4">
						<div>
							{$data_items[id].link_title}
						</div>
						<div class="flex gap-4">
							<a href="{$data_items[id].story_url}">View Story</a>
							<span>|</span>
							<a href="{$data_items[id].story_edit}">Edit Story</a>
							<span>|</span>
							<span>Karma: {$data_items[id].link_karma}</span>
							<span>|</span>
							<span>ID: {$data_items[id].link_id}</span>
							<span>|</span>
							<a href="{$data_items[id].link_url}" target="_blank">
								Third Party URL
							</a>
						</div>
					</div>
				</td>
				<td width="160px">{$data_items[id].link_date}</td>
				<td width="120px" style="text-transform:capitalize;">
					{$data_items[id].link_status}
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
	$('.cbi').change(function() {
		const cbId = $(this).attr("data-id");

		if ($(this).is(":checked")) {
			$("#cbwrap").append("<input type=\"hidden\" class=\"cbid cbid_"+cbId+"\" name=\"cbid_["+cbId+"]\" value=\"1\">");
		} else {
			$("#cbwrap").find(".cbid_"+cbId).remove();
		}
	});

	//
	$("#checkAll").click(function() {
		// $('input:checkbox').not(this).prop('checked', this.checked);

		const isChecked = this.checked;
		$("#cbwrap").html("");

		$('.cbi').each(function(i, curEl) {
			$(curEl).prop('checked', isChecked);

			if (isChecked) {
				const cbId = $(this).attr("data-id");
				$("#cbwrap").append("<input type=\"hidden\" class=\"cbid cbid_"+cbId+"\" name=\"cbid_["+cbId+"]\" value=\"1\">");
			}			
		});
	});

	//
	$('#tool_pagesize').change( function() {
		{/literal}const redirectTo = '{$page_url}';{literal}
		location.href = redirectTo + '?pagesize=' + $(this).val();;
	});
})(jQuery);
</script>
{/literal}

