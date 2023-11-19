{section name=msgIndex loop=$action_messages}
    <div class="alert alert-{$action_messages[msgIndex].type} mb-4">
        <p class="m-0">{$action_messages[msgIndex].message}</p>
    </div>
{/section}