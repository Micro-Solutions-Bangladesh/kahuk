{php}
    global $main_smarty;

    $session_messages = kahuk_get_session_messages();
    $main_smarty->assign('session_messages', $session_messages);
{/php}
{section name=msgIndex loop=$session_messages}
    <div class="mb-base">
        <div class="alert alert-{$session_messages[msgIndex].msgtype} mb-4">
            <p class="m-0">{$session_messages[msgIndex].msg}</p>
        </div>
    </div>
{/section}
