<?php
$modid = 27;
$gxendorse = 0;
$usefpcalendar =1; 
$usestudent = 1;
  require("Connections/freedomrising.php");
    include("Connections/menu.class.php");
?>
<?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
 
?><?php
// *** Insert Record: set Variables

  if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
      //Delete cached versions of output file
  
   // $MM_editConnection = MM__STRING;
    $MM_editTable  = "calendar2";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "calendar_gxlist2.php";
   $startdate = DateConvertIn($startdate);
       if ($nonstateregion !=1) {$region = $_POST[lstate];} 
   $MM_fieldsStr = "type|value|startdate|value|time|value|event|value|description|value|longdescription|value|organization|value|contact|value|email|value|phone1|value|url|value|location|value|city|value|lstate|value|lcountry|value|laddress|value|lzip|value|endorse|value|publish|value|repeat|value|student|value|fpevent|value|fporder|value|region|value|section|value";
   $MM_columnsStr = "typeid|none,none,NULL|date|',none,''|time|',none,''|event|',none,''|shortdesc|',none,''|fulldesc|',none,''|org|',none,''|contact1|',none,''|email1|',none,''|phone1|',none,''|url|',none,''|location|',none,''|lcity|',none,''|lstate|',none,''|lcountry|',none,''|laddress|',none,''|lzip|',none,''|endorse|',none,''|publish|none,1,0|repeat|none,1,0|student|none,1,0|fpevent|none,1,0|fporder|',none,''|region|',none,''|section|',none,''"; 
    
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }

   $state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();
   $eventtype=$dbcon->Execute("SELECT * FROM eventtype order by name asc") or DIE($dbcon->ErrorMsg());
   $eventtype_numRows=0;
   $eventtype__totalRows=$eventtype->RecordCount();
   
   $calendaritems__MMColParam = "90000000000";
if (isset($HTTP_GET_VARS["id"]))
  {$calendaritems__MMColParam = $HTTP_GET_VARS["id"];}
    $calendaritems=$dbcon->Execute("SELECT * FROM calendar2 where id = ".$calendaritems__MMColParam."") or DIE($dbcon->ErrorMsg());

