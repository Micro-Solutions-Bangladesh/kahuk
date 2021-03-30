<?php

	/* Redwine: creating a mysqli connection */
	$handle = new mysqli(EZSQL_DB_HOST,EZSQL_DB_USER,EZSQL_DB_PASSWORD,EZSQL_DB_NAME);
	/* check connection */
    if ($handle->connect_errno) {
        printf("Connect failed: %s\n", $handle->connect_error);
		exit();
	}

/* 
    Redwine: this is needed to be able to insert/update zeros in dates and timestamps.
    This change will not be retained after the services are restarted, so the code will always set it back the way we need it.
*/
$sql_mode = $handle->query("show variables like 'sql_mode';");
$nozeroindate = ',NO_ZERO_IN_DATE';
$nozerodate = ',NO_ZERO_DATE';
foreach ($sql_mode as $mode) {
    foreach ($mode as $key => $value) {
        if ($key == 'Value') {
            if (!empty($value)) {
                $pos_NO_ZERO_IN_DATE = strpos($value, $nozeroindate);
                $pos_NO_ZERO_DATE = strpos($value, $nozerodate);
                if ($pos_NO_ZERO_IN_DATE !== FALSE) {
                    $value = str_replace($nozeroindate, '', $value);
                }
                if ($pos_NO_ZERO_DATE !== FALSE) {
                    $value = str_replace($nozerodate, '', $value);
                }
                $handle->query("set GLOBAL sql_mode = \"$value\"");
            }/*  else {
                echo "<br />sql_mode is not set!";
            } */
        }
    }
}    
?>