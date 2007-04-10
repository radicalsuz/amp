<?php

class AMP_System_Observer {

    function AMP_System_Observer( ){
        //init
    }

    function update( &$source, $action, $passthru_values = null ){
        $update_method = 'on' . ucfirst( $action );
        if ( !method_exists( $this, $update_method )) return;
        return $this->$update_method( $source, $passthru_values );
    }

    function attach( &$target ){
        $objectSet = array( );
        //assign the array
        if ( is_array( $target )){
            $objectSet = $target;
        } else {
            $objectSet[] = &$target;
        }
        $test_object = current( $objectSet );
        //check for interface
        $add_method = method_exists( $test_object , 'addObserver') ?
                        'addObserver' : 'add_observer';
        if ( !method_exists( $test_object, $add_method )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED, get_class( $test_object ), $add_method, get_class( $this )) );
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
