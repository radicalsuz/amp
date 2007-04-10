<?php

require_once( 'AMP/System/List/Request.inc.php');
require_once( 'AMP/System/User/Profile/Profile.php');

class Calendar_List_Request extends AMP_System_List_Request {

    function Calendar_List_Request( &$source ){
        $this->init( $source );
    }

    function commitActionLocal( &$target_set, $action, $args = null ){
        if ( $action != 'export' ) return false;
        $this->export( $target_set, $args );
        return true;
    }

    function export( &$target_set, $args = null ){
		$sample = current($target_set);
		$keys = $sample->export_keys();
		$dump = array();
		foreach($keys as $key ) {
			$blank_set[ $key ] = null;
		}

		foreach($target_set as $source) {
			$values = $source->getData();	
            $user_values = array( );

            if ( isset( $values['uid']) && $values['uid']) {
                $owner = new AMP_System_User_Profile( AMP_Registry::getDbcon( ), $values['uid']);
                if ( $owner->hasData( )) {
                    $owner_data = $owner->getData( );
                    unset( $owner_data['id'] );
                    $user_values = array_combine_key( $keys, $owner_data );
                } 
            }
			$safe_values = array_combine_key($keys, $values);
			$dump[ $source->id ] = array_merge($blank_set, $safe_values, $user_values );
		}
		require_once('AMP/Renderer/CSV.php');
		$renderer = new AMP_Renderer_CSV();
		$file =  $renderer->format(array( $keys ));
	 	$file .= $renderer->format($dump);	
		$renderer->header( date("Y_m_d__") . get_class($this));
		print $file;
		exit;
	}
}

?>
