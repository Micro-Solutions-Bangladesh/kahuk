{php} header( "HTTP/1.1 503 Service Unavailable" ); {/php}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>{$site_name} - {#KAHUK_Visual_RSS_Description#}</title>
	<meta name="description" content="Is currently in maintenance mode. We should be back online shortly. Thank you for your patience." />
	
	<style>
	{literal}
	hr {
		border-bottom: 1px solid #46657c;
		border-top: 0;
		margin: 40px -50px;
	}
	.text-bold {
		font-weight: bold;
	}
	.page {
        /* box-shadow: 0.5rem 0.5rem #21303b, -0.5rem -0.5rem #bae6fd; */
		/* border-radius: 5px; */
		max-width: 600px;
		margin: 50px auto;
		padding: 20px 50px 40px;
		text-align: center;

		/* #1 */
		border: 5px solid hsl(0, 0%, 40%);
		
		/* #3 */
		outline: 5px solid hsl(0, 0%, 60%);
		
		/* #4 AND INFINITY!!! (CSS3 only) */
		box-shadow:
			0 0 0 10px hsl(0, 0%, 80%),
			0 0 0 15px hsl(0, 0%, 90%);
	}
    .text-deep-100 {
        color: #21303b;
    }
	{/literal}
	</style>
</head>
<body>
	<div class="page text-deep-100">
		<h1>Maintenance</h1>
		<p>
			{$site_name} Is currently in maintenance mode. We should be back online shortly. Thank you for your patience.
		</p>
		<p>
			<a class="text-bold text-deep-100" href="javascript:location.reload(true)">Refresh Page</a>
		</p>
		<hr />
		<footer>
			Copyright &copy; {php} echo date('Y'); {/php} {$site_name}
		</footer>
	</div>
</body>
</html>