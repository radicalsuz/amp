<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Article/Comment/Public/ComponentMap.inc.php');

class Article_Comment_Public_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';
    var $allow_copy = false; 
    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Save Comment'),
            )
    ));


    function Article_Comment_Public_Form( ) {
        $name = 'comments';
        $this->init( $name );
    }

}
?>

