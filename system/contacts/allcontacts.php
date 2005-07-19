<?php
     
  
  require_once("../Connections/freedomrising.php");  

if (isset($HTTP_GET_VARS["repeat"])) {$repeat= $HTTP_GET_VARS["repeat"];}
else {$repeat = 50;}
if (isset($HTTP_GET_VARS["LastName"])) {$order_LastName = "order by LastName";}
else if (isset($HTTP_GET_VARS["FirstName"])) {$order_FirstName = "order by FirstName";}
else if (isset($HTTP_GET_VARS["HomePhone"])) {$order_HomePhone = "order by BusinessPhone";}
else if (isset($HTTP_GET_VARS["EmailAddress"])) {$order_EmailAddress = "order by EmailAddress";}
else if (isset($HTTP_GET_VARS["Business"])) {$order_Company = "order by Company";}
else if (isset($HTTP_GET_VARS["State"])) {$order_State = "order by BusinessState";}
else if (isset($HTTP_GET_VARS["id"])) {$order_id = "order by id";}
else if (isset($HTTP_GET_VARS["Region"])) {$order_Region = "order by Region";}
else  {$order_LastName = "order by LastName, id";}

if (isset($HTTP_GET_VARS["enteredby"])){$where_enteredby = "where enteredby= ".$HTTP_GET_VARS["enteredby"];}
if (isset($HTTP_GET_VARS["region"])){$where_region = "where regionid= ".$HTTP_GET_VARS["region"];}
if (isset($HTTP_GET_VARS["source"])){$where_source = "where source= ".$HTTP_GET_VARS["source"];}
if (isset($HTTP_GET_VARS["type"])){$where_type = "where classid = ".$HTTP_GET_VARS["type"];}
if (isset($HTTP_GET_VARS["modifiedby"])){$where_modifiedby = "where modifiedby= ".$HTTP_GET_VARS["modifiedby"];}

$sql = "Select distinct contacts2.FirstName, contacts2.LastName, contacts2.HomePhone, contacts2.BusinessPhone, contacts2.MobilePhone, contacts2.EmailAddress, contacts2.Email2Address, contacts2.Company, contacts2.id, contacts2.HomeState, contacts2.BusinessState from contacts2 $where_enteredby $where_region $where_source $where_type $where_modifiedby $order_LastName $order_FirstName $order_HomePhone $order_EmailAddress $order_Company $order_State $order_Region";
$sql2 ="$where_enteredby $where_region $where_source $where_type $where_modifiedby";

   $Recordset1=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
   
//if (isset($HTTP_GET_VARS["export"])) {include ("export.php");}
   
   $page_numRows=0;
   $page__totalRows= $Recordset1->RecordCount();
   
   $Repeat2__numRows = $repeat;
   $Repeat2__index= 0;
   $page_numRows = $page_numRows + $Repeat2__numRows;
   $page_total = $Recordset1->RecordCount();
   include ("pagation.php");

?><?php
   $allsource=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $alltype=$dbcon->Execute("SELECT id, title FROM contacts_class ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $allregion=$dbcon->Execute("SELECT id, title FROM region ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $allusers=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());

?><?php


?><?php $MM_paramName = ""; ?><?php
// *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained
 $MM_removeList = "&index=,&FirstName=,&LastName=,&HomePhone=,&EmailAddress=,&Business=,&State=,&id=,&PHPSESSID=";
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


<?php include("header.php"); ?>
<link rel="stylesheet" href="site.css" type="text/css">

<h2>All Contacts</h2>
<select name="enteredby" onChange="MM_jumpMenu('parent',this,0)">
 <option selected>Select By Entered</option>
 <?php while (!$allusers->EOF)   { 
?>
                <OPTION VALUE="allcontacts.php?&enteredby=<?php echo $allusers->Fields("id")?>  ">
                <?php echo $allusers->Fields("name");?>
                </OPTION>
                <?php  $allusers->MoveNext();}?></select>

<select name="source" onChange="MM_jumpMenu('parent',this,0)">
 <option selected>Select By Source</option>
 <?php while (!$allsource->EOF)   { 
?>
                <OPTION VALUE="allcontacts.php?&source=<?php echo $allsource->Fields("id")?> ">
                <?php echo $allsource->Fields("title");?>
                </OPTION>
                <?php  $allsource->MoveNext();}?></select>

