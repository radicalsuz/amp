<?php

define( 'AMP_CONTENT_LISTTYPE_TYPE', 'section' );
define( 'AMP_CONTENT_NAV_SECTION_LIST_FIELD', 'typelist' );
define( 'AMP_CONTENT_NAV_SECTION_PAGE_FIELD', 'typeid' );
define( 'NAVIGATION_STANDARD_SEARCH', 'moduleid' );
define( 'NAVIGATION_STANDARD_VALUE', '1' );

require_once( 'AMP/Content/Nav/LocationSet.inc.php' );
require_once( 'AMP/Content/Nav.inc.php' );

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

    function NavigationManager( &$template) {
        $this->init( $template );
    }

    function init( &$template ) {
        $this->template = &$template;
        $this->dbcon = &$template->dbcon;

        if (!($this->page = &$template->page)) return;
        if (!($local_navs = $this->_locateNavs())) return;
        $this->_loadNavs( $local_navs );
       
    }
            
    function output( $position ) {
        $output = "";
        if (!isset($this->_navSet[ $position ])) return false;
        foreach( $this->_navSet[ $position ] as $navid => $navcopy ) {
            $nav = &$this->getElement( $navid );
            $output .= $nav->output();
        }
        return $output;
    }

    ###############################
    ### public accessor methods ###
    ###############################

    function &getElement( $navid ) {
        foreach ($this->_navSet as $position => $positionSet ) {
            if (isset($positionSet[ $navid ])) return $this->_navSet[ $position ][ $navid ];
        }
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
            $this->_setFindPosition( $id );
            if (!($result = $this->$find_method())) continue;
            $locationSet = array_merge( $locationSet, $result );
        }

        if (empty($locationSet)) return false;
        return $locationSet;
    }

    function _loadNavs( $locations ) {
        $this->_navSet = array();
        foreach ($locations as $locationDef) {
            $id = $locationDef[ 'navid' ];
            if (! ( $position = substr($locationDef['position'], 0, 1 ))) continue; 

            $nav = &new NavigationElement( $this->dbcon, $id );
            if (! $nav->hasData() ) continue;

            $nav->initTemplate( $position, $this->template );
            $this->_navSet[ $position ][ $id ] = &$nav;
        }

    }

    ###################################
    ### public nav location methods ###
    ###################################

    function findNavs_Article() {
        return $this->findNavs_listSection( AMP_CONTENT_NAV_SECTION_PAGE_FIELD );
    }

    function findNavs_listClass() {
        return $this->findNavs_standardBase( "classlist", $this->page->class_id, 'findNavs_listSection' );
    }

    function findNavs_IntroText() {
        return $this->findNavs_standardBase( 'moduleid', $this->page->getIntroId(), 'findNavs_listSection' );
    }

    function findNavs_default() {
        return $this->findNavs_standardBase( NAVIGATION_STANDARD_SEARCH , NAVIGATION_STANDARD_VALUE);
    }


    function findNavs_standardBase( $target_field, $target_value, $default_find_method = 'findNavs_default'  ) {
        $locationSet = &new NavigationLocationSet( $this->dbcon );
        $locationSet->addCriteria( $target_field . "=" . $this->dbcon->qstr( $target_value ) );
        if ($position = $this->_getFindPosition()) {
            $locationSet->addCriteria( 'position like ' . $this->dbcon->qstr( $position.'%' ) );
        }
        $locationSet->readData();

        if (!$locationSet->RecordCount()) {
            if ( $target_field==NAVIGATION_STANDARD_SEARCH && $target_value==NAVIGATION_STANDARD_VALUE ) return false;
            return $this->$default_find_method();
        }

        return $locationSet->getArray();
    }

    function findNavs_listFrontpage() {
        return $this->findNavs_standardBase( "moduleid", AMP_CONTENT_CLASS_FRONTPAGE );
    }

    function findNavs_listSection( $target_column = AMP_CONTENT_NAV_SECTION_LIST_FIELD ) {

        $locationSet = &new NavigationLocationSet( $this->dbcon );
        $parent_set = $this->page->map->getAncestors( $this->page->section_id );
        if (empty($parent_set)) return false;

        $target_crit = $target_column . " in (" . join( ', ', array_keys( $parent_set )) . ")" ;
        $locationSet->addCriteria( $target_crit );
        if ($position = $this->_getFindPosition()) {
            $locationSet->addCriteria( 'position like ' . $this->dbcon->qstr( $position.'%' ));
        }
        $locationSet->readData();
        if (!$locationSet->RecordCount()) return $this->findNavs_default();

        foreach( $parent_set as $section_id => $name ) {
            if (!($results = $locationSet->filter( $target_column , $section_id ))) continue;
            return $results;
        }

        return $this->findNavs_default();
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

        if ($listType = $this->page->isList()) $find_method = 'findNavs_list'.$this->_localizeListType( $listType );
        if (! method_exists( $this, $find_method ) ) $find_method = "findNavs_default"; 

        return $find_method;
    }

    function _localizeListType( $listType ) {
        $prefix = 'AMP_CONTENT_LISTTYPE_' ;
        $global_listTypes = filterConstants( $prefix ); 
        if (!($local_listType = array_search( $listType, $global_listTypes ))) return false;
        return ucfirst( strtolower($local_listType));
    }


}
?>
