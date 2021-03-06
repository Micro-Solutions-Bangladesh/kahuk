
{if $user_logged_in}
	<div class="headline">
		<div class="sectiontitle">Saved Stories</div>
	</div>
	<div class="boxcontent" >
		<ul class="sidebar-saved" id="saved_stories">
			{checkActionsTpl location="tpl_widget_saved_start"}	
			{php}
			include_once('internal/Smarty.class.php');
			$main_smarty = new Smarty;

			include_once('config.php');
			include_once(KAHUK_LIBS_DIR.'link.php');
			include_once(KAHUK_LIBS_DIR.'search.php');
			include_once(KAHUK_LIBS_DIR.'smartyvariables.php');

			// -------------------------------------------------------------------------------------
			/* Redwine: added $current_user to the globals to accurately get the logged in user_id so that the logged in user will only see their saved stories and not someone's else */
			global $the_template, $main_smarty, $db, $current_user;
			$user_id = $current_user->user_id;
			$res = "select link_id,link_title,saved_id,saved_user_id,saved_link_id from ".table_links.",".table_saved_links." WHERE saved_user_id =$user_id and link_id = saved_link_id  ORDER BY saved_id DESC limit 5";

			$list_savedlinks = $db->get_results($res);
			if($list_savedlinks)
			{
				foreach($list_savedlinks as $row){            
					$story_url = getmyurl("story", $row->link_id);
					echo "<li><a class='switchurl' href='".$story_url."'>".$row->link_title."</a></li>";                
				}
			}
			{/php}
			{checkActionsTpl location="tpl_widget_saved_end"}
		</ul>
	</div>
{/if}
