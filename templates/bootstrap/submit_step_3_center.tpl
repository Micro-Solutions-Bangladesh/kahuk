{************************************
****** Submit Step 3 Template *******
*************************************}
<!-- submit_step_3_center.tpl -->
<div class="submit_page">
	<legend>{#KAHUK_Visual_Submit3_Header#}</legend>
	<h2>{#KAHUK_Visual_Submit3_Details#}</h2><br />
	{checkActionsTpl location="tpl_kahuk_submit_step3_start"}
	{* javascript that protects people from clicking away from the story before submitting it *}
	{literal}
	<SCRIPT>
		// Variable toggles exit confirmation on and foff.
		var gPageIsOkToExit = false;
		function submitEdgeStory()
		{
			// Set a variable so that our "before unload" exit handler knows not to verify
			// the page exit operation.
			gPageIsOkToExit = true;
			// Do the submission.
			// var frm = document.getElementById("thisform");
			frms = document.getElementsByName("ATISUBMIT");
			if (frms)
			{
				if (frms[0])
					frms[0].submit();
			}
		}
		window.onbeforeunload = function (event) 
		{
			// See if this is a safe exit.
			if (gPageIsOkToExit)
				return;

			if (!event && window.event) 
					event = window.event;
			
			event.returnValue = "You have not hit the Submit Button to submit your story yet.";
		}
	</SCRIPT>
	{/literal}
	{php}
		Global $db, $main_smarty, $dblang, $the_template, $linkres, $current_user;
		$linkres=new Link;
		$linkres->id=$link_id = $_POST['id'];
		if (!is_numeric($link_id)) die();
		$linkres->read(FALSE);
		$linkres->print_summary();

		$main_smarty->assign('submit_url', $url);
		$main_smarty->assign('submit_url_title', $linkres->url_title);
		$main_smarty->assign('submit_id', $linkres->id);
		$main_smarty->assign('submit_type', $linkres->type());
		$main_smarty->assign('submit_title', $link_title);
		$main_smarty->assign('submit_content', $link_content);
		$main_smarty->assign('submit_trackback', $trackback);
	{/php}
	<form action="{$URL_submit}" method="post" id="thisform" name="ATISUBMIT" >
		<input type="hidden" name="phase" value="3" />
		<input type="hidden" name="randkey" value="{$templatelite.post.randkey}" />
		<input type="hidden" name="id" value="{$submit_id}" />
		<input type="hidden" name="trackback" value="{$templatelite.post.trackback|escape:"html"}" />
		<br style="clear: both;" />
		<hr />
		<center>
			<input type="button" onclick="gPageIsOkToExit = true; document.location.href='{$kahuk_base_url}/submit.php?id='+this.form.id.value+'&trackback='+this.form.trackback.value;" value="{#KAHUK_Visual_Submit3_Modify#}" class="btn btn-default">&nbsp;&nbsp;
			<input type="button" onclick="submitEdgeStory();" value="{#KAHUK_Visual_Submit3_SubmitStory#}" class="btn btn-primary" />
		</center>
	{checkActionsTpl location="tpl_kahuk_submit_step3_end"} 
	</form>
	{checkActionsTpl location="tpl_submit_step_3_end"}
</div>
<!--/submit_step_3_center.tpl -->