<?php
  require("Connections/freedomrising.php");
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
  // *** Update Record: set variables
  
   if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
  
    $MM_editTable  = "template";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "template_list.php";
    $MM_fieldsStr =
"header1c|value|header2c|value|header3c|value|header4c|value|header5c|value|header6c|value|footerc|value|rnav1|value|rnav2|value|rnav3|value|rnav4|value|rnav5|value|rnav6|value|rnav7|value|rnav8|value|rnav9|value|rnav10|value|rnav11|value|rnav12|value|rnav13|value|lnav1|value|lnav2|value|lnav3|value|lnav4|value|lnav5|value|lnav6|value|lnav7|value|lnav8|value|lnav9|value|lnav10|value|lnav11|value|lnav12|value|lnav13|value|css|value|fp|value|imgpath|value|name|value";
    $MM_columnsStr = "header1|',none,''|header2|',none,''|header3|',none,''|header4|',none,''|header5|',none,''|header6|',none,''|footer|',none,''|rnav1|',none,''|rnav2|',none,''|rnav3|',none,''|rnav4|',none,''|rnav5|',none,''|rnav6|',none,''|rnav7|',none,''|rnav8|',none,''|rnav9|',none,''|rnav10|',none,''|rnav11|',none,''|rnav12|',none,''|rnav13|',none,''|lnav1|',none,''|lnav2|',none,''|lnav3|',none,''|lnav4|',none,''|lnav5|',none,''|lnav6|',none,''|lnav7|',none,''|lnav8|',none,''|lnav9|',none,''|lnav10|',none,''|lnav11|',none,''|lnav12|',none,''|lnav13|',none,''|css|',none,''|fp|',none,''|imgpath|',none,''|name|',none,''";
	
   
  require ("../Connections/insetstuff.php");
  }

require ("../Connections/dataactions.php");
ob_end_flush();

$Recordset1__MMColParam = "9000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM template WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?>
<?php include ("header.php");?>
      <h2><?php echo helpme("Overview"); ?>Edit/Add Template File </h2>
      <form ACTION="<?php echo $MM_editAction?>" METHOD="POST">
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
<br>
        <table width="90%" border="0">
          <tr> 
            <td class="name">Name</td>
            <td><input name="name" type="text" id="name" size="45" maxlength="50" value="<?php echo $Recordset1->Fields("name")?>"></td>
          </tr>
          <tr> 
            <td colspan="2" class="intitle"><?php echo helpme("main html template"); ?>main html template</td>
          </tr>
          <tr> 
            <td class="name"><p>Template Field #1</p>
              <p> header<br>
                start left nav</p>
              </td>
            <td> <textarea name="header1c" cols="65" rows="10" wrap="VIRTUAL" id="header1c"><?php echo $Recordset1->Fields("header1")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>Template Field #2</p>
              <p>end left nav<br>
                start main</p></td>
            <td> <textarea name="header4c" cols="65" rows="10" wrap="VIRTUAL" id="header4c"><?php echo $Recordset1->Fields("header4")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>Template Field #3</p>
              <p>end main<br>
                start right nav</p></td>
            <td> <textarea name="header5c" cols="65" rows="10" wrap="VIRTUAL" id="header5c"><?php echo $Recordset1->Fields("header5")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>Template Field #4<br>
                <br>
                end right nav <br>
                footer</p>
              </td>
            <td> <textarea name="footerc" cols="65" rows="10" wrap="VIRTUAL" id="footerc"><?php echo $Recordset1->Fields("footer")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" class="intitle"><?php echo helpme("side navigation"); ?>left side navigation (admin edit 
              only)</td>
          </tr>
          <tr> 
            <td class="name"><p>left nav #1</p>
              <p>start heading row</p></td>
            <td> <textarea name="lnav3" cols="65" rows="6" wrap="VIRTUAL" id="lnav3"><?php echo $Recordset1->Fields("lnav3")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>left nav #2</p>
              <p>end heading row</p></td>
            <td> <textarea name="lnav4" cols="65" rows="6" wrap="VIRTUAL" id="lnav4"><?php echo $Recordset1->Fields("lnav4")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>left nav #3</p>
              <p>start content table row<br>
                (repeats) </p></td>
            <td> <textarea name="lnav7" cols="65" rows="6" wrap="VIRTUAL" id="lnav7"><?php echo $Recordset1->Fields("lnav7")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>left nav #4</p>
              <p>end content table row<br>
                (repeats) </p></td>
            <td> <textarea name="lnav8" cols="65" rows="6" wrap="VIRTUAL" id="lnav8"><?php echo $Recordset1->Fields("lnav8")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>left nav #5</p>
              <p> spacer</p></td>
            <td> <textarea name="lnav9" cols="65" rows="6" wrap="VIRTUAL" id="lnav9"><?php echo $Recordset1->Fields("lnav9")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" class="intitle"><?php echo helpme("side navigation"); ?>right side side navigation (admin 
              edit only)</td>
          </tr>
          <tr> 
            <td class="name"><p>right nav #1</p>
              <p>start heading row</p></td>
            <td> <textarea name="rnav3" cols="65" rows="6" wrap="VIRTUAL" id="rnav3"><?php echo $Recordset1->Fields("rnav3")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>right nav #2</p>
              <p>close heading row</p></td>
            <td> <textarea name="rnav4" cols="65" rows="6" wrap="VIRTUAL" id="rnav4"><?php echo $Recordset1->Fields("rnav4")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>right nav #3</p>
              <p>start content row<br>
                (repeats) </p></td>
            <td> <textarea name="rnav7" cols="65" rows="6" wrap="VIRTUAL" id="rnav7"><?php echo $Recordset1->Fields("rnav7")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>right nav #4</p>
              <p>end content row<br>
                (repeats) </p></td>
            <td> <textarea name="rnav8" cols="65" rows="6" wrap="VIRTUAL" id="rnav8"><?php echo $Recordset1->Fields("rnav8")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name"><p>right nav #5</p>
              <p>spacer</p></td>
            <td> <textarea name="rnav9" cols="65" rows="6" wrap="VIRTUAL" id="rnav9"><?php echo $Recordset1->Fields("rnav9")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td colspan="2" class="intitle"><?php echo helpme("other template features"); ?>other template features</td>
          </tr>
          <tr> 
            <td class="name">css file</td>
            <td> <input name="css" type="text" id="css" value="<?php echo $Recordset1->Fields("css")?>" size="50"> 
            </td>
          </tr>
          <tr> 
            <td class="name">img path</td>
            <td> <input name="imgpath" type="text" id="imgpath" value="<?php echo $Recordset1->Fields("imgpath")?>" size="50"> 
              <?php echo $Recordset1->Fields("id") ?> </td>
          </tr>
        </table>
  <p> 
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
</form>

<?php
  $Recordset1->Close();
?>
<?php include ("footer.php");?>
