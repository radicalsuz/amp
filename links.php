<?php 
/*********************
05-06-2003  v3.01
Module:  Links
Description:  Links Display page
CSS: title, text
GET VARS: name - moves to  a sectional  anchor tag
To Do:  user added option
*********************/ 
$modid=11;
$mod_id = 12;
include("sysfiles.php");
include("header.php"); ?>
<?php 
if ($_GET["name"]) {
	 $name_link = $_GET["name"];
  	header ("Location: links.php#$name_link");  
}

if ($_GET[linktype] ) {
	$sql = "SELECT l. * , t.name FROM links l, linksreltype r, linktype t WHERE l.id = r.linkid AND l.linktype = t.id AND r.typeid =$_GET[linktype] AND l.publish = '1' order by t.name asc, l.linkname asc";
	}
	else  { 
	$sql = "SELECT l. * , t.name FROM links l, linktype t WHERE  l.linktype = t.id  AND l.publish = '1' AND t.publish = '1' order by t.name asc, l.linkname asc";
}

$links=$dbcon->CacheExecute($sql) or DIE($dbcon->ErrorMsg());

if ($_GET[linktype]) { 
	$linkt=$dbcon->CacheExecute("select  type from articletype where id = $_GET[linktype]") or DIE($dbcon->ErrorMsg());
	echo "<p class=title>".$linkt->Fields("type")."</p><br>";
}
 
while  (!$links->EOF) { 
   if (strtolower(trim($links->Fields("name"))) != $curType) {
   		echo "<p class=listtitle><a name=\"".$links->Fields("linktype")."\"></a>".$links->Fields("name")."</p><table width=100% border=0 cellpadding=3 cellspacing=0>";
   	}
	$curType = strtolower(trim($links->Fields("name")));
?>
	<tr><td>
	<p class="text">
	<a href="<?php echo $links->Fields("url")?>" target="_blank">

<? if ($links->Fields("image")) {  ?>
    	<img name="thumbnail" src="img/thumb/<?php echo $links->Fields("image")?>" alt="thumbnail" hspace="8" vspace="1" border="0" align="left"><br><?php 
    }  ?>

<?php 
	echo $links->Fields("linkname") ?>
	</a> &nbsp;&nbsp; <?php echo $links->Fields("description")?></td></tr>
<?php  
	$links->MoveNext();
	if (strtolower(trim($links->Fields("name"))) != $curType) {
		echo '</table>';
	}
}


?>

<p>&nbsp;</p>

<?php include("footer.php"); ?>
