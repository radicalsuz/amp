<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );

class ContentSearch_Form extends AMPSearchForm {

    var $component_header = "Search Articles";

    function ContentSearch_Form (){
        $name = "AMP_ContentSearch";
        $this->init( $name );
    }

    function setDynamicValues() {
        $map = &AMPContent_Map::instance();
        $this->setFieldValueSet( 'type',    $map->selectOptions() );
        $this->setFieldValueSet( 'class',   AMPContent_Lookup::instance('activeClasses'));
    }

    function getComponentHeader() {
        return $this->component_header;
    }

    function _formFooter() {
        return '&nbsp;&nbsp;&nbsp;<a href="article_list.php" class="standout">View All Articles</a>';
    }


}
?>