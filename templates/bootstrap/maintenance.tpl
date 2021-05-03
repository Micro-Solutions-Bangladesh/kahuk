{php} header( "HTTP/1.1 503 Service Unavailable" ); {/php}
{************************************
***** Maintenance Mode Template *****
*************************************}
<!DOCTYPE html>
<html dir="{#KAHUK_Visual_Language_Direction#}" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		{include file=$the_template"/meta.tpl"}
		<title>{#KAHUK_Visual_Name#} - {#KAHUK_Visual_RSS_Description#}</title>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css" rel="stylesheet">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="{$my_kahuk_base}/templates/{$the_template}/css/style.css" media="screen" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body dir="{#KAHUK_Visual_Language_Direction#}" {$body_args}>
		<div class="container" style="margin-top:30px;">
			<div class="jumbotron">
				<h1 style="font-size:3.4em;">Maintenance</h1>
				<p>{#KAHUK_Visual_Name#} Is currently in maintenance mode. We should be back online shortly. Thank you for your patience.</p>
				<p>
					<a href="javascript: history.go(-1)" class="btn btn-lg">&laquo; Go Back</a> or 
					<a href="javascript:location.reload(true)" class="btn btn-primary btn-lg">Refresh Page</a>
				</p>
			</div>
			<hr>
			<footer>
				<div id="footer">
					<span class="subtext"> 
						Copyright &copy; {php} echo date('Y'); {/php} {#KAHUK_Visual_Name#} 
						| <a href="https://www.kahuk.com/" target="_blank" rel="noopener noreferrer">Kahuk CMS</a> 
					</span>
				</div>
			</footer>
		</div> <!-- /container -->
	</body>
</html>