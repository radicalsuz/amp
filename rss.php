<?php
require_once("AMP/Base/Config.php");
require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );
require_once( "AMP/Content/RSS/Feed/Public/ComponentMap.inc.php");

$map = new ComponentMap_RSS_Feed_Public( );
$controller = &$map->get_controller( );

$currentPage = &AMPContent_Page::instance();
$controller->set_page( $currentPage );
AMP_directDisplay( $controller->execute( ));

require_once("AMP/BaseFooter.php");

#rss.php
# decription:  this page shows a list of all the rss feeds on the site
/*
$mod_id = 1 ; 
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/BaseTemplate.php' );
$R =$dbcon->CacheExecute("select id, title, description from rssfeed ");# or DIE($dbcon->ErrorMsg());

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
*/
?>
