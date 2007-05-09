<?php

require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'Modules/Calendar/ComponentMap.inc.php');

class Article_Public_Search_Form extends AMPSearchForm {

    var $component_header = "Search Calendar";

    function Article_Public_Search_Form ( ) {
        $name = "Content_Search";
        $this->init( $name, 'GET', AMP_CONTENT_URL_SEARCH );
    }

    function submitted( ) {
        if ( isset( $_GET['Search']) && $_GET['Search'] == 'Go' || ( isset( $_GET['q']) && $_GET['q'] )) {
            return true;
        }
        return parent::submitted( );
    }

}
?>
