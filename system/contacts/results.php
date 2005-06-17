<?php
     
  
  require_once("../Connections/freedomrising.php");  

?><?php
if (isset($HTTP_GET_VARS["repeat"])) {$repeat= $HTTP_GET_VARS["repeat"];}
else {$repeat = 50;}


$sql1= "SELECT DISTINCTROW contacts2.FirstName, contacts2.LastName, contacts2.HomePhone, contacts2.BusinessPhone, contacts2.MobilePhone, contacts2.EmailAddress, contacts2.Email2Address, contacts2.Company, contacts2.id, contacts2.HomeState, contacts2.BusinessState  ";
$sql3= "SELECT DISTINCT contacts2.* ";
 
  if (isset($camid)) {

 $sql2.= "FROM action, contacts2 where contacts2.id =action.perid and (contacts2.BusinessPhone LIKE '%$HomePhone%' or contacts2.HomePhone LIKE '%$HomePhone%' or contacts2.MobilePhone LIKE '%$HomePhone%') 
  AND (contacts2.EmailAddress LIKE '%$EmailAddress%' or contacts2.Email2Address LIKE '%$EmailAddress%') 
  AND (contacts2.BusinessStreet LIKE '%$BusinessStreet%' or contacts2.HomeStreet LIKE '%$BusinessStreet%') 
  AND (contacts2.BusinessStreet2 LIKE '%$BusinessStreet2%' or contacts2.HomeStreet2 LIKE '%$BusinessStreet2%') 
  AND (contacts2.BusinessCity LIKE '%$BusinessCity%' or contacts2.HomeCity LIKE '%$BusinessCity%') 
  AND (contacts2.BusinessState LIKE '%$BusinessState%' or contacts2.HomeState LIKE '%$BusinessState%') 
  AND (contacts2.BusinessPostalCode LIKE '%$BusinessPostalCode%' or contacts2.HomePostalCode LIKE '%$BusinessPostalCode%') 
  AND (contacts2.BusinessCountry LIKE '%$BusinessCountry%' or contacts2.HomeCountry LIKE '%$BusinessCountry%') 
  AND (contacts2.Suffix LIKE '%$Suffix%' AND contacts2.FirstName LIKE '%$FirstName%' AND contacts2.LastName LIKE '%$LastName%' AND contacts2.Company LIKE '%$Company%' AND contacts2.classid LIKE '%$classid%' AND contacts2.regionid LIKE '%$regionid%' AND contacts2.WebPage LIKE '%$WebPage%' AND contacts2.campus LIKE '%$campus%' AND contacts2.notes LIKE '%$notes%' AND contacts2.enteredby LIKE '%$enteredby%' AND action.field1 LIKE '%$field1%' AND action.field2 LIKE '%$field2%'  AND action.field3 LIKE '%$field3%' AND action.field4 LIKE '%$field4%' AND action.field5 LIKE '%$field5%' AND action.field6 LIKE '%$field6%' AND action.field7 LIKE '%$field7%' AND action.field8 LIKE '%$field8%' AND action.field9 LIKE '%$field9%' AND action.field10 LIKE '%$field10%' AND action.camid LIKE '%$camid%' AND contacts2.classid LIKE '%$classid%' AND contacts2.regionid LIKE '%$regionid%' AND contacts2.WebPage LIKE '%$WebPage%'AND contacts2.notes LIKE '%$notes%' AND contacts2.enteredby LIKE '%$enteredby%') Order By LastName Asc";
 }
