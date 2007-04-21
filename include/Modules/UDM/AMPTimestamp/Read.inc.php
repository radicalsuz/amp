<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Read_AMPTimestamp extends UserDataPlugin {
    var $options     = array( '_userid' => array( 'available'=>false ));
    var $_time_created;
    var $_time_edited;
    var $_field_prefix = 'AMPTimestamp_Read';

    function UserDataPlugin_Read_AMPTimestamp ( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ){
        $this->fields['last_edited'] = array( 
            'type' => 'static',
            'public'    => false,
            'enabled'   => true,
            'value'     => $this->_makeOutput( ));
    }

    function execute( $options = array( )){
        $options = array_merge( $this->getOptions( ), $options );
        $userid = (isset($options['_userid'])) ? $options['_userid'] : $this->options['_userid']['value'];
        if ( $this->readTime( $userid )) $this->setData( array( 'last_edited' => $this->_makeOutput( )));
    }

    function readTime( $userid ) {
        $sql = 'SELECT id, UNIX_TIMESTAMP( timestamp ) as edited, UNIX_TIMESTAMP( created_timestamp ) as created from userdata where id = ' . $this->dbcon->qstr( $userid );
        if ( !( $rs = $this->dbcon->GetRow( $sql ))) {
            trigger_error( 'timestamp read failed for userdata record #'.$userid);
            return false;
        }
        $this->setTimeEdited( $rs['edited'] );
        $this->setTimeCreated($rs['created']);
        return true;
        
    }

    function getTimeEdited( ){
        return $this->_time_edited;
    }

    function getTimeCreated( ) {
        return $this->_time_created;
    }

    function setTimeEdited( $value ){
        return $this->_time_edited = $value ;
    }

    function setTimeCreated( $value ) {
        return $this->_time_created = $value;
    }

    function _makeOutput( ){
        if ( !$edited = $this->getTimeEdited( )) return false;

            return  '<div class="item_timestamp">' 
                    . AMP_TEXT_UPDATED. ':&nbsp;' 
                    . date( 'Y-m-d g:i a',$edited) . '&nbsp;&nbsp;<BR />'
                    . AMP_TEXT_CREATED . ':&nbsp;'
                    . date( 'Y-m-d g:i a', $this->getTimeCreated( )) . '&nbsp;&nbsp;' 
                    . '</div>';
    }
}

?>
