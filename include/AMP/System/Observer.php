<?php
define ( 'AMP_TEXT_ERROR_OBSERVER_UNSUPPORTED', 'Cannot attach %s to %s because addObserver method doesn\'t exist');

class AMP_System_Observer {

    function AMP_System_Observer( ){
        //init
    }

    function update( &$source, $action ){
        $update_method = 'on' . ucfirst( $action );
        if ( !method_exists( $this, $update_method )) return;
        return $this->$update_method( $source );
    }

    function attach( &$target ){
        $objectSet = array( );
        //assign the array
        if ( is_array( $target )){
            $objectSet = &$target;
        } else {
            $objectSet[] = &$target;
        }
        //check for interface
        if ( !method_exists( current( $objectSet ), 'addObserver')) {
            trigger_error( sprintf( AMP_TEXT_ERROR_OBSERVER_UNSUPPORTED, get_class( $this ), get_class( current( $objectSet ))) );
            return false;
        }
        //attach same instance to each class
        foreach ( $objectSet as $key => $target_object ){
            $objectSet[$key]->addObserver( $this );
        }
        return true;
    }

}

?>