?>
<?php include ("header.php");?>


      <form action="<?php echo $MM_editAction?>" method="POST" name="form"  >
        <table width="100%" align="center">
          <tr> 
            <td colspan="2" class="banner">Add/Edit Event</td>
          </tr>
          <tr> 
            <td colspan="2"><input name="submit" type="submit" value="Save Changes"> 
              <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" class="intitle">Publishing</td>
          </tr>
          <tr> 
            <td class="name" ><strong><font color="#CC0000" size="3">PUBLISH</font></strong><br> 
            </td>
            <td ><input type="checkbox" name="publish" value="1" <?php If (($calendaritems->Fields("publish")) == "1") { echo "CHECKED";} ?>></td>
          </tr>
          <?php if ($usefpcalendar == 1) {?>
          <tr> 
            <td class="name" >Fornt Page Event</td>
            <td ><input name="fpevent" type="checkbox" id="fpevent" value="1" <?php If (($calendaritems->Fields("fpevent")) == "1") { echo "CHECKED";} ?>></td>
          </tr>
          <tr> 
            <td class="name" >Fornt Page Order</td>
            <td ><input name="fporder" type="text" id="fporder"  value="<?php echo $calendaritems->Fields("fporder")?>" size="5"> 
            </td>
          </tr>
          <?php }  ?>
          <tr> 
            <td colspan="2" class="intitle">Event Info</td>
          </tr>
          <tr> 
            <td width="28%" class="name">Event Name*</td>
            <td width="72%"> <input type="text" name="event" size="45" value="<?php  echo htmlspecialchars ($calendaritems->Fields("event"))?>"> 
            </td>
          </tr>
          <tr> 
            <td class="name">Type</td>
            <td class="name"> <select name="type" id="select3">
                <option>Select Event Type</option>
                <?php   
				while ((!$eventtype->EOF)){ 
?>
                <option value="<?php echo  $eventtype->Fields("id")?>" <?php if ($eventtype->Fields("id")==$calendaritems->Fields("typeid")) echo "SELECTED";?>> 
                <?php echo  $eventtype->Fields("name");?> </option>
                <?php
      $eventtype->MoveNext();
   
  } ?>
              </select> 
              <?php if ($usestudent == 1) {?>
              &nbsp;&nbsp; <input name="student" type="checkbox" id="student" value="1" <?php If (($calendaritems->Fields("student")) == "1") { echo "CHECKED";} ?>>
              Student Event 
              <?php } ?>
            </td>
          </tr>
          <tr> 
            <td class="name">Event Date*</td>
            <td class="text"> <input name="startdate" type="text" value="<?php echo DateConvertOut($calendaritems->Fields("date"))?>" size="20">
              Format must be month-day-year 12-30-2002) </td>
          </tr>
          <tr> 
            <td class="name">Event Time</td>
            <td><input name="time" type="text" value="<?php echo $calendaritems->Fields("time")?>" size="20"></td>
          </tr>
          <tr> 
            <td class="name">Weekly or Repeating</td>
            <td><input type="checkbox" name="repeat" value="1" <?php If (($calendaritems->Fields("repeat")) == "1") { echo "CHECKED";} ?>></td>
          </tr>
          <tr> 
            <td class="name">Web Site</td>
            <td><input name="url" type="text" value="<?php echo $calendaritems->Fields("url")?>" size="45"></td>
          </tr>
          <tr> 
            <td colspan="2" class="name"> <table width="100%" border="0">
                <tr> 
                  <td colspan="2" class="intitle" >Event Location</td>
                </tr>
                <tr> 
                  <td class="name">Event Location</td>
                  <td><textarea name="location" cols="55" rows="5" wrap="VIRTUAL" id="location2"><?php echo $calendaritems->Fields("location")?></textarea></td>
                </tr>
                <tr> 
                  <td class="name">Event City:*</td>
                  <td><input name="city" type="text" id="city2" value="<?php echo $calendaritems->Fields("lcity")?>" size="40"></td>
                </tr>
                <tr> 
                  <td class="name">Event State*</td>
                  <td><select name="lstate" id="select4">
                      <option>Select State</option>
                      <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
                      <option value="<?php echo  $state->Fields("id")?>" <?php if ($state->Fields("id")==$calendaritems->Fields("lstate")) echo "SELECTED";?>> 
                      <?php echo  $state->Fields("statename");?> </option>
                      <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
                    </select></td>
                </tr>
				<tr> 
       <?php if ($nonstateregion ==1) {
	   ?>           <td class="name">Region</td>
                  <td><select NAME="region" id="region">
                      <option>Select Region</option>
                      <?php  
					  $regionsel=$dbcon->Execute("SELECT * FROM region order by title asc") or DIE($dbcon->ErrorMsg());
					    while (!$regionsel->EOF)   {
?>
                      <OPTION VALUE="<?php echo  $regionsel->Fields("id")?>" <?php if ($regionsel->Fields("id") ==  $calendaritems->Fields("region")) {echo  "selected";}?>> 
				
                      <?php echo  $regionsel->Fields("title");?> </OPTION>
                      <?php
  $regionsel->MoveNext();
} ?>
                    </select></td>
                </tr><?php } ?>
                <tr> 
                  <td class="name">Event Country:*</td>
                  <td> <input name="lcountry" type="text" id="lcountry2" value="<?php echo $calendaritems->Fields("lcountry")?>" size="40"></td>
                </tr>
                <tr> 
                  <td class="name">Event Street Address:</td>
                  <td><input name="laddress" type="text" id="laddress2" value="<?php echo $calendaritems->Fields("laddress")?>" size="40"></td>
                </tr>
                <tr> 
                  <td class="name">Event Zip</td>
                  <td><input name="lzip" type="text" id="lzip2" value="<?php echo $calendaritems->Fields("lzip")?>" size="40"></td>
                </tr>
                <tr> 
                  <td colspan="2" class="intitle">Event Description</td>
                </tr>
              </table>
              Brief Description of the event *<br> <textarea name="description" rows="10" cols="65" wrap="VIRTUAL"><?php echo htmlspecialchars ($calendaritems->Fields("shortdesc"))?></textarea> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" class="name"> Full Description of the event (optional)<br> 
              <textarea name="longdescription" rows="15" cols="65" wrap="VIRTUAL"><?php echo htmlspecialchars ($calendaritems->Fields("fulldesc"))?></textarea> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" class="name">Endorsing Organizations (If any):<br> 
              <textarea name="organization" cols="65" rows="4" wrap="VIRTUAL"><?php echo $calendaritems->Fields("org")?></textarea> 
              <br> </td>
          </tr>
          <tr> 
            <td colspan="2"><table width="100%" border="0">
                <tr> 
                  <td valign="top"><table width="100%" border="0">
                      <tr> 
                        <td colspan="2" class="intitle">Public Contact Information</td>
                      </tr>
                      <tr> 
                        <td class="name">Contact Name:*</td>
                        <td><input name="contact" type="text" id="contact2" value="<?php echo $calendaritems->Fields("contact1")?>" size="40"></td>
                      </tr>
                      <tr> 
                        <td class="name">Contact Email: *</td>
                        <td><input name="email" type="text" value="<?php echo $calendaritems->Fields("email1")?>" size="40"></td>
                      </tr>
                      <tr> 
                        <td class="name">Contact Phone:</td>
                        <td><input name="phone1" type="text" value="<?php echo $calendaritems->Fields("phone1")?>" size="40"></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
          <?php if ($gxendorse == 1) {?>
          <tr> 
            <td align="center" class="name"><div align="left">Meet moveon criteria</div></td>
            <td align="center"><div align="left"><em> 
                <input name="endorse" type="checkbox" id="endorse" size="25" value="1" <?php if  (($calendaritems->Fields("endorse") == 1) or ($calendaritems->Fields("endorse") == "yes")) {echo "checked";} ?> >
                </em></div></td>
          </tr> <?php }?>
          <tr> 
            <td align="center" class="name">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
          <tr> 
            <td align="center" class="name"><div align="left">Related to Section</div></td>
            <td align="center"><div align="left">
                <select name="section">
                  <OPTION VALUE="<?php
				  $obj = new Menu; 
				   if  ($_GET[id]) {$typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$calendaritems->Fields("section")."") or DIE($dbcon->ErrorMsg());
				   echo  $typelab->Fields("id")?>" SELECTED><?php echo  $typelab->Fields("type")?></option>
                  <?php  }
				  echo $obj->select_type_tree(0); ?>
                </Select>
              </div></td>
          </tr>
      
       
          <tr> 
            <td colspan="2" class="banner">&nbsp;</td>
          </tr>
          <tr> 
            <td align="center" colspan="2"><div align="left"> 
                <input name="submit" type="submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
                <?php 
		}
		else { ?>
                <input type="hidden" name="MM_update" value="true">
                <?php } ?>
                <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
              </div></td>
          </tr>
        </table>
              
		
		
      </form>

      <?php include("footer.php"); ?>
