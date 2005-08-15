<?php

require_once ( 'AMP/Content/Display/List.inc.php' );
require_once ( 'AMP/Content/Section.inc.php' );

class SectionSet_Display extends AMPContent_DisplayList_HTML {
    var $_sourceItem_class = 'Section';

    function SectionSet_Display( &$sectionSet ) {
        $this->init( $sectionSet );
    }

    function _HTML_listItemDate ( $date ) {
		if (!$date) return false;
        return $this->_HTML_inSpan( DoDate( $date, '(F, Y)'), 'bodygreystrong') . $this->_HTML_newline();
    }

    function _HTML_listItemDescription( &$section ) {
        return
            $this->_HTML_listItemTitle( $section ) .
            $this->_HTML_listItemBlurb( $section->getBlurb() ) .
            $this->_HTML_listItemDate ( $section->getSectionDate() );
    }

}
?>
