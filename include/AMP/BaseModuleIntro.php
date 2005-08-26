<?php
if (AMP_USE_OLD_CONTENT_ENGINE) {
    require_once( 'AMP/BaseModuleIntro2.php' );
} else {
    if ( $intro_id != AMP_CONTENT_INTRO_ID_DEFAULT ) {
        $currentPage = &AMPContent_Page::instance();
        $introtext = &$currentPage->introtext;
        $intro_display = &$introtext->getDisplay();
        $currentPage->contentManager->setIntroDisplay( $intro_display );
    }
}
?>
