<?php

require_once( 'AMP/Form/SearchForm.inc.php');

class GalleryImageSearch extends AMPSearchForm {
    var $_component_header = "Search Photo Gallery";

    function GalleryImageSearch( ){
        $name = "ImageSearch";
        $this->init( $name );
    }

    function _formFooter() {
        $renderer = AMP_get_renderer( );
        return $renderer->link( AMP_url_add_vars( AMP_SYSTEM_URL_GALLERY_IMAGE, array( 'action=clear_bookmark')), sprintf( AMP_TEXT_VIEW_ALL, ucfirst( AMP_pluralize( AMP_TEXT_IMAGE ))) );
    }
}
?>
