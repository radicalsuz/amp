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
  // *** Update Record: set variables
  
  if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "petitionsigner";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "petition_signlist.php";
    $MM_fieldsStr = "select|value|firstname|value|lastname|value|city|value|state|value|country|value|email|value|comment|value|phone|value|fax|value";
    $MM_columnsStr = "petitionid|none,none,NULL|firstname|',none,''|lastname|',none,''|city|',none,''|state|',none,''|country|',none,''|email|',none,''|comment|',none,''|phone|',none,''|fax|',none,''";
  
    // create the $MM_fields and $MM_columns arrays
   $MM_fields = Explode("|", $MM_fieldsStr);
   $MM_columns = Explode("|", $MM_columnsStr);
    
    // set the form values
  for ($i=0; $i+1 < sizeof($MM_fields); ($i=$i+2)) {
    $MM_fields[$i+1] = $$MM_fields[$i];
    }
  
    // append the query string to the redirect URL
  if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
    $MM_editRedirectUrl .= ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
  ?><?php
  // *** Delete Record: declare variables
  if (isset($MM_delete) && (isset($MM_recordId))) {
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "petitionsigner";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "petiton_signlist.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }

  require ("../Connections/dataactions.php");
  ob_end_flush();
  
?>
<?php
$idvalue__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$idvalue__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
$values__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$values__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $petitiontitles=$dbcon->Execute("SELECT id, title FROM petition ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $petitiontitles_numRows=0;
   $petitiontitles__totalRows=$petitiontitles->RecordCount();
?><?php
   $idvalue=$dbcon->Execute("SELECT id, title FROM petition WHERE id = " . ($idvalue__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $idvalue_numRows=0;
   $idvalue__totalRows=$idvalue->RecordCount();
?><?php
   $values=$dbcon->Execute("SELECT * FROM petitionsigner WHERE id = " . ($values__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $values_numRows=0;
   $values__totalRows=$values->RecordCount();
?>
<html>
<head>
<?php include("header.php"); ?>
<h2 align="right"> Edit Petition Signer</h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="Form1" onSubmit=>
 
        
  <table border="0" width="90%" align="center">
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Petition</div>
      </td>
      <td> 
        <select name="select">
          <?php
  if ($petitiontitles__totalRows > 0){
    $petitiontitles__index=0;
    $petitiontitles->MoveFirst();
    WHILE ($petitiontitles__index < $petitiontitles__totalRows){
?>
          <OPTION VALUE="<?php echo  $petitiontitles->Fields("id")?>"<?php if ($petitiontitles->Fields("id")==$values->Fields("petitionid")) echo "SELECTED";?>> 
          <?php echo  $petitiontitles->Fields("title");?>
          </OPTION>
          <?php
      $petitiontitles->MoveNext();
      $petitiontitles__index++;
    }
    $petitiontitles__index=0;  
    $petitiontitles->MoveFirst();
  }
?>
        </select>
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">First Name</div>
      </td>
      <td> 
        <input type="text" name="firstname" size="50" value="<?php echo $values->Fields("firstname")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Last name</div>
      </td>
      <td> 
        <input type="text" name="lastname" size="50" value="<?php echo $values->Fields("lastname")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">City</div>
      </td>
      <td> 
        <input type="text" name="city" size="50" value="<?php echo $values->Fields("city")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">State</div>
      </td>
      <td> 
        <input type="text" name="state" size="50" value="<?php echo $values->Fields("state")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Country</div>
      </td>
      <td> 
        <input type="text" name="country" size="50" value="<?php echo $values->Fields("country")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">E-Mail</div>
      </td>
      <td> 
        <input type="text" name="email" size="50" value="<?php echo $values->Fields("email")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Comments</div>
      </td>
      <td> 
        <p> 
          <textarea name="comment" cols="50" wrap="VIRTUAL" rows="5"><?php echo $values->Fields("comment")?></textarea>
        </p>
        <p>Use HTML for Line Breaks (&lt;p&gt;)</p>
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Phone</div>
      </td>
      <td> 
        <input type="text" name="phone" size="50" value="<?php echo $values->Fields("phone")?>">
      </td>
    </tr>
    <tr> 
      <td align="right" valign="top"> 
        <div align="left">Fax</div>
      </td>
      <td> 
        <input type="text" name="fax" size="50" value="<?php echo $values->Fields("fax")?>">
      </td>
    </tr>
  </table>
      <p> 
    <input type="submit" name="Submit" value="Update">
  </p>
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $values->Fields("id") ?>">
</form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="delete" value="Delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $values->Fields("id") ?>">
</form>
<?php
  $petitiontitles->Close();
?>
<?php
  $idvalue->Close();
?>
<?php
  $values->Close();
?>
<?php include("footer.php"); ?>
