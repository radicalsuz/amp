<?php

require_once( 'AMP/Content/Tag/Public/Detail.php');

class AMP_Content_Tag_Public_Intro extends AMP_Content_Tag_Public_Detail {

    var $_css_class_container_item = 'list_intro article_public_detail';

    function AMP_Content_Tag_Public_Intro( $source ) {
        $this->__construct( $source );
    }

    function render_image( $source ) {
        if ( !( $image = $source->getImageFile( ))) return false;
        $image->set_display_metadata( array( 'align' => 'left'));
        return $image->display->render_image_format_main( $image->display->render_thumb( ));

        /*
        $image_url = AMP_Url_AddVars( 
                        AMP_CONTENT_URL_IMAGE, 
                            array(  'filename=' . $image->getName( ), 
                                    'class=' . AMP_IMAGE_CLASS_THUMB, 
                                    'width='.AMP_IMAGE_WIDTH_WIDE, 'action=resize' )
                                    );

       return $this->_renderer->image( $image_url, $this->_image_attr );
       */
    }

    function _renderFooter( ) {
        //stub
    }

}

?>
