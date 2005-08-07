<?php
#generic update page
$modid = "45";

$mod_name = "rss";

require("Connections/freedomrising.php");
$buildform = new BuildForm;

include_once("FeedOnFeeds/init.php");

$table = "px_feeds";
$listtitle ="Subscribed Feeds";
$listsql ="select * from $table WHERE(isNull(service) OR service='Content') ";
$orderby =" order by id desc ";
$fieldsarray=array('ID'=>'id',
					'Title'=>'title',
					'URL'=>'url');	

$url = $_POST['rss_url'];
if(!$url) $url = $_GET['rss_url'];
$opml = $_POST['opml_url'];
$file = $_POST['opml_file'];


$maxfilesize = & new Input('hidden', 'MAX_FILE_SIZE', '100000');
$html = $buildform->start_table('name');
$html .= $buildform->add_header('Add RSS Feed','banner');
$html .= $buildform->add_content($maxfilesize->fetch());
$html .= addfield('rss_url','RSS or Weblog URL:','text');
$html .= addfield('opml_url','OPML URL:','text');
$html .= $buildform->add_content($buildform->add_btn());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");

echo $form->fetch();

if($url) fof_add_feed($url);

if($opml)
{
	if(!$content_array = file($opml))
	{
		echo "Cannot open $opml<br>";
		return false;
	}

	$content = implode("", $content_array);

	$feeds = fof_opml_to_array($content);
}

if($_FILES['opml_file']['tmp_name'])
{
	if(!$content_array = file($_FILES['opml_file']['tmp_name']))
	{
		echo "Cannot open uploaded file<br>";
		return false;
	}

	$content = implode("", $content_array);

	$feeds = fof_opml_to_array($content);
}

foreach ($feeds as $feed)
{
	fof_add_feed($feed);
	echo "<hr size=1>";
	flush();
}
	
	
global $dbcon;

if ($sort) { $orderby =" order by $sort asc ";}
$query=$dbcon->Execute($listsql.$orderby) or DIE($dbcon->ErrorMsg());

echo "<h2>&nbsp;&nbsp;".$listtitle."</h2>";
echo "<table width='98%' border=0 cellspacing=1 cellpadding=0 align='center'> <tr class='intitle'> ";
echo " <td>&nbsp;</td>";
foreach ($fieldsarray as $k=>$v) {
	echo " <td><b><a href='".$_SERVER['PHP_SELF']."?action=list&sort=".$v."' class='intitle'>".$k."</a></b></td>";
}

if ($extra) {echo " <td>&nbsp;</td>";}
echo "</tr>";
$i= 0;
while (!$query->EOF) {
	 $i++;
	 $bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
$id = $query->Fields('id');
	echo "<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
	echo "<td> <div align='center'><a href=\"feeds_delete.php?feed=$id\" onclick=\"return confirm('What-- Are you SURE?')\">Delete</a></div></td>";
	foreach ($fieldsarray as $k=>$v) {
		if ($v =='publish' ) {
			if ($query->Fields($v) == 1) { $live= "live";}
			else { $live= "draft";}
			echo "<td> $live </td>";
		}
		else {
			echo "<td> ".$query->Fields($v)." </td>";
		}
	}
	
	if ($extra) {
		echo "<td> <div align='right'>";
		foreach ($extra as $k=>$v) {
			echo "<A HREF='".$v.$query->Fields("id")."'>$k</A>&nbsp;&nbsp;";
		}
		echo "</div></td>";
	}
	echo "</tr>";

	$query->MoveNext();
}		

include ("footer.php");
?>
