{section name=msgIndex loop=$sessionMessages}
    <div class="alert alert-{$sessionMessages[msgIndex].msgtype}">
        <p>{$sessionMessages[msgIndex].msg}</p>
    </div>
{/section}
<!-- session_messages.tpl -->
