<?php

require_once( 'AMP/UserData.php' );

class UserDataInput extends UserData {

    function UserDataInput ( &$dbcon, $instance, $admin = false ) {

        $this->UserData( $dbcon, $instance, $admin );

    }

    ##################################
    ### Public Data Access Methods ###
    ##################################

    /*****
     *
     * getUser ( [ int userid ] )
     *
     * fetches user data for a given userid. If userid is not present,
     * the object should be populated with sufficient data to allow
     * plugins to perform a Query-By-Example.
     *
     * See specific plugin documentation for more information.
     *
     *****/

    function getUser ( $userid = null ) {

        return $this->doAction( 'read', array( '_userid' => $userid, 'admin' => $this->admin ) );

    }

    /*****
     *
     * saveUser ()
     *
     * saves the data stored in the object. Requires HTML_QuickForm object
     * to validate submitted values before saving. This object is created
     * if not already present.
     *
     * Plugins should use the $form->process() function to call internal
     * methods, saving user-submitted data only once it has been laundered
     * with the HTML_QuickForm object.
     *
     * See specific plugin documentation for more information.
     *
     *****/

    function saveUser () {

        $options = array( 'admin' => $this->admin );

        if (!isset( $this->form )) {

            $this->doPlugin( 'QuickForm', 'build', $options );

        }

        $this->modTemplateID = $this->_module_def['modidresponse'];

        return $this->doAction( 'save', $options );

    }

    /*****
     *
     * findDuplicates ()
     *
     * Find Duplicate records in the UserData database.
     *
     *****/

    function findDuplicates () {

        return $this->doAction( 'duplicate_check' );

    }

    function _register_default_plugins () {

        // No plugins were attached to this module, but we can't very well
        // get along without data access functions. Register the default
        // AMP plugins.

        $r = $this->registerPlugin( 'AMP', 'read' ) or $r;
        $r = $this->registerPlugin( 'AMP', 'save' ) or $r;
        $r = $this->registerPlugin( 'AMP', 'duplicate_check' ) or $r;
        $r = $this->registerPlugin( 'AMP', 'authenticate' ) or $r;
        $r = $this->registerPlugin( 'AMP', 'email_admin' ) or $r;

    }



}

?>
