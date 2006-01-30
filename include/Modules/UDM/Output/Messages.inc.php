<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Messages_Output extends UserDataPlugin {

    var $_available = false;
    var $_css_class_error = 'page_error';
    var $_css_class_result = 'page_result';

    function UserDataPlugin_Messages_Output( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }

    function execute( ){
        $output = "";
        if ( $this->udm->hasErrors( ))    $output .= $this->_outputErrors( );
        if ( $this->udm->hasResults( ))   $output .= $this->_outputResults( );
    }

    function _outputErrors( ){
        $output = "";
        foreach ( $this->udm->getErrors() as $type => $error ) {
            $handler =& $this->udm->getErrorHandler($type);
			$message = $handler ? $this->_getHandlerOutput( $handler, $error ) : $error;
            $output .= $this->_formatError( $message );
        }
        return $output;
                
    }

    function _getHandlerOutput( $handler, $message ) {
        if (!is_array($message)) $message = array($message);
        return call_user_func_array($handler, $message);
    }

    function _formatError( $message ){
        return "<span class=\"".$this->_css_class_error."\">$message</span>\n";
    }
    function _outputResults( ){
        $output = "";
        foreach ( $this->udm->getResults() as $result ) {
            $output .=  "<span class=\"".$this->_css_class_result."\">$result</span>\n";
        }
        return $output;
                
    }


}

?>
