<?php
//passed varaibles $id and $modin if $id is not set it is an add article page if not it is an update page
  require("Connections/freedomrising.php");
$modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=$modin") or DIE($dbcon->ErrorMsg());
 
$modid=$modidselect->Fields("id");

 function customfields ($fieldtext,$fieldname,$fielddata) {  
 global $customfields, $Recordset1;
 	    if ($customfields->Fields("$fieldtext") != ($null)) {  //start field 
		  	echo  "<tr> <td align=\"right\" >";
          	echo $customfields->Fields("$fieldtext"); 
          	echo "</td> <td colspan=2>";
		if ($customfields->Fields("$fielddata") == ("1")){ 
            echo " <input type=\"text\" name=\"";
			echo $fieldname;
			echo "\" size=\"50\" value=\"";
			echo $Recordset1->Fields("$fieldname");
			echo "\" >"; }
		if ($customfields->Fields("$fielddata") == ("2")){ 
            echo " <input ";
			echo "type=\"checkbox\" name=\"";
			echo $fieldname;
			echo "\" value=1 ";
			if (($Recordset1->Fields("$fieldname")) == "1") { echo "CHECKED";}
			echo ">"; }
		if ($customfields->Fields("$fielddata") == ("3")){ 
            echo " <textarea name=\"";
			echo $fieldname;
			echo "\" wrap=\"VIRTUAL\" cols=\"55\" rows=\"7\">";
			echo $Recordset1->Fields("$fieldname");
			echo "</textarea>"; }
			echo "</td> </tr>"; } 
	} 


?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();
?><?php
  // *** Add/Update Record: set variables
  
  if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
        //Delete cached versions of output file
 
  
//    $MM_editConnection = $MM__STRING;
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "moddata_list.php";
    $MM_editTable  = "moduserdata";
	   if ($nonstateregion !=1) {$region = $_POST[State];} 
    $MM_fieldsStr = "Organization|value|FirstName|value|LastName|value|EmailAddress|value|Phone|value|Fax|value|WebPage|value|Address|value|Address2|value|City|value|State|value|PostalCode|value|Country|value|notes|value|field1|value|field2|value|field3|value|field4|value|field5|value|field6|value|field7|value|field8|value|field9|value|field10|value|publish|value|modin|value|field11|value|field12|value|field13|value|field14|value|field15|value|field16|value|field17|value|field18|value|field19|value|field20|value|region|value";
   $MM_columnsStr = "Organization|',none,''|FirstName|',none,''|LastName|',none,''|EmailAddress|',none,''|Phone|',none,''|Fax|',none,''|WebPage|',none,''|Address|',none,''|Address2|',none,''|City|',none,''|State|',none,''|PostalCode|',none,''|Country|',none,''|notes|',none,''|field1|',none,''|field2|',none,''|field3|',none,''|field4|',none,''|field5|',none,''|field6|',none,''|field7|',none,''|field8|',none,''|field9|',none,''|field10|',none,''|publish|none,none,NULL|modinid|none,none,NULL|field11|',none,''|field12|',none,''|field13|',none,''|field14|',none,''|field15|',none,''|field16|',none,''|field17|',none,''|field18|',none,''|field19|',none,''|field20|',none,''|region|',none,''";
  
  require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
  
// delcale recordsets
$modin = $HTTP_GET_VARS[modin];
$Recordset1__MMColParam = '90000000000';
if (isset($HTTP_GET_VARS["id"]))
	  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}
    	$Recordset1=$dbcon->Execute("SELECT * FROM moduserdata WHERE id = $Recordset1__MMColParam and modinid = $modin") or DIE($dbcon->ErrorMsg());

$customfields=$dbcon->Execute("SELECT * FROM modfields WHERE id = $modin") or DIE($dbcon->ErrorMsg());
$state=$dbcon->Execute("SELECT * FROM states") or DIE($dbcon->ErrorMsg());
   $state_numRows=0;
   $state__totalRows=$state->RecordCount();

 ?><?php include("header.php"); ?>

