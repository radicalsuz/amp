<?php

require_once('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Read_AMP extends UserDataPlugin {

    // Basic descriptive data.
    var $short_name  = 'udm_amp_read';
    var $long_name   = 'UserData Read Plugin';
    var $description = 'Reads core UserData from the AMP database';

    // We take one option, a userid, and no fields.
    var $options     = array( '_userid' => array( 'available'=>false
                                                  ),
                              'admin'   => array( 'value' => null) );
    var $fields      = array();

    // Available for use in forms.
    var $available   = true;

    function UserDataPlugin_Read_AMP ( &$udm, $plugin_instance=null  ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute ( $options = array( )) {

        $dbcon = $this->udm->dbcon;
        $this->_field_prefix="";

        // Check for the existence of a userid.
        if (!isset( $options['_userid'] ) &&
            !isset($this->options['_userid']['value'] )) return false;

        $userid = (isset($options['_userid'])) ? $options['_userid'] : $this->options['_userid']['value'];

        // Fetch the data.
        $sql = "SELECT * FROM userdata WHERE id=" . $dbcon->qstr( $userid );

        $rs = $dbcon->CacheExecute( $sql )
                or trigger_error( "Unable to fetch information about record #" . $userid . ': ' . $dbcon->ErrorMsg() );

        if ($userData = $rs->FetchRow()) {
            // Populate the form with the data we've fetched.
            $this->setData( $userData );

            // Set the primary uid to $userid
            $this->udm->uid = $userid;

            $this->udm->addFields( $this->uidField() );
            return true;

        }
        return false;

    }

    function uidField() {
        return array (
            'uid' => array(
                'public'=> true,
                'default' => $this->udm->uid,
                'value' => $this->udm->uid,
                'type' => 'hidden',
                'constant' => true,
                'enabled'=> true ) );
    }

}

?>
