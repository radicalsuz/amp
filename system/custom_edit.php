<?php

$modid = "31";
require_once("AMP/BaseDB.php");
require_once("Connections/freedomrising.php");

//Check to see if filename has been called via URL
if (!$_REQUEST['filename']) {
    $filename = false; 
    $filepath = false;
} else {
    $filename=basename($_REQUEST['filename']);			
    $filepath=AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.'custom'.DIRECTORY_SEPARATOR.$filename;
}
  
// If Edited file has been posted, save it to disk.
if ( $_POST['include_edit'] )  {
    if (get_magic_quotes_gpc()) $includefile=stripslashes($_POST['include_edit']);
    else $includefile = $_POST['include_edit'];
    $fp = fopen($filepath, "w+"); 
    $test = fwrite($fp, $includefile); 
    if ($test) {
        $filepath = "";
    } else {
        $msg_action = " : Unable to write to file ".$filename;
        $contents=$_POST['include_edit'];
    }
    fclose ($fp); 		
}
	
if (file_exists($filepath)) {
    if (!isset($msg_action)) {
        if($contents=file_get_contents($filepath,true) ){
            $msg_action = " : ".basename($filename);
        } else {
            $msg_action = " : couldn't open ".basename($filename);
        }
    }
    $output=   "<h2>".helpme("Overview")."Edit Custom File$msg_action</h2>";
    $output.= "<form ACTION=\"".$_SERVER['PHP_SELF']."\" METHOD=\"POST\" name=\"form\" id=\"form\">";
    $output.= '<input name="submit2" type="submit" value="Save Changes">
            <br>
            <table width="90%" border="0">
            <tr> 
                <td class="name"> <textarea name="include_edit" cols="85" rows="40" wrap="VIRTUAL" id="include_edit">'
                .$contents.'</textarea>
                <br>
                <br>
                    <input type="hidden" name="filename" value="'.$filename.'">
                </td>
            </tr>
            </table>
    <p> 
    <input name="submit" type="submit" value="Save Changes">
        </form>';
	
} else {

    $filepath = AMP_LOCAL_PATH.DIRECTORY_SEPARATOR.'custom'.DIRECTORY_SEPARATOR;

    if ($filedir = opendir($filepath)) {
        
        while (false !== ($file = readdir($filedir))) { 
            if (is_file($filepath.$file) && is_writable($filepath.$file)) {
                $fileset[]=$file;
            }

        }
        $searchbox = "File to edit:<BR>";
        if (count($fileset)) {
            $searchbox .= "<FORM NAME = 'filegrab' action='".$_SERVER['PHP_SELF']."' method='GET'>";
            $searchbox .= "<SELECT NAME=\"filename\" size=10>";
            foreach ($fileset as $fileitem) {
                $searchbox .= "<Option>$fileitem</option>";
            }
            $searchbox .="</select><input type='submit' value='Open'></form>";
        } else {
            $searchbox = "No files are available to edit";
        }
    } else {
        $searchbox = "No custom folder found - please contact your site administrator.";
    }
    $output=$searchbox; 
}
?>
<?php include ("header.php");
print $output;
include ("footer.php");

?>
