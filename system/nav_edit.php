<?php
require("Connections/freedomrising.php");

ob_start();
if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
	$MM_editTable  = "navtbl";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "nav_list.php";
    $MM_fieldsStr =
"name|value|sql|value|titleimg|value|titletext|value|titleti|value|linkfile|value|mfile|value|mcall1|value|mvar2|value|mcall2|value|repeat|value|linkextra|value|mvar1|value|linkfield|value|mvar1val|value|nosqlcode|value|nosql|value|templateid|value|modid|value|rss|value";
    $MM_columnsStr = "name|',none,''|sql|',none,''|titleimg|',none,''|titletext|',none,''|titleti|none,1,0|linkfile|',none,''|mfile|',none,''|mcall1|',none,''|mvar2|',none,''|mcall2|',none,''|repeat|',none,''|linkextra|',none,''|mvar1|',none,''|linkfield|',none,''|mvar1val|',none,''|nosqlcode|',none,''|nosql|none,1,0|templateid|',none,''|modid|',none,''|rss|',none,''";
    require ("../Connections/insetstuff.php");
  	require ("../Connections/dataactions.php");
  	ob_end_flush();
}

$Recordset1__MMColParam = "900000000";
if (isset($HTTP_GET_VARS["id"]))
  {$Recordset1__MMColParam = $HTTP_GET_VARS["id"];}

   $Recordset1=$dbcon->Execute("SELECT * FROM navtbl WHERE id = " . ($Recordset1__MMColParam) . "") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();

   $templatelab=$dbcon->Execute("SELECT id, name FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
   $templatelab_numRows=0;
   $templatelab__totalRows=$templatelab->RecordCount();
   	$modlab=$dbcon->Execute("SELECT id, name FROM modules ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $modlab_numRows=0;
   $modlab__totalRows=$modlab->RecordCount();
?>
<?php include ("header.php");?>
<script type="text/javascript">
function change(which) {
    document.getElementById('main').style.display = 'none';
	document.getElementById('advanced').style.display = 'none'; 
    document.getElementById(which).style.display = 'block';
    }
</script>
<form name="form1" method="POST" action="<?php echo $MM_editAction?>">
             <h2>Add/Edit Navigation File </h2>
			 <ul id="topnav">
	<li class="tab1"><a href="#" id="a0" onclick="change('main');" >Basic</a></li>
	<li class="tab2"><a href="#" id="a1" onclick="change('advanced');" >Advanced</a></li>
</ul>
 <div id="main" class="main" >
              
        <table width="100%" border="0" align="center">
          <tr valign="top"> 
            <td colspan="2" class="intitle">Navigation Info</td>
          </tr>
		  <tr valign="top"> 
            <td class="name">Navigation Name</td>
            <td> <input name="name" type="text" id="name" size="50" value="<?php echo $Recordset1->Fields("name")?>">            </td>
          </tr>
		    <tr valign="top">
            <td class="name">Associated Module (REQUIRED) </td>
            <td><select name="modid" id="modid">
			
                <option value="0">none</option>
                <?php
				if (!$_GET["id"]) { echo "<option value=\"19\"SELECTED> Content </option>";}
  if ($modlab__totalRows > 0){
    $modlab__index=0;
    $modlab->MoveFirst();
    WHILE ($modlab__index < $modlab__totalRows){
?>
                <option value="<?php echo  $modlab->Fields("id")?>"<?php if ($modlab->Fields("id")==$Recordset1->Fields("modid")) echo "SELECTED";?>> 
                <?php echo  $modlab->Fields("name");?> </option>
                <?php
      $modlab->MoveNext();
      $modlab__index++;
    }
    $modlab__index=0;  
    $modlab->MoveFirst();
  }
?>
              </select></td>
          </tr>
		            <tr valign="top"> 
            <td colspan="2" class="intitle">Navigation Content</td>
          </tr>
		            <tr valign="top"> 
            <td class="name">Navigation Title </td>
            <td> <p class="text"> 
                <input name="titletext" type="text" id="titletext" size="50" value="<?php echo $Recordset1->Fields("titletext")?>">
                <br>
                </td>
          </tr>
		    <tr valign="top"> 
            <td class="name">Title Image</td>
            <td> <p class="text"> 
                <input name="titleimg" type="text" id="titleimage" size="50" value="<?php echo $Recordset1->Fields("titleimg")?>">
              &nbsp;no path needed <br>
              <input name="titleti" type="checkbox" id="titleti" value="checkbox" <?php if (($Recordset1->Fields("titleti")) == "1") { echo "CHECKED";} ?>>
              Use Image </p></td>
          </tr>
        
				
		  
		  <tr valign="top"> 
            <td class="name"><p>Non-SQL Based Nav</p>
              <p> 
                <input name="nosql" type="checkbox" id="nosql" value="checkbox" <?php if (($Recordset1->Fields("nosql")) == "1") { echo "CHECKED";} ?>>
              </p></td>
            <td><textarea name="nosqlcode" cols="55" rows="20" wrap="VIRTUAL" id="sql"><?php echo $Recordset1->Fields("nosqlcode")?></textarea>            </td>
          </tr>

        
  
       
		  
         
		  
          <tr valign="top"> 
            <td colspan="2" class="intitle">Navigtation Template</td>
          </tr>
          <tr valign="top">
                  <td class="name">Template Override </td>
                  <td><select name="templateid" id="templateid">
                      <option value="">none</option>
                      <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                      <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$Recordset1->Fields("templateid")) echo "SELECTED";?>> 
                      <?php echo  $templatelab->Fields("name");?> </option>
                      <?php
      $templatelab->MoveNext();
      $templatelab__index++;
    }
    $templatelab__index=0;  
    $templatelab->MoveFirst();
  }
