<legend>Error Log</legend>
<p>Below you will find the contents of the /{php} echo LOG_FILE; {/php} file, where Kahuk CMS stores error messages. Not all of these error messages are significant, but you should carefully review each one to detect problems with your website. Once you have reviewed the errors below, dismiss them by clicking on the "Clear Log" button.</p>

<ul class="log-files">
{foreach from=$logfiles item=v key=i}
	<li>
		<a class="btn btn-primary" href="admin_log.php?show={$i}">Show {$i} log</a>
		<a class="btn btn-primary" href="admin_log.php?clear={$i}">Clear {$i} log</a>
	</li>
{/foreach}
</ul>

<br /><br />
{if $showfile eq 'error'}
	<pre>{php}
		if ( $fh = fopen('../'.LOG_FILE , "r") ) {
			readfile('../'.LOG_FILE); 
			fclose($fh);
		} else {
			echo "Error: error.log file can not be read.";
		}
	{/php}</pre>
{/if}

{if $showfile eq 'debug'}
	<pre>{php}
		if ( $fh = fopen('../logs/debug.log' , "r") ) {
			readfile('../logs/debug.log'); 
			fclose($fh);
		} else {
			echo "Error: debug.log file can not be read.";
		}
	{/php}</pre>
{/if}
<!--/error_log.tpl -->
