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
     * init ( &$udm )
     *
     * This method should be called by all plugins to ensure that any available
     * optons and fields are registered properly. Two optional callback
     * methods can be passed. These methods must be defined in the plugin
     * class.
     *
     *****/

    function init ( &$udm ) {

        $this->udm = &$udm;
        $this->dbcon = &$udm->dbcon;

    }

    /*****
     *
     * Execute; this method performs the plugin action.
     *
     * This function should always be overfidden in plugins to enable actions
     * to be performed.
     *
     *****/

    function execute ( $options = null ) {

        return false;

    }

    /*****
     *
     * Plugin Field Definition Methods
     *
     *****/

    function getFields ( $fields = null ) {

        if (isset($fields)) {

            if (!is_array( $fields )) $fields = array( $fields );

            return array_intersect_key( $this->fields, $fields );

        }

        return $this->fields;

    }

    function addField ( $field ) {

        $this->fields[ $field['id'] ] = $field;

        return &$this->fields[ $field['id'] ];

    }

    function removeField ( $field ) {

        return unset( $this->fields[ $field ] );

    }

    /*****
     *
     * Plugin Data Methods
     *
     * The data is *not* stored within the plugin, but rather in the UDM
     * object's data store.
     *
     *****/

    function getData ( $fields = null ) {

        return $this->udm->getData( $fields );

    }

    function setData ( $data ) {

        return $this->udm->setData( $data );

    }

    /*****
     *
     * Internal Functions
     *
     *****/

}

?>
