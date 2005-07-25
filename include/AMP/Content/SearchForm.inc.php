<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'AMP/System/XMLEngine.inc.php' );

class ContentSearch_Form extends AMPSearchForm {

    var $component_header = "Search Articles";

    function ContentSearch_Form (){
        $name = "AMP_ContentSearch";
        $this->init( $name );
        if ($this->addFields( $this->readFields() )) {
            $this->setDynamicValues();
        }
    }

    function setDynamicValues() {
        $map = &AMPContent_Map::instance();
        $this->setFieldValueSet( 'type',    $map->selectOptions() );
        $this->setFieldValueSet( 'class',   AMPContent_Lookup::instance('activeClasses'));
    }


    function readFields() {
        $sourcefile = 'AMP/Content/SearchFields' ;
        $fieldsource = &new AMPSystem_XMLEngine( $sourcefile ); 

        if ($fields = $fieldsource->readData()) {
            return $fields;
        }

        trigger_error ('field read failed for '.$sourcefile );
        return false;
    }

    function getComponentHeader() {
        return $this->component_header;
    }

    function _formFooter() {
        return '&nbsp;&nbsp;&nbsp;<a href="article_list.php" class="standout">View All Articles</a>';
    }


}
?>
