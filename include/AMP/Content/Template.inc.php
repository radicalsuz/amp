<?php

/* * * * * * * * * * * * *
 * 
 * AMPContent_Template
 *
 * AMP 3.5.1
 * 2005-07-31
 * Author: austin@radicaldesigns.org
 *
 * * * * * */

define ( 'MEMCACHE_KEY_TEMPLATE' , 'CurrentTemplate' );
require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'AMP/Content/Nav/Manager.inc.php' );

class AMPContent_Template extends AMPSystem_Data_Item {

	var $_nav_positions = array('left' => 'l', 'right' => 'r');
	var $_nav_html = array();
    var $_current_template_version;
    
    var $datatable = "template";
    var $_navManager;
    var $name_field = 'name';

	function AMPContent_Template( &$dbcon, $id = null) {
        $this->init( $dbcon, $id );
        $this->_assignNavHtml();
    }

    function execute( $html ) {
        return $this->_placeContent( $html );
    }

    function placeNavigation( &$page ) {
        if (isset($this->_navManager)) return true;

        $this->_navManager = & new NavigationManager( $this, $page );
        foreach( $this->_nav_positions as $position => $prefix ) {
            if (! $this->containsNav( $position ) ) continue;
            $this->_placeNav( $position, $this->_navManager->output( strtoupper($prefix) ) );
        }

    }


    #############################
    ### public data accessors ###
    #############################

    //accepts $position as full word or single letter
	function getNavHtml($position, $element) {
        $local_position = strtolower($position);
        if (!isset($this->_nav_html[$local_position])) {
            $local_position = array_search( strtolower($position), $this->_nav_positions );
        }

        if (!isset($this->_nav_html[$local_position][$element])) return false;
		return $this->_nav_html[$local_position][$element];
	}

    function getNavPositions() {
        return $this->_nav_positions;
    }

	function getNavImagePath() {
		return $this->getData("imgpath");
	}

	function getNavRepeat() {
		return $this->getData("repeat");
	}

	function getHtmlTemplate() {
		return $this->getData("header2");
	}

	function getCSS() {
        return $this->getData('css');
	}

	function getPageHeader() {
		return $this->getData('extra_header');
	}

    function setPage( &$page ) {
        $this->page = &$page ;
    }

    function containsNav( $position ) {
        return (strpos( $this->_getCurrentTemplate(), "[-$position nav-]" ) !== FALSE );
    }

    function get_url_edit( ) {
        if ( !isset( $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_TEMPLATE, 'id='. $this->id );
    }

    ##############################
    ### private helper methods ###
    ##############################

    function _getCurrentTemplate() {
        if (!isset($this->_current_template_version)) {
            $this->_setCurrentTemplate( evalhtml($this->getHtmlTemplate()));
        }
        return $this->_current_template_version;
    }

    function _setCurrentTemplate( $html ) {
        $this->_current_template_version = $html;
    }

    function _placeNav ( $position, $html ) {
        $this->_setCurrentTemplate( str_replace( "[-$position nav-]", $html, $this->_getCurrentTemplate() ));
    }

    function _placeContent( $html ) {
        return str_replace( "[-body-]", $html, $this->_getCurrentTemplate() );
    }

    function _assignNavHtml() {
		foreach ($this->_nav_positions as $position => $prefix) {
			$this->_nav_html[$position] =  array( 
                'start_heading' => $this->getData($prefix."nav3"),
                'close_heading' => $this->getData($prefix."nav4"),
                'start_content' => $this->getData($prefix."nav7"),
                'close_content' => $this->getData($prefix."nav8"),
                'content_spacer' => $this->getData($prefix."nav9")
            );
		}
	}

    ###################################
    ### legacy compatibility method ###
    ###################################

    function globalizeNavLayout( $return_layout = false ) {

        $layout = array();
        $layout['lNAV_HTML_1'] = $this->getNavHtml('left', 'start_heading');
        $layout['lNAV_HTML_2'] = $this->getNavHtml('left', 'close_heading');
        $layout['lNAV_HTML_3'] = $this->getNavHtml('left', 'start_content');
        $layout['lNAV_HTML_4'] = $this->getNavHtml('left', 'close_content');
        $layout['lNAV_HTML_5'] = $this->getNavHtml('left', 'content_spacer');
        $layout['rNAV_HTML_1'] = $this->getNavHtml('right', 'start_heading');
        $layout['rNAV_HTML_2'] = $this->getNavHtml('right', 'close_heading');
        $layout['rNAV_HTML_3'] = $this->getNavHtml('right', 'start_content');
        $layout['rNAV_HTML_4'] = $this->getNavHtml('right', 'close_content');
        $layout['rNAV_HTML_5'] = $this->getNavHtml('right', 'content_spacer');
        $GLOBALS = array_merge( $GLOBALS, $layout );
        if ($return_layout) return $layout;
    }
}
?>
