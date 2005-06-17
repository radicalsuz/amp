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

  // *** Update Record: set variables
  
   if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
   
  $delit=$dbcon->Execute(" DELETE from contacts_rel where perid =  $MM_recordId") or DIE($dbcon->ErrorMsg());
  if ($MM_update) {
   foreach ($custom as $k=>$v) {

   $addit=$dbcon->Execute( 'Insert into contacts_rel (fieldid,perid,value) VALUES ('.$k.','.$MM_recordId.',"'.addslashes($v).'")') or DIE($dbcon->ErrorMsg());
   				}
   }
  
//    $MM_editConnection = $MM_freedomrising_STRING;
    $MM_editTable  = "contacts2";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_fieldsStr = "Suffix|value|FirstName|value|LastName|value|Company|value|JobTitle|value|BusinessPhone|value|HomePhone|value|MobilePhone|value|BUsinessFax|value|EmailAddress|value|BusinessStreet|value|BusinessStreet2|value|BusinessCity|value|BusinessState|value|BusinessPostalCode|value|BusinessCountry|value|HomeStreet|value|HomeStreet2|value|HomeCity|value|HomeState|value|HomePostalCode|value|HomeCountry|value|classid|value|regionid|value|notes|value|WebPage|value|useaddress|value|enteredby|value|source|value|Email2Address|value|campus|value|modifiedby|value";
    $MM_columnsStr = "Suffix|',none,''|FirstName|',none,''|LastName|',none,''|Company|',none,''|JobTitle|',none,''|BusinessPhone|',none,''|HomePhone|',none,''|MobilePhone|',none,''|BusinessFax|',none,''|EmailAddress|',none,''|BusinessStreet|',none,''|BusinessStreet2|',none,''|BusinessCity|',none,''|BusinessState|',none,''|BusinessPostalCode|',none,''|BusinessCountry|',none,''|HomeStreet|',none,''|HomeStreet2|',none,''|HomeCity|',none,''|HomeState|',none,''|HomePostalCode|',none,''|HomeCountry|',none,''|classid|',none,''|regionid|',none,''|notes|',none,''|WebPage|',none,''|useaddress|',none,''|enteredby|',none,''|source|',none,''|Email2Address|',none,''|campus|',none,''|modifiedby|',none,''";

  // *** Delete Record: declare variables
  if (isset($MM_delete) ) {
    $MM_editRedirectUrl = "allcontacts.php";}
	else {$MM_editRedirectUrl = "contact.php";}

  	require ("../../Connections/insetstuff.php");
    require ("../../Connections/dataactions.php");
  }
  ?>
  <?php
  	  function customfield($id,$type,$name,$value) {  
 	 $fieldname = "custom[$id]";
		  if ($type == (2)){ //checkbox
		   echo "<tr> <td colspan=2><table><tr><td valign=top>";
		   echo " <input type=\"checkbox\" name=\"";
			echo $fieldname;
			echo "\" value = 1 ";
			if ($value == 1) { echo "checked"; }
			echo " >";
			echo "</td><td class=text>".$name."</td></tr></table></td></tr>";
			
		  }
		
		  else{
		 	echo  "<tr> <td>";
          	echo $name; 
          	echo "&nbsp;&nbsp;&nbsp;</td><td width=\"80%\">";}
			
		if ($type == ("1")){ 
            echo " <input type=\"text\" style=\"width: 262px;\" name=\"";
			echo $fieldname;
			echo "\" size=\"40\" value=\"".$value."\">"; }
		
		if ($type == ("3")){ 
            echo " <textarea name=\"";
			echo $fieldname;
			echo "\" wrap=\"VIRTUAL\" cols=\"38\" rows=\"5\"  style=\"width: 400px;\">".$value."</textarea>"; }
			echo "</td> </tr>"; } 
	
  
