<?php

  require_once("Connections/freedomrising.php");  

?><?php
if (isset($HTTP_GET_VARS["repeat"])) {$repeat= $HTTP_GET_VARS["repeat"];}
else {$repeat = 50;}


if (!isset($_GET[modin])){ $modin=8;} else {$modin=$_GET[modin];}
$modid=40;
$sql1= "SELECT DISTINCTROW userdata.First_Name, userdata.Last_Name, userdata.Phone, userdata.Cell_Phone, userdata.Work_Phone, userdata.Email, userdata.Company, userdata.id, userdata.region  ";
$sql3= "SELECT DISTINCTROW userdata.* ";
 
 $sql7.="FROM  userdata ";
if (is_array($_POST[interests])) { 
	$sql7.=", vol_relinterest";}
if (is_array($_POST[skills])) { 
	$sql7.=", vol_relskill";}
if (is_array($_POST[available])) { 
	$sql7.=", vol_relavailability";}

 $sql7.=" WHERE ";
if (is_array($_POST[interests])) {  $sql7.=" userdata.id = vol_relinterest.personid AND ";  }
if (is_array($_POST[skills])) { $sql7.=" userdata.id = vol_relskill.personid AND "; }
if (is_array($_POST[available])) { $sql7.=" userdata.id = vol_relavailability.personid AND "; }

 
if ($_POST[First_Name] != NULL) {$sql7.=  " userdata.First_Name LIKE '%".$_POST[First_Name]."%' AND";} 
if ($_POST[Last_Name]  != NULL) {$sql7.=   " userdata.Last_Name LIKE '%".$_POST[Last_Name]."%' and ";}
if ($_POST[Company]  != NULL){$sql7.=    " userdata.Company LIKE '%".$_POST[Company]."%' AND  ";}
if ($_POST[Notes]  != NULL){$sql7.=   " userdata.Notes LIKE '%".$_POST[notes]."%' AND  ";}
if ($_POST[Email]  == "EMPTY"){$sql7.=     " userdata.Email = '' AND  ";}
elseif ($_POST[Email]  != NULL){$sql7.=     " userdata.Email LIKE '%".$_POST[email]."%' AND  ";}
if ($_POST[Street]  == "NOT EMPTY"){$sql7.=     " (userdata.Street != NULL or userdata.Street !=  '') AND  ";}
elseif ($_POST[Street]  != NULL){$sql7.=   " userdata.Street LIKE '%".$_POST[Street]."%' AND  ";}
if ($_POST[City]  != NULL){$sql7.=   " userdata.City LIKE '%".$_POST[City]."%' AND  ";}
if ($_POST[State]  != NULL){$sql7.=   " userdata.State LIKE '%".$_POST[State]."%' AND  ";}
if ($_POST[region]  != NULL){$sql7.=   " userdata.region = '".$_POST[region]."' AND  ";}
if ($_POST[Zip]  != NULL){$sql7.=   " userdata.Zip LIKE '%".$_POST[Zip]."%' AND  ";}
if ($_POST[custom3] != NULL) {$sql7.=    "  userdata.custom3 LIKE '%".$_POST[custom3]."%' AND  ";}
#if ($avalibility  != NULL){$sql7.=   " userdata.avalibility  LIKE '%$avalibility%' AND  ";}
if ($_POST[custom5]  != NULL){$sql7.=   " userdata.custom5 LIKE '%".$_POST[custom5]."%' AND  ";}
if ($_POST[phone]  != NULL){$sql7.= " (userdata.Phone LIKE '%".$_POST[phone]."%' or userdata.Cell_Phone LIKE '%$phone%' or userdata.Work_Phone LIKE '%$phone%') AND  ";}

#if ($com != 0) {$sql7.=" (userdata.com1 = '$com' or userdata.com2 = '$com' or userdata.com3 = '$com') AND  ";}


