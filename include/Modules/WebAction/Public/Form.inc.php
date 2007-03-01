<?php

require_once( 'AMP/System/Form/XML.inc.php' );
require_once( 'Modules/WebAction/Public/ComponentMap.inc.php' );
require_once( 'AMP/UserData.php');
require_once( 'Modules/WebAction/Lookups.inc.php');
require_once( 'Modules/WebAction/WebAction.php' );

class WebAction_Public_Form extends AMPSystem_Form_XML {
    var $_action_id;
    var $_modin;
    var $_action;
    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'send' => array(
                'type' => 'submit',
                'label' => 'Send My Message!')
            )
        ));

    function WebAction_Public_Form( $action_id = null ) {
        if ( isset( $action_id ) && !is_object( $action_id )) {
            $this->set_action( $action_id );
        }
        $name = 'WebAction';
        $this->_init_action( );
        $this->init( $name, 'POST', AMP_CONTENT_URL_ACTION );
    }

    function _init_action( ) {
        $form_set = AMP_lookup( 'formsByAction');
        if ( !isset( $this->_action_id )) {
            $this->_action_id = 1;
            $this->_modin = AMP_FORM_ID_WEBACTION;
        }
        $this->_action = &new WebAction( AMP_Registry::getDbcon( ), $this->_action_id );

        if ( !isset( $form_set[ $this->_action_id ])) {
            unset( $this->_modin );
            return;
        }
        $this->_modin = $form_set[ $this->_action_id ];

    }

    function set_action( $action_id ) {
        $this->_action_id = $action_id;
        $this->_init_action( );
    }

    function adjustFields( $fields ) {
        if ( !isset( $this->_modin )) return $fields;

        $udm = &new UserData( AMP_Registry::getDbcon( ), $this->_modin );
        $end_field = array_pop( $fields );
        $fields = array_merge( $fields, $udm->fields );
        array_push( $fields, $end_field );

        $target_array = $this->_action->getTargets( );
        
        if ( $target_array ) {
            $values = AMP_evalLookup( $fields['target']['lookup'] );
            $target_values = array_combine_key( $target_array, $values );
            unset( $fields['target']['lookup'] );
            $fields['target']['values'] = $target_values;
            $fields['target']['default'] = join( ",", $target_array );
        }


        return $fields;
    }
}

?>
