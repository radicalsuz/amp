<?php
require_once( "Modules/VoterGuide/VoterGuide.php");
//require_once('Translation2.php');
require_once('utility.functions.inc.php');

if ( !defined( 'AMP_TEXT_VOTERGUIDE_DISCLAIMER')) define ( 'AMP_TEXT_VOTERGUIDE_DISCLAIMER' ,
        'This section paid for by League of Independent '
        . 'Voters Political Action Committee ( LIV PAC) 226 W. 135th St. 4th Fl. NY NY 10030. '
        . 'Not paid for by any candidate or candidate\'s committee.');

class VoterGuide_Display extends AMPDisplay_HTML {

    var $_voterguide;
	var $_voterguide_style;

    var $_css_class_title = "title";
    var $_css_class_date = "date";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_text = "text";

	var $_page_header;

	var $_printsafe = false;

    function VoterGuide_Display ( &$voterguide ) {
        $this->_voterguide = &$voterguide;
		if($style_id = $this->_voterguide->getData('style')) {
			require_once('AMP/Content/Header.inc.php');
			require_once('AMP/Content/Page.inc.php');
			$this->_page_header =& AMPContent_Header::instance(AMPContent_Page::instance());
			require_once('Modules/VoterGuide/Style/VoterGuide_Style.php');
			$this->_voterguide_style =& new VoterGuide_Style($this->_voterguide->dbcon, $style_id);
			$this->_page_header->addStylesheet($this->_voterguide_style->getData('url'));
			if($this->getPrintSafe() && $fullscreen = $this->_voterguide_style->getData('fullscreen_url')) {
				$this->_page_header->addStylesheet($fullscreen);
			}
		}
    }

    function execute ( ) {
		$output =
				$this->_HTML_intro().
				$this->_HTML_guideHeader( ).
                $this->_HTML_positionsList( ).
				$this->_HTML_spacer().
                $this->_HTML_guideFooter( ).
//				$this->_HTML_newline().
                $this->_HTML_expenditure_notice().
                $this->_HTML_notifyAdmin( );

		if(function_exists('tidy_repair_string')) {
			   tidy_setopt('output-xhtml', TRUE);
			   tidy_setopt('show-body-only', true);
			   tidy_setopt('vertical-space', true);
			   tidy_setopt('indent', TRUE);
			   tidy_setopt('indent-spaces', 2);
			   tidy_setopt('wrap', 200);
			   $output = tidy_repair_string($output);
		}
		return $output;
    }

	function _HTML_spacer() {
		return $this->_HTML_inDiv('', array('class' => 'spacer'));
	}

	function _HTML_module_title() {
		return '<div class="module_title"><a href="'.AMP_SITE_URL.'voterguides.php">Voter Guides</a></div>';
	}

	function _HTML_intro() {
		$intro = $this->_HTML_expenditure_notice();
		if($this->getPrintSafe()) {
			return $intro;
		}
		$intro = $this->_HTML_module_title()."\n$intro";

		$js=
"function set_tell_Url() {
   document.forms['tell_it_loud'].elements['url_link'].value=location.href;
   document.forms['tell_it_loud'].submit();
}";
		$this->_page_header->addJavascriptDynamic($js);

		$intro = $intro.
				$this->_HTML_inDiv('<a href="'.AMP_SITE_URL.'/newvoterguide">Click here to Post a New Voter Guide</a>', array('class' => 'new_guide_link')).
				$this->_HTML_inDiv('<form method="post" action="mailto2.php" name="tell_it_loud">
					<input type="hidden" name="url_link" />
					<a href="javascript:set_tell_Url();">Email a friend about this page</a>
					</form>', array('class' => 'tell_link'));

		$intro = $this->_HTML_inDiv($intro, array('class' => 'voterguide_intro'));
		return $intro;
	}

	function _HTML_expenditure_notice() {
		if(defined('AMP_TEXT_VOTERGUIDE_DISCLAIMER')) {
			return $this->_HTML_inDiv( AMP_TEXT_VOTERGUIDE_DISCLAIMER , array('class' => 'expenditure_notice' ));
		}
	}
		
    function _HTML_notifyAdmin( ) {
/*
		$params = array('prefetch' => false);
		$gettext_options = array(
    'langs_avail_file' => 'Modules/VoterGuide/langs.ini',
    'domains_path_file' => 'Modules/VoterGuide/domains.ini',
    'default_domain' => 'voterguide');
		$tr =& Translation2::factory('gettext', $gettext_options, $params);
		$tr->setLang('en');
*/

        $notifyLink = "if this guide is offensive, send email to: <a href=\"mailto:voterguides@indyvoter.org?subject=guide%20". $this->_voterguide->getShortName() . "%20is%20pissing%20me%20off\"> the League</a>";
        return $this->_HTML_inDiv( $notifyLink, array('class' => 'notify_link' ));
    }

