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

class AMPContentLookup_Hotwords extends AMPContent_Lookup {

    var $datatable = "hotwords";
    var $result_field = " Concat( ' <a href=\"', url, '\">', word, '</a> ') as newlink";
    var $id_field = " Concat( ' ', word, ' ') as hotword";

    function AMPContentLookup_Hotwords() {
        $this->criteria = "publish=1";
        $this->init();
    }
}

class AMPContentLookup_SectionHeaders extends AMPContent_Lookup {

    var $datatable = "articles";
    var $result_field = "Max(id) as art_id";
    var $id_field = "type";
    
    function AMPContentLookup_SectionHeaders() {
        $this->criteria = "class = ".AMP_CONTENT_CLASS_SECTIONHEADER." AND publish=".AMP_CONTENT_STATUS_LIVE . " AND !isnull( type ) group by type";
        $this->init();
    }

}
class AMPContentLookup_SectionFooters extends AMPContent_Lookup {

    var $datatable = "articles";
    var $result_field = "Max(id) as art_id";
    var $id_field = "type";
    
    function AMPContentLookup_SectionFooters() {
        $this->criteria = "class = ".AMP_CONTENT_CLASS_SECTIONFOOTER." AND publish=".AMP_CONTENT_STATUS_LIVE . " group by type";
        $this->init();
    }

}

class AMPContentLookup_SectionParents extends AMPContent_Lookup {

    var $datatable = "articletype";
    var $result_field = "parent";

    function AMPContentLookup_SectionParents() {
        $this->init();
    }
}

class AMPConstantLookup_Listtypes extends AMPConstant_Lookup {
    var $_prefix_values = "AMP_SECTIONLIST";
    var $_prefix_labels = "AMP_TEXT_SECTIONLIST";

    function AMPConstantLookup_Listtypes() {
        $this->init();
    }

}

?>
