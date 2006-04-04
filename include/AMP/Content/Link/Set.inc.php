<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/Link/Link.php');

class AMP_Content_Link_Set extends AMPSystem_Data_Set {

    var $datatable = 'links';
    var $_sourceItem_class = 'AMP_Content_Link';

    function AMP_Content_Link_Set( &$dbcon ) {
        $this->sort = array( 'linktype, if( ( isnull( linkorder ) OR linkorder = 0), "'.AMP_SORT_END.'", linkorder ), linkname');
        $this->init( $dbcon );
    }

    function addCriteriaLive( ) {
        $this->addCriteria( 'publish=1');
    }

    function addCriteriaSection( $section_id ) {
        if ( !is_numeric( $section_id )) return false;
        $sections = &AMPContent_Lookup::instance( 'sections');

        if ( !isset( $sections[ $section_id ])) return false;
        $this->addCriteria( 'type=' . $section_id );

    }

}

?>
