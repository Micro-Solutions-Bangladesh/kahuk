<?php

// show the template
header("HTTP/1.1 404 Not Found");
$main_smarty->assign('tpl_center', $the_template . '/error_404_center');
