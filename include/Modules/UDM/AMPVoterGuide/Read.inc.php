<?php

require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'Modules/VoterGuide/List.inc.php' );

class UserDataPlugin_Read_AMPVoterGuide extends UserDataPlugin {

    var $_available = false;
    var $_field_prefix = 'plugin_AMPVoterGuide';

    var $options = array(
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
        );

    function UserDataPlugin_Read_AMPVoterGuide ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $this->fields = array(

            'voterguide_list' => array(
                'type'=>'html',
                'public'=>false,
                'enabled'=>true
                )
            );
        $this->insertAfterFieldOrder( array_keys( $this->fields ) );
    }

    function execute( $options = array( )) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];
        
        if ( $this->udm->admin ) {
            /* changed order of arguments here for consistency AP 2007-04 */
            //$this->udm->unregisterPlugin( 'Save', 'AMPVoterGuide' );
            //$this->udm->unregisterPlugin( 'PositionSave', 'AMPVoterGuide' );
            $this->udm->unregisterPlugin( 'AMPVoterGuide', 'Save' );
            $this->udm->unregisterPlugin( 'AMPVoterGuide', 'PositionSave' );
        }

        $vgList = &new VoterGuide_List ( $this->dbcon );
        $vgList->getGuidesByOwner( $uid );

        $this->udm->fields[ $this->addPrefix('voterguide_list') ]['values'] = $this->inForm($vgList->output());
    }
}
?>
