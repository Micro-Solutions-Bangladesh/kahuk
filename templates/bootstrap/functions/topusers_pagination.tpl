<script type="text/javascript" language="javascript">
	
	var my_kahuk_url = "{$kahuk_base_url}";
	var Pager_setting = "{$Pager_setting}";
	var page_name = "{$pagename}";
	var total_row_for_topusers = "{$total_row_for_topusers}";
	var pageSize = "{$scrollpageSize}";
	
	{literal}
	
		$(document).ready(function(){
		
			var count=parseInt(pageSize);
		
			function pagination_for_top_users() 
			{ 
				var url = "";
			
			 	url = my_kahuk_url+"/load_data_for_topusers.php";
			
				var dataString = "start_up="+count+"&pagesize="+pageSize;
								
				$.ajax({
						type: "POST",
						url:url,
						data: dataString,
						beforeSend: function() {
							
							$("#tablesorter-demo tr:last").addClass("loader");
						},
						cache: false,
						success: function(html)	{
							
							if (html != "") {
								$("#tablesorter-demo tr:last").after(html); 
								$("#tablesorter-demo tr").removeClass("loader");
								count=count+parseInt(pageSize);
								$("#tablesorter-demo").trigger("update");
							}
						} 
				});
			}; 
		
		 	if(Pager_setting==2){
				$(window).scroll(function(){
					if ($(window).scrollTop() == $(document).height() - $(window).height()){
						if(parseInt(total_row_for_topusers)>=count)
						pagination_for_top_users();
					}
				}); 
		   } else if(Pager_setting==3){
			   
				if(parseInt(total_row_for_topusers)>count)  
					$('#tablesorter-demo').after("<div class='btn btn-default contine_read_story'>{/literal}{#KAHUK_Continue_Reading#}{literal}</div>");
				
				$(".contine_read_story").live("click", function(){
					if(parseInt(total_row_for_topusers) > count){
						pagination_for_top_users();
					}else{	
						$(this).hide();
						
						$('#tablesorter-demo').after("<div class='btn btn-default no_stories_left'>{/literal}{#KAHUK_No_More_Articles#}{literal}</div>");
					}
				});
		   }
		});
	{/literal}
</script>