<?php
$modid=11;
  require("Connections/freedomrising.php");
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?><?php
// *** Insert Record: set Variables

if (isset($MM_insert)){

   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "linktype";
   $MM_editRedirectUrl = "linktype_list.php";
   $MM_fieldsStr = "name|value";
   $MM_columnsStr = "name|',none,''";

  // create the $MM_fields and $MM_columns arrays
   $MM_fields = explode("|", $MM_fieldsStr);
   $MM_columns = explode("|", $MM_columnsStr);
  
  // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
 }

  // append the query string to the redirect URL
  if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
  }
}
require ("../Connections/dataactions.php");
ob_end_flush();
?><?php include("header.php"); ?>
<body bgcolor="#FFFFFF" text="#000000">
<form method="POST" action="<?php echo $MM_editAction?>" name="form1">
  <table border=0 cellpadding=2 cellspacing=0 align="center">
    <tr valign="baseline"> 
      <td nowrap align="right">Name:</td>
      <td> 
        <input type="text" name="name" value="" size="32">
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right">&nbsp;</td>
      <td> 
        <input type="submit" value="Insert Record" name="submit">
      </td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="true">
</form>
<p>&nbsp;</p>
<?php include("footer.php"); ?>
