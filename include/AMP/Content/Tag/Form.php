<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Tag/Tag.php');

class AMP_Content_Tag_Form extends AMPSystem_Form_XML {

    function AMP_Content_Tag_Form() {
        $name = "AMP_Tag_Form"; 
        $this->init( $name, 'POST', AMP_SYSTEM_URL_TAG );
    }

    function _formFooter( ) {
        $id = $this->getIdValue( );
        if ( !$id ) return false;
        require_once( 'AMP/Content/Tag/Item/List/Items.php');
        $list = &new AMP_Content_Tag_Item_List_Items( AMP_Registry::getDbcon( ), array( 'tag' => $id ));
        return $list->execute( );

    }
}

?>
