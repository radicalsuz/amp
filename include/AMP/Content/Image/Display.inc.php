<?php
require_once( 'AMP/Content/Display/HTML.inc.php' );
require_once( 'AMP/Content/Image.inc.php' );

class ContentImage_Display_allVersions extends AMPDisplay_HTML {

    var $_imageRef;
    var $_version_descriptions = array(
            AMP_IMAGE_CLASS_THUMB   => 'Thumbnail',
            AMP_IMAGE_CLASS_OPTIMIZED => 'Optimized',
            AMP_IMAGE_CLASS_ORIGINAL => 'Original'
            );

    function ContentImage_Display_allVersions ( $filename=null ) {
        if (isset($filename)) $this->setImageSource( $filename );

    }

    function setImageSource( $file_name ) {
        $this->_imageRef = &new Content_Image( $file_name );
        if (!file_exists( $this->_imageRef->getPath())) {
            unset( $this->_imageRef );
            return false;
        }
    }

    function execute() {
        if (!isset( $this->_imageRef )) return false;
        $output = "";
        foreach( $this->_version_descriptions as $version => $version_label ) {
            $output .= $this->_HTML_photoRow( $version );
        }
        return $this->_HTML_formatTable( $output );
    }

    function _HTML_photoRow( $version ) {
        $output  = $this->_HTML_inTD( $this->_version_descriptions[ $version ] . ": " );
        $output .= $this->_HTML_inTD( $this->_HTML_image( $this->_imageRef->getURL( $version ) )  );
        return '<TR>' . $output . '</TR>';
    }

    function _HTML_formatTable( $html ) {
        return "<HR>". $this->_HTML_inSpan( 'Versions of '.$this->_imageRef->getName(), 'page_result') . 
                $this->_HTML_newline()."<TABLE>\n" . $html . "</TABLE><HR>". $this->_HTML_newline();
    }

}

?>
