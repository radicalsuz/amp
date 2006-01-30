<?php
$mod_name = "system";

  require("Connections/freedomrising.php");
?><?php
  // *** Edit Operations: declare Tables
  $MM_editAction = $_SERVER['PHP_SELF'];
  if ($_SERVER['QUERY_STRING']) {
    $MM_editAction = $MM_editAction . "?" . $_SERVER['QUERY_STRING'];
  }

  $MM_abortEdit = 0;
  $MM_editQuery = "";
    ob_start();
?><?php
  // *** Update Record: set variables
if (isset($_REQUEST['MM_update']) && (isset($_REQUEST['MM_recordId']))) {
//    $MM_editConnection = $MM__STRING;

	$stripit= substr(trim($basepath), -1); 
	if ($stripit != "/") { $basepath = $basepath."/";}
    $MM_editTable  = "sysvar";
    $MM_editColumn = "id";
    $MM_recordId = "" . $_REQUEST['MM_recordId'] . "";
    $MM_editRedirectUrl = "sysvar.php";
    $MM_fieldsStr = "searchinstallbase|value|basepath_field|value|filepath|value|emfaq|value|emendorse|value|emgroup|value|emmedia|value|emride|value|emhousing|value|emcalendar|value|websitename|value|emfrom|value|sendmailpath|value|webname|value|metadescription|value|metacontent|value|template|value|emailfromname|value|thumb|value|optw|value|optl|value|cachesecs|value|email_tool|value|dia_user|value|dia_key|value|phplist_bounce_host|value|phplist_bounce_user|value|phplist_bounce_password|value";
    $MM_columnsStr = "searchinstallbase|',none,''|basepath|',none,''|filepath|',none,''|emfaq|',none,''|emendorse|',none,''|emgroup|',none,''|emmedia|',none,''|emride|',none,''|emhousing|',none,''|emcalendar|',none,''|websitename|',none,''|emfrom|',none,''|sendmailpath|',none,''|webname|',none,''|metadescription|',none,''|metacontent|',none,''|template|',none,''|emailfromname|',none,''|thumb|',none,''|optw|',none,''|optl|',none,''|cacheSecs|',none,''|email_tool|',none,''|dia_user|',none,''|dia_key|',none,''|phplist_bounce_host|',none,''|phplist_bounce_user|',none,''|phplist_bounce_password|',none,''";
  	$inupdate = $dbcon->Execute("Update moduletext set templateid = " . $_REQUEST['indextemplate'] . " where id =2") or DIE($dbcon->ErrorMsg());  


// PHPLIST CONFIG UPDATES
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_domain'] . "' where item ='domain' ") or DIE($dbcon->ErrorMsg());  
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_website'] . "' where item ='website' ") or DIE($dbcon->ErrorMsg());  
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_admin_address'] . "' where item ='admin_address' ") or DIE($dbcon->ErrorMsg());  
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_report_address'] . "' where item ='report_address' ") or DIE($dbcon->ErrorMsg());  
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_message_from_address'] . "' where item ='message_from_address' ") or DIE($dbcon->ErrorMsg());  
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_message_from_name'] . "' where item ='message_from_name' ") or DIE($dbcon->ErrorMsg());  
	$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_message_replyto_address'] . "' where item ='message_replyto_address' ") or DIE($dbcon->ErrorMsg());  
	$sql ="Update phplist_admin set password = '" . $_REQUEST['phplist_admin_password'] . "', email = '" . $_REQUEST['phplist_admin_email'] . "' where id ='1' ";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());  

// PUNBB CONFIG UPDATES
	$sql ="Update punbb_config set conf_value = '" . $_REQUEST['websitename'] . " Forum' where conf_name = 'o_board_title' ";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg()); 
	 
	$sql ="Update punbb_config set conf_value = '" . $_REQUEST['emfaq'] . "' where conf_name = 'o_admin_email' ";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());  
    $sql ="Update punbb_config set conf_value = '" . $_REQUEST['emfaq'] . "' where conf_name = 'o_webmaster_email' ";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());  
    $sql ="Update punbb_config set conf_value = '" . $_REQUEST['emfaq'] . "' where conf_name = 'o_mailing_list' ";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());  
    $sql ="Update punbb_config set conf_value = '" . $_REQUEST['basepath'] . "/punbb' where conf_name = 'o_base_url' ";
	$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());  	
	

	#$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_'] . "' where item ='' ") or DIE($dbcon->ErrorMsg());  
	#$dbcon->Execute("Update phplist_config set value = '" . $_REQUEST['phplist_'] . "' where item ='' ") or DIE($dbcon->ErrorMsg());  

 	require ("../Connections/insetstuff.php");
  	require ("../Connections/dataactions.php");
}  
  
   $setsysvar=$dbcon->Execute("SELECT s.*, t.name FROM sysvar s left join template t on s.template=t.id  WHERE s.id =1") or DIE($dbcon->ErrorMsg());
