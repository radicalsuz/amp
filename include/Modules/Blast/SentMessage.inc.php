<?php

class BlastSentMessage extends AMPSystem_Data_Item {
    function BlastSentMessage( &$dbcon ) {
        $this->init( $dbcon );
    }

    function queue( $message_id, $list_id ) {
        $base_data = array( 
            'messageid' => $message_id,
            'listid'    => $list_id,
            'entered'   => date( 'Y-m-d'),
            'modified'  => date( 'YmdHis')
            );
        $this->setData( $base_data );
        return $this->save( ) ;
    }

}
?>
