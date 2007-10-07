<?php

require_once( 'AMP/Form/XML.inc.php');
require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Display/Form.php');

class Share_Public_Form extends AMP_Display_Form {

    var $xml_fields_source = 'Modules/Share/Public/Fields.xml';
    var $submit = 
            array( 'save' => array( 
                        'label' => 'Share This',
                        ));
    var $action = AMP_CONTENT_URL_SHARE;

    var $udm_translations = array( 
        'sender_name'       =>  'Last_Name',
        'sender_email'      =>  'Email',
        'subject'           =>  'custom1',
//        'message'           =>  'custom2',

        'recipient_email'   =>  'custom5',
        'recipient_name'    =>  'custom6',
        'url'               =>  'custom3',
        'article'           =>  'custom4',

    );
    /*
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
    */

    function Share_Public_Form( ) {
        $this->__construct( );
        
        //$name = get_class( $this );
        //$this->init( $name, 'POST', AMP_CONTENT_URL_SHARE );
    }

    /*
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
    */

    function _after_init( ) {
        $url = ( isset( $_GET['url']) && $_GET['url']) ? $_GET['url'] : false;
        $article = ( isset( $_GET['article']) && $_GET['article']) ? $_GET['article'] : false;
        if ( !$url ) return false;
        $this->set( 'url', $url );

        $full_url = $url;
        if ( !( substr( $url, 0, 4) == 'http' )) {
            $full_url = AMP_SITE_URL . $url;
        }
        $this->set( 'message', $this->template_message( $full_url ));
        $this->set( 'article', $article );
    }

    function template_message( $url ) {
        if ( defined( 'AMP_RENDER_SHARE_MESSAGE') && function_exists( AMP_RENDER_SHARE_MESSAGE )) {
            $share_message = AMP_RENDER_SHARE_MESSAGE;
            return $share_message( $url, $this );
        }
        return $url;

    }

    function clean( $values ) {
        $new_values = array( );
        foreach( $values as $key => $value ) {
            if ( is_array( $value )) {
                $new_values[$key] = $this->clean( $value );
                continue;
            } 
            if ( strip_tags( $value ) != $value ) {
                return array( );
            }
            $new_values[ $key ] = $value;
        }
        if ( isset( $values['recipient_email'])) {
            if (eregi("\r",$values['recipient_email']) || eregi("\n",$values['recipient_email'])){
                trigger_error("possible spam at ".time()." :(".$content.")");
                return array( );
            }

        }
        if ( isset( $new_values['message'])) {
            if ( substr( $new_values['message'], strlen( $new_values['message'])-strlen( AMP_SITE_URL )) != AMP_SITE_URL ) {
                $new_values['message'] .= "\n\n\n This message sent to you from ".AMP_SITE_NAME .'  '.AMP_SITE_URL; 
            }
        }

        return parent::clean( $new_values );
        
    }

    function translate_for_udm( $data ) {
        $result = array( );
        foreach( $data as $key => $value ) {
            if ( isset( $this->udm_translations[$key])) {
                $result[ $this->udm_translations[$key]] = $value;
            }
        }
        return $result;
    }


}

?>
