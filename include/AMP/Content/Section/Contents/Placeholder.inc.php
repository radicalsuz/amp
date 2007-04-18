<?php

require_once( 'AMP/Content/Section/Contents/Articles.inc.php' );

class SectionContentSource_Placeholder extends SectionContentSource_Articles {

    function SectionContentSource_Placeholder( &$section ) {
        $this->init( $section );
    }

}

?>
