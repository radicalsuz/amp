<?php
$modid=40;
  require_once("Connections/freedomrising.php");  

?><?php
if (isset($HTTP_GET_VARS["repeat"])) {$repeat= $HTTP_GET_VARS["repeat"];}
else {$repeat = 50;}




$sql1= "SELECT DISTINCTROW vol_people.first_name, vol_people.last_name, vol_people.phone, vol_people.phone2, phone3, vol_people.email, vol_people.organization, vol_people.id, vol_people.hood  ";
$sql3= "SELECT DISTINCTROW vol_people.* ";
 
 $sql7.="FROM  vol_people ";
if ($interestid != NULL) { $sql7.=", vol_relinterest";}
if ($skillid != NULL) { $sql7.=", vol_relskill";}
 $sql7.=" WHERE ";
if ($interestid != NULL) {  $sql7.=" vol_people.id = vol_relinterest.personid AND ";  }
if ($skillid != NULL) { $sql7.=" vol_people.id = vol_relskill.personid AND "; }

 
if ($first_name != NULL) {$sql7.=  " vol_people.first_name LIKE '%$first_name%' AND";} 
if ($last_name  != NULL) {$sql7.=   " vol_people.last_name LIKE '%$last_name%' and ";}
if ($organization  != NULL){$sql7.=    " vol_people.organization LIKE '%$organization%' AND  ";}
if ($notes  != NULL){$sql7.=   " vol_people.notes LIKE '%$notes%' AND  ";}
if ($precinct  != NULL){$sql7.=   " vol_people.precinct = $precinct AND  ";}
if ($bounce  != NULL){$sql7.=   " vol_people.bounce = $bounce AND  ";}
if ($email  == "EMPTY"){$sql7.=     " vol_people.email = '' AND  ";}
elseif ($email  != NULL){$sql7.=     " vol_people.email LIKE '%$email%' AND  ";}
if ($address  == "NOT EMPTY"){$sql7.=     " (vol_people.address != NULL or vol_people.address !=  '') AND  ";}
elseif ($address  != NULL){$sql7.=   " vol_people.address LIKE '%$address%' AND  ";}
if ($city  != NULL){$sql7.=   " vol_people.city LIKE '%$city%' AND  ";}
if ($hood  != NULL){$sql7.=   " vol_people.hood = '$hood' AND  ";}
if ($zip  != NULL){$sql7.=   " vol_people.zip LIKE '%$zip%' AND  ";}
if ($officenotes != NULL) {$sql7.=    "  vol_people.officenotes LIKE '%$officenotes%' AND  ";}
if ($avalibility  != NULL){$sql7.=   " vol_people.avalibility  LIKE '%$avalibility%' AND  ";}
if ($otherinterest  != NULL){$sql7.=   " vol_people.otherinterest LIKE '%$otherinterest%' AND  ";}
if ($phone  != NULL){$sql7.= " (vol_people.phone LIKE '%$phone%' or vol_people.phone2 LIKE '%$phone%' or vol_people.phone3 LIKE '%$phone%') AND  ";}

if ($com != 0) {$sql7.=" (vol_people.com1 = '$com' or vol_people.com2 = '$com' or vol_people.com3 = '$com') AND  ";}


if ($skillid != NULL) {$sql7.= "  vol_relskill.skillid=$skillid  AND "; }
if ($interestid != NULL) {$sql7.= "  vol_relinterest.interestid=$interestid AND  "; } 

if ($mon_d != NULL) {$sql7.=  " vol_people.mon_d = $mon_d AND";} 
if ($tues_d != NULL) {$sql7.=  " vol_people.tues_d = $tues_d AND";} 
if ($wen_d != NULL) {$sql7.=  " vol_people.wen_d = $wen_d AND";} 
if ($thur_d != NULL) {$sql7.=  " vol_people.thur_d = $thur_d AND";} 
if ($fri_d != NULL) {$sql7.=  " vol_people.fri_d = $fri_d AND";} 
if ($sat_d != NULL) {$sql7.=  " vol_people.sat_d = $sat_d AND";} 
if ($sun_d != NULL) {$sql7.=  " vol_people.sun_d = $sun_d AND";} 

