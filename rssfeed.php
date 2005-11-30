<?php 
require_once( 'AMP/BaseDB.php' );

require_once( 'AMP/Content/RSS/Feed.inc.php' );
$feed_id = (isset($_GET['feed'])&&$_GET['feed']) ? $_GET['feed'] : null;
$feed = &new AMPContent_RSSFeed( $dbcon, $feed_id );

if ($display=&$feed->getDisplay()) {
    $display->execute();
}

?>
