<?php
$modid=3;
  require("Connections/freedomrising.php");
?>
<?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
  ob_start();

  if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
         //Delete cached versions of output file

  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "housing";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "housing_list.php";
    $MM_fieldsStr = "firstname|value|lastname|value|org|value|email|value|phone|value|avalible|value|beds|value|floor|value|tents|value|access|value|location|value|transport|value|parking|value|cooking|value|children|value|smoking|value|info|value|publish|value|board|value|pemail|value|need|value";
    $MM_columnsStr = "firstname|',none,''|lastname|',none,''|organization|',none,''|email|',none,''|phone|',none,''|avalible|',none,''|beds|',none,''|floor|',none,''|tents|',none,''|access|',none,''|location|',none,''|transport|',none,''|parking|',none,''|cooking|',none,''|children|',none,''|smoking|',none,''|info|',none,''|publish|',none,''|board|',none,''|pemail|',none,''|need|',none,''";
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }  
$housing__MMColParam = "9000000";
if (isset($HTTP_GET_VARS["id"]))
  {$housing__MMColParam = $HTTP_GET_VARS["id"];}

   $housing=$dbcon->Execute("SELECT * FROM housing WHERE id = " . ($housing__MMColParam) . "") or DIE($dbcon->ErrorMsg());
?>
<?php include("header.php"); ?>
<h2>Edit Posting on the Housing Board </h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
        <table width="500" border=0 align="center" cellpadding=2 cellspacing=0 class="name">
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left"><font color="#FF0000" size="3"><strong>Publish:</strong></font></div></td>
            <td><input type="checkbox" name="publish" value="1"<?php if ($housing->Fields("publish") == 1) {echo "checked";} ?> > </td>
          </tr>
      
	      <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap><div align="left">
              <input name="need" type="radio" value="need" <?php if ($housing->Fields("need") =="need") {echo "checked";} ?>>
              Need housing &nbsp;&nbsp;&nbsp;
              <input name="need" type="radio" value="have" <?php if ($housing->Fields("need") =="have") {echo "checked";} ?>>
              Have Housing </div>              </td>
          </tr>
	      <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">First Name</div></td>
            <td><input type="text" name="firstname" value="<?php echo $housing->Fields("firstname")?>" size="25"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Last Name</div></td>
            <td><input type="text" name="lastname" value="<?php echo $housing->Fields("lastname")?>" size="25"></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Organization:</div></td>
            <td> <input type="text" name="org" value="<?php echo $housing->Fields("org")?>" size="50"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">E-mail:</div></td>
            <td> <input type="text" name="email" value="<?php echo $housing->Fields("email")?>" size="32"> 
            </td>
          </tr>
		   <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Private E-mail:</div></td>
            <td> <input type="text" name="pemail" value="<?php echo $housing->Fields("pemail")?>" size="32"> 
            </td>
          </tr>
		  
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Phone:</div></td>
            <td> <input type="text" name="phone" value="<?php echo $housing->Fields("phone")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">When Available:</div></td>
            <td> <input type="text" name="avalible" value="<?php echo $housing->Fields("avalible")?>" size="50"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Number of Beds/Sofas:</div></td>
            <td> <input type="text" name="beds" value="<?php echo $housing->Fields("beds")?>" size="50"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Floor Space:</div></td>
            <td> <input type="text" name="floor" value="<?php echo $housing->Fields("floor")?>" size="50"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Tents in Yard:</div></td>
            <td> <input type="text" name="tents" value="<?php echo $housing->Fields("tents")?>" size="50"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"> <div align="left">Handicapped accessibility: 
              </div></td>
            <td> <input type="text" name="access" value="<?php echo $housing->Fields("access")?>" size="50"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
  <table width="500" border=0 align="center" cellpadding=2 cellspacing=0 class="name">
    <tr> 
      <td nowrap align="right" valign="top"> 
        <div align="left">Location (do not post address):<br>
          <textarea name="location" cols="30" rows="3" wrap="VIRTUAL"><?php echo $housing->Fields("location")?></textarea>
          <br>
        </div>
      </td>
      <td valign="baseline">Access to Public Transportation <br>
        <textarea name="transport" cols="30" rows="3" wrap="VIRTUAL"><?php echo $housing->Fields("transport")?></textarea>
      </td>
    </tr>
    <tr> 
      <td nowrap align="right" valign="top"> 
        <div align="left">Parking: <br>
          <textarea name="parking" cols="30" rows="3" wrap="VIRTUAL"><?php echo $housing->Fields("parking")?></textarea>
        </div>
      </td>
      <td valign="baseline">Cooking: <br>
        <textarea name="cooking" cols="30" rows="3" wrap="VIRTUAL"><?php echo $housing->Fields("cooking")?></textarea>
      </td>
    </tr>
    <tr> 
      <td nowrap align="right" valign="top"> 
        <div align="left">Can you accommodate children?<br>
          <textarea name="children" cols="30" rows="3" wrap="VIRTUAL"><?php echo $housing->Fields("children")?></textarea>
        </div>
      </td>
      <td valign="baseline">Smoking: <br>
        <textarea name="smoking" cols="30" rows="3" wrap="VIRTUAL"><?php echo $housing->Fields("smoking")?></textarea>
      </td>
    </tr>
    <tr> 
      <td nowrap align="right" valign="top" colspan="2"> 
        <div align="left">Other Information: <br>
          <textarea name="info" cols="50" rows="5" wrap="VIRTUAL"><?php echo $housing->Fields("info")?></textarea>
        </div>
      </td>
    </tr>
    <tr valign="baseline"> 
      <td nowrap align="right" colspan="2"><input type="hidden" name="board" value="2"> 
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
      </td>
    </tr>
  </table>
  </form>
<p>&nbsp;</p>
<?php include("footer.php"); ?>
<?php
  $housing->Close();
?>