else {
  $sql2.= "from contacts2 WHERE (contacts2.BusinessPhone LIKE '%$HomePhone%' or contacts2.HomePhone LIKE '%$HomePhone%' or contacts2.MobilePhone LIKE '%$HomePhone%') AND (contacts2.EmailAddress LIKE '%$EmailAddress%' or contacts2.Email2Address LIKE '%$EmailAddress%') AND (contacts2.BusinessStreet LIKE '%$BusinessStreet%' or contacts2.HomeStreet LIKE '%$BusinessStreet%') AND (contacts2.BusinessStreet2 LIKE '%$BusinessStreet2%' or contacts2.HomeStreet2 LIKE '%$BusinessStreet2%') AND (contacts2.BusinessCity LIKE '%$BusinessCity%' or contacts2.HomeCity LIKE '%$BusinessCity%') AND (contacts2.BusinessState LIKE '%$BusinessState%' or contacts2.HomeState LIKE '%$BusinessState%') AND (contacts2.BusinessPostalCode LIKE '%$BusinessPostalCode%' or contacts2.HomePostalCode LIKE '%$BusinessPostalCode%') AND (contacts2.BusinessCountry LIKE '%$BusinessCountry%' or contacts2.HomeCountry LIKE '%$BusinessCountry%') AND (contacts2.Suffix LIKE '%$Suffix%' AND contacts2.FirstName LIKE '%$FirstName%' AND contacts2.LastName LIKE '%$LastName%' AND contacts2.Company LIKE '%$Company%' AND contacts2.classid LIKE '%$classid%' AND contacts2.regionid LIKE '%$regionid%' AND contacts2.WebPage LIKE '%$WebPage%'AND  contacts2.campus LIKE '%$campus%' AND contacts2.notes LIKE '%$notes%' AND contacts2.enteredby LIKE '%$enteredby%') Order By LastName Asc";
}
$sql =$sql1.$sql2;
$sqlsend = $sql3.$sql2;
$Recordset1=$dbcon->Execute("$sql")or DIE($dbcon->ErrorMsg());

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
<form name="form3" method="post" action="mailblast.php">
 <input type="submit" name="Submit" value="Send Email">
  <input type="hidden" name="sqlp" value="<?php echo $sql2 ?>">
</form>
<form name="form2" method="post" action="export.php">
  <input type="submit" name="Submit" value="Download as CVS File">
  <input type="hidden" name="sqlsend" value="<?php echo $sqlsend ?>">
  <select name="results" onChange="MM_jumpMenu('parent',this,0)">
    <option selected>Results Options</option>
    <option value="results.php?results=phone&<?php echo $MM_keepBoth ?>">view 
    as call list</option>
    <option value="results.php?results=mail&<?php echo $MM_keepBoth ?>">view as 
    mailing list</option>
    <option value="results.php?results=email&<?php echo $MM_keepBoth ?>">view 
    as e-mail list</option>
    <option value="results.php?<?php echo $MM_keepBoth ?>">view results</option>
      </select>
