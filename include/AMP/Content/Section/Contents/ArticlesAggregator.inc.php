<?php

require_once( 'AMP/Content/Section/Contents/Articles.inc.php' ); 

class SectionContentSource_ArticlesAggregator extends SectionContentSource_Articles {

    var $_map;

    function SectionContentSource_ArticlesAggregator( &$section ) {
        $this->init( $section );
        $this->_map = &AMPContent_Map::instance();
    }


    ###################################
    ### private data source methods ###
    ###################################

    function _getSectionCriteria() {

        $base_section = "type=".$this->_section->id ;
        if (!($child_ids = $this->_getAllSubsections())) return $base_section;
    }

    function _getAllSubsections() {
        if (!($id_set = $this->_map->getDescendants( $this->_section->id ))) return false;
        return "type in (" . join( ", ", ( $id_set) ). ")";
    }

}
?>
