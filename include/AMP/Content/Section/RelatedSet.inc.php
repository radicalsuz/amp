<?php

require_once( 'AMP/System/Data/Set.inc.php' );

class SectionRelatedSet extends AMPSystem_Data_Set {

    var $datatable = "articlereltype";
    var $id_field = "articleid";

    function SectionRelatedSet( &$dbcon, $section_id=null ) {
        $this->init( $dbcon );
        if (isset($section_id )) $this->addCriteria( "typeid=".$section_id );
    }
}
?>

        
