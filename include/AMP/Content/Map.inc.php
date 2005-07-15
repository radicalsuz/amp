<?php

class AMPContent_Map {

    var $dbcon;
    var $map;
    var $fields = array("id","type","usenav","textorder","parent");
    var $dataset;
    var $top;

    function AMPContent_Map() {
    }

    function init( &$dbcon, $top = null ) {
        $this->dbcon = &$dbcon;
        if (!isset($top) || !$top ) $top = 1;
        $this->buildMap( $top );
    }

    function buildMap( $top ) {
        $this->top = $top;
        $sql = "Select " . join(", ", $this->fields ) ." from articletype order by parent, textorder, type";
        $this->dataset = $this->dbcon->CacheGetAssoc( $sql );
        $this->buildLevel( $top );
    }

    function buildLevel( $parent, $recursive=true ) {
        foreach( $this->dataset as $id => $mapitem ) {
            if( $mapitem['parent'] != $parent ) continue;
            $this->map[$parent][] = $id;
            if ($recursive) $this->buildLevel( $id );
        }
    }

    function getParent( $section_id ) {
        return $this->dataset[$section_id]['parent'];
    }

    function getName( $section_id ) {
        return $this->dataset[$section_id]['type'];
    }

    function getDepth( $section_id ) {
        if( !$section_id ) return 0;
        if( $section_id == $this->top ) return 0;
        return $this->getDepth( $this->getParent( $section_id )) + 1;
    }

    function selectOptions( $startLevel=null ) {
        if (!isset($startLevel)) $startLevel = $this->top;
        if (!isset( $this->map[$startLevel] )) return;
        if (!is_array( $this->map[$startLevel] )) return;
        $option_set =array();
        foreach( $this->map[ $startLevel ] as $child_section ) {
            $option_set[ $child_section ] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getDepth( $child_section )) . $this->getName( $child_section );
            $option_set = array_merge( $option_set, $this->selectOptions( $child_section ) );
        }
        return $option_set;
    }

    function &instance() {
        static $content_map = false;

        if(!$content_map) $content_map = new AMPContent_Map();
        return $content_map;
    }
}
