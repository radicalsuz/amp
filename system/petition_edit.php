<?php
$modid=7;
  require("Connections/freedomrising.php");
?>
<?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";

?>
<?php
 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
 if ($MM_insert) {
 $name = "Petition - $title";
 $field19text = "Show your name and comments on this site";
  $field20text = "verified";
 $ftype19 = 2;
 $ftype20 = 2;
  $pub19 = 1;
  $pub20 = 0;
  $sourceid = 3;
  $enteredby = 2;
  
   $MM_editTable  = "modfields";
   $MM_fieldsStr = "name|value|sourceid|value|enteredby|value|field19text|value|field20text|value|ftype19|value|ftype20|value|pub19|value|pub20|value";
   $MM_columnsStr = "name|',none,''|sourceid|',none,''|enteredby|',none,''|field19text|',none,''|field20text|',none,''|19ftype|',none,''|20ftype|',none,''|19pub|',none,''|20pub|',none,''";
   require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");
   
    $udmck=$dbcon->Execute("SELECT id from modfields order by id desc limit 1");
   $udmid = $udmck->Fields("id");
 }
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "petition";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_fieldsStr = "title|value|addressedto|value|shortdesc|value|text|value|intsigner|value|intsignerad|value|intsignerem|value|org|value|url|value|startdate|value|enddate|value|sourceid|value|enteredby|value|list1|value|list2|value|list3|value|uselists|value|udmid|value";
    $MM_columnsStr = "title|',none,''|addressedto|',none,''|shortdesc|',none,''|text|',none,''|intsigner|',none,''|intsignerad|',none,''|intsignerem|',none,''|org|',none,''|url|',none,''|datestarted|',none,''|dateended|',none,''|sourceid|none,none,NULL|enteredby|none,none,NULL|list1|none,none,NULL|list2|none,none,NULL|list3|none,none,NULL|uselists|none,1,0|udmid|',none,''";
	    require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");
	
	 if ($MM_insert) {
	     $ptck=$dbcon->Execute("SELECT id from petition order by id desc limit 1");
   $pid = $ptck->Fields("id");
   $MM_insert = NULL;
   $MM_update =1 ;
	  $redirect ="petition.php?pid=$pid&pthank=1" ;
	      $MM_editTable  = "modfields";
    $MM_editColumn = "id";
    $MM_recordId =  $udmid;
	$MM_fieldsStr = "redirect|value";
   $MM_columnsStr = "redirect|',none,''";
   	    require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");
	 }
  
    $MM_editRedirectUrl = "petition_list.php";
	header("Location: $MM_editRedirectUrl");
 }
?>

<?php
$Recordset1__MMColParam = "90000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
?>
<?php
   $Recordset1=$dbcon->Execute("SELECT * FROM petition WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
    $enteredby=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
	$enteredby_numRows=0;
   $enteredby__totalRows=$enteredby->RecordCount();
   	$source=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $source_numRows=0;
   $source__totalRows=$source->RecordCount();
   	$list=$dbcon->Execute("SELECT id, name from lists ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $list_numRows=0;
   $list__totalRows=$list->RecordCount();
?>

<?php include("header.php"); ?>
<h1 align="right">Edit Petition </h1>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
              
        <table border="0" align="center" width="90%">
          <tr> 
            <td align="right"> <div align="left">Title</div></td>
            <td> <input type="text" name="title" size="50" value="<?php echo $Recordset1->Fields("title")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"> <div align="left">Addressed to:</div></td>
            <td> <input type="text" name="addressedto" size="50" value="<?php echo $Recordset1->Fields("addressedto")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"> <div align="left">Short Description</div></td>
            <td> <textarea name="shortdesc" cols="50" wrap="VIRTUAL" rows="3"><?php echo $Recordset1->Fields("shortdesc")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td align="right"> <p align="left">Text of Petition</p>
              <p align="left">&nbsp;</p></td>
            <td> <p> 
                <textarea name="text" cols="50" rows="15"><?php echo $Recordset1->Fields("text")?></textarea>
              </p></td>
          </tr>
          <tr> 
            <td align="right"> <div align="left">Submitted By:</div></td>
            <td> <input type="text" name="intsigner" size="50" value="<?php echo $Recordset1->Fields("intsigner")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"> <div align="left">Contact Info:</div></td>
            <td> <input type="text" name="intsignerad" size="50" value="<?php echo $Recordset1->Fields("intsignerad")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"> <div align="left">E-mail:</div></td>
            <td> <input type="text" name="intsignerem" size="50" value="<?php echo $Recordset1->Fields("intsignerem")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" valign="top"> <div align="left">Organization</div></td>
            <td> <input type="text" name="org" size="50" value="<?php echo $Recordset1->Fields("org")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" valign="top"> <div align="left">URL</div></td>
            <td> <input type="text" name="url" size="50" value="<?php echo $Recordset1->Fields("url")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" valign="top"> <div align="left">Start Date</div></td>
            <td> <input type="text" name="startdate" size="50" value="<?php echo $Recordset1->Fields("datestarted")?>">
              2001-10-23</td>
          </tr>
          <tr> 
            <td align="right" valign="top"> <div align="left">End Date</div></td>
            <td> <input type="text" name="enddate" size="50" value="<?php echo $Recordset1->Fields("dateended")?>">
              2001-10-23 </td>
          </tr>
        </table>
		<input type="hidden" name="udmid" value="<?php echo $Recordset1->Fields("udmid") ?>">
  <input type="submit" name="Submit" value="Submit">
   <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
		<?php 
		}
		else { ?>
		<input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>"><input type="hidden" name="MM_update" value="true"><?php } ?>   
</form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="Delete" value="Delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>">
</form>
<?php
  $Recordset1->Close();
   $list->Close();
    $source->Close();
	 $enteredby->Close();
?>
<?php include("footer.php"); ?>
