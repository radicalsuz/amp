<?php
class UserDataPlugin_Save_SubmitRedirect extends UserDataPlugin {
    var $redirURL;
    var $available=true;
    var $options = array ( 
    'redirect_url'=>array( 
        'label'=>'Redirect URL',
        'type'=>'text',
        'available'=>'true',
        'default'=>''),
    'redirect_function'=>array( 
        'label'=>'Redirect Function',
        'type'=>'text',
        'available'=>'true',
        'default'=>'')
    );

    function UserDataPlugin_Redirect_Output ( &$udm, $plugin_instance) {
        $this->init( $udm, $plugin_instance);
    }

    function execute ( $options){
        $options = array_merge( $this->getOptions( ), $options);
        $redirURL = $this->makeRedirect( $options);
        if ( ! empty( $redirURL)){
            ampredirect( $redirURL);
        }
    }

    function makeRedirect ( $options){
        $redirURL = '';
        if ( $options['redirect_function'] !== ''){
            $redirURL = $options['redirect_function']( $this->udm->getData( ));
        } else {
            $redirURL = $options['redirect_url'];
        }
        return $redirURL;
    } 
}
?>
