<?php
	include_once('social_bookmark_settings.php');
	$do_not_include_in_pages = array();
	
	$include_in_pages = array('all');
	if( do_we_load_module() ) {		
		if(is_object($main_smarty)){
            if (Use_New_Story_Layout == 0) {
                module_add_action_tpl('tpl_plikli_story_tools_end', social_bookmark_tpl_path . 'social_bookmark_index.tpl');
            }else{
                module_add_action_tpl('tpl_plikli_story_tools_end', social_bookmark_tpl_path . 'social_bookmark_index_new_layout.tpl');
            }
		}
	}
?>