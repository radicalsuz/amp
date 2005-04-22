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

        $this->useDefaults = false;
        return $this->doAction( 'Read', array( '_userid' => $userid, 'admin' => $this->admin ) );

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

        $options = array( 'admin' => array ( 'value' => $this->admin ) );

        if (!isset( $this->form )) {
            $result = $this->doPlugin( 'QuickForm', 'build', $options );
        }

        if ( !$result )     return false;
        if ( !$this->form ) return false;

        $this->modTemplateID = $this->_module_def['modidresponse'];
        $save_results = $this->doAction( 'Save', $options );

        if ($this->_module_def['useemail']) {
            print "Emailing user and admin if i can...";
            $this->doAction( 'EmailUser' );
            $this->doAction( 'EmailAdmin' );
        }

        return $save_results;

    }

    /*****
     *
     * findDuplicates ()
     *
     * Find Duplicate records in the UserData database.
     *
     *****/

    function findDuplicates () {

        return $this->doAction( 'DuplicateCheck' );

    }

    function _register_default_plugins () {

        // No plugins were attached to this module, but we can't very well
        // get along without data access functions. Register the default
        // AMP plugins.

        $r = $this->registerPlugin( 'AMP', 'Read'           ) and $r;
        $r = $this->registerPlugin( 'AMP', 'Save'           ) and $r;
        $r = $this->registerPlugin( 'AMP', 'DuplicateCheck' ) and $r;
        $r = $this->registerPlugin( 'AMP', 'Authenticate'   ) and $r;
        $r = $this->registerPlugin( 'AMP', 'EmailAdmin'     ) and $r;

    }
}

?>
