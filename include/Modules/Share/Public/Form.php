<?php

require_once( 'AMP/Form/XML.inc.php');
require_once( 'AMP/System/Form/XML.inc.php');

class Share_Public_Form extends AMPSystem_Form_XML {

    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Share This'),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel'),
            )
    ));

    function Share_Public_Form( ) {
        $name = get_class( $this );
        $this->init( $name, 'POST', AMP_CONTENT_URL_SHARE );
    }

    function adjustFields( $fields ) {
        $new_fields = array( ) ;
        $save_fields = array( ) ;
        foreach( $fields as $key => $def ) {
            if ( strpos( $key, 'recipient') === 0 ) {
                $save_fields[$key] = $def;
                $save_fields[$key]['required'] = 0;
                $key .= "[0]";
            }
            $new_fields[ $key ] = $def;
        }
        for ( $i=1;$i<10;++$i) {
            foreach( $save_fields as $key => $def ) {
                $new_key = $key . '['.$i.']';
                $new_fields[$new_key] = $def;
            }
        }
        return $new_fields;
        
    }

}

?>
