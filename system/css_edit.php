<?php

$modid = "31";
  require_once("Connections/freedomrising.php");




  // *** Edit Operations: declare Tables
  $MM_editAction = $PHP_SELF;
  if ($QUERY_STRING) {
    $MM_editAction = $MM_editAction . "?" . $QUERY_STRING;
  }
  $MM_editQuery = "";
  
	if (!$_REQUEST['filename']) {
		$filename = "styles.css";
	} else {
		$filename=basename($_REQUEST['filename']);			
	}
  
   if ( $_POST['cssedit'] )  {
   
   		$fp = fopen(AMP_LOCAL_PATH.'/custom/'.$filename , "w+"); 
		$test = fwrite($fp,$_REQUEST['cssedit']); 
		fclose ($fp); 		
    }
	
if (file_exists_incpath($filename)) {
	if($contents=file_get_contents($filename,true) ){
		$msg_action = " : ".basename($filename);
	} else {
		$msg_action = " : couldn't open ".basename($filename);
	}
	
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
				<input type="hidden" name="filename" value="<?php echo $filename; ?>">
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
