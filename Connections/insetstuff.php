 <?php

// create the $MM_fields and $MM_columns arrays
$MM_fields = Explode("|", $MM_fieldsStr);
$MM_columns = Explode("|", $MM_columnsStr);

// set the form values
for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    if (isset($$MM_fields[$i])) {
        $MM_fields[$i+1] = $$MM_fields[$i];
    } else {
        $MM_fields[$i+1] = $_POST[$MM_fields[$i]];
    }
}

// append the query string to the redirect URL
if ($MM_editRedirectUrl && $_SERVER['QUERY_STRING'] && (strlen($_SERVER['QUERY_STRING']) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $_SERVER['QUERY_STRING'];
}

?>