?>
              </select></td>
            </tr><tr>   <tr valign="top"> 
            <td class="name">Link CSS Override</td>
            <td><input name="linkextra" type="text" id="linkextra" value="<?php echo $Recordset1->Fields("linkextra")?>" size="40">            </td>
          </tr>
        </table>
		</div>
		<div id="advanced" >
		<table width="100%" border="0" align="center">
		  <tr> 
            <td colspan="2" class="intitle">RSS Based Navigation</td>
          </tr>
		<tr> 
            <td class="name">RSS Feed URL</td>
            <td><input name="rss" type="text" size="40" value="<?php echo $Recordset1->Fields("rss")?>"></td>
          </tr>
		    <tr> 
            <td colspan="2" class="intitle">Dynamic Navigation Settings</td>
          </tr>
          <tr><tr>
                  <td class="name">Pull Content From Section</td>
                  <td><select name="templateid" id="templateid">
                      <option value="">none</option>
                      <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                      <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$Recordset1->Fields("templateid")) echo "SELECTED";?>> 
                      <?php echo  $templatelab->Fields("name");?> </option>
                      <?php
      $templatelab->MoveNext();
      $templatelab__index++;
    }
    $templatelab__index=0;  
    $templatelab->MoveFirst();
  }
?>
                    </select></td>
                </tr>
          <tr>
                  <td class="name">Pull Content From Class</td>
                  <td><select name="templateid" id="templateid">
                      <option value="">none</option>
                      <?php
  if ($templatelab__totalRows > 0){
    $templatelab__index=0;
    $templatelab->MoveFirst();
    WHILE ($templatelab__index < $templatelab__totalRows){
?>
                      <option value="<?php echo  $templatelab->Fields("id")?>"<?php if ($templatelab->Fields("id")==$Recordset1->Fields("templateid")) echo "SELECTED";?>> 
                      <?php echo  $templatelab->Fields("name");?> </option>
                      <?php
      $templatelab->MoveNext();
      $templatelab__index++;
    }
    $templatelab__index=0;  
    $templatelab->MoveFirst();
  }
?>
                    </select></td>
                </tr>
		  <tr> 
            <td class="name">SQL</td>
            <td><textarea name="sql" cols="55" rows="5" wrap="VIRTUAL" id="sql"><?php echo $Recordset1->Fields("sql")?></textarea> 
            </td>
			
		 <tr> 
            <td colspan="2" class="intitle"><?php echo helpme("Link"); ?>Link for Dynamic Content </td>
          </tr>
          <td colspan="2" class="name"><div align="center">&lt;a href=1?2(or id)=3(or 
              $id)&gt; 4 (or $linktext) &lt;a&gt;<br>
              (where 3and 4 are field values from above sql)</div></td>
          <tr> 
            <td height="26" class="name">Link File (1)</td>
            <td><input name="linkfile" type="text" id="linkfile" size="50" value="<?php echo $Recordset1->Fields("linkfile")?>"></td>
          </tr>
          <tr> 
            <td class="name">Other File Var (2)</td>
            <td> 
              <input name="mvar1" type="text" id="mvar1" size="50" value="<?php echo $Recordset1->Fields("mvar1")?>"></td>
          </tr>
          <tr> 
            <td class="name">Other File Var Value (3)</td>
            <td><input name="mvar1val" type="text" id="mvar1val" size="50" value="<?php echo $Recordset1->Fields("mvar1val")?>"></td>
          </tr>
          <tr> 
            <td class="name">Other Link Field (4)</td>
            <td><input name="linkfield" type="text" id="linkfield" size="50" value="<?php echo $Recordset1->Fields("linkfield")?>"></td>
          </tr>
            <td colspan="2" class="intitle"><?php echo helpme("More Link"); ?>Dynamic More Link</td>
          </tr>
		   <tr> 
            <td class="name">Content Repeats before more link </td>
            <td><input name="repeat" type="text" id="repeat" size="5" value="<?php echo $Recordset1->Fields("repeat")?>"></td>
          </tr>
          <tr> 
            <td colspan="2" class="name"><div align="center">&lt;A HREF=1?list=2&amp;3=4&gt;more&lt;/a&gt; 
                (where 4 is the db field from above sql)</div></td>
          </tr>
          <tr> 
            <td class="name">More link file (1)</td>
            <td> <input name="mfile" type="text" id="mfile" size="50" value="<?php echo $Recordset1->Fields("mfile")?>"> 
            </td>
          </tr>
          <tr> 
            <td class="name">More list name (2)</td>
            <td><input name="mcall1" type="text" id="mcall1" size="50" value="<?php echo $Recordset1->Fields("mcall1")?>"> 
            </td>
          </tr>
          <tr> 
            <td class="name">More Var #2 (3)</td>
            <td> <input name="mvar2" type="text" id="mvar2" size="50" value="<?php echo $Recordset1->Fields("mvar2")?>"> 
            </td>
          </tr>
          <tr> 
            <td class="name">More Field #2 (4)</td>
            <td> <input name="mcall2" type="text" id="mcall2"  size="50" value="<?php echo $Recordset1->Fields("mcall2")?>"> 
            </td>
          </tr>
		</table>
		</div>
  <p> 
            <input type="submit" name="Submit" value="Submit">
          <input type="submit" name="MM_delete" value="Delete Record">
       
   <?php if (empty($HTTP_GET_VARS["id"])== TRUE) { ?>
        <input type="hidden" name="MM_insert" value="true">
        <?php 
		}
		else { ?>
        <input type="hidden" name="MM_update" value="true">
        <?php } ?>
        <input type="hidden" name="MM_recordId" value="<?php echo $HTTP_GET_VARS["id"]; ?>">
      </form><?php
  $Recordset1->Close();
  $templatelab->Close();
?>
<?php include ("footer.php");?>
