<?php
  require("Connections/freedomrising.php");

  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }
  $MM_editQuery = "";
  
if (!$filename) {
	$filename = $base_path_amp."custom/styles.css";}
else {
	if (strpos($filename, $base_path_amp)===FALSE){
		$filename = $base_path_amp.$filename;
	}
}
  
   if ( $_POST[cssedit] )  {
   
   $fp = fopen($filename , "w+"); 
$test = fwrite($fp,"$_POST[cssedit]"); 
fclose ($fp); 
   
    }
	
if (file_exists($filename)) {
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$msg_action = " : ".basename($filename);
} else {
	$msg_action = " : New File : ".basename($filename);
}
?>
<?php include ("header.php");?>
<script language=JavaScript src="picker.js"></script>
      <h2><?php echo helpme("Overview"); ?>Edit CSS File<?php  echo $msg_action;?></h2>
		<form ACTION="<?php echo $MM_editAction?>" METHOD="POST" name="form" id="form">

        <input name="submit2" type="submit" value="Save Changes">
        <br>
        <table width="90%" border="0">
          <tr> 
            <td colspan="2" class="intitle"><?php echo helpme("CSS"); ?>CSS</td>
          </tr>
          <tr> 
            <td valign="top" class="name"><p>CSS<br>
              </p>
              <p>&nbsp; </p></td>
            <td class="name"> <textarea name="cssedit" cols="65" rows="40" wrap="VIRTUAL" id="cssedit"><?php echo $contents; ?></textarea>
              <br>
              <br>
              <a href="javascript:TCP.popup(document.forms['form'].elements['pick'])"><img width="15" height="13" border="0" alt="Click Here to Pick up the color" src="images/sel.gif"></a>Color 
              Picker<br>
              <input name="pick" type="text" id="pick">
            </td>
          </tr>
        </table>
  <p> 
   <input name="submit" type="submit" value="Save Changes">
      </form>

<?php include ("footer.php");?>
