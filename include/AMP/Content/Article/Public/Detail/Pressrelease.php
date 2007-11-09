<?php

class Article_Public_Detail_Pressrelease extends Article_Public_Detail {

    function Article_Public_Detail_Pressrelease( $dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

    function renderItem( $source ) {
        return
             $this->render_date( $source )
            . $this->render_contact( $source )
            . $this->render_title( $source )
            . $this->render_subtitle( $source ) 
            . $this->render_byline( $source )
            . $this->render_image( $source )
            . $this->render_body( $source );
    }

	function render_date( &$source ) {;
		$date = $source->getItemDate();
		if (!AMP_verifyDateValue( $date )) return false;

        return $this->_renderer->span( AMP_TEXT_PR_HEADING . DoDate( $date, AMP_CONTENT_DATE_FORMAT), array( 'class' => $this->_css_class_date )) 
                . $this->_renderer->newline();
	}

}


?>