<form name="Form1" action="<?php echo $MM_editAction?>" method="POST">
              
        <table width="100%" border="0" class="name">
          <tr> 
            <td colspan="2" class="banner"> 
              <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
              Add 
              <?php } else { ?>
              Update 
              <?php }?>
              &nbsp;<?php echo $customfields->Fields("name")?></td>
          </tr>
          <tr> 
            <td colspan="2" class="name"><input type="submit" name="Submit" value="Save Changes"> 
              <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')"> 
              <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left"><font color="#CC0000" size="3"><strong>PUBLISH</strong></font></div></td>
            <td><input <?php If (($Recordset1->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="publish" value="1"></td>
          </tr>
          <tr> 
            <td align="right" class="name"><div align="left">Organization</div></td>
            <td> <textarea name="Organization" cols="55" rows="3" wrap="VIRTUAL"><?php echo $Recordset1->Fields("Organization")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">First Name </div></td>
            <td> <input type="text" name="FirstName" size="50" value="<?php echo $Recordset1->Fields("FirstName")?>">
            </td>
          </tr>
          <tr>
            <td align="right" class="form"><div align="left">Last Name</div></td>
            <td><input type="text" name="LastName" size="50" value="<?php echo $Recordset1->Fields("LastName")?>"></td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">E-mail</div></td>
            <td> <input type="text" name="EmailAddress" size="50" value="<?php echo $Recordset1->Fields("EmailAddress")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">Phone Number</div></td>
            <td> <input type="text" name="Phone" size="35" value="<?php echo $Recordset1->Fields("Phone")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">Fax Number</div></td>
            <td> <input type="text" name="Fax" size="35" value="<?php echo $Recordset1->Fields("Fax")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">Web Page</div></td>
            <td> <input type="text" name="WebPage"  size="50" value="<?php echo $Recordset1->Fields("WebPage")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">Mailing Address</div></td>
            <td> <input type="text" name="Address"  size="50" value="<?php echo $Recordset1->Fields("Address")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form">&nbsp;</td>
            <td> <input type="text" name="Address2"  size="50" value="<?php echo $Recordset1->Fields("Address2")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">City</div></td>
            <td> <input type="text" name="City"  size="50" value="<?php echo $Recordset1->Fields("City")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right" class="form"><div align="left">State</div></td>
            <td> <select name="State" id="State">
                <option value = "">Select State</option>
                <?php    if ($state__totalRows > 0){
    $state__index=0;
    $state->MoveFirst();
    WHILE ($state__index < $state__totalRows){
?>
                <option value="<?php echo  $state->Fields("id")?>" <?php if ($state->Fields("id")==$Recordset1->Fields("State")) echo "SELECTED";?>> 
                <?php echo  $state->Fields("statename");?> </option>
                <?php
      $state->MoveNext();
      $state__index++;
    }
    $state__index=0;  
    $state->MoveFirst();
  } ?>
              </select>
              Zip 
              <input type="text" name="PostalCode" size="15" value="<?php echo $Recordset1->Fields("PostalCode")?>"> 
            </td>
				  <?php if ($nonstateregion ==1) {
	   ?> 
			<tr> <td class="name">Region</td>
                  <td><select NAME="region" id="region">
                      <option>Select Region</option>
                      <?php  
					  $regionsel=$dbcon->Execute("SELECT * FROM region order by title asc") or DIE($dbcon->ErrorMsg());
					    while (!$regionsel->EOF)   {
?>
                      <OPTION VALUE="<?php echo  $regionsel->Fields("id")?>"  <?php if ($regionsel->Fields("id") ==  $Recordset1->Fields("region")) {echo  "selected";}?> >
				
                      <?php echo  $regionsel->Fields("title");?> </OPTION>
                      <?php
  $regionsel->MoveNext();
} ?>
                    </select></td>
                </tr><?php }?>
         
          <tr> 
            <td align="right" class="form"><div align="left">Country</div></td>
            <td> <input type="text" name="Country" size="45" value="<?php echo $Recordset1->Fields("Country")?>"> 
            </td>
          </tr>
          <?php 
				
		customfields ('field1text','field1','1ftype');
		customfields ('field2text','field2','2ftype');
		customfields ('field3text','field3','3ftype'); 
		customfields ('field4text','field4','4ftype');
		customfields ('field5text','field5','5ftype');
		customfields ('field6text','field6','6ftype');
		customfields ('field7text','field7','7ftype');
		customfields ('field8text','field8','8ftype');
		customfields ('field9text','field9','9ftype');
		customfields ('field10text','field10','10ftype');
		customfields ('field11text','field11','11ftype');
		customfields ('field12text','field12','12ftype');
		customfields ('field13text','field13','13ftype');
		customfields ('field14text','field14','14ftype');
		customfields ('field15text','field15','15ftype');
		customfields ('field16text','field16','16ftype');
		customfields ('field17text','field17','17ftype');
		customfields ('field18text','field18','18ftype');
		customfields ('field19text','field19','19ftype');
		customfields ('field20text','field20','20ftype');
		?>
          <tr> 
            <td align="right" class="form"><div align="left">Other Comments</div></td>
            <td> <textarea name="notes" cols="55" rows="7" wrap="VIRTUAL"><?php echo $Recordset1->Fields("notes")?></textarea></td>
          </tr>
          <tr> 
            <td colspan="2" align="right" class="form"><div align="left"> 
                <input type="submit" name="Submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <input type="submit" name="preview" value="Preview" onclick="return confirmSubmit('Please save this record first or all changes will be lost\nPress OK to continue or CANCEL to return and save you work')">
              </div></td>
          </tr>
        </table>
        
        <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
        <input type="hidden" name="MM_insert" value="true">
        <?php 
		}
		else { ?>
        <input type="hidden" name="MM_update" value="true">
        <?php } ?>
        <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
      </form>
<?php include("footer.php"); ?>
<?php
  $Recordset1->Close();
  $state->Close();
?>
