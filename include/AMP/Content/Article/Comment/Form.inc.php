<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Article/Comment/ComponentMap.inc.php');

class ArticleComment_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';

    function ArticleComment_Form( ) {
        $name = 'comments';
        $this->init( $name );
    }

    function setDynamicValues( ){
       $this->addTranslation( 'date', '_makeDbDateTime', 'get');
    }

}

?>
