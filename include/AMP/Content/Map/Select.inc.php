<?php

require_once( 'AMP/Content/Map.inc.php' ) {

class ContentMap_Select {
    var $map;

    function ContentMap_Select() {
        $this->map = &AMPContent_Map::instance();
    }

    function &instance() {
        static $map_select = false;
        if (!$map_select) $map_select = new ContentMap_Select();
        return $map_select;
    }

    function indentedValues( $startLevel = null, $indent = "&nbsp;&nbsp;&nbsp;&nbsp" ) {
        if (!isset($startLevel)) $startLevel = $this->map->top;
        $child_set = $this->map->getChildren( $startLevel );
        foreach( $child_set as $child_section ) {
            $result_set[ $child_section ] = str_repeat( $indent, $this->map->getDepth( $child_section )-1) . $this->map->getName( $child_section );
            if (!($child_results =  $this->indentedValues( $child_section ))) continue ;
            $result_set = $result_set + $child_results;
        }

        return $result_set;
    }

    function getIndentedValues() {
        static $mapselect = false;
        if (!$mapselect) $mapselect = & ContentMap_Select::instance();
        return $mapselect->indentedValues();
    }

    function indentedOptions( $selected_value = null ) {
        return AMP_buildSelectOptions( $this->indentedValues(), $selected_value );
    }

    function getIndentedOptions( $selected_value = null) {
        static $mapselect = false;
        if (!$mapselect) $mapselect = & ContentMap_Select::instance();
        return $mapselect->indentedOptions( $selected_value );
    }
    function indentedOptions_withTop( $selected_value = null ) {
        return AMP_buildSelectOptions( (array(1=>$GLOBALS['SiteName']) + $this->indentedValues()), $selected_value );
    }

    function getIndentedOptions_withTop( $selected_value = null) {
        static $mapselect = false;
        if (!$mapselect) $mapselect = & ContentMap_Select::instance();
        return $mapselect->indentedOptions_withTop( $selected_value );
    }
    function indentedOptions_withNull( $selected_value = null ) {
        return AMP_buildSelectOptions( (array(''=>'Select One') + $this->indentedValues()), $selected_value );
    }

    function getIndentedOptions_withNull( $selected_value = null) {
        static $mapselect = false;
        if (!$mapselect) $mapselect = & ContentMap_Select::instance();
        return $mapselect->indentedOptions_withNull( $selected_value );
    }

}
?>
