<?php
  require("Connections/freedomrising.php");
  
  
  function art_publish($ids) {
	global $dbcon;
	if (is_array($ids)) {

	$q1 ="update articletype set  usenav=0";
			$dbcon->execute($q1) or die($dbcon->errorMsg());
	//	for($v=0; $v<$count; $v++) {
	while(list($key,$value)= each($ids)){ 
			$q = "update articletype set  usenav=1 where id=$key";
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items posted as draft.')
		);
	}
	//send_to($_SERVER['PHP_SELF'], $qs);
}

  function art_order($ids) {
	global $dbcon;
	if (is_array($ids)) {

	$q1 ="update articletype set  textorder=0";
			$dbcon->execute($q1) or die($dbcon->errorMsg());
	//	for($v=0; $v<$count; $v++) {
	while(list($key,$value)= each($ids)){ 
//	echo $value."<br>";
			$q = "update articletype set  textorder= '$value' where id=$key";
			$dbcon->execute($q) or die($dbcon->errorMsg());
		}
		$qs = array(
			'msg' => urlencode('Selected items posted as draft.')
		);
	}
	//send_to($_SERVER['PHP_SELF'], $qs);
}

  /**
 * a switch to see what the page should be doing
 */
switch($_POST['act']) {

	case 'Update':
		art_publish($_POST['publish']);
		art_order($_POST['order']);
		break;

}

  

   $type=$dbcon->Execute("SELECT *  FROM articletype ORDER BY parent, textorder ASC") or DIE($dbcon->ErrorMsg());
  
   $type_numRows=0;
   $type__totalRows=$type->RecordCount();

   

	$Recordset3=$dbcon->Execute("SELECT class, id FROM class  ORDER BY class ASC") or DIE($dbcon->ErrorMsg());
   $Recordset3_numRows=0;
   $Recordset3__totalRows=$Recordset3->RecordCount();

   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $type_numRows = $type_numRows + $Repeat1__numRows;

   $Repeat4__numRows = -1;
   $Repeat4__index= 0;
   $Recordset3_numRows = $Recordset3_numRows + $Repeat4__numRows;
?>
<?php $MM_paramName = ""; ?>
<?php
// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained
$MM_removeList = "&index=";
if ($MM_paramName != "") $MM_removeList .= "&".strtolower($MM_paramName)."=";
$MM_keepURL="";
$MM_keepForm="";
$MM_keepBoth="";
$MM_keepNone="";

// add the URL parameters to the MM_keepURL string
reset ($HTTP_GET_VARS);
while (list ($key, $val) = each ($HTTP_GET_VARS)) {
	$nextItem = "&".strtolower($key)."=";
	if (!stristr($MM_removeList, $nextItem)) {
		$MM_keepURL .= "&".$key."=".urlencode($val);
	}
}

// add the URL parameters to the MM_keepURL string
if(isset($HTTP_POST_VARS)){
	reset ($HTTP_POST_VARS);
	while (list ($key, $val) = each ($HTTP_POST_VARS)) {
		$nextItem = "&".strtolower($key)."=";
		if (!stristr($MM_removeList, $nextItem)) {
			$MM_keepForm .= "&".$key."=".urlencode($val);
		}
	}
}

// create the Form + URL string and remove the intial '&' from each of the strings
$MM_keepBoth = $MM_keepURL."&".$MM_keepForm;
if (strlen($MM_keepBoth) > 0) $MM_keepBoth = substr($MM_keepBoth, 1);
if (strlen($MM_keepURL) > 0)  $MM_keepURL = substr($MM_keepURL, 1);
if (strlen($MM_keepForm) > 0) $MM_keepForm = substr($MM_keepForm, 1);
?>
<?php include ("header.php");?>


      <table width="100%" border="0">
        <tr class="banner"> 
          <td>Article Section</td>
        </tr>
      </table><form  action="<?= $PHP_SELF ?>" method="POST">
      <table width="100%" border="0" align="center">
        <tr class="intitle"> 
          <td><font size="-4"><b>Section</b></font></td>
          <td><font size="-4"><b>Publish</b></font></td>
          <td><font size="-4"><b>ID</b></font></td>
          <td><b><font size="-4">Parent</font></b></td>
		  <td><b><font size="-4">Order</font></b></td>
          <td><font size="-4">Navigation<br>
            for Content</font></td>
          <td><font size="-4">Navigation<br>
            for section<br>
            index </font></td>
        </tr>
        <?php while (($Repeat1__numRows-- != 0) && (!$type->EOF)) 
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td><A HREF="type_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$type->Fields("id") ?>"><?php echo $type->Fields("type")?></a></td>
          <td> <input type="checkbox" name="publish[<?php echo $type->Fields("id")?>]" value="1" <?php if  ($type->Fields("usenav")) {echo "checked"; }?>> </td>
          <td> <?php echo $type->Fields("id")?> </td>
          <td>
            <?php $typename=$dbcon->Execute("SELECT type FROM articletype where id =".$type->Fields("parent")." ") or DIE($dbcon->ErrorMsg());
		  echo $typename->Fields("type")?>
          </td>
		  <td> <input name="order[<?php echo $type->Fields("id")?>]" type="text" value="<?php echo $type->Fields("textorder")?>" size="2"> </td>
          <td> <div align="right"><A HREF="type_nav_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$type->Fields("id") ?>">Edit</A> 
            </div></td>
          <td> <div align="right"><A HREF="typelist_nav_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$type->Fields("id") ?>">Edit</A> 
            </div></td>
        </tr>
        <?php
  $Repeat1__index++;
  $type->MoveNext();
}
?>
      </table>
	  <input type="submit" name="act" value="Update" class="name">
	  </form>
      <br>
      <table width="100%" border="0">
        <tr class="banner"> 
          <td>Article Class</td>
        </tr>
      </table>
      <table width="100%" border="0" align="center">
        <tr class="intitle"> 
          <td><b>Class</b></td>
          <td><b>ID</b></td>
          <td><font size="-4">Class<br>
            Settings</font></td>
          <td><font size="-4">Navigation<br>
            for Content</font><br>
          </td>
          <td><font size="-4">Navigation<br>
            for class<br>
            index </font></td>
        </tr>
        <?php while (($Repeat4__numRows-- != 0) && (!$Recordset3->EOF)) 
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $Recordset3->Fields("class")?> </td>
          <td> <?php echo $Recordset3->Fields("id")?> </td>
          <td> <div align="right"><a href="class.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset3->Fields("id") ?>">Edit</a></div></td>
          <td> <div align="right"><A HREF="class_nav_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset3->Fields("id") ?>">Edit</A> 
            </div></td>
          <td><div align="right"><A HREF="classlist_nav_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset3->Fields("id") ?>">Edit</A> 
            </div> </td>
        </tr>
        <?php
  $Repeat4__index++;
  $Recordset3->MoveNext();
}
?>
      </table>

      <?php
  $type->Close();
  $Recordset3->Close();
?>
      <?php include ("footer.php");?>
