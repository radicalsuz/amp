<?

$modid = 8;
$intro_id = 64;
include("AMP/BaseDB.php");

require_once('gallery2/embed.php');


// initiate G2
GalleryEmbed::init(array('embedUri' => 'gallery2.php', 'embedPath' => '/', 'relativeG2Path' => 'gallery2'));

// user interface: you could disable sidebar in G2 and get it as separate HTML to put it into a block
GalleryCapabilities::set('showSidebarBlocks', false);

// handle the G2 request
$g2moddata = GalleryEmbed::handleRequest();

$dbcon=&AMP_Registry::getDbcon();
$dbcon->SetFetchMode("ADODB_FETCH_ASSOC");
include("AMP/BaseTemplate.php");
 
print $g2moddata['headHtml'];
print $g2moddata['bodyHtml'];
#print_r ($g2moddata['sidebarBlocksHtml']);

include("AMP/BaseFooter.php");

?> 