<?php
require_once( "Modules/VoterGuide/VoterGuide.php");
require_once('utility.functions.inc.php');

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
        $output =   $this->_HTML_blocJoin(). 
					$this->_HTML_affiliations( $this->_voterguide->getAffiliation( ) ).    
                    $this->_HTML_title( $this->_voterguide->getName( ) ).
                    $this->_HTML_location( $this->_voterguide->getLocation( ) ).
                    $this->_HTML_date( $this->_voterguide->getItemDate( ) ).
                    $this->_HTML_blurb( $this->_voterguide->getBlurb( ) ).
                    $this->_HTML_docLink( $this->_voterguide->getDocumentRef( )) .
                    $this->_HTML_newline();

        return $this->_HTML_addImage( $output );

    }

    function _HTML_addImage( $html ) {
        if ( !( $imageRef = &$this->_voterguide->getImageRef( ))) return $html;
        return $this->_HTML_inDiv( $this->_HTML_image( $imageRef->getURL( AMP_IMAGE_CLASS_OPTIMIZED ) ), array( 'style'=>'float:right;position:relative;')) . $html;

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
        require_once( 'AMP/Content/Image.inc.php');
        $iconRef = &new Content_Image( $affiliations . "_icon.gif");
        if ( !file_exists( $iconRef->getPath( AMP_IMAGE_CLASS_ORIGINAL ))) return false;
        return $this->_HTML_inDiv( $this->_HTML_image( $iconRef->getURL( AMP_IMAGE_CLASS_ORIGINAL )), array( 'class' => 'voterguide_endorsement_logos'));
    }

    function _HTML_docLink ( &$doc ) {
        if ( !$doc ) return false;
        return $doc->display( 'div' );
    }

    function _HTML_guideFooter( ) {
        if ( !( $footer_blurb =  $this->_voterguide->getFooter( ))) return false;
        return $this->_HTML_in_P( $footer_blurb, array( 'class' => $this->_css_class_text));
    }


    function _HTML_positionsList( ) {
        require_once( 'Modules/VoterGuide/Position/SetDisplay.inc.php');
        $this->_positionDisplay = &new VoterGuidePositionSet_Display( $this->_voterguide->dbcon, $this->_voterguide->id );
        return $this->_HTML_inDiv( $this->_positionDisplay->execute( ), array( 'style' => 'border:1px solid silver; padding: 10px;'));
    }

	function _HTML_blocJoin() {
		$blocURL = AMP_Url_AddVars(AMP_FORM_URL_VOTERBLOC,'guide='.$this->_voterguide->id);
		if( !$blocURL ) {
			return false;
		}
		return $this->_HTML_link($blocURL, AMP_VOTERBLOC_LINK_TEXT);
	}
}
?>
