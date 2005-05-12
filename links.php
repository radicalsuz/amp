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
if (isset($_GET["name"]) && $_GET['name']) {
	 $name_link = $_GET["name"];
  	header ("Location: links.php#$name_link");  
  	
}
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");

if (isset($_GET['linktype']) && $_GET['linktype']) {
	$sql = "SELECT l. * , t.name FROM links l, linksreltype r, linktype t WHERE l.id = r.linkid AND l.linktype = t.id AND r.typeid=" . $dbcon->qstr($_GET['linktype']) . " AND l.publish = '1' order by t.name asc, l.linkname asc";
} else { 
	$sql = "SELECT l. * , t.name FROM links l, linktype t WHERE  l.linktype = t.id  AND l.publish = '1' AND t.publish = '1' order by t.name asc, l.linkname asc";
}

$links=$dbcon->CacheExecute($sql) or DIE($dbcon->ErrorMsg());

if (isset($_GET['linktype']) && $_GET['linktype']) { 
	$linkt=$dbcon->CacheExecute("select type from articletype where id=" . $dbcon->qstr($_GET['linktype'])) or DIE($dbcon->ErrorMsg());
	echo "<p class=title>".$linkt->Fields("type")."</p><br>";
}

if (!isset($curType)) $curType = null;
 
while  (!$links->EOF) { 
   if (strtolower(trim($links->Fields("name"))) != $curType) {
   		echo '<p class="linktype">
   				<a name="'.$links->Fields("linktype").'"></a>'.$links->Fields("name")."</p>";
   	}
	$curType = strtolower(trim($links->Fields("name")));
	
	?>
	<div class="links">
	<a href="<?php echo $links->Fields("url")?>" target="_blank">
	<? 
	if ($links->Fields("image")) {  ?>
    	<img name="thumbnail" src="img/thumb/<?php echo $links->Fields("image")?>" alt="thumbnail" border="0">
    	<?php 
    }      
	echo $links->Fields("linkname") ?>
	</a><p>
    <?php echo $links->Fields("description")?></p> <br />
	
	<?php  echo '</div>';
	$links->MoveNext();
	if (strtolower(trim($links->Fields("name"))) != $curType) {
		
	}
} 


?>

<p>&nbsp;</p>

<?php  include("AMP/BaseFooter.php");
?>
