<?php

require_once("Connections/freedomrising.php");
require_once('../gallery2/embed.php');
#
$usr_id =$_SERVER['REMOTE_GROUP'];

// initiate G2
GalleryEmbed::init(array('embedUri' => 'gallery2.php', 'embedPath' => '/system', 'relativeG2Path' => '../gallery2', 'activeUserId' => ''));

GalleryEmbed::addExternalIdMapEntry($usr_id, '6', 'GalleryUser');
GalleryEmbed::init(array('embedUri' => 'gallery2.php', 'embedPath' => '/system', 'relativeG2Path' => '../gallery2', 'activeUserId' => $usr_id));
$g2moddata = GalleryEmbed::handleRequest();


$dbcon=&AMP_Registry::getDbcon();
$dbcon->SetFetchMode("ADODB_FETCH_ASSOC");
include ("header.php");
 
print $g2moddata['headHtml'];
print $g2moddata['bodyHtml'];
#print_r ($g2moddata['sidebarBlocksHtml']);

include ("footer.php");



?>