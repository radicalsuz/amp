<?php 
$modid=40;
  require("Connections/freedomrising.php");
 
 ##################get the list of lists ########################
 $skill=$dbcon->Execute("SELECT * FROM vol_skill ORDER BY skill ASC") or DIE($dbcon->ErrorMsg());
 $interest=$dbcon->Execute("SELECT * FROM vol_interest ORDER BY interest ASC") or DIE($dbcon->ErrorMsg());
 
 
	 
################POPULATE FORM  ######################
   $Recordset1__MMColParam = "8000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
$Recordset1=$dbcon->Execute("SELECT * FROM vol_people WHERE id = $Recordset1__MMColParam") or DIE($dbcon->ErrorMsg());
$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
   $hood=$dbcon->Execute("SELECT * FROM vol_hood") or DIE($dbcon->ErrorMsg());
   $hood_numRows=0;
   $hood__totalRows=$hood->RecordCount();
   $com1=$dbcon->Execute("SELECT id, com FROM vol_com") or DIE($dbcon->ErrorMsg());
      $com1_numRows=0;
   $com1__totalRows=$com1->RecordCount();
   $com2=$dbcon->Execute("SELECT id, com FROM vol_com") or DIE($dbcon->ErrorMsg());
      $com2_numRows=0;
   $com2__totalRows=$com2->RecordCount();
   $com3=$dbcon->Execute("SELECT id, com FROM vol_com") or DIE($dbcon->ErrorMsg());
      $com3_numRows=0;
   $com3__totalRows=$com3->RecordCount();
?>
				 <?php include("header.php");?> <?php 
 ################ FORM DATA  ######################				  
				  //if ($HTTP_GET_VARS["thank"] == ($null)) { ?>
      <form method="POST" action="vol_results.php" name="form1">
 
        <table width="98%" border=0 align="center" cellpadding=2 cellspacing=0>
          <tr valign="baseline" class="banner"> 
            <td colspan="2" align="right" nowrap class="form"><div align="left">Add/Edit 
                Volenteer Information</div></td>
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
            <td> <p>
              <input type="text" name="email" value="<?php echo $Recordset1->Fields("email"); ?>" size="32">
              <br>
              EMPTY for null fields</p>
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right" class="form">Bounce</td>
            <td><input name="bounce" type="checkbox" id="bounce" value="1"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Day Phone:</td>
            <td><input name="phone" type="text" id="phone" value="<?php echo $Recordset1->Fields("phone"); ?>" size="32"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">Address</td>
            <td> <input name="address" type="text" id="address" value="<?php echo $Recordset1->Fields("address"); ?>" size="32"> 
              <br>
            NOT EMPTY for non null fields </td>
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
          <tr> 
            <td><div align="right">District</div></td>
            <td><select name="hood">
                <option value = "" selected>Select District</option>
				<option value = "0" >NULL</option>
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
          </tr><tr valign="baseline"> 
            <td nowrap align="right" class="form">Precinct</td>
            <td> <input name="precinct" type="text" id="precinct" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Affinity Groups 
              and Organizations:</td>
            <td><textarea name="organization" cols="45" rows="4" wrap="VIRTUAL" id="organization"><?php echo $Recordset1->Fields("organization")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Avalibility:</td>
            <td><textarea name="avalibility" cols="45" rows="4" wrap="VIRTUAL" id="avalibility"></textarea> 
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
          </tr><tr valign="baseline"> 
            <td align="right" valign="top" nowrap class="form">Avalibility</td>
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
                  <td>Wensday</td>
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
            <td><select name="com" id="com">
                <option value="0" selected>Select Committee</option>
                <?php    if ($com1__totalRows > 0){
    $com1__index=0;
    $com1->MoveFirst();
    WHILE ($com1__index < $com1__totalRows){
?>
                <option value="<?php echo  $com1->Fields("id")?>" <?php if ($com1->Fields("id")==$Recordset1->Fields("com")) echo "SELECTED";?>> 
                <?php echo  $com1->Fields("com");?> </option>
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

?>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="2"> <div align="center"> 
				  
                      <input name="interestid" type="radio" value="<?php echo $interest->Fields("id"); ?>" >
                    </div></td>
                  <td width="89%" > 
                    <?php echo $interest->Fields("interest"); ?> </td>
                </tr>
              </table></td>
          </tr>
          <?php  $interest->MoveNext(); }?>
          <tr valign="baseline"> 
            <td nowrap align="right" class="form">&nbsp;</td>
            <td><p>
                
                <br>
              </p></td>
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

?>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap class="form"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td colspan="2"> <div align="center"> 
				  
                      <input name="skillid" type="radio" value="<?php echo $skill->Fields("id"); ?>" >
                    </div></td>
                  <td width="89%" > 
                    <?php echo $skill->Fields("skill"); ?> </td>
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