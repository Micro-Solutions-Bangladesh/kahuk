<?php
session_start();

$page = 'terms';
define('page', $page);

$language = 'english';
$_GET['language'] = $language;

if (isset($_POST['iagree']) || (isset($_SESSION['agree']) && $_SESSION['agree'] != '')) {
    $selected = "checked";
    $_SESSION['agree'] = $selected;
} else {
    $selected = '';
    unset($_SESSION['agree']);
}


if (isset($_GET['action'])) {
    $sendto = addslashes(strip_tags($_GET['action']));
} else {
    $sendto = 'install';
}


$include='header.php'; 
if (file_exists($include)) { include_once($include); }
echo '<style type="text/css">
body{direction:LTR;}
fieldset{padding:0px 10px;}
form{padding:5px 10px 0px 10px;}
.smaller {font-size:1.2em;}
</style>';
?>


<fieldset><legend>Terms & Conditions</legend>
    <div class="alert-warning" style="padding:0px 5px;">
        <ol>
            <h1 class="smaller">You agree that downloading, installing and using Plikli CMS is subject to the terms & conditions cited below:</h1>
            <h2 class="smaller">USAGE OF PLIKLI CMS</h2>
            <li>Plikli CMS users MUST follow the same terms and conditions as stated by <a href="https://support.google.com/adspolicy/answer/6008942?visit_id=637032007827163448-2486794191&rd=1"><strong><em>Google Ads policies</em></strong></a> in its four sections:
        
                <ol>
                    <li>Prohibited content</li>
                    <li>Prohibited practices</li>
                    <li>Restricted content</li>
                    <li>Editorial and technical</li>
                </ol>
                <br /><strong>Any usage that violates these terms is prohibited!</strong>
            </li>
            <h2 class="smaller">ADS.TXT</h2>
            <li>Plikli CMS is a <span>free</span> service provided to users to download, install / upgrade and get full support 24/7.<br />
            Its only income comes from Solvemedia, and while it is extremely insignificant, it covers at least part of the domain and hosting plan expenses. The development, maintenance and full support is not rewarded by any means.<br />
            You, as a users, you might have Google Adsense or any other legitimate ads providers. <u>It is recommended that you use an ads.txt file provided by your ads provider.</u><br />
            
            You can check these links: <strong><a href="https://support.google.com/admanager/answer/7441288?hl=en">About ads.txt</a></strong> and <strong><a href="https://support.google.com/admanager/answer/7544382?hl=en">Create ads.txt</a></strong><br />
                <ul>
                    <li>You CANNOT DELETE the ads.txt provided by PLIKLI CMS.</li>
                    <li>Once you obtain the content of your ads.txt, YOU MUST COPY and PASTE it to APPEND it to the ads.txt provided by PLIKLI CMS. You CANNOT OVERRIDE the original content of the ads.txt provided by PLIKLI CMS.</li>
                </ul>
            </li>
            
            <br />
            <p><strong>Any infringement, of the Terms cited above, results in terminating the license, the right to support, installation and upgrade granted to You by Plikli CMS!</strong></p>
        </ol>

    </div>

    <form method="post">
        <label for="iagree">Click here to indicate that you have read and agree to the terms presented in the Terms and Conditions.</label>
        <input type="checkbox" id="iagree" name="iagree" value="1" <?php echo $selected; ?> />
        <input type="submit" name="Submit" class="btn btn-primary" value="I agree" />
    </form>
    
    <?php if (isset($selected) && $selected == 'checked') {
 echo "You agreed to the terms & Conditions. You can now proceed to <a href=\"./$sendto.php\"><strong>$sendto</strong></a> Plikli CMS<br /><br />";   
}
?>
</fieldset>



<?php
$include='footer.php'; if (file_exists($include)) {include_once($include);}

