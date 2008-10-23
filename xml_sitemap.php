<?php
require_once( 'AMP/Base/Config.php');
require_once( 'AMP/Content/Map/XmlSitemap.php');

$sitemap = new AMP_Content_Map_XmlSitemap( );
echo $sitemap->execute( );
?>
