<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Save extends UserDataPlugin {

    var $save_fields = array();

    var $options = array(
        'test' => array( 'test' => 'test1', 'testtest' => 'test2' )
    );

    function execute ( $options = null ) {

        // Since we only pass in the data that has actually been requested,
        // quit unless there are some fields to work with. 

        // First, give the plugin an opportunity to define which fields.
        if (method_exists( $this, 'getSaveFields' )) {
            $this->save_fields = $this->getSaveFields();
        }

        // If there are no fields requested, quit.
        if ( count($this->save_fields) === 0 ) {
            return false;
        }

        // Deletegate permissions. Note that the UDM will only return data that
        // the user has permissions to save.
        //
        // Since this data comes from the form, it's a good idea to stick to
        // these permissions, since other data may be unreliable.
        $data = $this->getData( $this->save_fields );
        return $this->save( $data );
    }

    function save ( $data ) {
        // This function *must* be overridden.
        trigger_error( "Error saving data. Please report this error to the system administrator." );
    }
}

?>
