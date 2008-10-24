<?php
include("AMP/BaseDB.php"); 

//$modid = 46;
if( AMP_CONTENT_ACTIVATE_FORUMS ) {

if (defined('AMP_FORUM_INTRO_ID')) {
    $intro_id = AMP_FORUM_INTRO_ID;
}
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");

?>
<IFRAME SRC="/punbb/"  FRAMEBORDER=0 width="100%" height="800">
</IFRAME>
<?php 
//echo $_SERVER['QUERY_STRING'];
require_once("AMP/BaseFooter.php");
} else {
    AMP_make_404( );
}


?>
