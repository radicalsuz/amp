<?php 
ob_start();
include ("includes/ampclass.php");
$buildform = new BuildForm;

if ($_GET["thank"] == ("1")) { 
	$mod_id = 60;
} 
else {
	$mod_id = 59;
}

$modid=40;

include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");
include_once("AMP/DBfunctions.inc.php"); 
require($base_path."includes/volfunctions.php");
//require($base_path."includes/diaemailfunctions.php");
 
// insert, update, delete
if ((($_POST[MM_update]) && ($_POST[MM_recordId])) or ($_POST[MM_insert]) or (($_POST[MM_delete]) && ($_POST[MM_recordId]))) {

	$uniqueid = randomid() ;
	$MM_recordId = $_POST[MM_recordId] ;
	$MM_editTable  = "userdata";
	$modinid = 8;
	$MM_fieldsStr = "Title|value|Last_Name|value|Suffix|value|First_Name|value|MI|value|Company|value|Notes|value|Email|value|Phone|value|Cell_Phone|value|Phone_Provider|value|Work_Phone|value|Pager|value|Home_Fax|value|WebPage|value|Street|value|City|value|State|value|Zip|value|Country|value|timestamp|value|Work_Fax|value|Street_2|value|Street_3|value|custom1|value|custom2|value|custom3|value|custom4|value|custom5|value|custom6|value|custom7|value|custom8|value|custom9|value|custom10|value|custom11|value|custom12|value|custom13|value|custom14|value|custom15|value|custom16|value|custom17|value|custom18|value|custom19|value|custom20|value|modinid|value|publish|value|region|value|occupation|value|Email|value";
	
	$MM_columnsStr = "Title|',none,''|Last_Name|',none,''|Suffix|',none,''|First_Name|',none,''|MI|',none,''|Company|',none,''|Notes|',none,''|Email|',none,''|Phone|',none,''|Cell_Phone|',none,''|Phone_Provider|',none,''|Work_Phone|',none,''|Pager|',none,''|Home_Fax|',none,''|Web_Page|',none,''|Street|',none,''|City|',none,''|State|',none,''|Zip|',none,''|Country|',none,''|timestamp|',none,''|Work_Fax|',none,''|Street_2|',none,''|Street_3|',none,''|custom1|',none,''|custom2|',none,''|custom3|',none,''|custom4|',none,''|custom5|',none,''|custom6|',none,''|custom7|',none,''|custom8|',none,''|custom9|',none,''|custom10|',none,''|custom11|',none,''|custom12|',none,''|custom13|',none,''|custom14|',none,''|custom15|',none,''|custom16|',none,''|custom17|',none,''|custom18|',none,''|custom19|',none,''|custom20|',none,''|modin|',none,''|publish|',none,''|region|',none,''|occupation|',none,''|pemail|',none,''";
	require ("DBConnections/insetstuff.php");
	require ("DBConnections/dataactions.php");
	if ($_POST[custom12] && $_POST[MM_insert]) {
	  $supporter = array('Title' => $Title, 'Last_Name' => $Last_Name, 'Suffix' => $Suffix, 'First_Name' => $First_Name, 'MI' => $MI, 'Company' => $Company, 'Notes' => $Notes, 'Email' => $Email, 'Phone' => $Phone, 'Cell_Phone' => $Cell_Phone, 'Phone_Provider' => $Phone_Provider, 'Work_Phone' => $Work_Phone, 'Pager' => $Pager, 'Home_Fax' => $Home_Fax, 'WebPage' => $WebPage, 'Street' => $Street, 'City' => $City, 'State' => $State, 'Zip' => $Zip, 'Country' => $Country,  'Work_Fax' => $Work_Fax, 'Street_2' => $Street_2, 'Street_3' => $Street_3, 'occupation' => $occupation    );
	
	  $supp_email = $Email;
	  $diaReq = new dia( 199, "http://www.demaction.org/dia/api/process.jsp" );
	  $result = $diaReq->add_supporter( $supp_email, $supporter );
	}
	$personid = $dbcon->Insert_ID();
#	die($personid);
	//update relational tables
	if ($_POST[MM_recordId]) {
		$d=$dbcon->Execute("delete  from vol_relskill where personid = $_POST[MM_recordId] ") or DIE($dbcon->ErrorMsg());
		$d=$dbcon->Execute("delete from vol_relinterest where personid = $_POST[MM_recordId]") or DIE($dbcon->ErrorMsg());
		$d=$dbcon->Execute("delete from vol_relavailability where personid = $_POST[MM_recordId]") or DIE($dbcon->ErrorMsg());
	}
	if (!$_POST[MM_delete]) {
		if (is_array($_POST[skills])) {
			while (list($k, $v) = each($_POST[skills])) { 
				$relupdate=$dbcon->Execute("INSERT INTO vol_relskill (personid,skillid) VALUES ('$personid','$v')") or DIE("30".$dbcon->ErrorMsg());
			}
		}
		if (is_array($_POST[interests])) {
			while (list($k, $v) = each($_POST[interests])) { 
				$relupdate=$dbcon->Execute("INSERT INTO vol_relinterest (personid,interestid) VALUES ('$personid','$v')") or DIE("35".$dbcon->ErrorMsg());
			}
		}
		if (is_array($_POST[avalible])) {
			while (list($k, $v) = each($_POST[avalible])) { 
				$relupdate=$dbcon->Execute("INSERT INTO vol_relavailability (personid,availabilityid) VALUES ('$personid','$v')") or DIE("40".$dbcon->ErrorMsg());
			}
		}
	}
	
/* 	//add to contat system
	if ($_POST[MM_insert]) {
		$uid = addcontact(4,2,$Organization,$FirstName,$LastName,$EmailAddress,$Phone,$Fax,$WebPage,$Address,$Address2,$City,$State,$PostalCode,$Country,$notes);
		contactadduid($personid,$uid,'moduserdata');
	} */

      redirect ("vol_update2.php?thank=1");
      #die("dd");
}
	 
