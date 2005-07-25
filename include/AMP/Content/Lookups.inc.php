<?php

require_once ('AMP/Content/Article/Set.inc.php');
require_once ('AMP/System/Lookups.inc.php');

class AMPContent_Lookup extends AMPSystem_Lookup {

    function AMPContent_Lookup () {
        $this->init();
    }

    function &instance( $type, $lookup_baseclass="AMPContentLookup" ) {
        return PARENT::instance( $type, $lookup_baseclass );
    }

}

class AMPContentLookup_Class extends AMPContent_Lookup {
    var $datatable = "class";
    var $result_field = "class";

    function AMPContentLookup_Class() {
        $this->init();
    }
}

class AMPContentLookup_Sections extends AMPContent_Lookup {
    var $datatable = "articletype";
    var $result_field = "type";

    function AMPContentLookup_Section() {
        $this->init();
    }
}

class AMPContentLookup_ActiveClasses extends AMPContentLookup_Class {
    
    var $dataset;
    
    function AMPContentLookup_ActiveClasses() {
        $articleset = & new ArticleSet ( AMP_Registry::getDbcon() );
        if (!( $counts = $articleset->getGroupedIndex( 'class' ))) return false;
        $class_set = & AMPContent_Lookup::instance( 'class' );
        $this->criteria = "id in (" . join( ', ', array_keys( $counts ) ). ")";
        $this->init();
    }
}


?>
