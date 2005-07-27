<?php
define ( 'AMP_CONTENT_LISTTYPE_CLASS', 'class' );
define ( 'AMP_CONTENT_LISTTYPE_SECTION', 'type' );

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
        if (!isset($this->dataset[$section_id]['parent'])) return false;
        return $this->dataset[$section_id]['parent'];
    }

    function getAncestors( $section_id ) {
        if (!$section_id) return null;
        if ($section_id == $this->top) return null;
        $self = array( $section_id => $this->getName( $section_id ) );
        $lineage = $this->getAncestors( $this->getParent( $section_id ) ); 
        if (!empty($lineage)) return $self + $lineage;
        return $self;
    }

    function getChildren( $section_id=null ) {
        if (!isset($section_id)) $section_id = $this->top;
        if (!isset($this->map[ $section_id ])) return false;
        return $this->map[ $section_id ];
        /*
        $child_sections = array();
        foreach ( $this->dataset as $key => $valueset ) {
            if (!$valueset['parent']==$section_id) continue;
            $child_sections[] = $key;
        }
        if (empty($child_sections)) return false;
        */


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

    function hasChildren( $section_id ) {
        if (!isset( $this->map[$section_id] )) return false;
        if (!is_array( $this->map[$section_id] )) return false;
        return count( $this->map[$section_id] );
    }

    function selectOptions( $startLevel=null ) {
        if (!isset($startLevel)) $startLevel = $this->top;
        if (!$this->hasChildren ($startLevel)) return;

        $option_set =array();
        foreach( $this->map[ $startLevel ] as $child_section ) {
            $option_set[ $child_section ] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getDepth( $child_section )-1) . $this->getName( $child_section );
            if (!($child_options =  $this->selectOptions( $child_section ))) continue ;
            $option_set = $option_set + $child_options;
        }
        return $option_set;
    }

    function selectSiteTree() {
        return array( $this->top, $GLOBALS['SiteName']) + $this->selectOptions();
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

    function _menuItemArticleList( $section_id ) {
        return array(
            'id'    =>  $section_id,
            'label' =>  $this->getName( $section_id ),
            'href'  =>  'article_list.php?type=' . $section_id
            );
    }

}
?>
