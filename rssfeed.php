<?php 
header('Content-type: text/xml');
print '<?xml version="1.0" encoding="ISO-8859-1"?>
';
#field list: title, description, limit, orderby, orderbyorder, where
$sqllimit= 15;
$orderby = "date";
$orderbyorder ="desc";
require_once( 'AMP/BaseDB.php' );

if ($_GET['feed']) {
	$f =$dbcon->CacheExecute("select * from rssfeed where id = ".$_GET[feed]." ") or DIE($dbcon->ErrorMsg());
	if ($f->Fields("description")) { $meta_description = $f->Fields("description");}
	if ($f->Fields("title")) {$SiteName = $f->Fields("title"); } 
	if ($f->Fields("sqllimit")) {$sqllimit = $f->Fields("sqllimit"); }
	if ($f->Fields("orderby")) {$orderby = $f->Fields("orderby"); }
	if ($f->Fields("orderbyorder")) {$orderbyorder = $f->Fields("orderbyorder"); }
	if ($f->Fields("sqlwhere")) {$sqlwhere = $f->Fields("sqlwhere"); }
}

if ($sqlwhere) {$sqlwhere = " and ".$sqlwhere;}
$sql = "SELECT distinct title, id, test, UNIX_TIMESTAMP(date) as date, UNIX_TIMESTAMP(updated) as updated, shortdesc FROM articles Left Join articlereltype on articleid = id  WHERE publish=1 $sqlwhere ORDER BY $orderby $orderbyorder  limit $sqllimit" ;
	
$R =$dbcon->CacheExecute($sql) or DIE($sql.$dbcon->ErrorMsg());

?>
<rss version="2.0">
   <channel>
   	<title><?= $SiteName ?></title>
	<link><?= $Web_url ?></link>
	<description><?= $meta_description ?></description>
	<language>en-us</language>
	<docs><?php echo $Web_url.'rssfeed.php?feed='.$_GET[feed];?></docs>
	<pubDate><?php echo date(r); ?></pubDate>
	<generator>Activist Mobilization Platform</generator>
	<webMaster><?= $admEmail ?></webMaster>
<?php 
function makesmall($ttext) {
	$aspace=" ";
	$maxTextLenght=9000;
	if(strlen($ttext) > $maxTextLenght ) {
		$ttext = substr(trim($ttext),0,$maxTextLenght); 
		$ttext = substr($ttext,0,strlen($ttext)-strpos(strrev($ttext),$aspace));
		$ttext = $ttext.'...';
	  }
	return $ttext;
}
while (!$R->EOF) {
$description = NULL;
if ($R->Fields("title")) {
	$link = $Web_url."article.php?id=".$R->Fields("id");
	if ($R->Fields("shortdesc")) {
		$description = makesmall($R->Fields("shortdesc"));
	}
	else {
		//$description = makesmall($R->Fields("test"));
	}
	$description = htmlspecialchars(strip_tags($description));
	if ($description == NULL) {$description= $R->Fields("title");}
	if (($R->Fields("date")) or ($R->Fields("date") != '0000-00-00')) {
		$date = date(r,$R->Fields("date"));
	} else {
		$date = date(r,$R->Fields("updated"));
	}
	
	$title = ereg_replace ("&", "and" ,$R->Fields("title"));
	$title = strip_tags($title)
	//}
	//else {$date = rssdateformat();}
?>
		<item>
			<title><?php echo $title?></title>
			<description><?php echo $description?></description>
			<link><?php echo $link ;?></link>
			<pubDate><?php echo $date ;?></pubDate>
			<guid><?php echo $link ?></guid>
		</item>
<?php
	} 
		$R->MoveNext();
}
?>

</channel>
</rss>
