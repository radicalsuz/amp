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

    function addCriteriaStatus( $value ){
        if ( !( $value || $value==='0')) return false;
        $this->addCriteria( 'usenav='.$value ) ;
    }
    function addCriteriaPublic( ){
        $protected_sections = AMPContentLookup::instance( 'protectedSections');
        if ( empty( $protected_sections )) return;
        $this->addCriteria( 'id not in( '. join( ',', array_keys( $protected_sections) ) .' )');

    }

    function addCriteriaSection( $section_id ){
        return $this->addCriteria( 'parent='.$section_id);
    }

    function addCriteriaSectionParent( $section_id ){
        $map = &AMPContent_Map::instance();
        if (!($subsection_set = $map->getChildren( $section_id ))) return $this->addCriteria( 'false' );
        return $this->addCriteria( "parent in (" . join( ", ", $subsection_set ) . ")" );
    }

}

?>
