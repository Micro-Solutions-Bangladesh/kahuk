/**
 * Close Alert
 */
 var closeAlert = () => {
    $( ".alert" ).on( "click", ".close", function(e) {
        e.preventDefault();
    
        $(this).parents(".alert").remove();
    });
};

/**
 * Skip default action (for like disable anchor tage action)
 */
var skipDefaultAction = () => {
    $( ".skip-action" ).on( "click", function(e) {
        e.preventDefault();
    });
};

/**
 * Toast
 * 
 * Example
 * createToast("Top right positioning. - 1");
 * createToast("Top right positioning. - 2", "success");
 * createToast("Top right positioning. - 3", "warning");
 * createToast("Top right positioning. - 3", "error");
 */
var closeableToast = () => {
    $( ".toast-wrap" ).on( "click", ".toast-close", function() {
        $(this).parents(".toast-item").remove();
    });
};

var createToast = (msgText, toastType = "info") => {
    if ($(".toast-wrap").length === 0) {
        $("body").append('<div class="toast-wrap"></div>');
    }

    var toastImgPath = '<path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path>';

    if ("success" === toastType) {
        toastImgPath = '<path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path>';
    }

    if ("warning" === toastType) {
        toastImgPath = '<path fill="currentColor" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path>';
    }

    if ("error" === toastType || "fail" === toastType) {
        toastImgPath = '<path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path>';
    }

    //
    var output = '<div class="toast-item dark-combination" role="alert">';

    output += '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle" class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">' + 
        toastImgPath + '</svg>';

    output +='<span>' + msgText + '</span>';

    output += '<button type="button" class="toast-close close-btn" aria-label="Close"></button>';
    output += '</div>';

    $('.toast-wrap').prepend(output);

    closeableToast();
};


/**
 * 
 */
;(function($) {
    //
    $( "#nav-main-toggle" ).on( "click", function() {
        $(".nav-main-items").toggleClass("active");
    });

    $( ".child-toggle" ).on( "click", function() {
        $(this).closest("li").toggleClass("show-child");
    });

    //
    $( ".story" ).on( "click", ".btn-reaction, .fork", function() {
        var clickedBtn = $(this);

        // No need to proceed when user is not logged-in
        if (clickedBtn.hasClass("require-login")) {
            return;
        }

        //
        var btnParent = clickedBtn.parents(".story");
        var storyId = btnParent.attr("data-id");
        var storyTitle = btnParent.attr("data-title");
        var dataDo = clickedBtn.attr("data-do");
        // console.log("Clicked: [storyId:" + storyId + ", storyTitle:" + storyTitle + ", dataDo:" + dataDo + "]");

        var reqData = {
            "id": storyId,
            "action": dataDo
        };

        if (clickedBtn.hasClass("fork")) {
            var ajxSubmit = $.ajax({
                type: 'POST',
                url: `${kahuk_url_ajax}?prefix=manage-fork`,
                data: reqData
            });

            ajxSubmit.done(function(data) {
                var toastMessage = '';
                var toastType = '';

                try {
                    data = JSON.parse(data);
                    const {
                        status = "none", 
                        message = "", 
                        story_karma = 0, 
                        user_id = 0, 
                        user_karma = 0
                    } = data;

                    toastType = status;

                    if (status === "success") {
                        toastMessage = message + " &quot;" + storyTitle + "&quot;";
                        var newCls = "icon";

                        if (dataDo === "fork") {
                            clickedBtn.attr("data-do", "unfork");
                            newCls += " icon-heart-1";
                        } else {
                            clickedBtn.attr("data-do", "fork");
                            newCls += " icon-heart-o";
                        }
                        
                        clickedBtn.find("i").attr("class", newCls);

                        //
                        $(".story-karma-" + storyId).html(story_karma);
                        $(".author-karma-" + user_id).html(user_karma);
                        
                    } else {
                        toastMessage = message;
                    }
                } catch(e) {
                    toastMessage = "Sorry! something went wrong!";
                    toastType = "warning";
                }

                if (toastType !== "none") {
                    createToast(toastMessage, toastType);
                }
            });

        } else {
            var reaction = clickedBtn.attr("data-reaction");

            reqData["reaction"] = reaction;

            var ajxSubmit = $.ajax({
                type: 'POST',
                url: `${kahuk_url_ajax}?prefix=manage-reaction`,
                data: reqData
            });

            ajxSubmit.done(function(data) {
                var toastMessage = "";
                var toastType = "";

                try {
                    data = JSON.parse(data);

                    const {
                        status = "none", 
                        message = "", 
                        reactions_count, 
                        story_karma = 0, 
                        user_id = 0, 
                        user_karma = 0
                    } = data;

                    toastType = status;
                    toastMessage = message;

                    if (status === "success") {
                        $(".story-" + storyId).find(".btn-reaction").removeClass("btn-active");
                        $(".story-" + storyId).find(".reaction-" + reaction).addClass("btn-active");

                        //
                        $(".story-karma-" + storyId).html(story_karma);
                        $(".author-karma-" + user_id).html(user_karma);

                        for (const [key, value] of Object.entries(reactions_count)) {
                            $(".story-" + storyId).find(".reaction-" + key).find(".counter").html(value);
                        }
                    }
                } catch(e) {
                    toastMessage = "Sorry! something went wrong!";
                    toastType = "warning";
                }

                if (toastType !== "none") {
                    createToast(toastMessage, toastType);
                }
            });
        }        
    });

    /**
     * 
     */
    $( ".follow-unfollow" ).click(function() {
        var elm = $(this);
        var reqData = {
            "user_login": elm.attr("data-user"),
            "action": elm.attr("data-do"),
            "reaction": elm.attr("data-reaction")
        };

        var ajxSubmit = $.ajax({
            type: 'POST',
            url: kahuk_url_ajax + "?prefix=manage-follow-unfollow",
            data: reqData
        });

        ajxSubmit.done(function( data ) {
            try {
                data = JSON.parse(data);

                if (data.status === "success") {
                    elm.attr("data-do", data.toggle_action);
                    elm.html(data.toggle_action);
                }

                createToast(data.message, data.status);
            } catch(e) {
                // Invalid JSON or JSON isn't in the browser
                console.log(`Invalid JSON or JSON isn't in the browser`);
            }
        });
    });

    //
    closeAlert();

    /**
     * Prevent submit the page by pressing the enter key which is taking the user back to the url submit page.
     * Mostly useable with input[typ=text] field
     */
    $('.disable-submit').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;

        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    //
    skipDefaultAction();

})(jQuery);
