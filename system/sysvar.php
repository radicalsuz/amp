<?php
$mod_name = "system";

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
  
  if (isset($MM_update) && (isset($MM_recordId))) {
//    $MM_editConnection = $MM__STRING;

$stripit= substr(trim($basepath), -1); 
if ($stripit != "/") { $basepath = $basepath."/";}
    $MM_editTable  = "sysvar";
    $MM_editColumn = "id";
    $MM_recordId = "" . $MM_recordId . "";
    $MM_editRedirectUrl = "sysvar.php";
    $MM_fieldsStr = "searchinstallbase|value|basepath|value|filepath|value|emfaq|value|emendorse|value|emgroup|value|emmedia|value|emride|value|emhousing|value|emcalendar|value|websitename|value|emfrom|value|sendmailpath|value|webname|value|metadescription|value|metacontent|value|template|value|emailfromname|value|thumb|value|optw|value|optl|value|cachesecs|value";
    $MM_columnsStr = "searchinstallbase|',none,''|basepath|',none,''|filepath|',none,''|emfaq|',none,''|emendorse|',none,''|emgroup|',none,''|emmedia|',none,''|emride|',none,''|emhousing|',none,''|emcalendar|',none,''|websitename|',none,''|emfrom|',none,''|sendmailpath|',none,''|webname|',none,''|metadescription|',none,''|metacontent|',none,''|template|',none,''|emailfromname|',none,''|thumb|',none,''|optw|',none,''|optl|',none,''|cacheSecs|',none,''";
  
$inupdate = $dbcon->Execute("Update moduletext set templateid = " . $_POST[indextemplate] . " where id =2") or DIE($dbcon->ErrorMsg());  
 require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
}  
  
   $setsysvar=$dbcon->Execute("SELECT s.*, t.name FROM sysvar s left join template t on s.template=t.id  WHERE s.id =1") or DIE($dbcon->ErrorMsg());
   $intemp=$dbcon->Execute("select m.templateid, t.name from moduletext m left join template t on m.templateid = t.id where m.id=2") or DIE($dbcon->ErrorMsg());
   $templs=$dbcon->Execute("select id, name from template") or DIE($dbcon->ErrorMsg());

  
  ?> <?php include ("header.php");
?>


<h2><?php echo helpme("Overview"); ?>Set system variables </h2>

<form method="post" action="<?php echo $MM_editAction?>" name="form1">
        <table width="100%" border=0 align="center" cellpadding=2 cellspacing=0 class="name">
          <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap><div align="left"><?php echo helpme("Web Settings"); ?>Web 
                Settings</div></td>
          </tr><tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Site Name</div></td>
            <td><input name="websitename" type="text" id="websitename" value="<?php echo $setsysvar->Fields("websitename")?>" size="32"></td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Site URL</div></td>
            <td><input name="basepath" type="text" id="basepath" value="<?php echo $setsysvar->Fields("basepath")?>" size="32"></td>
          </tr>
		  
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Meta Description</div></td>
            <td><textarea name="metadescription" cols="35" rows="4" wrap="VIRTUAL" id="metadescription"><?php echo $setsysvar->Fields("metadescription")?></textarea></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Meta Content</div></td>
            <td><textarea name="metacontent" cols="35" rows="4" wrap="VIRTUAL" id="metacontent"><?php echo $setsysvar->Fields("metacontent")?></textarea></td>
          </tr>
          <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap><div align="left"><?php echo helpme("System E-Mails"); ?>System 
                E-mails</div></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">User Submitted Article 
                e-mail:</div></td>
            <td> <input type="text" name="emendorse" value="<?php echo $setsysvar->Fields("emendorse")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">E-Mail Blast From address:</div></td>
            <td> <input type="text" name="emmedia" value="<?php echo $setsysvar->Fields("emmedia")?>" size="32"> 
            </td>
          </tr>
		  
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">E-Mail Blast From name:</div></td>
            <td> <input type="text" name="emailfromname" value="<?php echo $setsysvar->Fields("emailfromname")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Web Admin Alerts From:</div></td>
            <td> <input type="text" name="emfrom" value="<?php echo $setsysvar->Fields("emfrom")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">System Administrator's 
                E-Mail :</div></td>
            <td> <input type="text" name="emfaq" value="<?php echo $setsysvar->Fields("emfaq")?>" size="32"> 
            </td>
          </tr>
        
		   <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap><div align="left">Template Settings</div></td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Default Template</div></td>
            <td><select name="template">
			<option value="<?php echo $setsysvar->Fields("template") ;?>"><?php echo $setsysvar->Fields("name") ;?></option>
			<?php while (!$templs->EOF) { ?>
			<option value="<?php echo $templs->Fields("id") ;?>"><?php echo $templs->Fields("name") ;?></option>
			<?php $templs->MoveNext();
			 }  
			?>
			</select>
			</td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Index Template</div></td>
            <td><select name="indextemplate">
			<option value="<?php echo $intemp->Fields("templateid") ;?>"><?php echo $intemp->Fields("name") ;?></option>
			<?php $templs->MoveFirst();
			while (!$templs->EOF) { ?>
			<option value="<?php echo $templs->Fields("id") ;?>"><?php echo $templs->Fields("name") ;?></option>
			<?php $templs->MoveNext(); 
			}  
			 ?>
			</select>
			</td>
          </tr>
		    <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap><div align="left">Photo Settings</div></td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Optimized Image Width (wide 
                images)</div></td>
            <td> <input name="optw" type="text" id="optw" value="<?php echo $setsysvar->Fields("optw")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Optimized Image Width (narrow 
                iamges)</div></td>
            <td> <input name="optl" type="text" id="optl" value="<?php echo $setsysvar->Fields("optl")?>" size="32"> 
            </td>
          </tr><tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Thumbnail Image Width:</div></td>
            <td> <input name="thumb" type="text" id="thumb" value="<?php echo $setsysvar->Fields("thumb")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap><div align="left">Cache Settings</div></td>
          </tr><tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Cache Seconds:</div></td>
            <td> <input name="cachesecs" type="text" id="cachesecs" value="<?php echo $setsysvar->Fields("cacheSecs")?>" size="32"> 
            </td>
          </tr>
          <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap>&nbsp;</td>
          </tr>
          <tr valign="baseline"> 
            <td colspan="2" align="right" nowrap><div align="left"> 
                <input type="submit" value="Update Record" name="submit">
              </div></td>
          </tr>
        </table>
  <input type="hidden" name="MM_update" value="true">
  <input type="hidden" name="MM_recordId" value="<?php echo $setsysvar->Fields("id") ?>">
</form>
<p>&nbsp;</p>

<?php
  $setsysvar->Close(); ?>
 <?php include ("footer.php");?>

