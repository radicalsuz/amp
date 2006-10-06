<?php
require_once( 'AMP/System/Component/Controller.php');

class AMP_Display_Controller extends AMP_System_Component_Controller {

    var $_path_list;
    var $_path_detail;
    var $_path_source;

    var $_class_list;
    var $_class_detail;
    var $_class_source;
    
    var $_action_default = 'list';
    var $_action_default_single = 'detail';

    var $_criteria_public = array( 'live' => 1 );
    var $_criteria = array( );

    var $_display_mode = 'public';

    var $_model;
    var $_model_id;

    var $_publicpage_request = false;
    var $_publicpage_list = false;
    var $_publicpage_detail = false;

    function AMP_Display_Controller( ) {
        $this->__construct( );
    }

    function execute( $output = false ){
        $value = parent::execute( $output );
        $intro_value = $this->_init_intro( $output );
        if ( $output ) {
            return $intro_value . $value;
        }
    }

    function _set_location( ){
        if ( !$this->_publicpage_detail && $this->_publicpage_list ) {
            require_once( 'AMP/Content/Page.inc.php');
            $page = & AMPContent_Page::instance( );
            $page->setIntroText( $this->_publicpage_list );
        }

    }

    function _init_intro( $direct_output = false ) {
        if (  $this->_publicpage_request ) {
            require_once( 'AMP/Content/Page.inc.php');
            $page = & AMPContent_Page::instance( );
            $page->setIntroText( $this->_publicpage_request );
            $intro = &$page->getIntroText( );

            if ( !$intro ) return false; 
            $intro_display = $intro->getDisplay( );

            if ( $direct_output ) {
                return $intro_display->execute( );
            } else {
                $this->_display->add( $intro_display, AMP_CONTENT_DISPLAY_KEY_INTRO );
            }
        }
    }

    function commit_detail( ) {
        $source = &$this->_load( 'source');
        if ( !$source->read( $this->_model_id ) || !$source->isLive( )) {
            AMP_make_404( );
            return false;
        }
        $detail_display = &$this->_load( 'detail', $source );
        $this->_display->add( $detail_display );
        $this->_publicpage_request = $this->_publicpage_detail;
        $this->_set_location( );
        return true;

    }

    function commit_list( ) {
        $list_display_class = $this->_get_class( 'list' );
        if ( !$list_display_class ) return false;
        $list_display = & new $list_display_class( false, $this->_criteria );
        $this->_display->add( $list_display );
        $this->_publicpage_request = $this->_publicpage_list;
        return true;
    }

    function __construct( ) {
        $this->_init_request( );
        $this->_init_display( );
        $this->_init_criteria( );
    }

    function _init_display( ) {
        require_once( 'AMP/Content/Manager.inc.php');
        $this->_display = & AMPContent_Manager::instance( );
    }

    function _init_request( ) {
        $url_vars = AMP_URL_Read( );
        $this->_request_vars = $_POST;
        if ( $url_vars ) {
            $this->_request_vars = array_merge( $_POST, $url_vars );
        }

        $request_id = $this->assert_var( 'id' );
        $request_action = $this->assert_var( 'action' );

        //pull useful info from request values
        if ( $request_action ){
            $this->request ( $request_action );
        } elseif( $request_id ) {
            $this->_model_id = $request_id;
            $this->request( $this->_action_default_single );
        }


    }

    function _init_criteria( ) {
        $crit_var = '_criteria_' . $this->_display_mode ;
        if( isset( $this->$crit_var )) {
            $this->_criteria = array_merge( $this->_criteria, $this->$crit_var );
        }
    }

    function &_load( $component_type, $init_value = null ) {
        $empty_value = false;
        $class_name = $this->_get_class( $component_type );
        if ( !$class_name ) return $empty_value;

        if ( !isset( $init_value )) {
            $init_value = AMP_Registry::getDbcon( );
        }
        $result = & new $class_name( $init_value );
        return $result;


    }

    function _get_class( $component_type ) {
        $class_var = '_class_' . $component_type;
        if ( !isset( $this->$class_var )) return false;

        //read file if necessary
        if ( class_exists( $this->$class_var )) {
            return $this->$class_var;
        }

        $path_var = '_path_' . $component_type;
        if ( !isset( $this->$path_var )) return false;
        include_once( $this->$path_var );
        if ( !class_exists( $this->$class_var )) return false;

        return $this->$class_var;

    }
}

?>