if (is_array($_POST[skills])) {
	foreach ($_POST[skills] as $d_skill) {
		$sql7.= "  vol_relskill.skillid=$d_skill AND "; 
	}
}
if (is_array($_POST[interests])) {
	foreach($_POST[interests] as $d_interest) {
		$sql7.= "  vol_relinterest.interestid=$d_interest AND  "; 
	} 
}
if(is_array($_POST[available])) {
	foreach($_POST[available] as $d_avail) {
		$sql7.=" vol_relavailability.id=$d_avail AND ";
	}
}
$sql7.=" userdata.modin='8' AND ";


$sql7.= " userdata.id!=800000000000000 ";

$sql =$sql1.$sql7;
$sqlsend = $sql3.$sql7;
$Recordset1=$dbcon->Execute("$sql")or DIE($dbcon->ErrorMsg());
//echo $sql;
   $page_numRows=0;
   $page__totalRows= $Recordset1->RecordCount();
   

   $Repeat2__numRows = $repeat;
   $Repeat2__index= 0;
   $page_numRows = $page_numRows + $Repeat2__numRows;
   $page_total = $Recordset1->RecordCount();
   include ("pagation.php");
?><?php $MM_paramName = ""; ?><?php
 // *** Go To Record and Move To Record: create strings for maintaining URL and Form parameters

// create the list of parameters which should not be maintained
$MM_removeList = "&results=";
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

include ("header.php");
?>

<link rel="stylesheet" href="site.css" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<body>
<h2>Results </h2>
<?php #if ($userper[93] == 1){{} ?>
<!--<form name="form3" method="post" action="vol_mailblast.php">
  <input type="submit" name="Submit" value="Send Email">
  <input type="hidden" name="sqlp" value="<?php echo $sql7 ?>">
</form>--><?php #if ($userper[93] == 1){}} ?>
<form name="form2" method="post" action="export4.php?id=<?php echo $modin;?>">
  <input type="submit" name="Submit" value="Download as CSV File">
  <input type="hidden" name="sqlsend" value="<?php echo $sql7 ?>">
  <!--
  <select name="results" onChange="MM_jumpMenu('parent',this,0)">
    <option selected>Results Options</option>
    <option value="vol_results2.php?results=phone&<?php echo $MM_keepBoth ?>">view 
    as call list</option>
    <option value="vol_results2.php?results=mail&<?php echo $MM_keepBoth ?>">view as 
    mailing list</option>
    <option value="vol_results2.php?results=email&<?php echo $MM_keepBoth ?>">view 
    as e-mail list</option>
    <option value="vol_results2.php?<?php echo $MM_keepBoth ?>">view results</option>
      </select>-->
