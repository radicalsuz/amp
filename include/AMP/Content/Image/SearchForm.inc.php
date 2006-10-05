<?php

require_once( 'AMP/Form/SearchForm.inc.php');

class AMP_Content_Image_Search extends AMPSearchForm {
    var $_component_header = "Search Images";

    function AMP_Content_Image_Search( ){
        $name = "AMP_Content_Image_Search";
        $this->init( $name );
    }

}

?>
