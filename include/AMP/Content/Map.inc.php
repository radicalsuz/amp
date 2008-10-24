<?php
//require_once ( 'AMP/Content/Page/Urls.inc.php' );
//require_once ( 'AMP/Content/Config.inc.php');

class AMPContent_Map {

    var $dbcon;
    var $map;
    var $fields = array("id","type","usenav","textorder", "secure", "templateid", "css", "flash");
    var $dataset;
    var $childset;
    var $top;
    var $top_set = array( );
    var $_section_totals;
    var $_permission_level = false ;

    function AMPContent_Map( &$dbcon, $top = AMP_CONTENT_MAP_ROOT_SECTION ) {
		if (   !defined('AMP_SYSTEM_USER_ID') 
			|| ( AMP_SYSTEM_USER_ID == 0 )) {
            $this->_permission_level = 'default';
        }
        if ( defined( 'AMP_SYSTEM_PERMISSIONS_LOADING') && AMP_SYSTEM_PERMISSIONS_LOADING ) {
            $this->_permission_level = 'none';
        }

        $this->init( $dbcon, $top );
    }

    function init( &$dbcon, $top = null ) {
        $this->dbcon = &$dbcon;
        if (!isset($top) || !$top ) $top = 1;
        $this->top = $top;
        $this->addParentField( );

        $this->buildMap();
    }

    function getParentFieldSql( ){
        return "if ( id=".$this->top.", 0, parent)";
    }

    function addParentField( ){
        $this->fields[] = $this->getParentFieldSql() . ' as parent';
    }

    function getTotals( $section_id ) {
        if ( !isset( $this->_totals )) {
            $this->_totals = &AMPContent_Lookup::instance( 'sectionTotals' );
        }
        if ( !$this->_totals ) return false;
        if ( !isset( $this->_totals[$section_id])) return false;
        return $this->_totals[ $section_id ];
    }

    function buildMap() {
        $sql = "Select " . join(", ", $this->fields ) 
                ." from articletype "
                ." where id != ".AMP_CONTENT_SECTION_ID_TOOL_PAGES
                ." order by " . $this->getParentFieldSql( ) . ", textorder, id DESC";
        if ( AMP_DISPLAYMODE_DEBUG ) AMP_debugSQL( $sql, 'content_map');
        $this->dataset = &$this->dbcon->CacheGetAssoc( $sql );
        $this->childset = &AMPContent_Lookup::instance( 'sectionParents' );

        //$this->top = $this->find_top( $this->top );
        if ( $this->find_top( $this->top ) != $this->top ) {
            foreach( $this->top_set as $topchild ) {
                $this->childset[ $topchild ] = $this->top ;
                $this->dataset[$topchild]['parent'] = $this->top;
            }
        }
        
        $this->buildLevel( $this->top );
    }

    function find_top( $start ) {
        if ( $this->_permission_level == 'none' || $this->_permission_level == 'default' ) {
            return $start;
        }
        if ( AMP_allow( 'access', 'section', $start )) return $start;
        if (!( $keys = array_keys( $this->childset, $start ) )) return false;
        foreach( $keys as $child) {
            if ( $this->find_top( $child ) == $child ) {
                $this->top_set[] = $child;
            }
        }
        return false;

    }

    function _check_permissions( $parent_id, $child_ids = array( ) ) {
        $base_return = !empty( $child_ids ) ? $child_ids : true;
        if ( ( $this->_permission_level == 'none' || $this->_permission_level=='default')) return $base_return; 

        $section_value = 'access';
        $section_section_parent = 'section_' . $parent_id;
        $result = AMP_allow( 'view', 'section', $parent_id );
        if ( !$result ) return false;
        if ( empty( $child_ids )) return $result;

        $child_results = array( );
        foreach( $child_ids as $child_id  ) {
            $section_section = 'section_' . $child_id;
            $result = AMP_allow( 'view', 'section', $child_id );
            if ( !$result ) continue;
            $child_results[] = $child_id;
        }
        if ( empty( $child_results )) return false;
        return $child_results;
        
    }

    function buildLevel( $current_parent, $recursive=true ) {
        //if ( !AMP_allow( 'access', 'section', $current_parent ))  {
        //    return false;
        //}

        if (!( $keys = array_keys( $this->childset, $current_parent ) )) return false;
        //if ( !( $keys = $this->_check_permissions( $current_parent, $key_set ))) return false;

        $allowed_keys = array( );
        foreach( $keys as $child_id ) {
            unset( $this->childset[ $child_id ] );
            if ($recursive) $this->buildLevel( $child_id );

            if ( !$this->allowed( $child_id )) {
                continue;
            }

            $allowed_keys[] = $child_id;
        }
        $this->map[$current_parent] = $allowed_keys;
    }

