<?php


class BlastMessage extends AMPSystem_Data_Item {
    var $datatable = PHPLIST_TABLE_MESSAGE;
    var $_message_formats = array( 
        'default' => array( 
                'htmlformatted' => 0, 'format'=>'text'),
        'html' => array( 
                'htmlformatted' => 1, 'format'=>'text and HTML')
        );

    function BlastMessage( &$dbcon, $message_id ) {
        $this->init( $dbcon, $message_id );
    }

    function setFormat( $format='text') {
        if ( 'html' == $format ) return $this->mergeData( $this->_message_formats[$format] );
        
        return $this->mergeData( $this->_message_formats['default'] );
    }

    function setEmbargo( $timestamp = null ) {
        $this->mergeData( array( 'embargo' => date( 'YmdHis', $timestamp)));
    }

    function setMessage( $body, $body_header = false, $footer = false ) {
        $msg = ($body_header ? $body_header ."\n\n" : '') . $body . ($footer ? "\n\n-- \n". $footer : '');
        $this->mergeData( array( 'message' => $msg ));
    }

    function submit( ) {
        $this->mergeData( array( 'status' => 'submitted'));
        if ( isset( $this->id )) $this->clearMessageRecords( );
        $this->save( );
        

    }
    function clearMessageRecords( ) {
        $messageRecords = &new BlastSentMessage_Set( $this->dbcon );
        $messageRecords->deleteMessage( $this->id );
    }
    function queue($list_id) {
        $record = &new BlastSentMessage( $this->dbcon );
        $record->queue( $this->id, $list_id );
    }

}

?>
