<?php
$modid=7;
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
   $MM_editTable  = "petition";
   $MM_editRedirectUrl = "index.html";
   $MM_fieldsStr = "title|value|addressedto|value|shortdesc|value|text|value|intsigner|value|intsignerad|value|intsignerem|value|org|value|url|value|startdate|value|enddate|value";
   $MM_columnsStr = "title|',none,''|addressedto|',none,''|shortdesc|',none,''|text|',none,''|intsigner|',none,''|intsignerad|',none,''|intsignerem|',none,''|org|',none,''|url|',none,''|datestarted|',none,''|dateended|',none,''";

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
<h2 align="right"> Add Petition </h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
  <table border="0" width="90%" align="center">
    <tr> 
      <td align="left" valign="top">Title</td>
      <td> 
        <input type="text" name="title" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Addressed to:</div>
      </td>
      <td> 
        <input type="text" name="addressedto" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Short Description</div>
      </td>
      <td> 
        <textarea name="shortdesc" cols="50" wrap="VIRTUAL" rows="3"></textarea>
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <p align="left">Text of Petition</p>
      </td>
      <td> 
        <p> 
          <textarea name="text" cols="50" rows="15" wrap="PHYSICAL"></textarea>
        </p>
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Submitted By:</div>
      </td>
      <td> 
        <input type="text" name="intsigner" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Contact Info:</div>
      </td>
      <td> 
        <input type="text" name="intsignerad" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">E-mail:</div>
      </td>
      <td> 
        <input type="text" name="intsignerem" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Organization</div>
      </td>
      <td> 
        <input type="text" name="org" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">URL</div>
      </td>
      <td> 
        <input type="text" name="url" size="50">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Start Date</div>
      </td>
      <td> 
        <input type="text" name="startdate" size="50">
        2001-10-23</td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">End Date</div>
      </td>
      <td> 
        <input type="text" name="enddate" size="50">
        2001-10-23 </td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="true">
  <input type="submit" name="Submit" value="Submit">
</form>
<?php include("footer.php"); ?>
