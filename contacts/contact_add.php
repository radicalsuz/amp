<?php
     

  require_once("Connections/freedomrising.php");  

?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
?><?php
 ######### EMAIL INSERT RECORD  ################################## 
 $lists=$dbcon->Execute("SELECT * FROM lists where publish=1 ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
 
// *** Insert Record: set Variables
if (isset($MM_insert) && (isset($_POST[EmailAddress])) ){
 $emailck=$dbcon->Execute("SELECT id FROM email where email ='$_POST[EmailAddress]'") or DIE($dbcon->ErrorMsg());
if ( !$emailck->Fields("id") ) {
   $MM_editTable  = "email";
   $MM_fieldsStr = "EmailAddress|value|LastName|value|FirstName|value|Company|html|value";
   $MM_columnsStr = "email|',none,''|lastname|',none,''|firstname|',none,''|organization|',none,''|html|none,1,0";

  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  
  
 $newrec=$dbcon->Execute("SELECT id FROM email ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$recid=$newrec->Fields("id");}
else {$recid=$emailck->Fields("id");}

while  (!$lists->EOF)
   { 
if (isset($_POST[$lists->Fields("id")]))  {
$listid = $lists->Fields("id"); 
$subck=$dbcon->Execute("SELECT id FROM  subscription where userid =$recid and listid=$listid")or DIE($dbcon->ErrorMsg());
if ( $emailck->Fields("id") == NULL) {
 $MM_editTable  = "subscription";
  $MM_fieldsStr = "recid|value|listid|value";
   $MM_columnsStr = "userid|none,none,NULL|listid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	}
	}
	
	
  $lists->MoveNext();
  }

	
   }// end insert
   ########################################################################



  // *** Insert Record: set variables
  
  if (isset($MM_insert)) {
  
//    $MM_editConnection = $MM_freedomrising_STRING;
    $MM_editTable  = "contacts2";
   // $MM_editRedirectUrl = "allcontacts1.php";
    $MM_fieldsStr = "Suffix|value|FirstName|value|LastName|value|Company|value|JobTitle|value|BusinessPhone|value|HomePhone|value|MobilePhone|value|BUsinessFax|value|EmailAddress|value|BusinessStreet|value|BusinessStreet2|value|BusinessCity|value|BusinessState|value|BusinessPostalCode|value|BusinessCountry|value|HomeStreet|value|HomeStreet2|value|HomeCity|value|HomeState|value|HomePostalCode|value|HomeCountry|value|classid|value|regionid|value|notes|value|WebPage|value|useaddress|value|enteredby|value|source|value|Email2Address|value|campus|value";
    $MM_columnsStr = "Suffix|',none,''|FirstName|',none,''|LastName|',none,''|Company|',none,''|JobTitle|',none,''|BusinessPhone|',none,''|HomePhone|',none,''|MobilePhone|',none,''|BusinessFax|',none,''|EmailAddress|',none,''|BusinessStreet|',none,''|BusinessStreet2|',none,''|BusinessCity|',none,''|BusinessState|',none,''|BusinessPostalCode|',none,''|BusinessCountry|',none,''|HomeStreet|',none,''|HomeStreet2|',none,''|HomeCity|',none,''|HomeState|',none,''|HomePostalCode|',none,''|HomeCountry|',none,''|classid|',none,''|regionid|',none,''|notes|',none,''|WebPage|',none,''|useaddress|',none,''|enteredby|',none,''|source|',none,''|Email2Address|',none,''|campus|',none,''";
  
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	$getnext=$dbcon->Execute("select id from contacts2 order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	foreach ($custom as $k=>$v) {
   $addit=$dbcon->Execute( "Insert into contacts_rel (fieldid,perid,value) VALUES ('".$k."','".$getnext->Fields("id")."','".$v."')") or DIE($dbcon->ErrorMsg());
   				}
				header("Location: allcontacts1.php");
	
	}
	
?><?php
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

$calledrcd__MMColParam = "4000000000";
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
     $camps=$dbcon->Execute("SELECT * from contacts_campaign order by fieldorder asc") or DIE($dbcon->ErrorMsg());
?><?php
   $modifiedby=$dbcon->Execute("SELECT users.name  FROM contacts2 Inner Join users on contacts2.modifiedby=users.id  WHERE contacts2.id = " . ($modifiedby__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $modifiedby_numRows=0;
   $modifiedby__totalRows=$modifiedby->RecordCount();
    $source=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $source_numRows=0;
   $source__totalRows=$source->RecordCount();

   $region=$dbcon->Execute("SELECT id, title FROM region ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $region_numRows=0;
   $region__totalRows=$region->RecordCount();
   
    $allusers=$dbcon->Execute("SELECT id, name FROM users ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $allusers_numRows=0;
   $allusers__totalRows=$allusers->RecordCount();
?>
<?php include("header.php"); ?>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
  <table width="95%" border="0" cellspacing="0" cellpadding="0" class="toplinks">
    <tr class="toplinks"> 
      <td>Name</td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <table width="95%" border="0" cellspacing="3" cellpadding="0">
    
    <!--DWLayoutTable-->
    <tr class="title" > 
      <td class="title">Title</td>
      <td class="title">First Name</td>
      <td class="title">Last Name</td>
    </tr>
    <tr> 
      <td> <input type="text" name="Suffix" size="10" value="<?php echo $calledrcd->Fields("Suffix")?>"> 
      </td>
      <td> <input type="text" name="FirstName" size="40" value="<?php echo $calledrcd->Fields("FirstName")?>"> 
      </td>
      <td> <input type="text" name="LastName" size="40" value="<?php echo $calledrcd->Fields("LastName")?>"> 
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">type </td>
      <td class="title">Organization </td>
      <td class="title">Position</td>
    </tr>
    <tr> 
      <td> <select name="classid">
          <option value="1">none </option>
          <?php
  if ($allclass__totalRows > 0){
    $allclass__index=0;
    $allclass->MoveFirst();
    WHILE ($allclass__index < $allclass__totalRows){
?>
          <OPTION VALUE="<?php echo  $allclass->Fields("id")?>"<?php if ($allclass->Fields("id")==$calledrcd->Fields("classid")) echo "SELECTED";?>> 
          <?php echo  $allclass->Fields("title");?> </OPTION>
          <?php
      $allclass->MoveNext();
      $allclass__index++;
    }
    $allclass__index=0;  
    $allclass->MoveFirst();
  }
?>
        </select> </td>
      <td> <input type="text" name="Company" size="40" value="<?php echo $calledrcd->Fields("Company")?>">
      </td>
      <td> <input type="text" name="JobTitle" size="40" value="<?php echo $calledrcd->Fields("JobTitle")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">Region</td>
      <td class="title">Work Phone</td>
      <td class="title">Home Phone</td>
    </tr>
    <tr> 
      <td> <select name="regionid">
          <option value="1">none </option>
          <?php
  if ($region__totalRows > 0){
    $region__index=0;
    $region->MoveFirst();
    WHILE ($region__index < $region__totalRows){
?>
          <option value="<?php echo  $region->Fields("id")?>"> 
          <?php echo  $region->Fields("title");?> </option>
          <?php
      $region->MoveNext();
      $region__index++;
    }
    $region__index=0;  
    $region->MoveFirst();
  }
?>
        </select> </td>
      <td> <input type="text" name="BusinessPhone" size="40" value="<?php echo $calledrcd->Fields("BusinessPhone")?>">
      </td>
      <td> <input type="text" name="HomePhone" size="40" value="<?php echo $calledrcd->Fields("HomePhone")?>">
      </td>
    </tr>
    <tr class="title"> 
      <td>Entered By</td>
      <td class="title">Mobile Phone</td>
      <td class="title">Fax Phone</td>
    </tr>
    <tr> 
      <td> <input name="enteredby" type="hidden" value="<?php echo $ID ;?>"> 
      </td>
      <td> <input type="text" name="MobilePhone" size="40" value="<?php echo $calledrcd->Fields("MobilePhone")?>"> 
      </td>
      <td> <input type="text" name="BUsinessFax" size="40" value="<?php echo $calledrcd->Fields("BusinessFax")?>"> 
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
      <td> <input name="campus" type="text" id="Campus" value="<?php echo $calledrcd->Fields("campus")?>" size="40"> 
      </td>
    </tr>
    <tr class="title"> 
      <td colspan="2" class="title">Web Page</td>
      <td class="title">E-Mail</td>
    </tr>
    <tr> 
      <td colspan="2"> <input type="text" name="WebPage" size="50"> </td>
      <td> <input type="text" name="EmailAddress" size="40" value="<?php echo $calledrcd->Fields("EmailAddress")?>"> 
      </td>
    </tr>
    <tr class="title"> 
      <td colspan="2" class="title">Contact Source</td>
      <td class="title">E-Mail 2</td>
    </tr>
    <tr> 
      <td colspan="2"><select name="source">
          <option value="1">none </option>
          <?php
  if ($source__totalRows > 0){
    $source__index=0;
    $source->MoveFirst();
    WHILE ($source__index < $source__totalRows){
?>
          <option value="<?php echo  $source->Fields("id")?>"> 
          <?php echo  $source->Fields("title");?> </option>
          <?php
      $source->MoveNext();
      $source__index++;
    }
    $source__index=0;  
    $source->MoveFirst();
  }
?>
        </select>&nbsp;&nbsp;<a href="admin_source.php"><font size="1">add source</font></a>
      </td>
      <td> <input type="text" name="Email2Address" size="40" value="<?php echo $calledrcd->Fields("EmailAddress")?>"> 
      </td>
    </tr>
    <tr valign="top" class="title"> 
      <td colspan="3" class="title">Notes </td>
    </tr>
    <tr valign="top"> 
      <td colspan="3"> <textarea name="notes" cols="65" wrap="VIRTUAL" rows="4"></textarea>
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
      <td> <input type="radio" name="useaddress" value="business" <?php If (($calledrcd->Fields("useaddress")) == "business") { echo "CHECKED";} ?>> 
      </td>
      <td> <input type="text" name="BusinessStreet" size="40" value="<?php echo $calledrcd->Fields("BusinessStreet")?>"> 
      </td>
      <td> <input type="text" name="BusinessStreet2" size="40" value="<?php echo $calledrcd->Fields("BusinessStreet2")?>"> 
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">Region</td>
      <td class="title">City</td>
      <td class="title">State </td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> <input type="text" name="BusinessCity" size="40" value="<?php echo $calledrcd->Fields("BusinessCity")?>"> 
      </td>
      <td> <input type="text" name="BusinessState" size="10
	  " value="<?php echo $calledrcd->Fields("BusinessState")?>"> </td>
    </tr>
    <tr class="title"> 
      <td class="title">&nbsp;</td>
      <td class="title">Zip</td>
      <td class="title">Country</td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> <input type="text" name="BusinessPostalCode" size="10
	  " value="<?php echo $calledrcd->Fields("BusinessPostalCode")?>"> </td>
      <td> <select name="BusinessCountry">
          <option value="">Select A Country</option>
          <option value="USA">United States</option>
          <option value="Canada">Canada</option>
          <option value="England">United Kingdom</option>
          <option value="AFG" >Afghanistan</option>
          <option value="ALB" >Albania</option>
          <option value="DZA" >Algeria</option>
          <option value="ASM" >American Samoa</option>
          <option value="AND" >Andorra</option>
          <option value="AGO" >Angola</option>
          <option value="AIA" >Anguilla</option>
          <option value="ATG" >Antigua and Barbuda</option>
          <option value="ARG" >Argentina</option>
          <option value="ARM" >Armenia</option>
          <option value="ABW" >Aruba</option>
          <option value="AUS" >Australia</option>
          <option value="AUT" >Austria</option>
          <option value="AZE" >Azerbaijan</option>
          <option value="BHS" >Bahamas</option>
          <option value="BHR" >Bahrain</option>
          <option value="BGD" >Bangladesh</option>
          <option value="BRB" >Barbados</option>
          <option value="BLR" >Belarus</option>
          <option value="BEL" >Belgium</option>
          <option value="BLZ" >Belize</option>
          <option value="BEN" >Benin</option>
          <option value="BMU" >Bermuda</option>
          <option value="BTN" >Bhutan</option>
          <option value="BOL" >Bolivia</option>
          <option value="BIH" >Bosnia and Herzegovina</option>
          <option value="BWA" >Botswana</option>
          <option value="BRA" >Brazil</option>
          <option value="VGB" >British Virgin Islands</option>
          <option value="BRN" >Brunei Darussalam</option>
          <option value="BGR" >Bulgaria</option>
          <option value="BFA" >Burkina Faso</option>
          <option value="BDI" >Burundi</option>
          <option value="KHM" >Cambodia</option>
          <option value="CMR" >Cameroon</option>
          <option value="CAN" >Canada</option>
          <option value="CPV" >Cape Verde</option>
          <option value="CYM" >Cayman Islands</option>
          <option value="CAF" >Central African Republic</option>
          <option value="TCD" >Chad</option>
          <option value="CHL" >Chile</option>
          <option value="CHN" >China</option>
          <option value="COL" >Colombia</option>
          <option value="COM" >Comoros</option>
          <option value="COG" >Congo</option>
          <option value="COK" >Cook Islands</option>
          <option value="CRI" >Costa Rica</option>
          <option value="CIV" >Cote d\'Ivoire</option>
          <option value="HRV" >Croatia</option>
          <option value="CUB" >Cuba</option>
          <option value="CYP" >Cyprus</option>
          <option value="CZE" >Czech Republic</option>
          <option value="PRK" >Democratic People\'s Republic of Korea</option>
          <option value="COD" >Democratic Republic of the Congo</option>
          <option value="DNK" >Denmark</option>
          <option value="DJI" >Djibouti</option>
          <option value="DMA" >Dominica</option>
          <option value="DOM" >Dominican Republic</option>
          <option value="TMP" >East Timor</option>
          <option value="ECU" >Ecuador</option>
          <option value="EGY" >Egypt</option>
          <option value="SLV" >El Salvador</option>
          <option value="GNQ" >Equatorial Guinea</option>
          <option value="ERI" >Eritrea</option>
          <option value="EST" >Estonia</option>
          <option value="ETH" >Ethiopia</option>
          <option value="FRO" >Faeroe Islands</option>
          <option value="FLK" >Falkland Islands (Malvinas)</option>
          <option value="FJI" >Fiji</option>
          <option value="FIN" >Finland</option>
          <option value="FRA" >France</option>
          <option value="GUF" >French Guiana</option>
          <option value="PYF" >French Polynesia</option>
          <option value="GAB" >Gabon</option>
          <option value="GMB" >Gambia</option>
          <option value="GEO" >Georgia</option>
          <option value="DEU" >Germany</option>
          <option value="GHA" >Ghana</option>
          <option value="GIB" >Gibraltar</option>
          <option value="GRC" >Greece</option>
          <option value="GRL" >Greenland</option>
          <option value="GRD" >Grenada</option>
          <option value="GLP" >Guadeloupe</option>
          <option value="GUM" >Guam</option>
          <option value="GTM" >Guatemala</option>
          <option value="GIN" >Guinea</option>
          <option value="GNB" >Guinea-Bissau</option>
          <option value="GUY" >Guyana</option>
          <option value="HTI" >Haiti</option>
          <option value="VAT" >Holy See</option>
          <option value="HND" >Honduras</option>
          <option value="HKG" >Hong Kong Special Administrative</option>
          <option value="HUN" >Hungary</option>
          <option value="ISL" >Iceland</option>
          <option value="IND" >India</option>
          <option value="IDN" >Indonesia</option>
          <option value="IRN" >Iran (Islamic Republic of)</option>
          <option value="IRQ" >Iraq</option>
          <option value="IRL" >Ireland</option>
          <option value="ISR" >Israel</option>
          <option value="ITA" >Italy</option>
          <option value="JAM" >Jamaica</option>
          <option value="JPN" >Japan</option>
          <option value="JOR" >Jordan</option>
          <option value="KAZ" >Kazakhstan</option>
          <option value="KEN" >Kenya</option>
          <option value="KIR" >Kiribati</option>
          <option value="KWT" >Kuwait</option>
          <option value="KGZ" >Kyrgyzstan</option>
          <option value="LAO" >Lao People\'s Democratic Republic</option>
          <option value="LVA" >Latvia</option>
          <option value="LBN" >Lebanon</option>
          <option value="LSO" >Lesotho</option>
          <option value="LBR" >Liberia</option>
          <option value="LBY" >Libyan Arab Jamahiriya</option>
          <option value="LIE" >Liechtenstein</option>
          <option value="LTU" >Lithuania</option>
          <option value="LUX" >Luxembourg</option>
          <option value="MAC" >Macao Special Administrative Region of China</option>
          <option value="MDG" >Madagascar</option>
          <option value="MWI" >Malawi</option>
          <option value="MYS" >Malaysia</option>
          <option value="MDV" >Maldives</option>
          <option value="MLI" >Mali</option>
          <option value="MLT" >Malta</option>
          <option value="MHL" >Marshall Islands</option>
          <option value="MTQ" >Martinique</option>
          <option value="MRT" >Mauritania</option>
          <option value="MUS" >Mauritius</option>
          <option value="MEX" >Mexico</option>
          <option value="FSM" >Micronesia Federated States of,</option>
          <option value="MCO" >Monaco</option>
          <option value="MNG" >Mongolia</option>
          <option value="MSR" >Montserrat</option>
          <option value="MAR" >Morocco</option>
          <option value="MOZ" >Mozambique</option>
          <option value="MMR" >Myanmar</option>
          <option value="NAM" >Namibia</option>
          <option value="NRU" >Nauru</option>
          <option value="NPL" >Nepal</option>
          <option value="NLD" >Netherlands</option>
          <option value="ANT" >Netherlands Antilles</option>
          <option value="NCL" >New Caledonia</option>
          <option value="NZL" >New Zealand</option>
          <option value="NIC" >Nicaragua</option>
          <option value="NER" >Niger</option>
          <option value="NGA" >Nigeria</option>
          <option value="NIU" >Niue</option>
          <option value="NFK" >Norfolk Island</option>
          <option value="MNP" >Northern Mariana Islands</option>
          <option value="NOR" >Norway</option>
          <option value="PSE" >Occupied Palestinian Territory</option>
          <option value="OMN" >Oman</option>
          <option value="PAK" >Pakistan</option>
          <option value="PLW" >Palau</option>
          <option value="PAN" >Panama</option>
          <option value="PNG" >Papua New Guinea</option>
          <option value="PRY" >Paraguay</option>
          <option value="PER" >Peru</option>
          <option value="PHL" >Philippines</option>
          <option value="PCN" >Pitcairn</option>
          <option value="POL" >Poland</option>
          <option value="PRT" >Portugal</option>
          <option value="PRI" >Puerto Rico</option>
          <option value="QAT" >Qatar</option>
          <option value="KOR" >Republic of Korea</option>
          <option value="MDA" >Republic of Moldova</option>
          <option value="REU" >Réunion</option>
          <option value="ROM" >Romania</option>
          <option value="RUS" >Russian Federation</option>
          <option value="RWA" >Rwanda</option>
          <option value="SHN" >Saint Helena</option>
          <option value="KNA" >Saint Kitts and Nevis</option>
          <option value="LCA" >Saint Lucia</option>
          <option value="SPM" >Saint Pierre and Miquelon</option>
          <option value="VCT" >Saint Vincent and the Grenadines</option>
          <option value="WSM" >Samoa</option>
          <option value="SMR" >San Marino</option>
          <option value="STP" >Sao Tome and Principe</option>
          <option value="SAU" >Saudi Arabia</option>
          <option value="SEN" >Senegal</option>
          <option value="SYC" >Seychelles</option>
          <option value="SLE" >Sierra Leone</option>
          <option value="SGP" >Singapore</option>
          <option value="SVK" >Slovakia</option>
          <option value="SVN" >Slovenia</option>
          <option value="SLB" >Solomon Islands</option>
          <option value="SOM" >Somalia</option>
          <option value="ZAF" >South Africa</option>
          <option value="ESP" >Spain</option>
          <option value="LKA" >Sri Lanka</option>
          <option value="SDN" >Sudan</option>
          <option value="SUR" >Suriname</option>
          <option value="SJM" >Svalbard and Jan Mayen Islands</option>
          <option value="SWZ" >Swaziland</option>
          <option value="SWE" >Sweden</option>
          <option value="CHE" >Switzerland</option>
          <option value="SYR" >Syrian Arab Republic</option>
          <option value="TWN" >Taiwan Province of China</option>
          <option value="TJK" >Tajikistan</option>
          <option value="THA" >Thailand</option>
          <option value="MKD" >The former Yugoslav Republic of Macedonia</option>
          <option value="TGO" >Togo</option>
          <option value="TKL" >Tokelau</option>
          <option value="TON" >Tonga</option>
          <option value="TTO" >Trinidad and Tobago</option>
          <option value="TUN" >Tunisia</option>
          <option value="TUR" >Turkey</option>
          <option value="TKM" >Turkmenistan</option>
          <option value="TCA" >Turks and Caicos Islands</option>
          <option value="TUV" >Tuvalu</option>
          <option value="UGA" >Uganda</option>
          <option value="UKR" >Ukraine</option>
          <option value="ARE" >United Arab Emirates</option>
          <option value="GBR" >United Kingdom</option>
          <option value="TZA" >United Republic of Tanzania</option>
          <option value="USA" >United States</option>
          <option value="VIR" >United States Virgin Islands</option>
          <option value="URY" >Uruguay</option>
          <option value="UZB" >Uzbekistan</option>
          <option value="VUT" >Vanuatu</option>
          <option value="VEN" >Venezuela</option>
          <option value="VNM" >Viet Nam</option>
          <option value="WLF" >Wallis and Futuna Islands</option>
          <option value="ESH" >Western Sahara</option>
          <option value="YEM" >Yemen</option>
          <option value="YUG" >Yugoslavia</option>
          <option value="ZMB" >Zambia</option>
          <option value="ZWE" >Zimbabwe</option>
        </select> </td>
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
      <td> <input type="radio" name="useaddress" value="home" <?php If (($calledrcd->Fields("useaddress")) == "home") { echo "CHECKED";} ?>> 
      </td>
      <td> <input type="text" name="HomeStreet" size="40" value="<?php echo $calledrcd->Fields("HomeStreet")?>"> 
      </td>
      <td> <input type="text" name="HomeStreet2" size="40" value="<?php echo $calledrcd->Fields("HomeStreet2")?>"> 
      </td>
    </tr>
    <tr class="title"> 
      <td class="title">&nbsp;</td>
      <td class="title">City</td>
      <td class="title">State </td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> <input type="text" name="HomeCity" size="40" value="<?php echo $calledrcd->Fields("HomeCity")?>"> 
      </td>
      <td> <input type="text" name="HomeState" size="10
	  " value="<?php echo $calledrcd->Fields("HomeState")?>"> </td>
    </tr>
    <tr class="title"> 
      <td class="title">&nbsp;</td>
      <td class="title">Zip</td>
      <td class="title">Country</td>
    </tr>
    <tr> 
      <td>&nbsp; </td>
      <td> <input type="text" name="HomePostalCode" size="10
	  " value="<?php echo $calledrcd->Fields("HomePostalCode")?>"> </td>
      <td> <select name="HomeCountry">
          <option value="">Select A Country</option>
          <option value="USA">United States</option>
          <option value="Canada">Canada</option>
          <option value="England">United Kingdom</option>
          <option value="AFG" >Afghanistan</option>
          <option value="ALB" >Albania</option>
          <option value="DZA" >Algeria</option>
          <option value="ASM" >American Samoa</option>
          <option value="AND" >Andorra</option>
          <option value="AGO" >Angola</option>
          <option value="AIA" >Anguilla</option>
          <option value="ATG" >Antigua and Barbuda</option>
          <option value="ARG" >Argentina</option>
          <option value="ARM" >Armenia</option>
          <option value="ABW" >Aruba</option>
          <option value="AUS" >Australia</option>
          <option value="AUT" >Austria</option>
          <option value="AZE" >Azerbaijan</option>
          <option value="BHS" >Bahamas</option>
          <option value="BHR" >Bahrain</option>
          <option value="BGD" >Bangladesh</option>
          <option value="BRB" >Barbados</option>
          <option value="BLR" >Belarus</option>
          <option value="BEL" >Belgium</option>
          <option value="BLZ" >Belize</option>
          <option value="BEN" >Benin</option>
          <option value="BMU" >Bermuda</option>
          <option value="BTN" >Bhutan</option>
          <option value="BOL" >Bolivia</option>
          <option value="BIH" >Bosnia and Herzegovina</option>
          <option value="BWA" >Botswana</option>
          <option value="BRA" >Brazil</option>
          <option value="VGB" >British Virgin Islands</option>
          <option value="BRN" >Brunei Darussalam</option>
          <option value="BGR" >Bulgaria</option>
          <option value="BFA" >Burkina Faso</option>
          <option value="BDI" >Burundi</option>
          <option value="KHM" >Cambodia</option>
          <option value="CMR" >Cameroon</option>
          <option value="CAN" >Canada</option>
          <option value="CPV" >Cape Verde</option>
          <option value="CYM" >Cayman Islands</option>
          <option value="CAF" >Central African Republic</option>
          <option value="TCD" >Chad</option>
          <option value="CHL" >Chile</option>
          <option value="CHN" >China</option>
          <option value="COL" >Colombia</option>
          <option value="COM" >Comoros</option>
          <option value="COG" >Congo</option>
          <option value="COK" >Cook Islands</option>
          <option value="CRI" >Costa Rica</option>
          <option value="CIV" >Cote d\'Ivoire</option>
          <option value="HRV" >Croatia</option>
          <option value="CUB" >Cuba</option>
          <option value="CYP" >Cyprus</option>
          <option value="CZE" >Czech Republic</option>
          <option value="PRK" >Democratic People\'s Republic of Korea</option>
          <option value="COD" >Democratic Republic of the Congo</option>
          <option value="DNK" >Denmark</option>
          <option value="DJI" >Djibouti</option>
          <option value="DMA" >Dominica</option>
          <option value="DOM" >Dominican Republic</option>
          <option value="TMP" >East Timor</option>
          <option value="ECU" >Ecuador</option>
          <option value="EGY" >Egypt</option>
          <option value="SLV" >El Salvador</option>
          <option value="GNQ" >Equatorial Guinea</option>
          <option value="ERI" >Eritrea</option>
          <option value="EST" >Estonia</option>
          <option value="ETH" >Ethiopia</option>
          <option value="FRO" >Faeroe Islands</option>
          <option value="FLK" >Falkland Islands (Malvinas)</option>
          <option value="FJI" >Fiji</option>
          <option value="FIN" >Finland</option>
          <option value="FRA" >France</option>
          <option value="GUF" >French Guiana</option>
          <option value="PYF" >French Polynesia</option>
          <option value="GAB" >Gabon</option>
          <option value="GMB" >Gambia</option>
          <option value="GEO" >Georgia</option>
          <option value="DEU" >Germany</option>
          <option value="GHA" >Ghana</option>
          <option value="GIB" >Gibraltar</option>
          <option value="GRC" >Greece</option>
          <option value="GRL" >Greenland</option>
          <option value="GRD" >Grenada</option>
          <option value="GLP" >Guadeloupe</option>
          <option value="GUM" >Guam</option>
          <option value="GTM" >Guatemala</option>
          <option value="GIN" >Guinea</option>
          <option value="GNB" >Guinea-Bissau</option>
          <option value="GUY" >Guyana</option>
          <option value="HTI" >Haiti</option>
          <option value="VAT" >Holy See</option>
          <option value="HND" >Honduras</option>
          <option value="HKG" >Hong Kong Special Administrative</option>
          <option value="HUN" >Hungary</option>
          <option value="ISL" >Iceland</option>
          <option value="IND" >India</option>
          <option value="IDN" >Indonesia</option>
          <option value="IRN" >Iran (Islamic Republic of)</option>
          <option value="IRQ" >Iraq</option>
          <option value="IRL" >Ireland</option>
          <option value="ISR" >Israel</option>
          <option value="ITA" >Italy</option>
          <option value="JAM" >Jamaica</option>
          <option value="JPN" >Japan</option>
          <option value="JOR" >Jordan</option>
          <option value="KAZ" >Kazakhstan</option>
          <option value="KEN" >Kenya</option>
          <option value="KIR" >Kiribati</option>
          <option value="KWT" >Kuwait</option>
          <option value="KGZ" >Kyrgyzstan</option>
          <option value="LAO" >Lao People\'s Democratic Republic</option>
          <option value="LVA" >Latvia</option>
          <option value="LBN" >Lebanon</option>
          <option value="LSO" >Lesotho</option>
          <option value="LBR" >Liberia</option>
          <option value="LBY" >Libyan Arab Jamahiriya</option>
          <option value="LIE" >Liechtenstein</option>
          <option value="LTU" >Lithuania</option>
          <option value="LUX" >Luxembourg</option>
          <option value="MAC" >Macao Special Administrative Region of China</option>
          <option value="MDG" >Madagascar</option>
          <option value="MWI" >Malawi</option>
          <option value="MYS" >Malaysia</option>
          <option value="MDV" >Maldives</option>
          <option value="MLI" >Mali</option>
          <option value="MLT" >Malta</option>
          <option value="MHL" >Marshall Islands</option>
          <option value="MTQ" >Martinique</option>
          <option value="MRT" >Mauritania</option>
          <option value="MUS" >Mauritius</option>
          <option value="MEX" >Mexico</option>
          <option value="FSM" >Micronesia Federated States of,</option>
          <option value="MCO" >Monaco</option>
          <option value="MNG" >Mongolia</option>
          <option value="MSR" >Montserrat</option>
          <option value="MAR" >Morocco</option>
          <option value="MOZ" >Mozambique</option>
          <option value="MMR" >Myanmar</option>
          <option value="NAM" >Namibia</option>
          <option value="NRU" >Nauru</option>
          <option value="NPL" >Nepal</option>
          <option value="NLD" >Netherlands</option>
          <option value="ANT" >Netherlands Antilles</option>
          <option value="NCL" >New Caledonia</option>
          <option value="NZL" >New Zealand</option>
          <option value="NIC" >Nicaragua</option>
          <option value="NER" >Niger</option>
          <option value="NGA" >Nigeria</option>
          <option value="NIU" >Niue</option>
          <option value="NFK" >Norfolk Island</option>
          <option value="MNP" >Northern Mariana Islands</option>
          <option value="NOR" >Norway</option>
          <option value="PSE" >Occupied Palestinian Territory</option>
          <option value="OMN" >Oman</option>
          <option value="PAK" >Pakistan</option>
          <option value="PLW" >Palau</option>
          <option value="PAN" >Panama</option>
          <option value="PNG" >Papua New Guinea</option>
          <option value="PRY" >Paraguay</option>
          <option value="PER" >Peru</option>
          <option value="PHL" >Philippines</option>
          <option value="PCN" >Pitcairn</option>
          <option value="POL" >Poland</option>
          <option value="PRT" >Portugal</option>
          <option value="PRI" >Puerto Rico</option>
          <option value="QAT" >Qatar</option>
          <option value="KOR" >Republic of Korea</option>
          <option value="MDA" >Republic of Moldova</option>
          <option value="REU" >Réunion</option>
          <option value="ROM" >Romania</option>
          <option value="RUS" >Russian Federation</option>
          <option value="RWA" >Rwanda</option>
          <option value="SHN" >Saint Helena</option>
          <option value="KNA" >Saint Kitts and Nevis</option>
          <option value="LCA" >Saint Lucia</option>
          <option value="SPM" >Saint Pierre and Miquelon</option>
          <option value="VCT" >Saint Vincent and the Grenadines</option>
          <option value="WSM" >Samoa</option>
          <option value="SMR" >San Marino</option>
          <option value="STP" >Sao Tome and Principe</option>
          <option value="SAU" >Saudi Arabia</option>
          <option value="SEN" >Senegal</option>
          <option value="SYC" >Seychelles</option>
          <option value="SLE" >Sierra Leone</option>
          <option value="SGP" >Singapore</option>
          <option value="SVK" >Slovakia</option>
          <option value="SVN" >Slovenia</option>
          <option value="SLB" >Solomon Islands</option>
          <option value="SOM" >Somalia</option>
          <option value="ZAF" >South Africa</option>
          <option value="ESP" >Spain</option>
          <option value="LKA" >Sri Lanka</option>
          <option value="SDN" >Sudan</option>
          <option value="SUR" >Suriname</option>
          <option value="SJM" >Svalbard and Jan Mayen Islands</option>
          <option value="SWZ" >Swaziland</option>
          <option value="SWE" >Sweden</option>
          <option value="CHE" >Switzerland</option>
          <option value="SYR" >Syrian Arab Republic</option>
          <option value="TWN" >Taiwan Province of China</option>
          <option value="TJK" >Tajikistan</option>
          <option value="THA" >Thailand</option>
          <option value="MKD" >The former Yugoslav Republic of Macedonia</option>
          <option value="TGO" >Togo</option>
          <option value="TKL" >Tokelau</option>
          <option value="TON" >Tonga</option>
          <option value="TTO" >Trinidad and Tobago</option>
          <option value="TUN" >Tunisia</option>
          <option value="TUR" >Turkey</option>
          <option value="TKM" >Turkmenistan</option>
          <option value="TCA" >Turks and Caicos Islands</option>
          <option value="TUV" >Tuvalu</option>
          <option value="UGA" >Uganda</option>
          <option value="UKR" >Ukraine</option>
          <option value="ARE" >United Arab Emirates</option>
          <option value="GBR" >United Kingdom</option>
          <option value="TZA" >United Republic of Tanzania</option>
          <option value="USA" >United States</option>
          <option value="VIR" >United States Virgin Islands</option>
          <option value="URY" >Uruguay</option>
          <option value="UZB" >Uzbekistan</option>
          <option value="VUT" >Vanuatu</option>
          <option value="VEN" >Venezuela</option>
          <option value="VNM" >Viet Nam</option>
          <option value="WLF" >Wallis and Futuna Islands</option>
          <option value="ESH" >Western Sahara</option>
          <option value="YEM" >Yemen</option>
          <option value="YUG" >Yugoslavia</option>
          <option value="ZMB" >Zambia</option>
          <option value="ZWE" >Zimbabwe</option>
        </select> </td>
    </tr>
	<tr class="toplinks"> 
      <td class="toplinks" colspan="3">Custom Fields </td>
    </tr>
	<tr > 
      <td colspan="3"> <table width="100%">
    <?php
	   while  (!$camps->EOF) {  
	   echo "<tr class=title><td class=title colspan=3>".$camps->Fields("name")."</td></tr>";
	  
	$getf=$dbcon->Execute("Select * from contacts_fields f where camid = ".$camps->Fields("id")." order by fieldorder asc") or DIE($dbcon->ErrorMsg());
	
	while  (!$getf->EOF) {  
	
	  customfield($getf->Fields("id"),$getf->Fields("type"),$getf->Fields("name"),$value);
	
	   $getf->MoveNext();}
	    $camps->MoveNext();}
?></table>
</td>
    </tr>
  </table>
  <table width="95%" border=0 align="left" cellpadding=2 cellspacing=0>  
    <tr valign="baseline" class="toplinks"> 
      <td colspan="2" align="right" class="toplinks"><div align="left"><strong>Add 
          to Email Lists</strong></div></td>
    </tr>
    <?php

	 while (!$lists->EOF)
   { 

//$instance=$dbcon->Execute("SELECT id FROM subscription WHERE userid = ".$Recordset1__MMColParam." and listid= ".($lists->Fields("id"))." LIMIT 1") or DIE($dbcon->ErrorMsg());
	//	$inst=$instance->Fields("id");
			?>
    <tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="20%" colspan="2" class="results"> <div align="left"></div>
              <?php echo $lists->Fields("name"); ?></td>
            <td ><input name="<?php echo $lists->Fields("id"); ?>" type="checkbox"  value="1" > 
              <input name="b<?php echo ($lists->Fields("id")); ?>" type="hidden" value="1"> 
            </td>
          </tr>
        </table></td>
    </tr>
    <?php
  $lists->MoveNext();
}?>
    <tr> 
      <td class="table">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Receive E-Mails in HTML 
        <input type="checkbox" name="html" value="1"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="table"><input type="submit" name="Submit" value="Update"></td>
      <td>&nbsp;</td>
    </tr>
  </table>
    
  <p>&nbsp;</p>
  <p><br>
    <br>
  </p>
    <input type="hidden" name="MM_insert" value="true"></p>
  </form>

  
</body>
</html>

<?php include ("footer.php");?>
