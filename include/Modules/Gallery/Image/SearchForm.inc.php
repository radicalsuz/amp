<?php

require_once( 'AMP/Form/SearchForm.inc.php');

class GalleryImageSearch extends AMPSearchForm {
    var $_component_header = "Search Photo Gallery";

    function GalleryImageSearch( ){
        $name = "ImageSearch";
        $this->init( $name );
    }

}
?>
