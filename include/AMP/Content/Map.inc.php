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

    function getChildren( $section_id ) {
        $child_sections = array();
        foreach ( $this->dataset as $key => $valueset ) {
            if (!$valueset['parent']==$section_id) continue;
            $child_sections[] = $key;
        }
        if (empty($child_sections)) return false;

        return $child_sections;

    }

    function getName( $section_id ) {
        if (!isset($this->dataset[ $section_id ])) return false;
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
            $option_set[ $child_section ] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getDepth( $child_section )-1) . $this->getName( $child_section );
            if (!($child_options =  $this->selectOptions( $child_section ))) continue ;
            $option_set = $option_set + $child_options;
        }
        return $option_set;
    }

    function &instance() {
        static $content_map = false;

        if(!$content_map) $content_map = new AMPContent_Map();
        return $content_map;
    }

    function getMenu( $startLevel = null, $itemtype = 'ArticleList' ) {
        $menu_item_method = '_menuItem'. $itemtype;
        $menuset= array();
        foreach ($this->dataset as $section_id => $section_info) {
            $menuset[$this->getParent($section_id)][$section_id]=$this->$menu_item_method( $section_id );
        }
        return $menuset;
    }
    /*
        if (!isset($startLevel)) $startLevel = $this->top;
        if (!( $name = $this->getName( $startLevel ))) return;
        $menu_item_function = '_menuItem'. $itemtype;
        $result = array();

        $result[$startLevel] = $this->$menu_item_function( $startLevel );
        if (!( $children = $this->getChildren($startLevel) )) return $result;

        foreach ($children as $child_id ) {
            $result[$startLevel] = array_merge( $result[$startLevel], $this->getMenu( $child_id, $itemtype ));
        }

        return $result;
    }
    */

    function _menuItemArticleList( $section_id ) {
        return array(
            'id'    =>  $section_id,
            'label' =>  $this->getName( $section_id ),
            'href'  =>  'article_list.php?type=' . $section_id
            );
    }

}
?>
