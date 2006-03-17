<?php

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
        $add_method = method_exists( current( $objectSet ), 'addObserver') ?
                        'addObserver' : 'add_observer';
        if ( !method_exists( current( $objectSet ), $add_method )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED, get_class( current( $objectSet )), $add_method, get_class( $this )) );
            return false;
        }
        //attach same instance to each class
        foreach ( $objectSet as $key => $target_object ){
            $objectSet[$key]->$add_method( $this );
        }
        return true;
    }

}

?>
