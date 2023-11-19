{section name=msgIndex loop=$session_messages}
    <div class="alert alert-{$session_messages[msgIndex].msgtype} mb-4">
        <p class="m-0">{$session_messages[msgIndex].msg}</p>
    </div>
{/section}