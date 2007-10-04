<?php
require_once( 'AMP/Content/Page/Urls.inc.php');

class AMP_Content_Redirect_Handler {

    var $_extensions_ignore =  array( 'jpeg', 'jpg', 'gif', 'png' );
    var $_request_page;
    var $_request_uri;
    var $_request_extension;
    var $_request_status = 200;

    var $_status_messages = array( 
        200 => '200 OK',
        301 => '301 Moved Permanently',
        404 => '404 Not Found'
    );

    function AMP_Content_Redirect_Handler( ){
        $this->__construct( );
    }

    function _init_request_page( ) {
        $this->_request_uri = substr( $_SERVER['REQUEST_URI'], 1 );
        $this->_request_page = $_SERVER['PHP_SELF'];
    }

    function _init_request_vars( ){
    }

    function _init_no_search( ){
        $extension_start = strrpos( $this->_request_page, '.' );
        if ( !$extension_start ) return;

        $this->_request_extension = substr( $this->_request_page, $extension_start + 1 );
        if ( array_search( $this->_request_extension, $this->_extensions_ignore ) !== FALSE ) {
            $this->_request_status = 404;
            $this->sendStatus();
            exit;
        }

    }

    function __construct( ){
        $this->_init_request_page( );
        $this->_init_request_vars( );
        $this->_init_no_search( );
    }

    function execute( ){
        if ( $this->_checkCustomFolder( )) return ;
        
        $this->_request_status = 404;
        if ( $this->_checkRedirects( ))    return ;
        $this->commit_default( ); 
    }

    function _checkCustomFolder( ){
        $custom_folder_filename = AMP_LOCAL_PATH . "/custom/" . $this->_request_page;
        if ( !file_exists( $custom_folder_filename )) return false;
        $_SERVER['PHP_SELF'] = "custom/" .  $this->_request_page;
        ob_start( );
        extract( $GLOBALS );
        include( $custom_folder_filename );
        print ob_get_clean( );
        return true;
    }

    function _checkRedirects( ){
        $found_redirect = false;
        $found_redirect = &$this->_searchRedirects( 
                        array(  'alias'  => $this->_request_uri, 
                                'status' => AMP_CONTENT_STATUS_LIVE ));
        if ( !$found_redirect ){
            $found_redirect = &$this->_searchRedirects( 
                            array(  'conditional_alias'  => $this->_request_uri, 
                                    'status' => AMP_CONTENT_STATUS_LIVE ));
        } 
        if ( !$found_redirect ) return false;
        $this->_request_status = 301;
        return $this->_sendRedirect( $found_redirect );
         
    }

    function commit_default( ){
        $this->sendStatus( );
        if ( strpos( AMP_CONTENT_URL_404, 'http' ) !== false ) {
            ampredirect(  AMP_CONTENT_URL_404 );
        } else {
            ampredirect (AMP_SITE_URL . AMP_CONTENT_URL_404 );
        }
    }

    function &_searchRedirects( $criteria ) {
        require_once( 'AMP/Content/Redirect/Redirect.php');
        $redirect_source = &new AMP_Content_Redirect( AMP_Registry::getDbcon( ));
        $results = $redirect_source->search( $redirect_source->makeCriteria( $criteria ) );
        if ( !$results ) return $results;
        $first_result = current( $results );
        return $first_result;
    }

    function sendStatus( ){
        header( 'Status: ' . $_SERVER['SERVER_PROTOCOL'] . $this->getStatusMessage( ));
    }

    function getStatus( ){
        return $this->_request_status;
    }

    function getStatusMessage( ){
        if ( isset( $this->_status_messages[ $this->getStatus( )])){
            return $this->_status_messages[ $this->getStatus( )];
        }
        return false;
    }

    function _sendRedirect( &$redirect  ){
        $target_url = $redirect->assembleTargetUrl( $this->_request_uri );
        $this->sendStatus( );
        ampredirect( $target_url );
        return true;
    }

}

?>
