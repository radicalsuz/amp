<?php

class UserDataPlugin {

    // The UDM is passed to plugins to enable the runtime modificaion of
    // behaviour of the data modules. However, to whatever extent possible, the
    // UDM object should be accessed for reads only as much as possible.
    var $udm;
    var $dbcon;

    // $options contains information about available settings for the module.
    // $fields contains information about available user-fields for the module.
    var $options;
    var $fields;

    // $available, bool, denotes whether or not this module is available for
    // use by system admins via the plugin menu. True for available, false for
    // not.
    var $available;
    var $instance;

    // The executed flag should be set upon sucessful execution of the plugin.
    // This is to prevent multiple runs of the plugin.
    var $executed;

    /*****
     *
     * init ( ADOdb $dbcon [, str $options_callback [, str $fields_callback ] ] )
     *
     * This method should be called by all plugins to ensure that any available
     * optons and fields are registered properly. Two optional callback
     * methods can be passed. These methods must be defined in the plugin
     * class.
     *
     *****/

    function init ( &$udm, $options_callback = null, $fields_callback = null ) {

        $this->udm = &$udm;
        $this->dbcon = &$udm->dbcon;

        // Register Options
        $this->_register_options( $options_callback );

        // Register Fields
        $this->_register_fields( $fields_callback );

    }

    function getFields ( $admin = false ) {

    }

    function getOptions () {

    }

    function _register_optons ( $callback = null ) {

        if ( method_exists( $this, $callback ) ) {

        }

    }

    function _register_fields ( $callback = null ) {

        if ( method_exists( $this, $callback ) ) {


        }

    }

}

?>
