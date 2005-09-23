<?php
require_once( "Modules/VoterGuide/VoterGuide.php");

class VoterGuide_Display extends AMPDisplay_HTML {

    var $_voterguide;

    var $_css_class_title = "title";
    var $_css_class_date = "date";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_text = "text";

    function VoterGuide_Display ( &$voterguide ) {
        $this->_voterguide = &$voterguide;
    }

    function execute ( ) {
        return  $this->_HTML_guideHeader( ).
                $this->_HTML_positionsList( ).
                $this->_HTML_guideFooter( );
    }

    function _HTML_guideHeader( ) {
        $output =   $this->_HTML_title( $this->_voterguide->getName( ) ).
                    $this->_HTML_location( $this->_voterguide->getLocation( ) ).
                    $this->_HTML_date( $this->_voterguide->getItemDate( ) ).
                    $this->_HTML_blurb( $this->_voterguide->getBlurb( ) ).
                    $this->_HTML_affliations( $this->_voterguide->getAffliation( ) ).    
                    $this->_HTML_docLink( $this->_voterguide->getDocumentRef( ));

        return $this->_HTML_addImage( $output );

    }

    function _HTML_addImage( $html ) {
        if ( !( $imageRef = &$this->_voterguide->getImageRef( ))) return $html;
        return $this->_HTML_inDiv( $this->_HTML_image( $imageRef->getURL ), array( 'style'=>'float:right;position:relative;')) . $html;

    }

    function _HTML_title( $title ) {
        if ( !$title ) return false;
        return $this->_HTML_in_P( $title, array( 'class' => $this->_css_class_title ));
    }

    function _HTML_location( $location ){
        if ( !$location ) return false;
        return $this->_HTML_inSpan( $location, $this->_css_class_subtitle ). $this->_HTML_newline();
    }

    function _HTML_date( $date ){
		if (!$date) return false;
        return $this->_HTML_inSpan( DoDate( $date, 'F jS, Y'), $this->_css_class_date) . $this->_HTML_newline();

    }

    function _HTML_blurb( $blurb ) {
        if ( !$blurb ) return false;
        return $this->_HTML_in_P( $blurb, array( 'class' => $this->_css_class_text));
    }

    function _HTML_affiliations ( $affiliations ) {
        if ( !$affiliations ) return false;
        $icon_path = AMP_LOCAL_PATH . "/img/original/" . $affiliations . "_icon.gif";
        if ( !file_exists( $icon_path )) return false;
        return $this->_HTML_inDiv( $this->_HTML_image( $icon_path ), array( 'class' => 'vg_endorsement_logos'));
    }

    function _HTML_docLink ( &$doc ) {
        if ( !$doc ) return false;
        return $doc->display( 'div' );
    }

    function _HTML_footer( ) {
        if ( !( $footer_blurb =  $this->_voterguide->getFooter( ))) return false;
        return $this->_HTML_in_P( $footer_blurb, array( 'class' => $this->_css_class_text));
    }


    function _HTML_positionsList( ) {
        $this->_positionDisplay = &new VoterGuidePositionSet_Display( $this->_voterguide->dbcon, $this->_voterguide->id );
    }
}
