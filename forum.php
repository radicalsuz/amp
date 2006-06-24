<?php
include("AMP/BaseDB.php"); 

//$modid = 46;

if (AMP_FOURM_INTRO_TEXT) {
    $intro_id = AMP_FOURM_INTRO_TEXT;
}
include("AMP/BaseTemplate.php"); 
//include("AMP/BaseModuleIntro.php");
?>
<IFRAME SRC="/punbb/"  FRAMEBORDER=0 width="100%" height="800">
</IFRAME>
<?php echo $_SERVER['QUERY_STRING'];?>
<?php require_once("AMP/BaseFooter.php");


?>
