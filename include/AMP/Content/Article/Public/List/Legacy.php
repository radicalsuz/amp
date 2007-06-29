<?php

require_once( 'AMP/Content/Article/Public/List.php');

class Article_Public_List_Legacy extends Article_Public_List {

    var $_css_class_container_listentry = "list_entry";
    var $_css_class_container_listimage = "list_image";

    var $_layout_table_attr = array(
        'width'         => '100%',
        'border'        => '0',
        'cellspacing'   => '0',
        'cellpadding'   => '0' );
    var $_thumb_attr = array(
        'vspace' => 2,
        'hspace' => 4,
        'class'  => 'imgpad' );

	function Article_Public_List_Legacy ( $source = false, $criteria = array()) {
		$source = false;
		$this->__construct($source, $criteria );
	}

    function _renderItem( &$source ) {
        return      $this->render_title( $source )
				  . $this->render_date(  $source )
				  . $this->render_source($source )
                  . $this->render_blurb( $source );
    }

    function _renderItemContainer( $content, &$source ) {
        $image = $this->render_image( $source );
        return 
        $this->_renderer->table( 
            $this->_renderer->tr( 
                $this->_renderer->td( $image, array( 'class' => $this->_css_class_container_listimage ))
              . $this->_renderer->td( $content, array( 'class' => $this->_css_class_container_listentry ))
            ),
            $this->_layout_table_attr
        )
        . $this->_renderer->newline( );

    }

    function render_image( $source ) {
		$image = $source->getImageRef();
		if ( !$image) return false; 

        return $this->_renderer->image($image->getURL(AMP_IMAGE_CLASS_THUMB), $this->_thumb_attr );
    }

    function render_blurb( $source ) {
        return $this->_renderer->span( AMP_trimText( $source->getBlurb(), AMP_CONTENT_ARTICLE_BLURB_LENGTH_MAX ), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_TEXT ));
    }

}

?>
