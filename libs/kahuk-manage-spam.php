<?php
/**
 * 
 */
function kahuk_check_spam( $url ) {
    global $MAIN_SPAM_RULESET, $USER_SPAM_RULESET;

    $regex_url   = "/(http:\/\/|https:\/\/|ftp:\/\/|www\.)([^\/\"<\s]*)/im";
    $mk_regex_array = array();

    preg_match_all($regex_url, $text, $mk_regex_array);

    for ($cnt = 0; $cnt < count($mk_regex_array[2]); $cnt++) {
        $test_domain = rtrim($mk_regex_array[2][$cnt], "\\");
        if (strlen($test_domain) > 3) {
            $domain_to_test = $test_domain . ".multi.surbl.org";

            if (strstr(gethostbyname($domain_to_test), '127.0.0')) {
                // $this->logSpam("surbl rejected $test_domain");
                return true;
            }
        }
    }

    $retVal = kahuk_check_spam_rules($MAIN_SPAM_RULESET, strtoupper($text));

    if (!$retVal) {
        $retVal = kahuk_check_spam_rules($USER_SPAM_RULESET, strtoupper($text));
    }

    return $retVal;
}


/**
 * check a file of local rules
 * . . the rules are written in a regex format for php
 *  . . or one entry per line eg: bigtimespammer.com on one line
 * 
 */
function kahuk_check_spam_rules( $ruleFile, $text )
{
    if ( ! file_exists( $ruleFile ) ) {
        echo $ruleFile . " does not exist\n";
        return false;
    }

    $handle = fopen( $ruleFile, "r" );

    while ( !feof( $handle ) ) {
        $buffer = fgets($handle, 4096);
        $splitbuffer = explode("####", $buffer);
        // Parse domain name from a line
        $expression = parse_url(trim($splitbuffer[0]), PHP_URL_HOST);
        if (!$expression) $expression = trim($splitbuffer[0]);
        // Make it regexp compatible
        $expression = str_replace('.', '\.', $expression);
        // Check $text against http://<domain>
        if (strlen($expression) > 0 && preg_match("/\/\/([^\.]+\.)*$expression(\/|$)/i", $text)) {
            // $this->logSpam("$ruleFile violation: $expression");
            return true;
        }
    }
    fclose($handle);
    return false;
}
