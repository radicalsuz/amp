<?php
require_once( 'AMP/Content/Article/Public/Detail.php');
require_once( 'AMP/System/Introtext.inc.php');

class Article_Public_Detail_Page extends Article_Public_Detail {
    function Article_Public_Detail_Page( $source ) {
        $this->__construct( $source );
    }

    function render_title( $source ) {
        if ( !( $title = $source->getTitle( ))) return false;
        return $this->_renderer->p( converttext( $title ), array( 'class' => $this->_css_class_title ));
    }

    function render_body( $source ) {
        if ( !( $body = $source->getBody( ))) return false;
        $body = ( $source->isHtml( )) ? $body : converttext( $body );

        //hot words
		if ($hw = AMP_lookup('hotwords')) {
            $body = str_replace( array_keys($hw), array_values($hw), $body );
        }

        return $this->_renderer->p( eval_includes( $body ), array( 'class' => $this->_css_class_body ));

    }
}
?>
