{if $actioncode eq 'invalidurl'}
    <div class="alert alert-danger">
        <p>{#KAHUK_Visual_Submit2Errors_InvalidURL#}: {$submit_url}</p>
    </div>
{/if}
{if $actioncode eq 'sessionexpired'}
    <div class="alert alert-warning">
        <p>Session for the URL is expired!</p>
    </div>
{/if}