<?php
header("Location: userdata_list.php?modin=8");
/*
 $modid=40;
  require("Connections/freedomrising.php");
if (isset($HTTP_GET_VARS["repeat"])) {$repeat= $HTTP_GET_VARS["repeat"];}
else {$repeat = 50;}
if (isset($HTTP_GET_VARS["last_name"])) {$order_last_name = "order by last_name";}
else if (isset($HTTP_GET_VARS["first_name"])) {$order_first_name = "order by first_name";}
else if (isset($HTTP_GET_VARS["phone"])) {$order_phone = "order by phone";}
else if (isset($HTTP_GET_VARS["email"])) {$order_email = "order by email";}
else if (isset($HTTP_GET_VARS["organization"])) {$order_organization = "order by organization";}
else if (isset($HTTP_GET_VARS["hood"])) {$order_hood = "order by hood";}
else if (isset($HTTP_GET_VARS["id"])) {$order_id = "order by id";}
else if (isset($HTTP_GET_VARS["Region"])) {$order_Region = "order by hood";}
else  {$order_last_name = "order by last_name, id";}

if (isset($HTTP_GET_VARS["enteredby"])){$where_enteredby = "where enteredby= ".$HTTP_GET_VARS["enteredby"];}
if (isset($HTTP_GET_VARS["region"])){$where_region = "where hood= ".$HTTP_GET_VARS["region"];}
if (isset($HTTP_GET_VARS["source"])){$where_source = "where source= ".$HTTP_GET_VARS["source"];}
if (isset($HTTP_GET_VARS["type"])){$where_type = "where classid = ".$HTTP_GET_VARS["type"];}
if (isset($HTTP_GET_VARS["modifiedby"])){$where_modifiedby = "where modifiedby= ".$HTTP_GET_VARS["modifiedby"];}

$sql = "Select * from vol_people $where_enteredby $where_region $where_source $where_type $where_modifiedby $order_last_name $order_first_name $order_phone $order_email $order_organization $order_hood $order_Region $order_id";
$sql2 ="$where_enteredby $where_region $where_source $where_type $where_modifiedby";

   $Recordset1=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
   
    $page_numRows=0;
   $page__totalRows= $Recordset1->RecordCount();
   
   $Repeat2__numRows = $repeat;
   $Repeat2__index= 0;
   $page_numRows = $page_numRows + $Repeat2__numRows;
   $page_total = $Recordset1->RecordCount();
   include ("pagation.php");
   
   $allregion=$dbcon->Execute("SELECT id FROM vol_hood") or DIE($dbcon->ErrorMsg());
   $allusers=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
?><?php $MM_paramName = ""; ?><?php
// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained
 $MM_removeList = "&index=,&first_name=,&last_name=,&phone=,&email=,&organization=,&hood=,&id=,&PHPSESSID=";
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

?><?php
include ("header.php");
?>

<h2>Volunteers</h2><form name="form2" method="post" action="export.php">
  <input type="submit" name="Submit" value="Download as CVS File">
        &nbsp;&nbsp; 
        <select name="enteredby" onChange="MM_jumpMenu('parent',this,0)">
          <option selected>Select By Entered</option>
          <option>unnamed1</option>
          <option value="vol_list.php?&enteredby=<?php echo $allusers->Fields("id")?>  "> 
          <?php echo $allusers->Fields("name");?> </option>
          <option>unnamed2</option>
        </select>
        <select name="region" onChange="MM_jumpMenu('parent',this,0)">
          <option selected>Select By Distirct</option>
          <option>unnamed1</option>
          <option value="vol_list.php?®ion=<?php echo $allregion->Fields("id")?> "> 
          <?php echo $allregion->Fields("id");?> </option>
          <option>unnamed2</option>
        </select>
      </form>
<p class="table"> Displaying: <?php echo ($MM_offset +1) ?> - <?php echo ($MM_size+$MM_offset) ?> 
  of <?php echo $MM_rsCount ?> Records&nbsp;<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
  </strong> 
  <select name="repeat" onChange="MM_jumpMenu('parent',this,0)">
    <option selected># to Display</option>
    <option value="vol_list.php?<?php echo  $MM_keepURL ?>&repeat=10">10</option>
    <option value="vol_list.php?<?php echo  $MM_keepURL ?>&repeat=50">50</option>
    <option value="vol_list.php?<?php echo  $MM_keepURL ?>&repeat=100">100</option>
    <option value="vol_list.php?<?php echo  $MM_keepURL ?>&repeat=250">250</option>
    <option value="vol_list.php?<?php echo  $MM_keepURL ?>&repeat=-1">All</option>
  </select>
  &nbsp;&nbsp; 
  <?php if ($MM_offset != 0) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go"> 
  &laquo;&nbsp;First Page</a> 
  <?php } // end $MM_offset != 0 ?>
  <?php if ($MM_offset != 0) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go">&laquo;&nbsp;</a><a href="<?php echo $MM_movePrev?>" class="go">Previous 
  Page </a> 
  <?php } // end $MM_offset != 0 ?>
  <?php if (!$MM_atTotal) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveNext?>" class="go">Next 
  Page &raquo;</a> 
  <?php } // end !$MM_atTotal ?>
  <?php if (!$MM_atTotal) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveLast?>" class="go">Last 
  Page &raquo;</a> 
  <?php } // end !$MM_atTotal ?>
  <br>
  <a href="export.php">Export to CSV File</a></p>

      <table cellpadding="1" cellspacing="1" width="95%">
    <tr class="toplinks"> 
      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&first_name=1" class="toplinks"><b>First 
        Name</b></a></td>
      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&last_name=1" class="toplinks"><b>Last 
        Name</b></a></td>
      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&phone=1" class="toplinks"><b>Phone</b></a></td>
      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&email=1" class="toplinks"><b>Email</b></a></td>
      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&organization=1" class="toplinks"><b>Organization</b></a></td>
	      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&hood=1" class="toplinks"><b>Distirct</b></a></td>
      <td><a href="vol_list.php?<?php echo  $MM_keepURL ?>&id=1" class="toplinks"><b>ID</b></a></td>
      <td><b></b></td>
	  <td><b></b></td>
    </tr>
    <?php while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td > 
        <?php echo $Recordset1->Fields("first_name")?>
      </td>
      <td> 
        <?php echo $Recordset1->Fields("last_name")?>
      </td>
      <td> 
      
		<?php if ($Recordset1->Fields("phone") != ('')) { 
	  	   echo  $Recordset1->Fields("phone") ;} 
	elseif ($Recordset1->Fields("phone2") != ('')) { 
	 echo $Recordset1->Fields("phone2");}
	 elseif ($Recordset1->Fields("phone3") != (''))  
	 {echo $Recordset1->Fields("phone3");}
	 ?>
	
      </td>
          <td> 
            
            <A href="mailto:<?php echo $Recordset1->Fields("email")?>"><?php echo $Recordset1->Fields("email")?></A> 
           
          </td>
      <td> 
        <?php echo $Recordset1->Fields("organization")?>
      </td>
	  <td> 
       <?php 
	  	   echo $Recordset1->Fields("hood"); 
	?>
	
      </td>
      <td> 
        <?php echo $Recordset1->Fields("id")?>
        </td>
		<td></td>
		<td><?php if ($userper[87] == 1){{} ?><a href="vol_personedit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</a><?php if ($userper[87] == 1){}} ?></td>
    </tr>
    <?php
  $Repeat2__index++;
  $Recordset1->MoveNext();
}
?>
  </table>
 
 
  <?php if ($MM_offset != 0) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go"> 
  &laquo;&nbsp;First Page</a> 
  <?php } // end $MM_offset != 0 ?>
  <?php if ($MM_offset != 0) { ?>
  &nbsp; <a href="<?php echo $MM_moveFirst?>" class="go">&laquo;&nbsp;</a><a href="<?php echo $MM_movePrev?>" class="go">Previous 
  Page </a> 
  <?php } // end $MM_offset != 0 ?>
  <?php if (!$MM_atTotal) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveNext?>" class="go">Next 
  Page &raquo;</a> 
  <?php } // end !$MM_atTotal ?>
  <?php if (!$MM_atTotal) { ?>
  &nbsp;&nbsp; <a href="<?php echo $MM_moveLast?>" class="go">Last 
  Page &raquo;</a> 
  <?php } // end !$MM_atTotal ?>

<?php
include ("footer.php");
*/
?>
