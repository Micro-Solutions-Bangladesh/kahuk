<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="{#KAHUK_Visual_Language_Direction#}" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="{$kahuk_base_url}/templates/admin/css/bootstrap.no-icons.min.css">
	<link rel="stylesheet" type="text/css" href="{$kahuk_base_url}/templates/admin/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="{$kahuk_base_url}/templates/admin/css/jquery.pnotify.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="{$kahuk_base_url}/templates/admin/css/bootstrap-fileupload.min.css" media="screen">
	<link rel="stylesheet" type="text/css" href="{$kahuk_base_url}/templates/admin/css/style.css" media="screen">

	<meta name="Language" content="en-us">
	<meta name="Robots" content="none">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title>{#KAHUK_Visual_Name#} Admin Panel</title>
	
	<link rel="icon" href="{$kahuk_base_url}/favicon.ico" type="image/x-icon"/>	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
	<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.easing.1.3.js"></script>
	<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.coda-slider-2.0.js"></script> 
	<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.pnotify.js"></script>
	<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.masonry.min.js"></script>
	<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/bootstrap-fileupload.min.js"></script>
    <script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery_cookie.js"></script>
    <script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/leftmenu.js"></script>
	 
	{if $pagename eq "admin_index"}
		<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.ui.widget.js"></script> 
		<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.ui.mouse.js"></script> 
		<script type="text/javascript" src="{$kahuk_base_url}/templates/admin/js/jquery/jquery.ui.sortable.js"></script>
		<link type="text/css" href="{$kahuk_base_url}/templates/admin/css/jquery.ui.theme.css" rel="stylesheet" /> 
		<link type="text/css" href="{$kahuk_base_url}/templates/admin/css/admin_home.css" rel="stylesheet" />		
		<link type="text/css" href="{$kahuk_base_url}/templates/admin/css/coda-slider-2.0.css" rel="stylesheet" media="screen" />
	{/if}
	
    {$Jscript}
	
	<script src="{$kahuk_base_url}/templates/admin/js/simpleedit.js" type="text/javascript"></script>
	{if $pagename eq "admin_index"}
		{literal}
		<script type="text/javascript">
		$(function() {
			$(".column").sortable({
				connectWith: '.column'
			});
			$(".portlet").addClass("ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
				.find(".portlet-header")
					.addClass("ui-widget-header")
					.end()
				.find(".portlet-content");
			$(".ui-icon-minusthick").click(function() {
				$(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
				$(this).parents(".portlet:first").find(".portlet-content:first").toggle();
				$(this).parents(".portlet:first").find(".portlet-content:first").each(function(index) {
					$.get("admin_index.php", { action: "minimize", display: this.style.display, id: this.parentNode.id }, function(data){
					});
				});
			});
			$(".ui-icon-plusthick").click(function() {
				$(this).toggleClass("ui-icon-plusthick").toggleClass("ui-icon-minusthick");
				$(this).parents(".portlet:first").find(".portlet-content:first").toggle();
				$(this).parents(".portlet:first").find(".portlet-content:first").each(function(index) {
					$.get("admin_index.php", { action: "minimize", display: this.style.display, id: this.parentNode.id }, function(data){
					});
				});
				var panelHeight = $(this).parents(".portlet:first").find(".panel:first").height();
				var codaslider = $(this).parents(".portlet:first").find(".coda-slider:first");
				codaslider.codaSlider();
	//			codaslider.css({ height: panelHeight });
			});
			jQuery(document).ajaxError(function(event, request, settings){ alert("Error"); });
			$( ".column" ).sortable({
				stop: function(event, ui) { 
					var data = '';
					$(".portlet").each(function(index) {
						data += this.id + ',';
					});
					$.get("admin_index.php", { action: "move", left: ui.offset.left, top: ui.offset.top, id: ui.item[0].id, list: data }, function(data){
	//  					alert("data load " + data);
					});
				}
			});
	//		$(".column").disableSelection();
		});
		$().ready(function() {
			$(".coda-slider").each(function(index) {
			$('#'+this.id).codaSlider();
			});
		});
		</script>
		{/literal}
	{/if}

</head>
<body dir="{#KAHUK_Visual_Language_Direction#}">
{if $pagename neq "admin_login"}
	<header role="banner" class="navbar navbar-inverse">
		<div class="container">
			<div class="navbar-header">
				<button data-target=".bs-navbar-collapse" data-toggle="collapse" type="button" class="navbar-toggle">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="https://kahuk.com/">
					<img src="{$kahuk_base_url}/templates/admin/img/kahuk-light.png" />
				</a>
			</div>
			<nav role="navigation" class="collapse navbar-collapse bs-navbar-collapse">
				<ul class="nav navbar-nav">
					<li{if $pagename eq "admin_index"} class="active"{/if}><a href="{$kahuk_base_url}/admin/admin_index.php">{#KAHUK_Visual_AdminPanel#} Panel</a></li>
					<li><a href="{$kahuk_base_url}/">{#KAHUK_Visual_Home#}</a></li>
					
					<li><a href="{$page_logout_url}">{#KAHUK_Visual_Logout#}</a></li>
				</ul><!--/.nav -->
			</nav>
		</div>
	</header>
	
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="well sidebar-nav">
					<ul class="nav nav-list">
						<div id="AdminAccordion" class="accordion">
							<div class="accordion-group">
								<div class="btn btn-default col-md-12 accordion-heading">
									<span class="accordion-heading-title">
										<li class="nav-header"><i class="fa fa-user" /></i>&nbsp; {#KAHUK_Visual_AdminPanel_Manage_Nav#}</li>
									</span>
									{if $moderated_total_count neq ''}
										<span class="badge accordion-heading-alert">
											{$moderated_total_count}
										</span>
									{/if}
								</div>
								<div class="accordion-body " id="CollapseManage">
									<ul class="accordion-inner">
										<li{if $pagename eq "admin_links"} class="active"{/if} id="manage_submissions"><a href="{$kahuk_base_url}/admin/admin_links.php">Submissions {if $moderated_submissions_count != '0'}<span class="pull-right badge badge-gray">{$moderated_submissions_count}</span>{/if}</a></li>
										<li{if $pagename eq "admin_comments"} class="active"{/if} id="manage_comments"><a href="{$kahuk_base_url}/admin/admin_comments.php">Comments {if $moderated_comments_count != '0'}<span class="pull-right badge badge-gray">{$moderated_comments_count}</span>{/if}</a></li>
										<li{if $pagename eq "admin_users" || $pagename eq "admin_user_validate"} class="active"{/if} id="manage_users"><a href="{$kahuk_base_url}/admin/admin_users.php">Users {if $moderated_users_count != '0'}<span class="pull-right badge badge-gray">{$moderated_users_count}</span>{/if}</a></li>
										<li{if $pagename eq "admin_group"} class="active"{/if} id="manage_groups"><a href="{$kahuk_base_url}/admin/admin_group.php">Groups {if $moderated_groups_count != '0'}<span class="pull-right badge badge-gray">{$moderated_groups_count}</span>{/if}</a></li>

										<li{if $pagename eq "domain_management"} class="active"{/if} id="domain_management"><a href="{$kahuk_base_url}/admin/domain_management.php">Manage Domains</a></li>

										
                                    </ul>
								</div>
							</div>
						</div>
					</ul>
				</div>
			</div>
			<div class="col-md-9">
				<div class="row">
					<div id="main_content">

						{include file=$tpl_center.".tpl"}
						{* Start Pagination *}
						{if ($pagename eq "admin_users" && $templatelite.get.mode=='') || $pagename eq "admin_comments" || $pagename eq "admin_links" || $pagename eq "admin_user_validate"}	
							{php} 
							Global $db, $main_smarty, $rows, $offset,$page_size;

							do_pages($rows, $pagesize ? $pagesize : 30, $the_page); 
							{/php}
						{/if} 
						{* End Pagination *}
						
					</div>
					
				</div><!-- /row -->
			</div><!-- /col-md-9 -->
		</div><!-- /row -->
		<hr />
		<footer>
			<p>Powered by <a href="https://kahuk.com/">Kahuk CMS</a></p>
			
		</footer>
	</div><!-- /container -->
	{* JavaScript to prevent the carousel function from automatically changing content *}
	{literal}
		<script type='text/javascript'>//<![CDATA[ 
			$(window).load(function(){
				$(function() {
					$('.carousel').each(function(){
						$(this).carousel({
							interval: false
						});
					});
				});
			});//]]>  
		</script>
		<!-- JavaScript to allow multiple sidebar accordions to be open -->
		<script type='text/javascript'>//<![CDATA[ 
			$(window).load(function(){
				$('.collapse').collapse({
					toggle: false
				});
				//$(".collapse").collapse()
			});//]]>  
		</script>
		<script type="text/javascript">
			$(document).ready(function() {
			// https://gist.github.com/1688900
			// Support for AJAX loaded modal window.
			// Focuses on first input textbox after it loads the window.
				$('[data-toggle="modal"]').click(function(e) {
					e.preventDefault();
					var href = $(this).attr('href');
					if (href.indexOf('#') == 0) {
						$(href).modal('open');
					} else {
						$.get(href, function(data) {
							$('<div class="modal" >' + data + '</div>').modal();
						}).success(function() { $('input:text:visible:first').focus(); });
					}
				});
			});
		</script>
	{/literal}
{else}{include file=$tpl_center.".tpl"}{/if}
</body>
</html>