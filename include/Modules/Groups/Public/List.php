<?php

require_once( 'AMP/Display/List.php');
require_once( 'Modules/Calendar/Event.php');

class Groups_Public_List extends AMP_Display_List {
    var $name = 'GroupsList';
    var $_source_object = 'Group';
    var $_suppress_messages = true;

    var $_pager_active = true;
    var $_pager_limit = 100;
    var $_class_pager = 'AMP_Display_Pager_Content';
    var $_path_pager = 'AMP/Display/Pager/Content.php';

    var $_css_class_title = 'eventtitle';
    var $_css_class_byline = 'eventsubtitle';
    var $_css_class_blurb = 'text';
    var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => 'date, lstate, lcity'
    );

    var $_search; 
    var $_source_criteria = array( 'live' => 1, 'repeat' => 0, 'current' => 1 );

    function Groups_Public_List( $source = false, $criteria = array( ) ) {
        $source = false;
        $this->__construct( $source, $criteria );
    }

    function _renderItem( &$source ) {
        return $this->_renderer->link( $source->getURL( ), $source->getShortLocation( ) . ': ' . $source->getName( ), array( 'class' => $this->_css_class_title ))
                . $this->_renderer->newline( )
                . $this->_renderer->inSpan( DoDate( $source->getItemDate( ), 'l, F jS Y' ) 
                                                    . ( $source->getItemDate( ) ? $this->_renderer->space( 2 ) : '' ) 
                                                    . $source->getData( 'time'), 
                                                array( 'class' => $this->_css_class_byline )) 
                . $this->_renderer->in_P( $source->getBlurb( ), array( 'class' => $this->_css_class_blurb ));
    }

    function _init_criteria( ) {
        $this->_init_search( );
        /*
        if ( isset( $this->_source_criteria['date']) 
            &&  ((   isset( $this->_source_criteria['date']['Y']) && $this->_source_criteria['date']['Y'])
                || ( isset( $this->_source_criteria['date']['M'])  && $this->_source_criteria['date']['M']))) {
            unset( $this->_source_criteria['current']);
        }
        //legacy support for existing URLs
        if ( isset( $_REQUEST['old']) && $_REQUEST['old'] == 'all') {
            unset( $this->_source_criteria['current']);
            unset( $this->_source_criteria['date']);
        }
        if ( isset( $_REQUEST['old']) && $_REQUEST['old'] && !isset( $this->_source_criteria['old'] )) {
            $this->_source_criteria['old'] = $_REQUET['old'];
        }
        if ( isset( $_REQUEST['area']) && $_REQUEST['area'] && !isset( $this->_source_criteria['area'] )) {
            $this->_source_criteria['area'] = $_REQUEST['area'];
        }
        */

    }

    function _init_search( ) {
        require_once( 'AMP/System/ComponentLookup.inc.php');
        $map = ComponentLookup::instance( get_class( $this ));
        $search = $map->getComponent( 'search');
        if ( !$search ) return;
        $search->Build( true );
        $search_criteria = array( );

        if ( $search->submitted( )){
            $search_criteria = $search->getSearchValues( );
        } else {
            $search->applyDefaults( );
        }

        $this->_search = &$search;

        $this->_source_criteria = array_merge( $this->_source_criteria, $search_criteria );
    }

    function _output_empty( ) {
        $this->message( AMP_TEXT_SEARCH_NO_MATCHES );
        return $this->render_search( ) . AMP_TEXT_SEARCH_NO_MATCHES;
    }

    function _renderHeader( ) {
        return $this->render_search( ) ;

    }

    function render_search( ) {
        if ( !isset( $this->_search )) return false;
        return $this->_search->execute( );
    }

}


?>
