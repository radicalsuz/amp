<?php

require_once( 'AMP/Display/Controller.php' );

class AMP_Content_Tag_Public_Controller extends AMP_Display_Controller {

    var $_path_list = 'AMP/Content/Tag/Public/List.php';
    var $_path_detail = 'AMP/Content/Tag/Public/Detail.php';
    var $_path_source = 'AMP/Content/Tag/Tag.php';

    var $_class_list = 'AMP_Content_Tag_Public_List';
    var $_class_detail = 'AMP_Content_Tag_Public_Detail';
    var $_class_source = 'AMP_Content_Tag';

    var $_publicpage_list = AMP_CONTENT_PUBLICPAGE_ID_TAGS_DISPLAY;

    function AMP_Content_Tag_Public_Controller( ) {
        $this->__construct( );
    }

}

?>