$calledrcd__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$calledrcd__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
$enteredby__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$enteredby__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
$modifiedby__MMColParam = "1";
if (isset($HTTP_GET_VARS["id"]))
  {$modifiedby__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $calledrcd=$dbcon->Execute("SELECT * FROM contacts2 WHERE id = " . ($calledrcd__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $calledrcd_numRows=0;
   $calledrcd__totalRows=$calledrcd->RecordCount();
?><?php
   $allclass=$dbcon->Execute("SELECT id, title FROM contacts_class ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $allclass_numRows=0;
   $allclass__totalRows=$allclass->RecordCount();
?><?php
   $enteredby=$dbcon->Execute("SELECT users.name  FROM contacts2 Inner Join users on contacts2.enteredby=users.id  WHERE contacts2.id = " . ($enteredby__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $enteredby_numRows=0;
   $enteredby__totalRows=$enteredby->RecordCount();
?><?php
   $modifiedby=$dbcon->Execute("SELECT users.name  FROM contacts2 Inner Join users on contacts2.modifiedby=users.id  WHERE contacts2.id = " . ($modifiedby__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $modifiedby_numRows=0;
   $modifiedby__totalRows=$modifiedby->RecordCount();
    $camps=$dbcon->Execute("SELECT * from contacts_campaign order by fieldorder asc") or DIE($dbcon->ErrorMsg());

   $region=$dbcon->Execute("SELECT id, title FROM region ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $region_numRows=0;
   $region__totalRows=$region->RecordCount();
      $source=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $source_numRows=0;
   $source__totalRows=$source->RecordCount();
    $allusers=$dbcon->Execute("SELECT id, name FROM users ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $allusers_numRows=0;
   $allusers__totalRows=$allusers->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $camps_numRows = $camps_numRows + $Repeat1__numRows;
?><?php include ("header.php");
?>


<script type="text/javascript">
//<![CDATA[

function change(which) {
    document.getElementById('main').style.display = 'none';
<?php while  (!$camps->EOF) {  
echo " document.getElementById('div".$camps->Fields("id")."').style.display = 'none'; ";
 $camps->MoveNext();}
 $camps->MoveFirst();
 ?>
    document.getElementById(which).style.display = 'block';
    }

//]]>
</script>

<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
<a href="#" onclick="change('main');" >Contact Info</a>&nbsp;&nbsp; 
<?php while  (!$camps->EOF) {  
echo "<a href=\"#\" onclick=\"change('div".$camps->Fields("id")."');\" >".$camps->Fields("name")." </a>&nbsp;&nbsp; ";
 $camps->MoveNext();}
 $camps->MoveFirst();
 ?>
 
<div id="main" class="main" style="display: block;">
  <table width="95%" border="0" cellspacing="0" cellpadding="0" class="toplinks">
    <tr class="toplinks"> 
      <td>Personal Information</td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="95%" border="0" cellspacing="3" cellpadding="0">
    <tr class="title" > 
      <td class="title">Title</td>
      <td class="title">First Name</td>
      <td class="title">Last Name</td>
    </tr>
    <tr> 
      <td> 
        <input type="text" name="Suffix" size="10" value="<?php echo $calledrcd->Fields("Suffix")?>">
      </td>
      <td> 
        <input type="text" name="FirstName" size="40" value="<?php echo $calledrcd->Fields("FirstName")?>">
      </td>
      <td> 
        <input type="text" name="LastName" size="40" value="<?php echo $calledrcd->Fields("LastName")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">Type </td>
      <td class="title">Organization </td>
      <td class="title">Position</td>
    </tr>
    <tr> 
      <td> 
        <select name="classid">
		
	<option value="">none </option>
          <?php
  if ($allclass__totalRows > 0){
    $allclass__index=0;
    $allclass->MoveFirst();
    WHILE ($allclass__index < $allclass__totalRows){
?>
          <OPTION VALUE="<?php echo  $allclass->Fields("id")?>"<?php if ($allclass->Fields("id")==$calledrcd->Fields("classid")) echo "SELECTED";?>> 
          <?php echo  $allclass->Fields("title");?>
          </OPTION>
          <?php
      $allclass->MoveNext();
      $allclass__index++;
    }
    $allclass__index=0;  
    $allclass->MoveFirst();
  }
?>
        </select>
      </td>
      <td> 
        <input type="text" name="Company" size="40" value="<?php echo $calledrcd->Fields("Company")?>">
      </td>
      <td> 
        <input type="text" name="JobTitle" size="40" value="<?php echo $calledrcd->Fields("JobTitle")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">Region</td>
      <td class="title">Work Phone</td>
      <td class="title">Home Phone</td>
    </tr>
    <tr> 
      <td> 
        <select name="regionid">
		<option value="">none </option>
          <?php
  if ($region__totalRows > 0){
    $region__index=0;
    $region->MoveFirst();
    WHILE ($region__index < $region__totalRows){
?>
          <option value="<?php echo  $region->Fields("id")?>"<?php if ($region->Fields("id")==$calledrcd->Fields("regionid")) echo "SELECTED";?>> 
          <?php echo  $region->Fields("title");?>
          </option>
          <?php
      $region->MoveNext();
      $region__index++;
    }
    $region__index=0;  
    $region->MoveFirst();
  }
?>
        </select>
      </td>
      <td> 
        <input type="text" name="BusinessPhone" size="40" value="<?php echo $calledrcd->Fields("BusinessPhone")?>">
      </td>
      <td> 
        <input type="text" name="HomePhone" size="40" value="<?php echo $calledrcd->Fields("HomePhone")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td>Entered By</td>
      <td class="title">Mobile Phone</td>
      <td class="title">Fax Phone</td>
    </tr>
	
    <tr> 
      <td> 
        <select name="enteredby">
		
                <?php
  if ($allusers__totalRows > 0){
    $allusers__index=0;
    $allusers->MoveFirst();
    WHILE ($allusers__index < $allusers__totalRows){
?>
                <OPTION VALUE="<?php echo $allusers->Fields("id")?>"<?php if ($allusers->Fields("id")==$calledrcd->Fields("enteredby")) echo "SELECTED";?>>
                <?php echo $allusers->Fields("name");?>
                </OPTION>
                <?php
      $allusers->MoveNext();
      $allusers__index++;
    }
    $allusers__index=0;  
    $allusers->MoveFirst();
  }
?>  </select>
      </td>
      <td> 
        <input type="text" name="MobilePhone" size="40" value="<?php echo $calledrcd->Fields("MobilePhone")?>">
      </td>
      <td> 
        <input type="text" name="BUsinessFax" size="40" value="<?php echo $calledrcd->Fields("BusinessFax")?>">
      </td>
    </tr>
	<tr class="title"> 
      <td>&nbsp;</td>
      <td class="title">&nbsp;</td>
      <td class="title">Campus</td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td>&nbsp;</td>
      <td> <input name="campus" type="text" id="campus" value="<?php echo $calledrcd->Fields("campus")?>" size="40"> 
      </td>
    </tr>
    <tr class="title"> 
      <td colspan="2" class="title">Web Page</td>
      <td class="title">E-Mail</td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <input type="text" name="WebPage" size="50" value="<?php echo $calledrcd->Fields("WebPage")?>">
      </td>
      <td> 
        <input type="text" name="EmailAddress" size="40" value="<?php echo $calledrcd->Fields("EmailAddress")?>">
      </td>
    </tr>
		<tr class="title"> 
      <td colspan="2" class="title">Contact Source</td>
      <td class="title">E-Mail 2</td>
    </tr>
    <tr> 
      <td colspan="2"> 
        <select name="source">
          <option value="1">none </option>
          <?php
  if ($source__totalRows > 0){
    $source__index=0;
    $source->MoveFirst();
    WHILE ($source__index < $source__totalRows){
?>
          <option value="<?php echo  $source->Fields("id")?>"<?php if ($source->Fields("id")==$calledrcd->Fields("source")) echo "SELECTED";?>> 
          <?php echo  $source->Fields("title");?> </option>
          <?php
      $source->MoveNext();
      $source__index++;
    }
    $source__index=0;  
    $source->MoveFirst();
  }
?>
        </select> <a href="admin_source.php"><font size="1">add source</font></a></td>
      <td> 
        <input type="text" name="Email2Address" size="40" value="<?php echo $calledrcd->Fields("Email2Address")?>">
      </td>
    </tr>
    <tr valign="top" class="title"> 
      <td colspan="3" class="title">Notes </td>
    </tr>
    <tr valign="top"> 
      <td colspan="3"> 
        <table align="right"><tr><td><input type="submit" name="Submit" value="Update"></td></tr></table><textarea name="notes" cols="65" wrap="VIRTUAL" rows="4"><?php echo $calledrcd->Fields("notes")?></textarea>
      </td>
    </tr>


    <tr class="toplinks"> 
      <td class="toplinks" colspan="3">Work Address</td>
    </tr>
    <tr class="title"> 
      <td class="title">Mailing<br>
        Address</td>
      <td class="title">Address</td>
      <td class="title">Address2</td>
    </tr>
    <tr> 
      <td> 
        <input type="radio" name="useaddress" value="business" <?php If (($calledrcd->Fields("useaddress")) == "business") { echo "CHECKED";} ?>>
      </td>
      <td> 
        <input type="text" name="BusinessStreet" size="40" value="<?php echo $calledrcd->Fields("BusinessStreet")?>">
      </td>
      <td> 
        <input type="text" name="BusinessStreet2" size="40" value="<?php echo $calledrcd->Fields("BusinessStreet2")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">Region</td>
      <td class="title">City</td>
      <td class="title">State </td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> 
        <input type="text" name="BusinessCity" size="40" value="<?php echo $calledrcd->Fields("BusinessCity")?>">
      </td>
      <td> 
        <input type="text" name="BusinessState" size="10
	  " value="<?php echo $calledrcd->Fields("BusinessState")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">&nbsp;</td>
      <td class="title">Zip</td>
      <td class="title">Country</td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> 
        <input type="text" name="BusinessPostalCode" size="10
	  " value="<?php echo $calledrcd->Fields("BusinessPostalCode")?>">
      </td>
      <td> 
        <input type="text" name="BusinessCountry" size="40" value="<?php echo $calledrcd->Fields("BusinessCountry")?>">
      </td>
    </tr>
    <tr class="toplinks"> 
      <td class="toplinks" colspan="3">Home Address</td>
    </tr>
    <tr class="title"> 
      <td class="title">Mailing<br>
        Address</td>
      <td class="title">Address</td>
      <td class="title">Address2</td>
    </tr>
    <tr> 
      <td> 
        <input type="radio" name="useaddress" value="home" <?php If (($calledrcd->Fields("useaddress")) == "home") { echo "CHECKED";} ?>>
      </td>
      <td> 
        <input type="text" name="HomeStreet" size="40" value="<?php echo $calledrcd->Fields("HomeStreet")?>">
      </td>
      <td> 
        <input type="text" name="HomeStreet2" size="40" value="<?php echo $calledrcd->Fields("HomeStreet2")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">&nbsp;</td>
      <td class="title">City</td>
      <td class="title">State </td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> 
        <input type="text" name="HomeCity" size="40" value="<?php echo $calledrcd->Fields("HomeCity")?>">
      </td>
      <td> 
        <input type="text" name="HomeState" size="10
	  " value="<?php echo $calledrcd->Fields("HomeState")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">&nbsp;</td>
      <td class="title">Zip</td>
      <td class="title">Country</td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> 
        <input type="text" name="HomePostalCode" size="10
	  " value="<?php echo $calledrcd->Fields("HomePostalCode")?>">
      </td>
      <td> 
        <input type="text" name="HomeCountry" size="40" value="<?php echo $calledrcd->Fields("HomeCountry")?>">
      </td>
    </tr>
	  
  </table>
  </div>
  
 
    <?php
	   while  (!$camps->EOF) {  
	   echo "<div id=\"div".$camps->Fields("id")."\" class=\"div".$camps->Fields("id")."\" style=\"display: none;\">";
	  
	   echo '<table width="95%" border="0" cellspacing="0" cellpadding="0" >';
	   echo "<tr class=title><td class=title colspan=3>".$camps->Fields("name")."</td></tr>";
	  
	$getf=$dbcon->Execute("Select * from contacts_fields f where camid = ".$camps->Fields("id")." order by fieldorder asc") or DIE($dbcon->ErrorMsg());
	
	while  (!$getf->EOF) {  
	$value ='';
	$relv=$dbcon->Execute("Select value from contacts_rel  where fieldid = ".$getf->Fields("id")." and perid = ".$_GET[id]."") or DIE($dbcon->ErrorMsg());
	$value= $relv->Fields("value");
	  customfield($getf->Fields("id"),$getf->Fields("type"),$getf->Fields("name"),$value);
	
	   $getf->MoveNext();}
	   echo "</table></div>";
	    $camps->MoveNext();}
		
?>
  
  <p> 
    <input type="submit" name="Submit" value="Update">

  </p>
<input type="hidden" name="modifiedby" value="<?php echo $ID ;?>">
  <input type="hidden" name="MM_update" value="true">

  <input type="hidden" name="MM_recordId" value="<?php echo $calledrcd->Fields("id") ?>">
</form>    <form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="hidden" name="MM_delete" value="true">
	 <input type="hidden" name="MM_recordId" value="<?php echo $calledrcd->Fields("id") ?>">
	<input type="submit" name="Submit2" value="Delete"></form>


<?php include ("footer.php");?>

