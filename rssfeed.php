<?php 
require_once( 'AMP/BaseDB.php' );

require_once( 'AMP/Content/RSS/Feed.inc.php' );
$feed_id = (isset($_GET['feed'])&&$_GET['feed']) ? $_GET['feed'] : null;
$section_id = (isset($_GET['section'])&&$_GET['section']) ? $_GET['section'] : false;
$class_id = (isset($_GET['class'])&&$_GET['class']) ? $_GET['class'] : false;

$feed = &new AMPContent_RSSFeed( $dbcon, $feed_id );
if ( $section_id ) $feed->setSection( $section_id );
if ( $class_id ) $feed->setClass( $class_id );
if ( $class_id && $section_id ) $feed->setCombineLogic( );

$feed->getDataSource( );

if ($display=&$feed->getDisplay()) {
    $display->execute();
}

?>
