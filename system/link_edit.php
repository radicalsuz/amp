<?php

$modid=11;

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");

$obj = new SysMenu;

ob_start();

if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) ) {
    $MM_editTable  = "links";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "link_list.php";
    $MM_fieldsStr = "name|value|desc|value|select|value|url|value|type|value|subtype|value|catagory|value|checkbox|value|image|value";
    $MM_columnsStr = "linkname|',none,''|description|',none,''|linktype|none,none,NULL|url|',none,''|type|none,none,NULL|subtype|none,none,NULL|catagory|none,none,NULL|publish|none,1,0|image|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
  
	if ($MM_reltype) {
		if ($MM_insert) {
			$getid=$dbcon->Execute("select id from links order by id desc limit 1") or DIE($dbcon->ErrorMsg());
			$MM_recordId = $getid->Fields("id") ;
 		} 
		$reldelete=$dbcon->Execute("Delete FROM linksreltype WHERE linkid =$MM_recordId") or DIE($dbcon->ErrorMsg());
   		if (!$MM_delete) {
    		while (list($k, $v) = each($reltype)) { 
				$relupdate=$dbcon->Execute("INSERT INTO linksreltype VALUES ( $MM_recordId,$v)") or DIE($dbcon->ErrorMsg());
			}
		}
	}
	ob_end_flush();
}
 
$called__MMColParam = "90000000";
if (isset($_GET["id"])){ $called__MMColParam = $_GET["id"];}
$called=$dbcon->Execute("SELECT * FROM links WHERE id = " . ($called__MMColParam) . "") or DIE("36".$dbcon->ErrorMsg());
if (isset($id)) {$typevar=$called->Fields("type");}
else {$typevar=1;}
if (!$typevar) {$typevar=1;}
$typelab=$dbcon->Execute("SELECT id, type FROM articletype where id = ".$typevar."") or DIE("39".$dbcon->ErrorMsg());
$related=$dbcon->Execute("SELECT t.type, r.typeid FROM linksreltype r, articletype t where  t.id =r.typeid and linkid = " . ($called__MMColParam) . "") or DIE("40".$dbcon->ErrorMsg());
$linktype=$dbcon->Execute("SELECT * FROM linktype") or DIE($dbcon->ErrorMsg());
$linktype_numRows=0;
$linktype__totalRows=$linktype->RecordCount();

?><?php include ("header.php"); ?>

<h2>Edit Link </h2>
<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form1">
        <table width="100%" border=0 align="center" cellpadding=2 cellspacing=0 class="name">
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Name:</div></td>
            <td> <input type="text" name="name" value="<?php echo $called->Fields("linkname")?>" size="45"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td align="right" valign="top" nowrap><div align="left">Desc:</div></td>
            <td> <textarea name="desc" cols="40" wrap="VIRTUAL" rows="4"><?php echo $called->Fields("description")?></textarea> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Link type</div></td>
            <td> <select name="select">
                <?php
  if ($linktype__totalRows > 0){
    $linktype__index=0;
    $linktype->MoveFirst();
    WHILE ($linktype__index < $linktype__totalRows){
?>
                <OPTION VALUE="<?php echo  $linktype->Fields("id")?>"<?php if ($linktype->Fields("id")==$called->Fields("linktype")) echo "SELECTED";?>> 
                <?php echo  $linktype->Fields("name");?> </OPTION>
                <?php
      $linktype->MoveNext();
      $linktype__index++;
    }
    $linktype__index=0;  
    $linktype->MoveFirst();
  }
?>
              </select> </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Url:</div></td>
            <td> <input type="text" name="url" value="<?php echo $called->Fields("url")?>" size="45"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Thumbnail:</div></td>
            <td> <input type="text" name="image" value="<?php echo $called->Fields("image")?>" size="45"><br>&nbsp;<a href="imgdir.php" target="_blank">view images</a> 
                | <a href="imgup.php" target="_blank">upload image</a><br>
 
            </td>
          </tr>

         <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Related Sections:</div></td>
            <td> <select multiple name='reltype[]' size='8'>
			<?php while ((!$related->EOF)){ ?>
                <option value="<?php echo  $related->Fields("typeid")?>" selected ><?php echo  $related->Fields("type")?></option>
			<?php 	$related->MoveNext(); }?>
                <?php echo $obj->select_type_tree($MX_top); ?>
              </select>
              </td>
          </tr>
          <tr> 
            <td> <div align="left">publish</div></td>
            <td> <input <?php If (($called->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="checkbox"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap> <div align="left"><input name="submit" type="submit" value="Save Changes">
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
<?php

include ("footer.php");
$called->Close();
$linktype->Close();

?>
