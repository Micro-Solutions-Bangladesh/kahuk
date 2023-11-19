<div class="modal fade fixed top-0 left-0 hidden w-full h-full outline-none overflow-x-hidden overflow-y-auto"
    id="LoginModal" tabindex="-1" aria-labelledby="LoginModalTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered relative w-auto pointer-events-none">
        <div
            class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white bg-clip-padding rounded-md outline-none">
            <div
                class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-gray-200 rounded-t-md">
                <h5 class="text-xl font-medium leading-normal" id="exampleModalScrollableLabel">
                    Login
                </h5>
                <button type="button" class="btn-close w-4 h-4" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="signin" action="{$page_login_url}" method="post">
                <div class="modal-body relative p-4">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" id="myEmail" class="form-control" placeholder="Username/Email" tabindex="1" value="{if isset($login_username)}{$login_username}{/if}">
                        <label for="myEmail" class="text-gray-700">Username/Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" id="myPassword" class="form-control" placeholder="Password" tabindex="2" value="">
                        <label for="myPassword" class="text-gray-700">Password</label>
                    </div>
                    <div class="mb-3">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="persistent" id="remember" value="1" tabindex="3">
                            <span class="ml-2">{#KAHUK_Visual_Login_Remember#}</span>
                        </label>
                    </div>
                </div>

                <input type="hidden" name="processlogin" value="1"/>
                <input type="hidden" name="return" value="{if !empty($get.return)}{$get.return}{/if}"/>

                <div
                    class="modal-footer flex flex-wrap items-center justify-between p-4 border-t border-gray-200 rounded-b-md">
                    <button type="submit" class="btn-primary flex-auto rounded">
                        Login
                    </button>

                    <a class="flex-auto rounded ml-4" href="{$page_pass_reset_url}" title="{#KAHUK_Visual_Login_ForgottenPassword#}?">
                        {#KAHUK_Visual_Login_ForgottenPassword#}?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>