if ($mon_n != NULL) {$sql7.=  " vol_people.mon_n = $mon_n AND";} 
if ($tues_n != NULL) {$sql7.=  " vol_people.tues_n = $tues_n AND";} 
if ($wen_n != NULL) {$sql7.=  " vol_people.wen_n = $wen_n AND";} 
if ($thur_n != NULL) {$sql7.=  " vol_people.thur_n = $thur_n AND";} 
if ($fri_n != NULL) {$sql7.=  " vol_people.fri_n = $fri_n AND";} 
if ($sat_n != NULL) {$sql7.=  " vol_people.sat_n = $sat_n AND";} 
if ($sun_n != NULL) {$sql7.=  " vol_people.sun_n = $sun_n AND";} 



$sql7.= " vol_people.id!=800000000000000 ";

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
<h2>Results </h2><?php if ($userper[93] == 1){{} ?>
<form name="form3" method="post" action="vol_mailblast.php">
  <input type="submit" name="Submit" value="Send Email">
  <input type="hidden" name="sqlp" value="<?php echo $sql7 ?>">
</form><?php if ($userper[93] == 1){}} ?>
<form name="form2" method="post" action="export.php">
  <input type="submit" name="Submit" value="Download as CVS File">
  <input type="hidden" name="sqlsend" value="<?php echo $sqlsend ?>">
  <select name="results" onChange="MM_jumpMenu('parent',this,0)">
    <option selected>Results Options</option>
    <option value="vol_results.php?results=phone&<?php echo $MM_keepBoth ?>">view 
    as call list</option>
    <option value="vol_results.php?results=mail&<?php echo $MM_keepBoth ?>">view as 
    mailing list</option>
    <option value="vol_results.php?results=email&<?php echo $MM_keepBoth ?>">view 
    as e-mail list</option>
    <option value="vol_results.php?<?php echo $MM_keepBoth ?>">view results</option>
      </select>
</form>
<p class="table"> Displaying: <?php echo ($MM_offset +1) ?> - <?php echo ($MM_size+$MM_offset) ?> 
  of <?php echo $MM_rsCount ?> Records&nbsp;<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
  </strong> 
  <select name="repeat" onChange="MM_jumpMenu('parent',this,0)">
    <option selected># to Display</option>
    <option value="vol_results.php?<?php echo  $MM_keepBoth ?>&repeat=10">10</option>
    <option value="vol_results.php?<?php echo  $MM_keepBoth ?>&repeat=50">50</option>
    <option value="vol_results.php?<?php echo  $MM_keepBoth ?>&repeat=100">100</option>
    <option value="vol_results.php?<?php echo  $MM_keepBoth ?>&repeat=250">250</option>
    <option value="vol_results.php?<?php echo  $MM_keepBoth ?>&repeat=-1">All</option>
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
  <?php } // end !$MM_atTotal ?> </p>
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
    <?php  while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td > <?php echo $Recordset1->Fields("first_name")?> </td>
      <td><?php echo $Recordset1->Fields("last_name")?></td>
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
        <a href="mailto:<?php echo $Recordset1->Fields("email")?>"><?php echo $Recordset1->Fields("email")?></a> 
      </td>
      <td> <?php echo $Recordset1->Fields("hood")?> </td>
      <td> <?php echo $Recordset1->Fields("id")?> </td>
      <td></td>
	  <td> <?php if ($userper[87] == 1 or $standalone == 1){{} ?><a href="vol_personedit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</a><?php if ($userper[87] == 1 or $standalone == 1){}} ?></td>
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
      <td><b>Organization</b></td>
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
      <td ><P><a href="contact.php?id=<?php echo $Recordset1->Fields("id")?>"><?php echo $Recordset1->Fields("first_name")?>&nbsp;<?php echo $Recordset1->Fields("last_name")?></a></td>
      <td> <?php echo $Recordset1->Fields("organization")?> </td>
      <td><?php echo $Recordset1->Fields("phone")?> </td>
      <td><?php echo $Recordset1->Fields("phone2 ")?> </td>
	  <td><?php echo $Recordset1->Fields("phone3 ")?> </td>
      <td> 	<A href="mailto:<?php echo $Recordset1->Fields("email")?>"><?php echo $Recordset1->Fields("email")?></A>
			</td>
      <td>
	  <?php 
	  	   echo $Recordset1->Fields("hood"); ?>
	
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
  <p><?php echo $Recordset1->Fields("first_name")?> <?php echo $Recordset1->Fields("last_name")?><br>
 
   <?php if ($Recordset1->Fields("address") != ($null)) { ?>
   		 <?php echo $Recordset1->Fields("address")?><br>
		<?php echo $Recordset1->Fields("city")?>&nbsp;CA&nbsp;<?php echo $Recordset1->Fields("zip")?></p>
 

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
  <?php echo $Recordset1->Fields("email")?>, 
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