    function _HTML_guideHeader( ) {
        $output =  $this->_HTML_affiliations( $this->_voterguide->getAffiliation( ) ).    
                    $this->_HTML_title( $this->_voterguide->getName( ) ).
                    $this->_HTML_location( $this->_voterguide->getLocation( ) ).
                    $this->_HTML_date( $this->_voterguide->getItemDate( ) ).
                    $this->_HTML_blurb( $this->_voterguide->getBlurb( ) ).
//                    $this->_HTML_newline().
                    $this->_HTML_docLink( $this->_voterguide->getDocumentRef( )) .
//                    $this->_HTML_newline().
                    $this->_HTML_blocJoin();
//                    $this->_HTML_newline(2);

        return $this->_HTML_inDiv($this->_HTML_addImage( $output ), array('class' => 'voterguide_header'));

    }

    function _HTML_addImage( $html ) {
        if ( !( $imageRef = &$this->_voterguide->getImageRef( ))) return $html;
        return $this->_HTML_inDiv( $this->_HTML_image( $imageRef->getURL( AMP_IMAGE_CLASS_OPTIMIZED ) ), array( 'class'=>'vg_image')) . $html;

    }

    function _HTML_title( $title ) {
        if ( !$title ) return false;
        return $this->_HTML_inDiv( $title, array( 'class' => $this->_css_class_title ));
    }

    function _HTML_location( $location ){
        if ( !$location ) return false;
        return $this->_HTML_inDiv( $location, array('class' => $this->_css_class_subtitle ));
    }

    function _HTML_date( $date ){
		if (!$date) return false;
        return $this->_HTML_inDiv( DoDate( $date, 'F jS, Y'), array('class' => $this->_css_class_date));
    }

    function _HTML_blurb( $blurb ) {
        if ( !$blurb ) return false;
        return $this->_HTML_inDiv( $blurb, array( 'class' => $this->_css_class_text));
    }

    function _HTML_affiliations ( $affiliations ) {
        if ( !$affiliations ) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $iconRef = &new Content_Image( $affiliations . "_icon.gif");
        if ( !file_exists( $iconRef->getPath( AMP_IMAGE_CLASS_ORIGINAL ))) return false;
//        return $this->_HTML_inDiv( $this->_HTML_image( $iconRef->getURL( AMP_IMAGE_CLASS_ORIGINAL )), array( 'class' => 'voterguide_endorsement_logos'));
		return $this->_HTML_inDiv('', array('class' => 'voterguide_endorsement_logos'));
    }

    function _HTML_docLink ( &$doc ) {
        if ( !$doc ) return false;
        return 'Download PDF of guide:'.$doc->display( 'div' );
    }

    function _HTML_guideFooter( ) {
        if ( !( $footer_blurb =  $this->_voterguide->getFooter( ))) return false;
        return $this->_HTML_inDiv( $footer_blurb, array( 'class' => 'vg_footer')).
                    $this->_HTML_blocJoin();
    }


    function _HTML_positionsList( ) {
        require_once( 'Modules/VoterGuide/Position/SetDisplay.inc.php');
        $this->_positionDisplay = &new VoterGuidePositionSet_Display( $this->_voterguide->dbcon, $this->_voterguide->id );
		if($style_id = $this->_voterguide->getData('style')) {
			$this->_positionDisplay->setLayoutCSS(true);
		}
		return $this->_positionDisplay->execute( );
    }

	function _HTML_blocJoin() {
		require_once('Modules/VoterGuide/Controller.inc.php');
		$blocURL = VoterGuide_Controller::getJoinURL($this->_voterguide);
//		$blocURL = AMP_Url_AddVars(AMP_FORM_URL_VOTERBLOC,'guide='.$this->_voterguide->id);
		if( !$blocURL ) {
			return false;
		}
		return $this->_HTML_inDiv($this->_HTML_link($blocURL, AMP_VOTERBLOC_LINK_TEXT), array('class' => 'vg_endorse_link'));
	}

	function setPrintSafe($printsafe = true) {
		$this->_printsafe = $printsafe;
	}

	function getPrintSafe() {
		return $this->_printsafe;
	}
}
?>
