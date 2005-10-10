<?php
require_once( 'AMP/System/Data/Search.inc.php');

class ContentSearch extends AMPSystem_Data_Search {
    function ContentSearch ( &$source ) {
        $this->init( $source );
    }
}
?>
