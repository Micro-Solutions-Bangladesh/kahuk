{if $user_authenticated eq 1}
	<script type="text/javascript">
	var save_lang_text="{#KAHUK_MiscWords_Save_Links_Save#}";
	var remove_lang_text="{#KAHUK_MiscWords_Save_Links_Remove#}";
	var UserURLSave="{$user_url_saved}";
	{literal}
	$(function() {
		$(".favorite").live("click", function(){
			var oparation = $(this).attr("btnaction");
			var return_value="";
			var link_id=$(this).attr("linkid");
			var dataString = 'action='+oparation+'&link_id='+link_id;
			var message="";
			link_title=$(this).attr("title");
			var parent = $(this);
			
			$.ajax({
				type: "POST",
				url:my_base_url+my_kahuk_base+"/kahuk-ajax.php",
				data: dataString,
				beforeSend: function() {
					parent.addClass("loader");
				},
				cache: false,
				success: function(html)	{
					return_value=html;
					
					if(return_value==1){
						parent.attr('btnaction','unsave-story');
						message='Saved \"'+link_title+'\" to <a href="'+UserURLSave+'">Favorites</a>.';
						parent.html(remove_lang_text);
						$.pnotify({
							pnotify_text: message,
							pnotify_sticker: false,
							pnotify_history: false,
							pnotify_notice_icon: 'fa fa-star'
						});
					}else if(return_value==2){
						parent.attr('btnaction','save-story');
						message='Removed \"'+link_title+'\" from <a href="'+UserURLSave+'">Favorites</a>.';
						parent.html(save_lang_text);
						$.pnotify({
							pnotify_text: message,
							pnotify_sticker: false,
							pnotify_history: false,
							pnotify_notice_icon: 'fa fa-star-o'
						});
					}else{
						$.pnotify({
							pnotify_text: "Error",
							pnotify_sticker: false,
							pnotify_history: false,
							pnotify_notice_icon: 'fa fa-exclamation-circle'
						});
					}

					parent.removeClass("loader");
				}
			});
			return false;
		});
	});
	{/literal}
	</script>
{/if}