</form>
<p class="table"> Displaying: <?php echo ($MM_offset +1) ?> - <?php echo ($MM_size+$MM_offset) ?> 
  of <?php echo $MM_rsCount ?> Records&nbsp;<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
  </strong> 
  <select name="repeat" onChange="MM_jumpMenu('parent',this,0)">
    <option selected># to Display</option>
    <option value="results.php?<?php echo  $MM_keepBoth ?>&repeat=10">10</option>
    <option value="results.php?<?php echo  $MM_keepBoth ?>&repeat=50">50</option>
    <option value="results.php?<?php echo  $MM_keepBoth ?>&repeat=100">100</option>
    <option value="results.php?<?php echo  $MM_keepBoth ?>&repeat=250">250</option>
    <option value="results.php?<?php echo  $MM_keepBoth ?>&repeat=-1">All</option>
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
      <td><b>Org</b></td>
      <td><b>ID</b></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php  while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td > <?php echo $Recordset1->Fields("FirstName")?> </td>
      <td><?php echo $Recordset1->Fields("LastName")?></td>
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
        <a href="mailto:<?php echo $Recordset1->Fields("EmailAddress")?>"><?php echo $Recordset1->Fields("EmailAddress")?></a> 
        <?php } 
	 elseif ($Recordset1->Fields("Email2Address") != (''))  { ?>
        <a href="mailto:<?php echo $Recordset1->Fields("Email2Address")?>"><?php echo $Recordset1->Fields("Email2Address")?></a> 
        <?php } ?>
      </td>
      <td> <?php echo $Recordset1->Fields("Company")?> </td>
      <td> <?php echo $Recordset1->Fields("id")?> </td>
      <td><a href="contact.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">view</a></td>
	  <td> <?php if ($userper[95] == 1 or $standalone == 1){{} ?><a href="contact_edit.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</a><?php if ($userper[95] == 1 or $standalone == 1){}} ?></td>
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
      <td><b>Work</b></td>
      <td><b>Home</b></td>
	  <td><b>Mobile</b></td>
      <td>Email</td>
      <td>State</td>
    </tr>
    <?php while (($Repeat2__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
    <tr bordercolor="#333333" bgcolor="#CCCCCC" class="results"> 
      <td ><P><a href="contact.php?id=<?php echo $Recordset1->Fields("id")?>"><?php echo $Recordset1->Fields("FirstName")?>&nbsp;<?php echo $Recordset1->Fields("LastName")?></a></td>
      <td> <?php echo $Recordset1->Fields("Company")?> </td>
      <td><?php echo $Recordset1->Fields("HomePhone")?> </td>
      <td><?php echo $Recordset1->Fields("BusinessPhone ")?> </td>
	  <td><?php echo $Recordset1->Fields("MobilePhone ")?> </td>
      <td> <?php if ($Recordset1->Fields("EmailAddress") != ('')) { ?>
	<A href="mailto:<?php echo $Recordset1->Fields("EmailAddress")?>"><?php echo $Recordset1->Fields("EmailAddress")?></A>
	<?php } 
	 elseif ($Recordset1->Fields("Email2Address") != (''))  { ?>
	 <A href="mailto:<?php echo $Recordset1->Fields("Email2Address")?>"><?php echo $Recordset1->Fields("Email2Address")?></A>
		  <?php } ?></td>
      <td>
	  <?php if ($Recordset1->Fields("BusinessState") == ('')) { 
	  	   echo $Recordset1->Fields("HomeState"); } 
	else {echo $Recordset1->Fields("BusinessState");}?>
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
  <p><?php echo $Recordset1->Fields("FirstName")?> <?php echo $Recordset1->Fields("LastName")?><br>
  <?php if ($Recordset1->Fields("useaddress") == (business)) { ?>
  	  <?php echo $Recordset1->Fields("BusinessStreet")?><br>
		<?php echo $Recordset1->Fields("BusinessCity")?>&nbsp;<?php echo $Recordset1->Fields("BusinessState")?>&nbsp;<?php echo $Recordset1->Fields("BusinessPostalCode")?></p>
  <?php } ?>
  <?php if ($Recordset1->Fields("useaddress") == (home)) { ?>
		<?php echo $Recordset1->Fields("HomeStreet")?><br>
		<?php echo $Recordset1->Fields("HomeCity")?>&nbsp;<?php echo $Recordset1->Fields("HomeState")?>&nbsp;<?php echo $Recordset1->Fields("HomePostalCode")?></p>
  <?php } ?> 
    <?php if ($Recordset1->Fields("useaddress") == ($null)) { ?>
   <?php if ($Recordset1->Fields("BusinessStreet") != ($null)) { ?>
   		 <?php echo $Recordset1->Fields("BusinessStreet")?><br>
		<?php echo $Recordset1->Fields("BusinessCity")?>&nbsp;<?php echo $Recordset1->Fields("BusinessState")?>&nbsp;<?php echo $Recordset1->Fields("BusinessPostalCode")?></p>
    <?php } ?>
	<?php if ($Recordset1->Fields("BusinessStreet") == ($null)) { ?>
 		<?php echo $Recordset1->Fields("HomeStreet")?><br>
		<?php echo $Recordset1->Fields("HomeCity")?>&nbsp;<?php echo $Recordset1->Fields("HomeState")?>&nbsp;<?php echo $Recordset1->Fields("HomePostalCode")?></p>
		 

  <?php } ?>  <?php } ?>  
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
  <?php echo $Recordset1->Fields("EmailAddress")?>, 
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

