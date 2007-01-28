<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_Redirect extends UserDataPlugin_Save {
    var $available = true;
    var $_field_prefix = 'Redirect_Save';

    var $options = array( 
        'public_field' => array( 
            'type' => 'checkbox',
            'default' => false,
            'label' => 'Allow Public Users to set Value',
            'available' => true
        ),
        'option_label' => array( 
            'type' => 'text',
            'label' => 'Label for field',
            'default' => 'Add URL Alias',
            'available' => true
        )
    );

    function UserDataPlugin_Save_Redirect( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $options = $this->getOptions( );
        $this->fields = array( 
            'alias_name' => array( 
                'type' => 'text',
                'public' => $options['public_field'],
                'label' => $options['option_label'],
                'enabled' => true 
            )
        );
    }

    function save( $data ) {
        if ( !( isset( $data['alias_name']) && $data['alias_name']) ) {
            return true;
        }

        $alias_name = urlencode( $data['alias_name']);
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        $existing_items = $redirect->search( $redirect->makeCriteria( array( 'alias' => $alias_name )));
        if ( $existing_items ){
            foreach( $existing_items as $existing_redirect ){
                $existing_redirect->setTarget( $this->getURL( ));
                $existing_redirect->save( );
            }
            return true;
        }
        $redirect->setDefaults( );
        $redirect->setAlias( $alias_name );
        $redirect->setTarget( $this->getURL( ));
        return $redirect->save( );

    }

    function getURL( ) {
        return AMP_url_add_vars( AMP_CONTENT_URL_FORM_DISPLAY, array( 'modin=' . $this->udm->instance, 'uid='.$this->udm->uid ));
    }

    function getSaveFields( ) {
        return $this->getAllDataFields( );
    }
}

?>
