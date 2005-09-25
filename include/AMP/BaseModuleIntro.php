<?php
    if ( $intro_id != AMP_CONTENT_INTRO_ID_DEFAULT ) {
        $currentPage = &AMPContent_Page::instance();
        $introtext = &$currentPage->getIntroText();
        $intro_display = &$introtext->getDisplay();
        $currentPage->contentManager->addDisplay( $intro_display, AMP_CONTENT_DISPLAY_KEY_INTRO );
    }
?>
