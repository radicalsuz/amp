<?php

require_once( 'AMP/System/List/Request.inc.php');

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
			$safe_values = array_combine_key($keys, $values);
			$dump[ $source->id ] = array_merge($blank_set, $safe_values);
		}
		require_once('AMP/Display/Renderer/CSV.php');
		$renderer = new AMP_Display_Renderer_CSV();
		$file =  $renderer->format(array( $keys ));
	 	$file .= $renderer->format($dump);	
		$renderer->header( date("Y_m_d__") . get_class($this));
		print $file;
		exit;
	}
}

?>