</form>
<!--
<p class="table"> Displaying: <?php echo ($MM_offset +1) ?> - <?php echo ($MM_size+$MM_offset) ?> 
  of <?php echo $MM_rsCount ?> Records&nbsp;<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
  </strong> 
  <select name="repeat" onChange="MM_jumpMenu('parent',this,0)">
    <option selected># to Display</option>
    <option value="vol_results2.php?<?php echo  $MM_keepBoth ?>&repeat=10">10</option>
    <option value="vol_results2.php?<?php echo  $MM_keepBoth ?>&repeat=50">50</option>
    <option value="vol_results2.php?<?php echo  $MM_keepBoth ?>&repeat=100">100</option>
    <option value="vol_results2.php?<?php echo  $MM_keepBoth ?>&repeat=250">250</option>
    <option value="vol_results2.php?<?php echo  $MM_keepBoth ?>&repeat=-1">All</option>
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
  <?php } // end !$MM_atTotal ?>--> </p>
   <?php if ($HTTP_GET_VARS["results"] == (NULL)){?>
<table cellpadding="1" cellspacing="1" width="95%">
    <tr class="toplinks"> 
      <td><b>Name</b></td>
      <td><b> Last Name</b></td>
      <td><b>Phone</b></td>
      <td><b>Email</b></td>
      <td><b>District</b></td>
      <td><b>ID</b></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php  while ((!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td > <?php echo $Recordset1->Fields("First_Name")?> </td>
      <td><?php echo $Recordset1->Fields("Last_Name")?></td>
      <td> 
        <?php if ($Recordset1->Fields("Phone") != ('')) { 
	  	   echo  $Recordset1->Fields("Phone") ;} 
	elseif ($Recordset1->Fields("Cell_Phone") != ('')) { 
	 echo $Recordset1->Fields("Cell_Phone");}
	 elseif ($Recordset1->Fields("Work_Phone") != (''))  
	 {echo $Recordset1->Fields("Work_Phone");}
	 ?>
      </td>
      <td> 
        <a href="mailto:<?php echo $Recordset1->Fields("Email")?>"><?php echo $Recordset1->Fields("Email")?></a> 
      </td>
      <td> <?php echo $Recordset1->Fields("region")?> </td>
      <td> <?php echo $Recordset1->Fields("id")?> </td>
      <td></td>
	  <td> <?php if ($userper[87] == 1 or $standalone == 1){{} ?><a href="vol_personedit2.php?modin=8&<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."uid=".$Recordset1->Fields("id") ?>">edit</a><?php if ($userper[87] == 1 or $standalone == 1){}} ?></td>
    </tr>
    <?php
  $Repeat2__index++;
  $Recordset1->MoveNext();
}
?>
  </table>
  <p>&nbsp; </p>
</form>
<?php } ?>
<!-- END MODULE -->
<!--START  PHONE LIST MODULE -->
  <?php if ($HTTP_GET_VARS["results"] == ('phone')){?>
<form name="form1">
   <table cellpadding="1" cellspacing="1" width="95%">
    <tr class="toplinks"> 
      <td><b>Name</b></td>
      <td><b>Company</b></td>
      <td><b>Day</b></td>
      <td><b>Evening</b></td>
	  <td><b>Mobile</b></td>
      <td>Email</td>
      <td>District</td>
    </tr>
    <?php while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td ><P><a href="contact.php?id=<?php echo $Recordset1->Fields("id")?>"><?php echo $Recordset1->Fields("First_Name")?>&nbsp;<?php echo $Recordset1->Fields("Last_Name")?></a></td>
      <td> <?php echo $Recordset1->Fields("Company")?> </td>
      <td><?php echo $Recordset1->Fields("Phone")?> </td>
      <td><?php echo $Recordset1->Fields("Cell_Phone ")?> </td>
	  <td><?php echo $Recordset1->Fields("Work_Phone ")?> </td>
      <td> 	<A href="mailto:<?php echo $Recordset1->Fields("Email")?>"><?php echo $Recordset1->Fields("Email")?></A>
			</td>
      <td>
	  <?php 
	  	   echo $Recordset1->Fields("region"); ?>
	
	 </td>
    </tr>
    <?php
  $Repeat2__index++;
  $Recordset1->MoveNext();
}
?>
  </table> 
  </p>
  </form>
<?php } ?>
<!-- END MODULE -->
<!--START MAILING LIST MODULE -->
  <?php if ($HTTP_GET_VARS["results"] == ('mail')){?>
  <form name="form1">
 <?php while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <p><?php echo $Recordset1->Fields("First_Name")?> <?php echo $Recordset1->Fields("Last_Name")?><br>
 
   <?php if ($Recordset1->Fields("Street") != ($null)) { ?>
   		 <?php echo $Recordset1->Fields("Street")?><br>
		<?php echo $Recordset1->Fields("City")?>&nbsp;CA&nbsp;<?php echo $Recordset1->Fields("Zip")?></p>
 

  <?php } ?>  
<?php
  $Repeat2__index++;
  $Recordset1->MoveNext();
}
?></form>
<?php } ?>
<!-- END MODULE -->
<!--START E-MAIL LIST MODULE -->
  <?php if ($HTTP_GET_VARS["results"] == ('email')){?>
  <form name="form1">
 <?php while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <?php echo $Recordset1->Fields("Email")?>, 
<?php
  $Repeat2__index++;
  $Recordset1->MoveNext();
}
?></form>
<?php } ?>
<!-- END MODULE -->
  <?php
  $Recordset1->Close();
  include ("footer.php");
?>

