<?php 
$modid=40;
  require("Connections/freedomrising.php");
 
 ##################get the list of lists ########################
 $skill=$dbcon->Execute("SELECT * FROM vol_skill ORDER BY skill ASC") or DIE($dbcon->ErrorMsg());
 $interest=$dbcon->Execute("SELECT * FROM vol_interest ORDER BY interest ASC") or DIE($dbcon->ErrorMsg());
 
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
 
######### INSERT RECORD  ################################## 
// *** Insert Record: set Variables
 if (isset($MM_insert)){
   // $MM_editConnection = MM__STRING;
   $MM_editTable  = "vol_people";
     $MM_fieldsStr = "precinct|value|last_name|value|first_name|value|email|value|city|value|state|value|zip|value|hood|value|address|value|phone|value|phone2|value|phone3|value|organization|value|notes|value|officenotes|value|avalibility|value|otherinterest|value|com1|value|com2|value|com3|value|mon_d|value|mon_n|value|tues_d|value|tues_n|value|wen_d|value|wen_n|value|thur_d|value|thur_n|value|fri_d|value|fri_n|value|sat_d|value|sat_n|value|sun_d|value|sun_n|value";
   $MM_columnsStr = "precinct|',none,''|last_name|',none,''|first_name|',none,''|email|',none,''|city|',none,''|state|',none,''|zip|',none,''|hood|',none,''|address|',none,''|phone|',none,''|phone2|',none,''|phone3|',none,''|organization|',none,''|notes|',none,''|officenotes|',none,''|avalibility|',none,''|otherinterest|',none,''|com1|',none,''|com2|',none,''|com3|',none,''|mon_d|',none,''|mon_n|',none,''|tues_d|',none,''|tues_n|',none,''|wen_d|',none,''|wen_n|',none,''|thur_d|',none,''|thur_n|',none,''|fri_d|',none,''|fri_n|',none,''|sat_d|',none,''|sat_n|',none,''|sun_d|',none,''|sun_n|',none,''";
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  
  
 $newrec=$dbcon->Execute("SELECT id FROM vol_people ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$personid=$newrec->Fields("id");

while (($Repeat1__numRows-- != 0) && (!$skill->EOF)) 
   { 
if (isset($HTTP_POST_VARS[$skill->Fields("id")]))  {
$skillid = $skill->Fields("id"); 

 $MM_editTable  = "vol_relskill";
  $MM_fieldsStr = "personid|value|skillid|value";
   $MM_columnsStr = "personid|none,none,NULL|skillid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	}
	
	$Repeat1__index++;
  $skill->MoveNext();
  }
  
  while (($Repeat2__numRows-- != 0) && (!$interest->EOF)) 
   { 
if (isset($HTTP_POST_VARS[$interest->Fields("id")]))  {
$interestid = $interest->Fields("id"); 

 $MM_editTable  = "vol_relinterest";
  $MM_fieldsStr = "personid|value|interestid|value";
   $MM_columnsStr = "personid|none,none,NULL|interestid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	}
	
	$Repeat2__index++;
  $interest->MoveNext();
  }

	 $MM_editRedirectUrl = "vol_list.php";
	 header ("Location: $MM_editRedirectUrl");
   }// end insert
   
######### UPDATE RECORD  ##################################  

if  (isset($MM_update)){  //start update
  $MM_editTable  = "vol_people";
    $MM_editColumn = "id";  
	$MM_recordId = "" . $MM_recordId . "";
      $MM_fieldsStr = "precinct|value|bounce|value|last_name|value|first_name|value|email|value|city|value|state|value|zip|value|hood|value|address|value|phone|value|phone2|value|phone3|value|organization|value|notes|value|officenotes|value|avalibility|value|otherinterest|value|com1|value|com2|value|com3|value|mon_d|value|mon_n|value|tues_d|value|tues_n|value|wen_d|value|wen_n|value|thur_d|value|thur_n|value|fri_d|value|fri_n|value|sat_d|value|sat_n|value|sun_d|value|sun_n|value";
   $MM_columnsStr = "precinct|',none,''|bounce|',none,''|last_name|',none,''|first_name|',none,''|email|',none,''|city|',none,''|state|',none,''|zip|',none,''|hood|',none,''|address|',none,''|phone|',none,''|phone2|',none,''|phone3|',none,''|organization|',none,''|notes|',none,''|officenotes|',none,''|avalibility|',none,''|otherinterest|',none,''|com1|',none,''|com2|',none,''|com3|',none,''|mon_d|',none,''|mon_n|',none,''|tues_d|',none,''|tues_n|',none,''|wen_d|',none,''|wen_n|',none,''|thur_d|',none,''|thur_n|',none,''|fri_d|',none,''|fri_n|',none,''|sat_d|',none,''|sat_n|',none,''|sun_d|',none,''|sun_n|',none,''";
   

   require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");

$personid= $MM_recordId;
$MM_update = NULL;
while  (!$skill->EOF) 
   { //start repeat
     $supvar3= "s".$skill->Fields("id");
  $instance = ($HTTP_POST_VARS[$supvar3]);

  if ($instance == 500){ //insert
  $skillid = $skill->Fields("id");
  $MM_insert=1; 

	$MM_editTable  = "vol_relskill";
  $MM_fieldsStr = "skillid|value|personid|value";
   $MM_columnsStr = "skillid|none,none,NULL|personid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");}


	if ($instance == (NULL)) { //start delete
	
	$skillid = $skill->Fields("id");
$supvar= "b".$skillid;
	 $MM_recordId = ($HTTP_POST_VARS["$supvar"]);
	if  ($MM_recordId != $null){
	$MM_delete = 1;
  $MM_editColumn = "id";  
$MM_editTable  = "vol_relskill";
  
	require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");}
}//end deletet	
	 
	

  $skill->MoveNext();
  } //end repeat 
 
 $MM_update = NULL;
while  (!$interest->EOF) 
   { //start repeat
   $supvar4= "i".$interest->Fields("id");
  $instance2 = ($HTTP_POST_VARS[$supvar4]);

  if ($instance2 == 600){ //insert
  $interestid = $interest->Fields("id");
  $MM_insert=1; 

	$MM_editTable  = "vol_relinterest";
  $MM_fieldsStr = "interestid|value|personid|value";
   $MM_columnsStr = "interestid|none,none,NULL|personid|none,none,NULL"; 
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");}


	if ($instance2 == (NULL)) { //start delete
	
	$interestid = $interest->Fields("id");
$supvar2= "c".$interestid;
	 $MM_recordId = ($HTTP_POST_VARS["$supvar2"]);
	if  ($MM_recordId != $null){
	$MM_delete = 1;
  $MM_editColumn = "id";  
$MM_editTable  = "vol_relinterest";
  
	require ("../Connections/insetstuff.php");
   require ("../Connections/dataactions.php");}
}//end deletet	
	 
	

  $interest->MoveNext();
  } //end repeat 
 
 
 
   header ("Location: vol_personedit.php");
  } 
 
  //end update   
   
######### DELETE RECORD  ##################################

 if  (isset($MM_delete)){  
   $dbcon->Execute("DELETE FROM vol_relskill WHERE personid = $MM_recordId") or DIE($dbcon->ErrorMsg());
   $dbcon->Execute("DELETE FROM vol_relinterest WHERE personid = $MM_recordId") or DIE($dbcon->ErrorMsg());
   $dbcon->Execute("DELETE FROM vol_people WHERE id = $MM_recordId") or DIE($dbcon->ErrorMsg());
   
   header ("Location: vol_list.php");
    }//end delete
	 
################POPULATE FORM  ######################
   $Recordset1__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
$Recordset1=$dbcon->Execute("SELECT * FROM vol_people WHERE id = $Recordset1__MMColParam") or DIE($dbcon->ErrorMsg());
$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
 
 if ($electoralwebsite  == 1) {
   $hood=$dbcon->Execute("SELECT * FROM vol_hood") or DIE($dbcon->ErrorMsg());
   $hood_numRows=0;
   $hood__totalRows=$hood->RecordCount(); }
   
   $com1=$dbcon->Execute("SELECT id, com FROM vol_com") or DIE($dbcon->ErrorMsg());
      $com1_numRows=0;
   $com1__totalRows=$com1->RecordCount();

?>
				 <?php include("header.php");?> <?php 
 ################ FORM DATA  ######################				  
				  //if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
      <form method="POST" action="<?php echo $MM_editAction?>" name="form1">
 
        <table width="98%" border=0 align="center" cellpadding=2 cellspacing=0>
          <tr valign="baseline" class="banner"> 
            <td colspan="2" align="right" nowrap class="form"><div align="left">Add/Edit 
                Volunteer Information</div></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">First Name:</td>
            <td><input type="text" name="first_name" value="<?php echo $Recordset1->Fields("first_name")?>" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Last Name:</td>
            <td><input type="text" name="last_name" value="<?php echo $Recordset1->Fields("last_name")?>" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">E-mail:</td>
            <td> <input type="text" name="email" value="<?php echo $Recordset1->Fields("email"); ?>" size="32">
              <br>
              <input type="checkbox" name="bounce" value="1" <?php if ($Recordset1->Fields("bounce")){echo "checked";}?> >
            bouncing            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Day Phone:</td>
            <td> <input name="phone" type="text" id="phone" value="<?php echo $Recordset1->Fields("phone"); ?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Evening Phone</td>
            <td> <input name="phone2" type="text" id="phone2" value="<?php echo $Recordset1->Fields("phone2"); ?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Cell Phone</td>
            <td> <input name="phone3" type="text" id="phone3" value="<?php echo $Recordset1->Fields("phone3"); ?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Address</td>
            <td> <input name="address" type="text" id="address" value="<?php echo $Recordset1->Fields("address"); ?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">City:</td>
            <td> <input type="text" name="city" value="<?php echo $Recordset1->Fields("city")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Zip</td>
            <td> <input type="text" name="zip" value="<?php echo $Recordset1->Fields("zip")?>" size="15"> 
            </td>
          </tr>
    <?php if ($electoralwebsite  == 1) {?>      <tr> 
            <td><div align="right">District</div></td>
            <td><select name="hood">
                <option value = "" selected>Select District</option>
                <?php    if ($hood__totalRows > 0){
    $hood__index=0;
    $hood->MoveFirst();
    WHILE ($hood__index < $hood__totalRows){
?>
                <option value="<?php echo  $hood->Fields("id")?>" <?php if ($hood->Fields("id")==$Recordset1->Fields("hood")) echo "SELECTED";?>> 
                <?php echo  $hood->Fields("id");?> - <?php echo  $hood->Fields("hood");?> 
                </option>
                <?php
      $hood->MoveNext();
      $hood__index++;
    }
    $hood__index=0;  
    $hood->MoveFirst();
  } ?>
              </select></td>
          </tr> <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form"> Precinct :</td>
            <td><input name="precinct " type="text" id="precinct " value="<?php echo $Recordset1->Fields("precinct")?>" size="45"> 
            </td>
          </tr><?php } ?>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Affinity Groups 
              and Organizations:</td>
            <td><textarea name="organization" cols="45" rows="4" wrap="VIRTUAL" id="organization"><?php echo $Recordset1->Fields("organization")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Availability:</td>
            <td><textarea name="avalibility" cols="45" rows="4" wrap="VIRTUAL" id="avalibility"><?php echo $Recordset1->Fields("avalibility")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Other Information:</td>
            <td><textarea name="notes" cols="45" rows="4" wrap="VIRTUAL" id="notes"><?php echo $Recordset1->Fields("notes")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Office Notes</td>
            <td><textarea name="officenotes" cols="45" rows="4" wrap="VIRTUAL" id="officenotes"><?php echo $Recordset1->Fields("officenotes")?></textarea> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Availability</td>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr class="intitle"> 
                  <td>Day</td>
                  <td>Day</td>
                  <td>Night</td>
                </tr>
                <tr> 
                  <td>Monday</td>
                  <td><input type="checkbox" name="mon_d" value="1" <?php if ($Recordset1->Fields("mon_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="mon_n" value="1" <?php if ($Recordset1->Fields("mon_n")){echo "checked";}?> ></td>
                </tr>
                <tr> 
                  <td>Tuesday</td>
                  <td><input type="checkbox" name="tues_d" value="1" <?php if ($Recordset1->Fields("tues_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="tues_n" value="1" <?php if ($Recordset1->Fields("tues_n")){echo "checked";}?> ></td>
                </tr>
                <tr> 
                  <td>Wednesday</td>
                  <td><input type="checkbox" name="wen_d" value="1" <?php if ($Recordset1->Fields("wen_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="wen_n" value="1" <?php if ($Recordset1->Fields("wen_n")){echo "checked";}?> >
                  </td>
                </tr>
                <tr> 
                  <td>Thursday</td>
                  <td><input type="checkbox" name="thur_d" value="1" <?php if ($Recordset1->Fields("thur_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="thur_n" value="1" <?php if ($Recordset1->Fields("thur_n")){echo "checked";}?> ></td>
                </tr>
                <tr> 
                  <td>Friday</td>
                  <td><input type="checkbox" name="fri_d" value="1" <?php if ($Recordset1->Fields("fri_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="fri_n" value="1" <?php if ($Recordset1->Fields("fri_n")){echo "checked";}?> ></td>
                </tr>
                <tr> 
                  <td>Saturday</td>
                  <td><input type="checkbox" name="sat_d" value="1" <?php if ($Recordset1->Fields("sat_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="sat_n" value="1" <?php if ($Recordset1->Fields("sat_n")){echo "checked";}?> ></td>
                </tr>
                <tr> 
                  <td>Sunday</td>
                  <td><input type="checkbox" name="sun_d" value="1" <?php if ($Recordset1->Fields("sun_d")){echo "checked";}?> ></td>
                  <td><input type="checkbox" name="sun_n" value="1" <?php if ($Recordset1->Fields("sun_n")){echo "checked";}?> ></td>
                </tr>
              </table> </td>
          </tr>
          <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" valign="top" nowrap class="form"><div align="center">Committee 
                Interest </div></td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Committee 1</td>
            <td><select name="com1">
                <option value = "" selected>Select Committee</option>
                <?php    if ($com1__totalRows > 0){
    $com1__index=0;
    $com1->MoveFirst();
    WHILE ($com1__index < $com1__totalRows){
?>
                <option value="<?php echo  $com1->Fields("id")?>" <?php if ($com1->Fields("id")==$Recordset1->Fields("com1")) echo "SELECTED";?>> 
                <?php echo  $com1->Fields("com");?> 
                </option>
                <?php
      $com1->MoveNext();
      $com1__index++;
    }
    $com1__index=0;  
    $com1->MoveFirst();
  } ?>
              </select></td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Committee 2</td>
            <td><select name="com2">
              <option value = "" selected>Select Committee</option>
              <?php    if ($com1__totalRows > 0){
    $com1__index=0;
    $com1->MoveFirst();
    WHILE ($com1__index < $com1__totalRows){
?>
              <option value="<?php echo  $com1->Fields("id")?>" <?php if ($com1->Fields("id")==$Recordset1->Fields("com2")) echo "SELECTED";?>> <?php echo  $com1->Fields("com");?> </option>
              <?php
      $com1->MoveNext();
      $com1__index++;
    }
    $com1__index=0;  
    $com1->MoveFirst();
  } ?>
            </select></td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Committee 3</td>
            <td><select name="com3">
              <option value = "" selected>Select Committee</option>
              <?php    if ($com1__totalRows > 0){
    $com1__index=0;
    $com1->MoveFirst();
    WHILE ($com1__index < $com1__totalRows){
?>
              <option value="<?php echo  $com1->Fields("id")?>" <?php if ($com1->Fields("id")==$Recordset1->Fields("com3")) echo "SELECTED";?>> <?php echo  $com1->Fields("com");?> </option>
              <?php
      $com1->MoveNext();
      $com1__index++;
    }
    $com1__index=0;  
    $com1->MoveFirst();
  } ?>
            </select></td>
          </tr>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="intitle"><div align="center"><strong><br>
                Select Interest<br>
                <br>
                </strong></div></td>
          </tr>
          <?php while  (!$interest->EOF)   { 

$instance2=$dbcon->Execute("SELECT id FROM vol_relinterest WHERE personid = ".$Recordset1__MMColParam." and interestid= ".($interest->Fields("id"))." LIMIT 1") or DIE($dbcon->ErrorMsg());
		$inst2=$instance2->Fields("id");
			$instance2->Close();?>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="2"> <div align="center"> 
                      <input name="i<?php echo $interest->Fields("id"); ?>" type="checkbox" value="<?php echo ("$inst2"); ?><?php if (empty($inst2)){
						echo "600"; }?>" <?php 
			
			if (isset($inst2)){ echo "checked";} ?>>
                    </div></td>
                  <td width="89%" > <input name="c<?php echo ($interest->Fields("id")); ?>" type="hidden" value="<?php echo ("$inst2"); ?>"> 
                    <?php echo $interest->Fields("interest"); ?> </td>
                </tr>
              </table></td>
          </tr>
          <?php  $interest->MoveNext(); }?>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Specifically, I 
              would like to :</td>
            <td><textarea name="otherinterest" cols="45" rows="4" wrap="VIRTUAL"><?php echo $Recordset1->Fields("otherinterest")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap class="form"><div align="center"><strong><br>
                Select Skills<br>
                <br>
                </strong></div></td>
          </tr>
          <?php while  (!$skill->EOF)   { 

$instance=$dbcon->Execute("SELECT id FROM vol_relskill WHERE personid = ".$Recordset1__MMColParam." and skillid= ".($skill->Fields("id"))." LIMIT 1") or DIE($dbcon->ErrorMsg());
		$inst=$instance->Fields("id");
			$instance->Close();?>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="2" width="20%"> <div align="right"> 
                      <input name="s<?php echo $skill->Fields("id"); ?>" type="checkbox" value="<?php echo ("$inst"); ?><?php if (empty($inst)){
						echo "500"; }?>" <?php 
			
			if (isset($inst)){ echo "checked";} ?>>
                    </div></td>
                  <td > <input name="b<?php echo ($skill->Fields("id")); ?>" type="hidden" value="<?php echo ("$inst"); ?>"> 
                    <?php echo $skill->Fields("skill"); ?></td>
                </tr>
              </table></td>
          </tr>
          <?php  $skill->MoveNext(); }?>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td>&nbsp; </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td><input type="submit" name="<?php if (($HTTP_GET_VARS["id"])== ($null)) {echo "MM_insert";} else {echo "MM_update";}?>" value="Submit"> 
              <input type="submit" name="MM_delete" value="Delete" onClick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
              <input type="hidden" name="MM_recordId" value="<?php echo $Recordset1->Fields("id") ?>"> 
            </td>
          </tr>
        </table>
  
</form>


<?php include("footer.php"); ?>