################POPULATE FORM  ######################
 
$R__MMColParam = "8000000";

if (isset($_GET["id"])) {
	if (($_GET["token"]) == (($_GET["id"])*3+792875)) {
		$R__MMColParam = $_GET["id"];
	}
}

$R=$dbcon->Execute("SELECT * FROM userdata WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
#$hood=$dbcon->Execute("SELECT * FROM vol_hood") or DIE($dbcon->ErrorMsg());
#$com1=$dbcon->Execute("SELECT id, com FROM vol_com") or DIE($dbcon->ErrorMsg());
$skill=$dbcon->Execute("SELECT * FROM vol_skill ORDER BY  orderby asc") or DIE($dbcon->ErrorMsg());
$interest=$dbcon->Execute("SELECT * FROM vol_interest ORDER BY orderby asc") or DIE($dbcon->ErrorMsg());
$rec_id = & new Input('hidden', 'MM_recordId', $_GET[id]);


$customfields=$dbcon->Execute("SELECT * FROM userdata_fields WHERE id = 8 ") or DIE($dbcon->ErrorMsg());


   
################ FORM DATA  ######################				  
if ($_GET["thank"] == (NULL)) { ?>
      <form method="POST" action="<?php echo $PHP_SELF ?>" name="form1">
 
 <?php        
 echo $buildform->start_table('form');
echo addfield('Title','Prefix:','text',$R->Fields("Title"),'',10); 
echo addfield('First_Name','First Name:','text',$R->Fields("First_Name")); 
echo addfield('MI','MI:','text',$R->Fields("MI"),'',5); 
echo addfield('Last_Name','Last Name:','text',$R->Fields("Last_Name"));
echo addfield('Suffix','Suffix:','text',$R->Fields("Suffix"),'',10);  
echo addfield('Email','E-mail:','text',$R->Fields("Email")); 
echo addfield('Phone','Phone:','text',$R->Fields("Phone")); 
echo addfield('Cell_Phone','Cell Phone:','text',$R->Fields("Cell_Phone")); 
echo addfield('Work_Phone','Work Phone:','text',$R->Fields("Work_Phone")); 
echo addfield('Pager','Pager:','text',$R->Fields("Pager")); 
echo addfield('Home_Fax','Home_Fax:','text',$R->Fields("Home_Fax")); 
echo addfield('Street','Street:','text',$R->Fields("Street")); 
echo addfield('Street_2','Street 2:','text',$R->Fields("Street_2")); 
echo addfield('City','City:','text',$R->Fields("City")); 
$state_options = makelistarray($state,'state','statename','Select State');
$statesel = & new Select('State',$state_options,$R->Fields("State"));
echo  $buildform->add_row('State', $statesel);
echo addfield('Zip','Zip','text',$R->Fields("Zip"),'',10); 

/*if ($hood->RecordCount() > 0) {
	$hood_options = makelistarray($hood,'id','hood','Select Region');
	$hoodsel = & new Select('region',$hood_options,$R->Fields("region"));
	echo  $buildform->add_row('Region', $hoodsel);
}*/

#if ($electoralwebsite  == 1) {echo addfield('custom1','Precinct:','text',$R->Fields("custom1")); }
echo addfield('Company','Organization:','text',$R->Fields("Company")); 
echo addfield('occupation','Position:','text',$R->Fields("occupation"));


echo addfield('Notes','Other Information','textarea',$R->Fields("Notes")); 
echo buildcustomfields(9,$R);
echo buildcustomfields(10,$R);
echo buildcustomfields(11,$R);
echo addfield('custom12','Can we send you Email Updates:','checkbox',$R->Fields("custom12"),'1');  
echo buildcustomfields(13,$R);
echo buildcustomfields(14,$R);
echo buildcustomfields(15,$R);
echo buildcustomfields(16,$R);
echo buildcustomfields(17,$R);
echo buildcustomfields(18,$R);
echo buildcustomfields(19,$R);
echo buildcustomfields(20,$R);

		 ?> 

		  <tr valign="baseline"> 
      <td align="right" valign="top" nowrap class="form">Availability</td>
      <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="text">
          <tr class="intitle"> 
            <td><strong>Day</strong></td>
            <td><strong>Day</strong></td>
            <td><strong>Night</strong></td>
          </tr>
          <tr> 
            <td>Monday</td>
            <td><input type="checkbox" name="avalible[]" value="1" <?php if (checkav(1,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="2" <?php if (checkav(2,$R__MMColParam)){echo "checked";}?> ></td>
          </tr>
          <tr> 
            <td>Tuesday</td>
            <td><input type="checkbox" name="avalible[]" value="3" <?php if (checkav(3,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="4" <?php if (checkav(4,$R__MMColParam)){echo "checked";}?> ></td>
          </tr>
          <tr> 
            <td>Wednesday</td>
            <td><input type="checkbox" name="avalible[]" value="5" <?php if (checkav(5,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="6" <?php if (checkav(6,$R__MMColParam)){echo "checked";}?> > 
            </td>
          </tr>
          <tr> 
            <td>Thursday</td>
            <td><input type="checkbox" name="avalible[]" value="7" <?php if (checkav(7,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="8" <?php if (checkav(8,$R__MMColParam)){echo "checked";}?> ></td>
          </tr>
          <tr> 
            <td>Friday</td>
            <td><input type="checkbox" name="avalible[]" value="9" <?php if (checkav(9,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="10" <?php if (checkav(10,$R__MMColParam)){echo "checked";}?> ></td>
          </tr>
          <tr> 
            <td>Saturday</td>
            <td><input type="checkbox" name="avalible[]" value="11" <?php if (checkav(11,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="12" <?php if (checkav(12,$R__MMColParam)){echo "checked";}?> ></td>
          </tr>
          <tr> 
            <td>Sunday</td>
            <td><input type="checkbox" name="avalible[]" value="13" <?php if (checkav(13,$R__MMColParam)){echo "checked";}?> ></td>
            <td><input type="checkbox" name="avalible[]" value="14" <?php if (checkav(14,$R__MMColParam)){echo "checked";}?> ></td>
          </tr>
        </table></td>
    </tr>
<?php 
echo addfield('custom4','Other Availability:','textarea',$R->Fields("custom4")); 
/*if ($com1->RecordCount() > 0){ 
	echo $buildform->add_header('Commitee Interest');		

	$com_options = makelistarray($com1,'id','com','Select Committee');
	$com1sel = & new Select('custom6',$com_options,$R->Fields("custom6"));
	echo  $buildform->add_row('Committee 1', $com1sel);

	$com_options = makelistarray($com1,'id','com','Select Committee');
	$com1sel = & new Select('custom7',$com_options,$R->Fields("custom7"));
	echo  $buildform->add_row('Committee 2', $com1sel);

	$com_options = makelistarray($com1,'id','com','Select Committee');
	$com1sel = & new Select('custom8',$com_options,$R->Fields("custom8"));
	echo  $buildform->add_row('Committee 3', $com1sel);
 
}*/

echo $buildform->add_header('Select Interests');  
while  (!$interest->EOF)   { 
?>
<tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td colspan="2">  
                <div align="right">
                  <input name="interests[]" type="checkbox" value="<?php echo $interest->Fields("id"); ?>" <?php if (checkin($interest->Fields("id"),$R__MMColParam)){ echo "checked";} ?>>
                </div></td>
            <td width="80%" ><?php echo $interest->Fields("interest"); ?></td>
          </tr>
        </table></td>
    </tr>
<?php
	$interest->MoveNext(); 
}
?>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
<?php

echo addfield('custom5','Specifically, I would like to:','textarea',$R->Fields("custom5")); 
echo $buildform->add_header('Select Skills');

while (!$skill->EOF)  { 

?>

<tr valign="baseline"> 
      <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td colspan="2" width="20%"> <div align="right"> 
                <input name="skills[]" type="checkbox" value="<?php echo $skill->Fields("id"); ?>" <?php if (checkskill($skill->Fields("id"),$R__MMColParam)){ echo "checked";} ?>>
              </div></td>
            <td ><?php echo $skill->Fields("skill"); ?></td>
          </tr>
        </table></td>
    </tr>
<?php  
	$skill->MoveNext(); 
}

echo  $buildform->add_content('<br><br>'.$buildform->add_btn() .'&nbsp;'.$rec_id->fetch());
echo $buildform->end_table();
echo "</form>";
 
 
 }

include("AMP/BaseFooter.php"); ?>
