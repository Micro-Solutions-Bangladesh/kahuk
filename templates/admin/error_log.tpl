<legend>Error Log</legend>
<p>Below you will find the contents of the /{php} echo LOG_FILE; {/php} file, where Kahuk CMS stores error messages. Not all of these error messages are significant, but you should carefully review each one to detect problems with your website. Once you have reviewed the errors below, dismiss them by clicking on the "Clear Log" button.</p>

<ul class="log-files">
{foreach from=$logfiles item=logfile key=i}
	<li>
		<a class="btn btn-primary" href="admin_log.php?show={$logfile.filename}">Show {$logfile.basename}</a>
		<a class="btn btn-primary" href="admin_log.php?delete={$logfile.filename}">Delete {$logfile.basename}</a>
	</li>
{/foreach}
</ul>

<br /><br />
{if $kahuk_logs neq ''}
	<pre>Log File: {$kahuk_viewable_log_file}<br><br>{$kahuk_logs}</pre>
{/if}
<!--/error_log.tpl -->
