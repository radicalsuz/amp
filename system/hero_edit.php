<?php
$modid=4;
  require("Connections/freedomrising.php");
    include("Connections/menu.class.php");
 $obj = new Menu; 
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
  
    if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
    
 
  
//    $MM_editConnection = $MM__STRING;
    $MM_editTable  = "heros";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "hero_list.php";
    $MM_fieldsStr = "publish|value|name|value|description|value|type|value|picture|value|picsel|value";
    $MM_columnsStr = "publish|',none,''|name|',none,''|description|',none,''|typeid|',none,''|picture|',none,''|picsel|',none,''";
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();
   }
 
 $hero__MMColParam = "90000000";
if (isset($HTTP_GET_VARS["id"]))
  {$hero__MMColParam = $HTTP_GET_VARS["id"];}

   $hero=$dbcon->Execute("SELECT * FROM heros WHERE id = " . ($hero__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $hero_numRows=0;
   $hero__totalRows=$hero->RecordCount();

if (isset($id)) {$typevar=hero->Fields("typeid");}
else {$typevar=1;}
  $typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE($dbcon->ErrorMsg());
?><?php  include("header.php"); ?>
<h2>Add/Edit Land Use Hero</h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
        <table width="100%" border="0" cellspacing="0" cellpadding="3" class="text">
          <tr> 
            <td class="name">Publish</td>
            <td> <input <?php If (($hero->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="publish"> 
            </td>
          </tr>
          <tr> 
            <td class="name"> Name</td>
            <td> <input name="name" type="text" id="name" value="<?php echo $hero->Fields("firstname")?>" size="40"> 
            </td>
          </tr>
          <tr> 
            <td class="name">Description</td>
            <td> <textarea name="description" cols="50" rows="10" wrap="VIRTUAL" id="description"><?php echo $hero->Fields("longanswer")?></textarea> 
            </td>
          </tr>
          <tr> 
            <td class="name">Type </td>
            <td> <select name="type">
	  <OPTION VALUE="<?php echo  $typelab->Fields("id")?>" SELECTED><?php echo  $typelab->Fields("type")?></option>
	  
	  
	  <?php echo $obj->select_type_tree(0); ?></Select> </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Filename</td>
            <td> <input type="text" name="picture" size="50" value="<?php echo $hero->Fields("picture")?>"> 
              <br> &nbsp;<a href="imgdir.php" target="_blank">view images</a> 
              | <a href="imgup.php" target="_blank">upload image</a> </td>
          </tr>
          <tr> 
            <td valign="top" class="name">Image Selection</td>
            <td class="text"> <input type="radio" name="picsel" value="original" <?php if ($hero->Fields("picsel") == "original") echo("CHECKED");?>>
              Original 
              <input name="picsel" type="radio" value="pic" <?php if ($hero->Fields("picsel") == "pic") echo("CHECKED");?>>
              Optimized </td>
          </tr>
          <tr> 
            <td colspan="2"> <input name="submit" type="submit" value="Save Changes"> 
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
<?php include ("footer.php")?>
