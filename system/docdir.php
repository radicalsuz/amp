<?php
  require("Connections/freedomrising.php");?>
    <?php include("header.php");?>
	<h2><?php echo helpme(""); ?>Documents</h2>
	<?php
	if (isset($actdel)){
	$dir_name="".$base_path_amp."downloads/";
	unlink($dir_name.$actdel);
	}
$dir_name="".$base_path_amp."downloads";
  echo "<table cellpadding=10>" ;
$dir = opendir($dir_name);
$basename = basename($dir_name);
$fileArr = array();

while ($file_name = readdir($dir))
{
  if (($file_name !=".") && ($file_name !=
".."))
  {
    #Get file modification date...
    #
    $fName = "$dir_name/$file_name";
    $fTime = filemtime($fName);
    $fileArr[$file_name] = $fTime;    
  }
}

# Use arsort to get most recent first
# and asort to get oldest first
arsort($fileArr);

$numberOfFiles = sizeOf($fileArr);
for($t=0;$t<$numberOfFiles;$t++)
{
    $thisFile = each($fileArr);
    $thisName = $thisFile[0];
    $thisTime = $thisFile[1];
    $thisTime = date("M d Y", $thisTime);?>
	<tr>
  <td>&nbsp;</td>
	<td width='150'><b><a href="/downloads/<?php echo $thisName ?>"><?php echo $thisName ?></a></b></td>	
		<td><?php echo $thisTime ?></td><td><a href="docdir.php?actdel=<?php echo $thisName ?>">delete</a></td>
		
		</tr>
		<?php
}
closedir ($dir);
  echo"</table>";
  
?><?php   include("footer.php");?>