<?php
$modid=2;
  require("Connections/freedomrising.php");
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();

  if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "ride";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "ride_list.php";
    $MM_fieldsStr = "firstname|value|lastname|value|phone|value|email|value|depatingfrom|value|depaturedate|value|returningto|value|returndate|value|numpeople|value|ride|value|commets|value|publish|value|board|value";
    $MM_columnsStr = "firstname|',none,''|lastname|',none,''|phone|',none,''|email|',none,''|depatingfrom|',none,''|depaturedate|',none,''|returningto|',none,''|returndate|',none,''|numpeople|',none,''|need|',none,''|commets|',none,''|publish|',none,''|board|',none,''";
   require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }  
   
$ride__MMColParam = "90000000";
if (isset($HTTP_GET_VARS["id"]))
  {$ride__MMColParam = $HTTP_GET_VARS["id"];}
?><?php
   $ride=$dbcon->Execute("SELECT * FROM ride WHERE id = " . ($ride__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $ride_numRows=0;
   $ride__totalRows=$ride->RecordCount();
?>
<?php include("header.php"); ?>

<h2>Update Ride Board </h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="rieinput">
        <table width="90%" border="0" class="name">
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left"><font color="#FF0000" size="3"><strong>Publish:</strong></font></div></td>
            <td><input type="checkbox" name="publish" value="1"<?php if ($ride->Fields("publish") == 1) {echo "checked";} ?> > 
            </td>
          </tr>
          <td align="right"><div align="left">First Name</div></td>
          <td> <input name="firstname" type="text" value="<?php echo $ride->Fields("firstname")?>" size="35" > 
          </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Last Name</div></td>
            <td><input name="lastname" type="text" value="<?php echo $ride->Fields("lastname")?>" size="35"></td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Phone Number</div></td>
            <td> <input type="text" name="phone" size="45" value="<?php echo $ride->Fields("phone")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">E-mail</div></td>
            <td> <input type="text" name="email" size="45" value="<?php echo $ride->Fields("email")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Location Depating From</div></td>
            <td> <input type="text" name="depatingfrom" size="45" value="<?php echo $ride->Fields("depatingfrom")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Depature Date</div></td>
            <td> <input type="text" name="depaturedate" value="<?php echo $ride->Fields("depaturedate")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Location Returning To</div></td>
            <td> <input type="text" name="returningto" size="45" value="<?php echo $ride->Fields("returningto")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Return Date</div></td>
            <td> <input type="text" name="returndate" value="<?php echo $ride->Fields("returndate")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Number of People</div></td>
            <td> <input type="text" name="numpeople" size="15" value="<?php echo $ride->Fields("numpeople")?>"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Need a Ride</div></td>
            <td> <input <?php If ($ride->Fields("need") == "need") echo("CHECKED");?> type="radio" name="ride" value="need"> 
            </td>
          </tr>
          <tr> 
            <td align="right"><div align="left">Have a Ride to Offer</div></td>
            <td> <input <?php If ($ride->Fields("need") == "have") echo("CHECKED");?> type="radio" name="ride" value="have"> 
            </td>
          </tr>
          <tr> 
            <td align="right">Commets</td>
            <td> <textarea name="commets" cols="40" wrap="VIRTUAL" rows="4"><?php echo htmlspecialchars( $ride->Fields("commets"))?></textarea> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" align="right"><input type="hidden" name="board" value="2"> 
              <input name="submit" type="submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
                <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
                <input type="hidden" name="MM_insert" value="true">
                <?php 
		}
		else { ?>
                <input type="hidden" name="MM_update" value="true">
                <?php } ?>
                <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>"></td>
          </tr>
        </table>
  <p> 
    <input type="submit" name="Submit" value="Save">
  </p>
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $ride->Fields("id") ?>">
</form>
<form name="delete" method="POST" action="<?php echo $MM_editAction?>">
  <input type="submit" name="Delete" value="Delete">
  <input type="hidden" name="MM_delete" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $ride->Fields("id") ?>">
</form>
<?php include("footer.php"); ?>
<?php
  $ride->Close();
?>
