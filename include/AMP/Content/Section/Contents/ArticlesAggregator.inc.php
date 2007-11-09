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

    function _addCriteriaSection( ){
        $article = new Article(  AMP_Registry::getDbcon( ));
        $raw_crit = $this->_section->getDisplayCriteria( );
        if( isset( $raw_crit['section'] ) && $raw_crit['section'] ) {
            $raw_crit['in_section_descendant'] = $raw_crit['section'];
            unset( $raw_crit['section']);
        }
        $crit = $article->makeCriteria( $raw_crit ); 
        foreach( $crit as $crit_item ) {
            $this->_source->addCriteria( $crit_item );
        }
        #$this->_source->addCriteriaSectionDescendent( $this->_section->id );
    }

    function getSectionCriteria() {
        //deprecated

        $base_section = "type=".$this->_section->id ;
        if (!($child_ids = $this->_getAllSubsections())) return $base_section;
				return "(" . $child_ids . ' OR '. $base_section . ")";
    }

    function _getAllSubsections() {

        //deprecated
        if (!($id_set = $this->_map->getDescendants( $this->_section->id ))) return false;
        return "type in (" . join( ", ", ( $id_set) ). ")";
    }

    function _setSort() {
            $this->_setBaseSort();
    }

}
?>