    function allowed( $section_id ) {
        if ( $this->_permission_level=='none') return true;
        if ( $this->readAncestors( $section_id, 'secure' ) 
             && !( AMP_Authenticate( 'admin') || AMP_Authenticate( 'content' ))) {
            return false;
        }
        if ( $this->_permission_level=='default') return true;
        return AMP_allow( 'access', 'section', $section_id );
    }

    function getParent( $section_id ) {
        if ( $section_id == $this->top ) return false;
        if (!isset($this->dataset[$section_id]['parent'])) return false;
        return $this->dataset[$section_id]['parent'];
    }

    function getAllParents( ) {
        return array_keys( $this->map );
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
    }

    function getDescendants( $section_id ) {
        if (!$section_id ) return false;
        if (!($children = $this->getChildren($section_id))) return false;
        $results = array();
        foreach( $children as $child ) {
            $results[] = $child;
            if (!($descendants = $this->getDescendants( $child ))) continue;
            $results = array_merge( $results,$descendants );
        }
        return $results;
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

    function selectOptionsExcludingSection( $section_to_avoid, $startLevel=null ) {
        if (!isset($startLevel)) $startLevel = $this->top;
        if (!$this->hasChildren ($startLevel)) return;

        $option_set =array();
        foreach( $this->map[ $startLevel ] as $child_section ) {
            if ( $child_section == $section_to_avoid ) continue;
            $option_set[ $child_section ] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getDepth( $child_section )-1) . $this->getName( $child_section );
            if (!($child_options =  $this->selectOptionsExcludingSection( $section_to_avoid, $child_section ))) continue ;
            $option_set = $option_set + $child_options;
        }
        return $option_set;
    }


    function selectOptionsLive( $startLevel=null ) {
        if (!isset($startLevel)) $startLevel = $this->top;
        if (!$this->hasChildren ($startLevel)) return;

        $option_set =array();
        foreach( $this->map[ $startLevel ] as $child_section ) {
            if ( $this->readSection( $child_section, 'usenav') != AMP_CONTENT_STATUS_LIVE ) continue;
            $option_set[ $child_section ] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getDepth( $child_section )-1) . $this->getName( $child_section );
            if (!($child_options =  $this->selectOptions( $child_section ))) continue ;
            $option_set = $option_set + $child_options;
        }
        return $option_set;
    }

    function hasMap() {
        return (is_array( $this->map ));
    }

    function selectSiteTree() {
        return array( $this->top, AMP_SITE_NAME ) + $this->selectOptions();
    }

    function &instance( $complete = false ) {
        static $content_map = false;

        if ( !$complete ) {
            if(!$content_map) $content_map = new AMPContent_Map( AMP_Registry::getDbcon() );
            return $content_map;
        } else {
            require_once( 'AMP/Content/Map/Complete.php');
            $content_map_complete = new AMP_Content_Map_Complete( AMP_Registry::getDbcon(), AMP_CONTENT_MAP_ROOT_SECTION );
            return $content_map_complete;

        }
    }


    function getMenu( $startLevel = null, $itemtype = 'ArticleList' ) {
        $menu_item_method = '_menuItem'. $itemtype;
        $menuset= array();
        foreach ($this->dataset as $section_id => $section_info) {
            if ( !$this->_check_permissions( $section_id )) continue;
            $menuset[$this->getParent($section_id)][$section_id]=$this->$menu_item_method( $section_id );
        }
        return $menuset;
    }

    function _menuItemArticleList( $section_id ) {
        return array(
            'id'    =>  $section_id,
            'label' =>  AMP_trimText( AMP_clearSpecialChars( $this->getName( $section_id ) ), 80 ) 
                        . " ( ".$this->getTotals( $section_id )." )",
            'href'  =>  AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'section=' . $section_id ))
            );
    }

    function readSection( $section_id, $fieldname ) {
        if (!isset($this->dataset[ $section_id ][ $fieldname ])) return false;
        return $this->dataset[ $section_id ][ $fieldname ];
    }

    function readAncestors( $section_id, $fieldname ) {
        if ($answer = $this->readSection( $section_id, $fieldname )) return $answer;
        
        if (!$ancestors = $this->getAncestors( $section_id )) return false;
        foreach ($ancestors as $id => $name ) {
            if ($answer = $this->readSection( $id, $fieldname )) return $answer;
        }
        if ($answer = $this->readSection( $this->top, $fieldname )) return $answer;
        return false;
    }
        

}
?>
