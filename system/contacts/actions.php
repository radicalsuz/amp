<?php
     
  
  require_once("../Connections/freedomrising.php");  

?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
?><?php
// *** Insert Record: set Variables

if (isset($MM_insert)){

   // $MM_editConnection = MM_freedomrising_STRING;
   $MM_editTable  = "action";
   $MM_editRedirectUrl = "contact.php?id=" . ($HTTP_GET_VARS["perid"]) . "";
    $MM_fieldsStr = "field1|value|field2|value|field3|value|field4|value|field5|value|field6|value|field7|value|field8|value|field9|value|field10|value|perid|value|camid|value";
   $MM_columnsStr = "field1|',none,''|field2|',none,''|field3|',none,''|field4|',none,''|field5|',none,''|field6|',none,''|field7|',none,''|field8|',none,''|field9|',none,''|field10|',none,''|perid|',none,''|camid|',none,''";

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


  if (isset($MM_update) && (isset($MM_recordId))) {
  
//    $MM_editConnection = $MM_freedomrising_STRING;
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "contact.php";
     $MM_editTable  = "action";
   $MM_editRedirectUrl = "contact.php?id=" . ($HTTP_GET_VARS["perid"]) . "";
    $MM_fieldsStr = "field1|value|field2|value|field3|value|field4|value|field5|value|field6|value|field7|value|field8|value|field9|value|field10|value|perid|value|camid|value";
   $MM_columnsStr = "field1|',none,''|field2|',none,''|field3|',none,''|field4|',none,''|field5|',none,''|field6|',none,''|field7|',none,''|field8|',none,''|field9|',none,''|field10|',none,''|perid|',none,''|camid|',none,''";
  
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
  
  // *** Delete Record: declare variables
  if (isset($MM_delete) && (isset($MM_recordId))) {
//    $MM_editConnection = $MM_freedomrising_STRING;
    $MM_editTable  = "action";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "allcontacts.php";
  
    if ($MM_editRedirectUrl && $QUERY_STRING && (strlen($QUERY_STRING) > 0)) {
      $MM_editRedirectUrl = $MM_editRedirectUrl . ((strpos($MM_editRedirectUrl, '?') == false)?"?":"&") . $QUERY_STRING;
    }
  }
require ("../../Connections/dataactions.php");
ob_end_flush();
?><?php

if (isset($HTTP_GET_VARS["id"]))
  {$calledrcd__MMColParam = $HTTP_GET_VARS["id"];}

if (isset($HTTP_GET_VARS["cam"]))
  {$campaignrcd__MMColParam = $HTTP_GET_VARS["cam"];}

if (isset($HTTP_GET_VARS["perid"]))
  {$person__MMColParam = $HTTP_GET_VARS["perid"];}

   $campaignrcd=$dbcon->Execute("SELECT * FROM campaigns WHERE id = " . ($campaignrcd__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $campaignrcd_numRows=0;
   $campaignrcd__totalRows=$campaignrcd->RecordCount();
   
$person=$dbcon->Execute("SELECT id, FirstName, LastName FROM contacts2 WHERE id = " . ($person__MMColParam) . "") or DIE($dbcon->ErrorMsg());

   $person_numRows=0;
   $person__totalRows=$person->RecordCount();

   $calledrcd=$dbcon->Execute("SELECT * FROM action WHERE camid = " . ($campaignrcd__MMColParam) . " and perid = " . ($person__MMColParam) . "") or DIE($dbcon->ErrorMsg());

   $calledrcd_numRows=0;
   $calledrcd__totalRows=$calledrcd->RecordCount();

   $allcamp=$dbcon->Execute("SELECT id, name FROM campaigns") or DIE($dbcon->ErrorMsg());
   $allcamp_numRows=0;
   $allcamp__totalRows=$allcamp->RecordCount();

include ("header.php");
?>

<html>
<head>
<title>actions</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="site.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000">
<table width="95%" border="0" cellspacing="0" cellpadding="10">
  <tr> 
    <td> 
      <form name="form1" method="POST" action="<?php echo $MM_editAction?>">
	  
	  
        <table width="100%" border="0" cellspacing="5" class="table">
          <tr class="toplinks"> 
            <td> 
              <p>Campaign</p>
            </td>
            <td> 
              <select name="select" onChange="MM_jumpMenu('parent',this,0)">
                <?php
  if ($allcamp__totalRows > 0){
    $allcamp__index=0;
    $allcamp->MoveFirst();
    WHILE ($allcamp__index < $allcamp__totalRows){
?>
                <OPTION VALUE="actions.php?cam=<?php echo  $allcamp->Fields("id")?>&perid=<?php echo  $person->Fields("id");?>"<?php if ($allcamp->Fields("id")==$campaignrcd->Fields("id")) echo "SELECTED";?>> 
                <?php echo  $allcamp->Fields("name");?>
                </OPTION>
                <?php
      $allcamp->MoveNext();
      $allcamp__index++;
    }
    $allcamp__index=0;  
    $allcamp->MoveFirst();
  }
?>
              </select>
            </td>
            <td>
              <div align="right"><a href="contact.php?id=<?php echo  $person->Fields("id");?> " class="toplinks"> 
                <?php echo  $person->Fields("FirstName");?>
                <?php echo  $person->Fields("LastName");?>
                </a> </div>
            </td>
          </tr>
          <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field1text") ;
			  
			  ?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("1ftype") == ("1")){ ?>
              <input type="text" name="field1" size="40" value="<?php echo $calledrcd->Fields("field1")?>">
              <?php //$1type = "',none,''" ; 
			  }
			if ($campaignrcd->Fields("1ftype") == ("2")){ ?>
              <input <?php If (($calledrcd->Fields("field1")) == "1") { echo "CHECKED";} ; ?> type="checkbox" name="field1" value="1">
              <?php  $bob = "none,1,0" ;
			  }
			  if ($campaignrcd->Fields("1ftype") == ("3")){ ?>
              <textarea name="field1" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field1")?></textarea>
              <?php  //$1type = "',none,''" ;
			   } ?>
            </td>
          </tr>
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field2text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field2text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("2ftype") == ("1")){ ?>
              <input type="text" name="field2" size="40" value="<?php echo $calledrcd->Fields("field2")?>">
              <?php }
			 if ($campaignrcd->Fields("2ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field2")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field2" value="1">
              <?php }
			  if ($campaignrcd->Fields("2ftype") == ("3")){ ?>
              <textarea name="field2" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field2")?></textarea>
              <?php }} ?>
            </td>
          </tr>
          <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field3text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field3text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("3ftype") == ("1")){ ?>
              <input type="text" name="field3" size="40" value="<?php echo $calledrcd->Fields("field3")?>">
              <?php }
			 if ($campaignrcd->Fields("3ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field3")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field3" value="1">
              <?php }
			  if ($campaignrcd->Fields("3ftype") == ("3")){ ?>
              <textarea name="field3" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field3")?></textarea>
              <?php } }?>
            </td>
          </tr>
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field4text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field4text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("4ftype") == ("1")){ ?>
              <input type="text" name="field4" size="40" value="<?php echo $calledrcd->Fields("field4")?>">
              <?php }
			 if ($campaignrcd->Fields("4ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field4")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field4" value="1">
              <?php }
			  if ($campaignrcd->Fields("4ftype") == ("3")){ ?>
              <textarea name="field4" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field4")?></textarea>
              <?php }}?>
            </td>
          </tr>
          <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field5text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field5text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("5ftype") == ("1")){ ?>
              <input type="text" name="field5" size="40" value="<?php echo $calledrcd->Fields("field5")?>">
              <?php }
			 if ($campaignrcd->Fields("5ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field5")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field5" value="1">
              <?php }
			  if ($campaignrcd->Fields("5ftype") == ("3")){ ?>
              <textarea name="field5" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field5")?></textarea>
              <?php }} ?>
            </td>
          </tr>
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field6text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field6text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("6ftype") == ("1")){ ?>
              <input type="text" name="field6" size="40" value="<?php echo $calledrcd->Fields("field6")?>">
              <?php }
			 if ($campaignrcd->Fields("6ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field6")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field6" value="1">
              <?php }
			  if ($campaignrcd->Fields("6ftype") == ("3")){ ?>
              <textarea name="field6" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field6")?></textarea>
              <?php }} ?>
            </td>
          </tr>
          <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field7text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field7text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("7ftype") == ("1")){ ?>
              <input type="text" name="field7" size="40" value="<?php echo $calledrcd->Fields("field7")?>">
              <?php }
			 if ($campaignrcd->Fields("7ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field7")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field7" value="1">
              <?php }
			  if ($campaignrcd->Fields("7ftype") == ("3")){ ?>
              <textarea name="field7" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field7")?></textarea>
              <?php }} ?>
            </td>
          </tr>
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field8text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field8text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("8ftype") == ("1")){ ?>
              <input type="text" name="field8" size="40" value="<?php echo $calledrcd->Fields("field8")?>">
              <?php }
			 if ($campaignrcd->Fields("8ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field8")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field8" value="1">
              <?php }
			  if ($campaignrcd->Fields("8ftype") == ("3")){ ?>
              <textarea name="field8" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field8")?></textarea>
              <?php }} ?>
            </td>
          </tr>
          <tr> 
            <td> 
              <?php echo $campaignrcd->Fields("field9text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field9text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("9ftype") == ("1")){ ?>
              <input type="text" name="field9" size="40" value="<?php echo $calledrcd->Fields("field9")?>">
              <span class="table"> 
              <?php }
			 if ($campaignrcd->Fields("9ftype") == ("2")){ ?>
              </span> 
              <input <?php if (($calledrcd->Fields("field9")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field9" value="1">
              <?php }
			  if ($campaignrcd->Fields("9ftype") == ("3")){ ?>
              <textarea name="field9" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field9")?></textarea>
              <?php }} ?>
            </td>
          </tr>
          <tr class="title"> 
            <td> 
              <?php echo $campaignrcd->Fields("field10text")?>
            </td>
            <td colspan="2"> 
              <?php if ($campaignrcd->Fields("field10text")!= ($null)){ ?>
              <?php if ($campaignrcd->Fields("10ftype") == ("1")){ ?>
              <input type="text" name="field10" size="40" value="<?php echo $calledrcd->Fields("field10")?>">
              <?php }
			 if ($campaignrcd->Fields("10ftype") == ("2")){ ?>
              <input <?php if (($calledrcd->Fields("field10")) == "1") { echo "CHECKED";} ?> type="checkbox" name="field10" value="1">
			  
			  
              <?php }
			  if ($campaignrcd->Fields("10ftype") == ("3")){ ?>
              <textarea name="field10" wrap="VIRTUAL" cols="40" rows="5"><?php echo $calledrcd->Fields("field10")?></textarea>
              <?php } }?>
            </td>
          </tr>
        </table>
		<input type="hidden" name="camid" value="<?php echo $cam ?>">
		<input type="hidden" name="perid" value="<?php echo $perid ?>">
<?php if ($calledrcd->Fields("id") == ($null)){?>
        <input type="hidden" name="MM_insert" value="true">
<?php
}
 if ($calledrcd->Fields("id") != ($null)){?>
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $calledrcd->Fields("id") ?>"><?php  } ?>
   <input type="submit" name="Submit" value="Update">   </form>  <form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="hidden" name="MM_delete" value="true">
	 <input type="hidden" name="MM_recordId" value="<?php echo $calledrcd->Fields("id") ?>">
	<input type="submit" name="Submit2" value="Delete"></form>
	
      &nbsp; </td>
  </tr>
</table>
</body>
</html>
<?php
  $campaignrcd->Close();
?>
<?php
  $calledrcd->Close();
?>
<?php
  $allcamp->Close();
  include ("footer.php");
?>
