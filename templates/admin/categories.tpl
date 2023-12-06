<!-- categories.tpl -->
<legend>{#KAHUK_Visual_AdminPanel_Category_Manage#}</legend>

{include file=$the_template"/template-parts/session-messages.tpl"}

<div class="section flex flex-col gap-4 mb-16">
	<label class="block">
		<span class="text-gray-700">What category you want to edit?</span>
		<select id="categories_dropdown" class="form-control">
			<option>Select Category</option>
			{foreach from=$hierarchical_categories item=catItem}
				<option value="{$catItem.category_id}">{$catItem.category_name}</option>

				{if $catItem.sub_items}
					{foreach from=$catItem.sub_items item=childLabel1}
						<option value="{$childLabel1.category_id}">&nbsp;&nbsp;- {$childLabel1.category_name}</option>

						{if $childLabel1.sub_items}
							{foreach from=$childLabel1.sub_items item=childLabel2}
								<option value="{$childLabel2.category_id}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-- {$childLabel2.category_name}</option>
							{/foreach}
						{/if}
					{/foreach}
				{/if}
			{/foreach}
		</select>
	</label>
</div>

{literal}
<script>
function showdel(id) {
	document.getElementById('del' + id).style.display = 'block';
	var content = document.getElementById('test' + id + '-content');
	var height = content.style.height;
	content.style.height = (parseInt(height.substr(0, height.indexOf('p'))) + 180) + 'px';
}
</script>
{/literal}

<div class="tabbable tabs-left">
	<!--Parent of the Accordion-->
	<ul class="nav nav-tabs admin-category-tabs">
		{section name=thecat loop=$cat_array}
			<li class="{if $templatelite.section.thecat.index==0}active{/if}">
				<a data-toggle="tab" href="#cat-{$cat_array[thecat].category_id}">{$cat_array[thecat].category_name}</a>
			</li>
		{/section}
		<li class="add_new_category_tab">
			<a data-toggle="tab" href="#AddNew">{#KAHUK_Visual_AdminPanel_Category_Add#}</a>
		</li>
	</ul>
	<br />
	<div class="tab-content">
		{section name=thecat loop=$cat_array}
			<div id="cat-{$cat_array[thecat].category_id}"
				class="tab-pane {if $templatelite.section.thecat.index==0}active{/if} fade in">
				<form method='post'>
					{$hidden_token_category_manager}
					<input type='hidden' name='category_id' value='{$cat_array[thecat].category_id}'>
					<input type='hidden' name='action' value='save'>
					
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_ID#}</td>
								<td>{$cat_array[thecat].category_id}</td>
							</tr>
							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_Name#}</td>
								<td>
									<input type="text" name="name" class="form-control" 
										value="{$cat_array[thecat].category_name}"
									>
								</td>
							</tr>
							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_URL#}</td>
								<td>
									<input type="text" name="category_safe_name" class="form-control" 
										value="{$cat_array[thecat].category_safe_name}"
									>
								</td>
							</tr><tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_Order#}</td>
								<td>
									<input type="number" name="category_order" class="form-control" 
										value="{$cat_array[thecat].category_order}"
									>
								</td>
							</tr>
							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_Meta_Desc#}</td>
								<td>
									<input type="text" name="category_desc" class="form-control" 
										value="{$cat_array[thecat].category_desc}"
									>
								</td>
							</tr>
							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_Meta_Keywords#}</td>
								<td>
									<input type="text" name="category_keywords" class="form-control" 
										value="{$cat_array[thecat].category_keywords}"
									>
								</td>
							</tr>

							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_Parent#}</td>
								<td>
									<select name="parent" class="form-control">
										<option value="0">Select Parent</option>

										{foreach from=$hierarchical_categories item=catItem}
											{if ($catItem.category_id neq $cat_array[thecat].category_id)}
												<option value="{$catItem.category_id}" {if $catItem.category_id eq $cat_array[thecat].category_parent}selected{/if}>{$catItem.category_name}</option>

												{if $catItem.sub_items}
													{foreach from=$catItem.sub_items item=childLabel1}
														{if ($childLabel1.category_id neq $cat_array[thecat].category_id)}
															<option value="{$childLabel1.category_id}" {if $childLabel1.category_id eq $cat_array[thecat].category_parent}selected{/if}>- {$childLabel1.category_name}</option>
														{/if}
													{/foreach}
												{/if}
											{/if}
										{/foreach}
									</select>
								</td>
							</tr>

							<tr>
								<td>{#KAHUK_Visual_AdminPanel_Category_Status#}</td>
								<td>
									<select name="category_status" class="form-control">
										<option value="">Select Status</option>

										{foreach from=$globalCategoryStatuses item=catStatus}
										<option value="{$catStatus}" {if $catStatus eq $cat_array[thecat].category_status}selected="selected"{/if}>{$catStatus|ucfirst}</option>
										{/foreach}
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<input type="button" class="btn btn-danger" value="{#KAHUK_Visual_AdminPanel_Category_Delete#}"
										onclick="showdel({$cat_array[thecat].category_id})"
										{if sizeof($cat_array)<=2}disabled{/if} 
									>
								</td>
								<td valign='top'>
									<input type="submit" class="btn btn-primary" 
										value="{#KAHUK_Visual_AdminPanel_Category_Update#}"
									>
								</td>
							</tr>
						</tbody>
					</table>

					<div id="del{$cat_array[thecat].category_id}" class="alert alert-error" style="display:none;">
						<p><strong>{#KAHUK_Visual_AdminPanel_Category_Select#}</strong></p>
						<input type="radio" name="sub" value="move" onclick="this.form.delete1.disabled=false;">
						{#KAHUK_Visual_AdminPanel_Category_Move#}:
						<select name="move">
							{foreach from=$cat_array item=cat}
								{if $cat.category_id!=0 && $cat.category_id!=$cat_array[thecat].category_id}
									<option value="{$cat.category_id}">{$cat.category_name}</option>
								{/if}
							{/foreach}
						</select><br />
						<input type="radio" name="sub" value="delete" onclick="this.form.delete1.disabled=false;">
						{#KAHUK_Visual_AdminPanel_Category_Delete_Stories#}<br /><br />
						<input type="radio" name="sub1" value="move" checked>
						{#KAHUK_Visual_AdminPanel_Subcategory_Move#}<br />
						<input type="radio" name="sub1" value="delete">
						{#KAHUK_Visual_AdminPanel_Subcategory_Delete#}<br /><br />
						<input
							onclick="if (confirm('{#KAHUK_Visual_View_User_Reset_Pass_Confirm#}')) document.location.href='admin_categories.php?action=remove&id={$cat_array[thecat].category_id}&sub='+(this.form.sub[0].checked ? 'move' : 'delete')+'&move='+this.form.move.options[this.form.move.selectedIndex].value+'&sub1='+(this.form.sub1[0].checked ? 'move' : 'delete')+'&token='+this.form.token.value;"
							value="{#KAHUK_Visual_AdminPanel_Category_Delete#}" disabled name='delete1' type="button"
							class="btn btn-danger">
						<input
							onclick="document.getElementById('del{$cat_array[thecat].category_id}').style.display='none';"
							value="{#KAHUK_Visual_AdminPanel_Category_Cancel#}" type="button" class="btn btn-default">
					</div>
				</form>
			</div>
		{/section}

		<div id="AddNew" class="tab-pane fade in">
			<form method='post'>
				{$hidden_token_category_manager}
				<input type='hidden' name='action' value='save'>
				<table class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_Name#}</td>
							<td><input name="name" class="form-control" placeholder="New Category" type="text"></td>
						</tr>
						<tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_URL#}</td>
							<td><input name="category_safe_name" class="form-control" value="" type="text"></td>
						</tr>
						<tr>
						<td>{#KAHUK_Visual_AdminPanel_Category_Order#}</td>
							<td>
								<input type="number" name="category_order" class="form-control" 
									value=""
								>
							</td>
						</tr>
						<tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_Meta_Desc#}</td>
							<td><input name="category_desc" class="form-control" value="" type="text"></td>
						</tr>
						<tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_Meta_Keywords#}</td>
							<td><input name="category_keywords" class="form-control" value="" type="text"></td>
						</tr>
						<tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_Parent#}</td>
							<td>
								<select name="parent" class="form-control">
									<option value="0"> --- </option>
									{foreach from=$cat_array item=cat}
										{if $cat.category_id!=0}
											<option value="{$cat.category_id}">{$cat.category_name}</option>
										{/if}
									{/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td>{#KAHUK_Visual_AdminPanel_Category_Status#}</td>
							<td>
								<select name="category_status" class="form-control">
									{foreach from=$globalCategoryStatuses item=catStatus}
										<option value="{$catStatus}">{$catStatus|ucfirst}</option>
									{/foreach}
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input value="{#KAHUK_Visual_AdminPanel_Category_Add#}" type="submit"
									class="btn btn-primary"></td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<!--/#AddNew -->
	</div>
	<!--/.tab-content -->
</div>
<!--/.tabbable-->

{literal}
<script>
;(function($) {
	$("#categories_dropdown").change(function () {
        var end = this.value;
        var firstDropVal = $('#categories_dropdown').val();

		console.log(end + " :: " + firstDropVal)

		$('.nav-tabs a[href="#cat-' + end + '"]').tab('show');
    });
})(jQuery);
</script>
{/literal}
