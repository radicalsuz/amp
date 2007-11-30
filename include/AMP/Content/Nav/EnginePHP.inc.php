<?php
require_once( 'AMP/Content/Nav/EngineHTML.inc.php');

class NavEngine_PHP extends NavEngine {
    var $_engine_type = 'PHP';

    function NavEngine_PHP( &$nav ){
        $this->init( $nav );
    }

    function execute( ) {
        if ( $badge_id = $this->nav->getBadgeId( )) return $this->do_badge( $badge_id );

        if (!($filename = $this->nav->getIncludeFile())) return false;
        $fullpath = file_exists_incpath( $filename );
        if ( !$fullpath ) $fullpath = file_exists_incpath( 'AMP/Nav/' . $filename );
        if ( !$fullpath ) return false;

        $nav_class = $this->nav->getIncludeClass( );
        $nav_function = $this->nav->getIncludeFunction( );

        if ( !$nav_class && !$nav_function ) {
            return $this->_raw_include( $fullpath );
        }

        //include the file -- don't make a mess in these guys!
        include_once( $fullpath ) ;

        if ( !$nav_class && is_callable( $nav_function )) {
            return $nav_function( $this->get_arguments( ));
        }

        return $this->_raw_include( $fullpath );
        /*
        $nav = false;
        if ( $nav_class && class_exists( $nav_class )) {
            $nav = &new $nav_class( );
        }

        if ( $nav ) {
            if ( is_callable( array( $nav, $nav_function ))) {
                return $nav->$nav_function( );
            }
            if ( method_exists( $nav, 'execute' )) {
                return $nav->execute( );
            }
        }
        */


    }

    function do_badge( $badge_id ) {
        require_once( 'AMP/Content/Badge/Badge.php');
        $badge = new AMP_Content_Badge( AMP_Registry::getDbcon( ), $badge_id );
        if( $badge->hasData( )) {
            #return $badge->execute( );
            $result = $badge->execute( );
            return $result;
        }
        return false;

    }

    function get_arguments( ) {
        $args = $this->nav->getIncludeFunctionArguments( );
        require_once( 'AMP/Content/Badge/Badge.php');
        $badge = new AMP_Content_Badge( AMP_Registry::getDbcon( ));
        $badge->setData( array( 'include_function_args' => $args ));
        return $badge->getIncludeFunctionArguments( );
    }

    function _raw_include( $fullpath ){
        ob_start();
        extract( $GLOBALS );
        include_once( $fullpath );
        $include_value = ob_get_contents();
        ob_end_clean();

        return $include_value;

    }
}
?>
