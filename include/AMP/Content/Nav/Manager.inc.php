<?php

#define( 'AMP_CONTENT_LISTTYPE_TYPE', 'section' );
define( 'AMP_CONTENT_NAV_SECTION_LIST_FIELD', 'typelist' );
define( 'AMP_CONTENT_NAV_SECTION_PAGE_FIELD', 'typeid' );
define( 'NAVIGATION_STANDARD_SEARCH', 'layout_id' );
define( 'NAVIGATION_STANDARD_VALUE', '1' );

require_once( 'AMP/Content/Nav/LocationSet.inc.php' );

/* * * * * * * * * * * * *
 * 
 *  NavigationManager
 *
 *  collects Navigation elements for the current page
 *
 *  AMP 3.5.1
 *  2005-07-31
 *  Author: austin@radicaldesigns.org
 *
 * * * * * * */

class NavigationManager {

    var $dbcon;
    var $template;
    var $page;

    var $_navSet;
    var $_current_seek_position;
    var $_default_attempted = false;

    function NavigationManager( &$template, &$page ) {
        $this->init( $template, $page );
    }

    function init( &$template, &$page) {
        $this->template = &$template;
        $this->page     = &$page;
        $this->dbcon    = &$template->dbcon;

        if (!($local_navs = $this->_locateNavs())) return;
        $this->_loadNavs( $local_navs );
       
    }
            
    function output( $position ) {
        $output = "";
        if (!isset($this->_navSet[ $position ])) return false;

        foreach( $this->_navSet[ $position ] as $order => $navcopy ) {
            $element = &$this->getElement( $order, $position );
            $output .= $element->execute();
        }

        return $this->render_nav_block( $output, $position );
    }

    function render_nav_block( $content, $position ) {
        $nav_blocks = AMP_lookup( 'navBlocks');
        $nav_key = array_search( strtolower( $position ), $nav_blocks );

        $renderer = AMP_get_renderer( );
        return $renderer->div( $content, array( 
                                'class' => AMP_CONTENT_CSS_CLASS_NAV_BLOCK,
                                'id'    => sprintf( AMP_CONTENT_CSS_ID_NAV_BLOCK, $nav_key )));

    }

    ###############################
    ### public accessor methods ###
    ###############################

    function &getElement( $order, $position ) {
        if (isset($this->_navSet[ $position ][ $order ])) return $this->_navSet[ $position ][ $order ];
        return false;
    }


    ###########################################
    ###  private navigation loading methods ###
    ###########################################

    function _locateNavs() {
        $find_method = $this->_getFindMethod();
        $locationSet = array();

        $positions = $this->template->getNavPositions();
        foreach ($positions as $desc => $id ) {
            $this->_default_attempted = false;
            $this->_setFindPosition( $id );
            if (!($result = $this->$find_method())) continue;
            $locationSet = array_merge( $locationSet, $result );
        }

        if (empty($locationSet)) return false;
        return $locationSet;
    }

    function _loadNavs( $locations ) {
        $this->_navSet = array();
        $ordered_set = array( );
        foreach ($locations as $locationDef) {
            $nav_id = $locationDef[ 'navid' ];
            $badge_id = isset( $locationDef['badge_id']) ? $locationDef[ 'badge_id' ] : false ;

            if (! ( $position = substr($locationDef['position'], 0, 1 ))) continue; 
            $order = substr( $locationDef['position'], 1 );

            if ( $nav_id ) {
                $element = $this->_loadNavElement( $nav_id, $position, $order );
            }
            if ( $badge_id ) {
                $element = $this->_loadBadge( $badge_id, $position, $order );
            }
            if ( !$element ) continue;

            $order_of = $this->find_actual_order( $order, $ordered_set ) ;
            $this->_navSet[ $position ][ $order_of ] = &$element;
            unset( $element );
        }

    }

    function find_actual_order( $order, &$ordered_set ) {
        if ( !isset( $ordered_set[$order])) {
            $ordered_set[$order] = 1;
            return $order;
        }
        return $this->find_actual_order( ++$order, $ordered_set );
    }

    function &_loadNavElement( $nav_id, $nav_block, $order ) {
        $false = false;
        require_once( 'AMP/Content/Nav.inc.php' );
        $nav = &new NavigationElement( $this->dbcon, $nav_id );
        if (! $nav->hasData() ) return $false;

        $nav->initTemplate( $nav_block, $this->template );
        $nav->order = $order;
        return $nav;
    }

    function &_loadBadge( $badge_id, $nav_block, $order ) {
        $false = false;
        require_once( 'AMP/Content/Badge/Badge.php' );
        $badge = &new AMP_Content_Badge( $this->dbcon, $badge_id );
        if (! $badge->hasData() ) return $false;
        return $badge;

    }

    ###################################
    ### public nav location methods ###
    ###################################

    function findNavs_Article() {
        return $this->findNavs_listSection( 'navLayoutsBySection');
        //return $this->findNavs_listSection( AMP_CONTENT_NAV_SECTION_PAGE_FIELD );
    }

