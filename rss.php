<?php
#rss.php
# decription:  this page shows a list of all the rss feeds on the site
$mod_id = 1 ; 
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/BaseTemplate.php' );
$R =$dbcon->CacheExecute("select  id, title, description   from rssfeed ") or DIE($dbcon->ErrorMsg());

$rssintro = '';
echo '<p class="title">RSS Feeds</p>';
echo '<ul>';
	echo '<li><b><a href="rssfeed.php">Latest Updates</a></b> - The most recently added pages on this website</li>';
while (!$R->EOF) {
	echo '<li><b><a href="rssfeed.php?feed='.$R->Fields("id").'">'.$R->Fields("title") .'</a></b> - '. $R->Fields("description").'</li>';
	$R->MoveNext();
}
echo '</ul>';


require_once( 'AMP/BaseFooter.php' );
?>