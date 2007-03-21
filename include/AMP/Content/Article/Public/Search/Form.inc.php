<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'Modules/Calendar/ComponentMap.inc.php');

class Article_Public_Search_Form extends AMPSearchForm {

    var $component_header = "Search Calendar";

    function Article_Public_Search_Form ( ) {
        $name = "Content_Search";
        $this->init( $name, 'GET', AMP_CONTENT_URL_SEARCH );
    }

}
?>
