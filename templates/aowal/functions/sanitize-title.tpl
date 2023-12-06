{literal}
    <script type="text/javascript">
        ;(function($) {
            // the following code deals with the Title, Tags input fields
            $('.sanitize-title').keyup(function() {
                var yourInput = $(this).val();
                re = /[`~!@#$%^&*()|+=?;:'",.<>\{\}\[\]\\\/]/gi;
                var isSplChar = re.test(yourInput);

                if (isSplChar) {
                    var no_spl_char = yourInput.replace(re, '');
                    $(this).val(no_spl_char);
                }
            });

            $('.sanitize-title').bind("paste", function() {
                setTimeout(function() {
                    //get the value of the input text
                    var data = $(this).val();

                    //replace the special characters to '' 
                    var dataFull = data.replace(/[`~!@#$%^&*()|+=?;:'",.<>\{\}\[\]\\\/]/gi, '');

                    //set the new value of the input text without special characters
                    $(this).val(dataFull);
                });
            });
        })(jQuery);
    </script>
{/literal}