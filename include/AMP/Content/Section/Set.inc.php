<?php

require_once( 'AMP/System/Data/Set.inc.php' );

class SectionSet extends AMPSystem_Data_Set {
    var $datatable = "articletype";

    function SectionSet( &$dbcon ) {
        $this->init( $dbcon );
    }

    function getSections() {
        return $this->instantiateItems( $this->getArray() );
    }

}

?>