    function findNavs_listClass() {
        $layout_set = &AMPContent_Lookup::instance( 'navLayoutsByClass');
        if ( !$layout_set ) return $this->findNavs_listSection( );
        $layout_id = array_search( $this->page->getClassId( ), $layout_set );
        if ( !$layout_id ) return $this->findNavs_listSection( );
        return $this->findNavs_standardBase( $layout_id, 'findNavs_listSection' );
        //return $this->findNavs_standardBase( "classlist", $this->page->getClassId(), 'findNavs_listSection' );
    }

    function findNavs_IntroText() {
        $layout_set = &AMPContent_Lookup::instance( 'navLayoutsByIntrotext');
        if ( !$layout_set ) return $this->findNavs_listSection( );
        $layout_id = array_search( $this->page->getIntroId( ), $layout_set );
        if ( !$layout_id ) return $this->findNavs_listSection( );
        return $this->findNavs_standardBase( $layout_id, 'findNavs_listSection' );
        //return $this->findNavs_standardBase( 'moduleid', $this->page->getIntroId(), 'findNavs_listSection' );
    }

    function findNavs_default() {
        $layout_set = &AMPContent_Lookup::instance( 'navLayoutsByIntrotext');
        if ( !$layout_set ) return false;
        $layout_id = array_search( AMP_CONTENT_INTRO_ID_DEFAULT, $layout_set );
        if ( !$layout_id ) return false;
        $this->_default_attempted = true;
        return $this->findNavs_standardBase( $layout_id );
    }


    //function findNavs_standardBase( $target_field, $target_value, $default_find_method = 'findNavs_default'  ) {
    function findNavs_standardBase( $layout_id, $default_find_method = 'findNavs_default'  ) {
        $locationSet = &new NavigationLocationSet( $this->dbcon );
        $locationSet->addCriteriaLayout( $layout_id );
        if ($position = $this->_getFindPosition()) {
            $locationSet->addCriteriaPositionPrefix( $position );
        }
        $locationSet->readData();

        if (!$locationSet->RecordCount()) {
            if ( $this->_default_attempted ) return false;
            return $this->$default_find_method();
        }

        return $locationSet->getArray();
    }

    function findNavs_listFrontpage() {
        $layout_set = &AMPContent_Lookup::instance( 'navLayoutsByIntrotext');
        if ( !$layout_set ) return $this->findNavs_default( );
        $layout_id = array_search( AMP_CONTENT_INTRO_ID_FRONTPAGE, $layout_set );
        if ( !$layout_id ) return $this->findNavs_default( );

        return $this->findNavs_standardBase( $layout_id );
        //return $this->findNavs_standardBase( "moduleid", AMP_CONTENT_CLASS_FRONTPAGE );
    }

    //function findNavs_listSection( $target_column = AMP_CONTENT_NAV_SECTION_LIST_FIELD ) {
    function findNavs_listSection( $lookup_name = 'navLayoutsBySectionList' ) {
        $map = &AMPContent_Map::instance( );
        $parent_set = $map->getAncestors( $this->page->getSectionId() );
        if (empty($parent_set)) return $this->findNavs_default();

        $layout_set = &AMPContent_Lookup::instance( $lookup_name );
        $layout_id = false;
        if ( !$layout_set ) return $this->findNavs_default( );

        foreach( $parent_set  as $section_id => $section_name  ){
            if ( $layout_id = array_search( $section_id, $layout_set )) break;
        }
        if ( !$layout_id ) return $this->findNavs_default( );

        #$target_crit = $target_column . " in (" . join( ', ', array_keys( $parent_set )) . ")" ;

        $locationSet = &new NavigationLocationSet( $this->dbcon );
        $locationSet->addCriteriaLayout( $layout_id );

        if ($position = $this->_getFindPosition()) {
            $locationSet->addCriteriaPositionPrefix( $position );
        }
        $locationSet->readData();
        if (!$locationSet->RecordCount()) return $this->findNavs_default();
        return $locationSet->getArray( );

        /*
        foreach( $parent_set as $section_id => $name ) {
            if (!($results = $locationSet->filter( $target_column , $section_id ))) continue;
            return $results;
        }

        return $this->findNavs_default();
        */
    }

    ##############################
    ### private helper methods ###
    ##############################

    function _setFindPosition( $position ) {
        return ($this->_current_seek_position = $position);
    }

    function _getFindPosition() {
        if (!isset($this->_current_seek_position)) return false;
        return $this->_current_seek_position;
    }

    function _getFindMethod() {
        $find_method = "nothing";

        if ($this->page->isArticle()) return 'findNavs_Article';
        if ($this->page->isTool() ) return 'findNavs_IntroText';

        if ($listType = $this->page->isList()) $find_method = 'findNavs_list'. ucfirst( $listType );
        if (! method_exists( $this, $find_method ) ) $find_method = "findNavs_default"; 

        return $find_method;
    }

}
?>