#php list values
   $phplist_website=$dbcon->Execute("SELECT value from phplist_config where item = 'website'") or DIE($dbcon->ErrorMsg());
   $phplist_domain=$dbcon->Execute("SELECT value from phplist_config where item = 'domain'") or DIE($dbcon->ErrorMsg());
   $phplist_admin_address=$dbcon->Execute("SELECT value from phplist_config where item = 'admin_address'") or DIE($dbcon->ErrorMsg());
   $phplist_report_address=$dbcon->Execute("SELECT value from phplist_config where item = 'report_address'") or DIE($dbcon->ErrorMsg());
   $phplist_message_from_address=$dbcon->Execute("SELECT value from phplist_config where item = 'message_from_address'") or DIE($dbcon->ErrorMsg());
   $phplist_message_from_name=$dbcon->Execute("SELECT value from phplist_config where item = 'message_from_name'") or DIE($dbcon->ErrorMsg());
   $phplist_message_replyto_address=$dbcon->Execute("SELECT value from phplist_config where item = 'message_replyto_address'") or DIE($dbcon->ErrorMsg());
   $phplist_admin=$dbcon->Execute("SELECT * from phplist_admin where id= '1'") or DIE($dbcon->ErrorMsg());


   $intemp=$dbcon->Execute("select m.templateid, t.name from moduletext m left join template t on m.templateid = t.id where m.id=2") or DIE($dbcon->ErrorMsg());
   $templs=$dbcon->Execute("select id, name from template") or DIE($dbcon->ErrorMsg());
 include ("header.php");
 
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
            <td><input name="basepath_field" type="text" id="basepath" value="<?php echo $setsysvar->Fields("basepath")?>" size="32"></td>
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
                images)</div></td>
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
            <td colspan="2" align="right" nowrap><div align="left">Email List Settings</div></td>
          </tr>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Email Tool:</div></td>
            <td> <select name="email_tool">
			<?php if ($setsysvar->Fields("email_tool")) { echo "<option>".$setsysvar->Fields("email_tool")."</option>" ;} ?>
			<option value="">Select Tool</option> 
				<option value="phplist">PHPlist</option>
				<option value="DIA">Democracy In Action</option>

			</select>
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">DIA User:</div></td>
            <td> <input name="dia_user" type="text" value="<?php echo $setsysvar->Fields("dia_user")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">DIA Key:</div></td>
            <td> <input name="dia_key" type="text" value="<?php echo $setsysvar->Fields("dia_key")?>" size="32"> 
            </td>
          </tr>
		  		          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">AMP E-Mail Blast From address:</div></td>
            <td> <input type="text" name="emmedia" value="<?php echo $setsysvar->Fields("emmedia")?>" size="32"> 
            </td>
          </tr>
		  
          <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">AMP E-Mail Blast From name:</div></td>
            <td> <input type="text" name="emailfromname" value="<?php echo $setsysvar->Fields("emailfromname")?>" size="32"> 
            </td>
          </tr>
		  		  <tr valign="baseline" class="intitle"> 
            <td colspan="2" align="right" nowrap><div align="left">PHPList Settings</div></td>
          </tr>
		  
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Admin Email:</div></td>
            <td> <input name="phplist_admin_email" type="text" value="<?php echo $phplist_admin->Fields("email")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Admin Password:</div></td>
            <td> <input name="phplist_admin_password" type="text" value="<?php echo $phplist_admin->Fields("password")?>" size="32"> 
            </td>
          </tr>

		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Bounce Host:</div></td>
            <td> <input name="phplist_bounce_host" type="text" value="<?php echo $setsysvar->Fields("phplist_bounce_host")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Bounce User:</div></td>
            <td> <input name="phplist_bounce_user" type="text" value="<?php echo $setsysvar->Fields("phplist_bounce_user")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Bounce Password:</div></td>
            <td> <input name="phplist_bounce_password" type="text" value="<?php echo $setsysvar->Fields("phplist_bounce_password")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Website Domain (no http):</div></td>
            <td> <input name="phplist_website" type="text" value="<?php echo $phplist_website->Fields("value")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Email Domain:</div></td>
            <td> <input name="phplist_domain" type="text" value="<?php echo $phplist_domain->Fields("value")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Admin Address:</div></td>
            <td> <input name="phplist_admin_address" type="text" value="<?php echo $phplist_admin_address->Fields("value")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Report Address:</div></td>
            <td> <input name="phplist_report_address" type="text" value="<?php echo $phplist_report_address->Fields("value")?>" size="32"> 
            </td>
          </tr>
		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Message From Address:</div></td>
            <td> <input name="phplist_message_from_address" type="text" value="<?php echo $phplist_message_from_address->Fields("value")?>" size="32"> 
            </td>
          </tr>		  
				  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Message From Name:</div></td>
            <td> <input name="phplist_message_from_name" type="text" value="<?php echo $phplist_message_from_name->Fields("value")?>" size="32"> 
            </td>
          </tr>  
		  		  <tr valign="baseline"> 
            <td nowrap align="right"><div align="left">Phplist Reply to Address:</div></td>
            <td> <input name="phplist_message_replyto_address" type="text" value="<?php echo $phplist_message_replyto_address->Fields("value")?>" size="32"> 
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