<select name="region" onChange="MM_jumpMenu('parent',this,0)">
 <option selected>Select By Region</option>
 <?php while (!$allregion->EOF)   { 
?>
                <OPTION VALUE="allcontacts.php?&region=<?php echo $allregion->Fields("id")?> ">
                <?php echo $allregion->Fields("title");?>
                </OPTION>
                <?php  $allregion->MoveNext();}?></select>

<select name="type" onChange="MM_jumpMenu('parent',this,0)">
 <option selected>Select By Type</option>
 <?php while (!$alltype->EOF)   { 
?>
                <OPTION VALUE="allcontacts.php?&type=<?php echo $alltype->Fields("id")?>  ">
                <?php echo $alltype->Fields("title");?>
                </OPTION>
                <?php  $alltype->MoveNext();}?></select>
<p class="table"> Displaying: <?php echo ($MM_offset +1) ?> - <?php echo ($MM_size+$MM_offset) ?> 
  of <?php echo $MM_rsCount ?> Records&nbsp;<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
  </strong> 
  <select name="repeat" onChange="MM_jumpMenu('parent',this,0)">
    <option selected># to Display</option>
    <option value="allcontacts.php?<?php echo  $MM_keepURL ?>&repeat=10">10</option>
    <option value="allcontacts.php?<?php echo  $MM_keepURL ?>&repeat=50">50</option>
    <option value="allcontacts.php?<?php echo  $MM_keepURL ?>&repeat=100">100</option>
    <option value="allcontacts.php?<?php echo  $MM_keepURL ?>&repeat=250">250</option>
    <option value="allcontacts.php?<?php echo  $MM_keepURL ?>&repeat=-1">All</option>
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
  <a href="export.php?sql2=<?php echo  $sql2 ?>">Export to CSV File</a></p>
<form name="form1">
  <table cellpadding="1" cellspacing="1" width="95%">
    <tr class="toplinks"> 
      <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&FirstName=1" class="toplinks"><b>First 
        Name</b></a></td>
      <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&LastName=1" class="toplinks"><b>Last 
        Name</b></a></td>
      <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&HomePhone=1" class="toplinks"><b>Phone</b></a></td>
      <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&EmailAddress=1" class="toplinks"><b>Email</b></a></td>
      <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&Business=1" class="toplinks"><b>Organization</b></a></td>
	  <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&State=1" class="toplinks"><b>State</b></a></td>
      <td><a href="allcontacts.php?<?php echo  $MM_keepURL ?>&id=1" class="toplinks"><b>ID</b></a></td>
      <td><b></b></td>
	  <td><b></b></td>
    </tr>
    <?php while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td > 
        <?php echo $Recordset1->Fields("FirstName")?>
      </td>
      <td> 
        <?php echo $Recordset1->Fields("LastName")?>
      </td>
      <td> 
      
		<?php if ($Recordset1->Fields("BusinessPhone") != ('')) { 
	  	   echo  $Recordset1->Fields("BusinessPhone") ;} 
	elseif ($Recordset1->Fields("MobilePhone") != ('')) { 
	 echo $Recordset1->Fields("MobilePhone");}
	 elseif ($Recordset1->Fields("HomePhone") != (''))  
	 {echo $Recordset1->Fields("HomePhone");}
	 ?>
	
      </td>
      <td> 
   	   <?php if ($Recordset1->Fields("EmailAddress") != ('')) { ?>
	<A href="mailto:<?php echo $Recordset1->Fields("EmailAddress")?>"><?php echo $Recordset1->Fields("EmailAddress")?></A>
	<?php } 
	 elseif ($Recordset1->Fields("Email2Address") != (''))  { ?>
	 <A href="mailto:<?php echo $Recordset1->Fields("Email2Address")?>"><?php echo $Recordset1->Fields("Email2Address")?></A>
		  <?php } ?>
      </td>
      <td> 
        <?php echo $Recordset1->Fields("Company")?>
      </td>
	  <td> 
       <?php if ($Recordset1->Fields("BusinessState") == ('')) { 
	  	   echo $Recordset1->Fields("HomeState"); } 
	else {echo $Recordset1->Fields("BusinessState");}?>
	
      </td>
      <td> 
        <?php echo $Recordset1->Fields("id")?>
        </td>
		<td><a href="contact.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">view</a></td>
		<td><?php if ( AMP_Authorized( AMP_PERMISSION_CONTACT_EDIT)) {?><a href="contact_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</a><?php } ?></td>
    </tr>
    <?php
  $Repeat2__index++;
  $Recordset1->MoveNext();
}
?>
  </table>

</form>

<div align="right" class="table">
 
 
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
  
</div> 

<?php include ("footer.php");?>

