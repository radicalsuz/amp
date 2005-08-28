<?php

define ('AMP_CONTENT_DOCUMENT_PATH', 'downloads');
define ('AMP_CONTENT_DOCUMENT_TYPE_PDF', 'pdf');
define ('AMP_CONTENT_DOCUMENT_TYPE_WORD', 'word');
define ('AMP_CONTENT_DOCUMENT_TYPE_DEFAULT', 'file');
define ('AMP_CONTENT_DOCUMENT_TYPE_IMAGE', 'img');

define ('AMP_ICON_WORD', 'worddoc.gif' );
define ('AMP_ICON_PDF', 'pdf.gif' );
define ('AMP_ICON_IMAGE', 'img.gif' );
define ('AMP_ICON_PATH', '/system/images/' );

class DocumentLink  {

    var $_filename;
    var $_filetype;

    function DocumentLink() {
    }

    function setFile( $filename, $filetype = AMP_CONTENT_DOCUMENT_TYPE_DEFAULT ) {
        $this->_filename = $filename;
        $this->setFileType( $filetype );
    }

    function setFileType( $filetype ) {
        $this->_filetype = $filetype;
    }

    function display() {
        $display = & new ArticleDocumentLink_Display( $this );
        return $display->execute();
    }

    function getFileName() {
        return $this->_filename;
    }

    function getFileType() {
        return $this->_filetype;
    }

    function getURL() {
        return AMP_CONTENT_URL_DOCUMENTS . $this->getFileName();
    }

}

class ArticleDocumentLink_Display extends AMPDisplay_HTML {

    var $document_link;
    var $icon_styleAttr = array(
        'border' => '0',
        #'width'  => '20',
        #'height' => '16',
        'align'  => 'absmiddle' );

    var $_file_descriptions = array(
        AMP_CONTENT_DOCUMENT_TYPE_WORD  =>  'Microsoft Word Document',
        AMP_CONTENT_DOCUMENT_TYPE_PDF   =>  'PDF',
        AMP_CONTENT_DOCUMENT_TYPE_DEFAULT =>  'File',
        AMP_CONTENT_DOCUMENT_TYPE_IMAGE =>  'Image File' );
    

    function ArticleDocumentLink_Display( $document_link ) {
        $this->setDocument( $document_link );
    }

    function setDocument( $document_link ) {
        $this->document_link = &$document_link;
    }

    function execute() {
        return  $this->_HTML_start().
                $this->_HTML_docLink().
                $this->_HTML_end();
    }

    function _HTML_start() {
        $output = $this->_HTML_newline(2);
        $output .=  '<table align="center" width="50%" class="docbox"><tr><td  bordercolor="#000000">'."\n".
                    '<div align="center">';
        return $output;
    }

    function getIconAttr() {
        return $this->icon_styleAttr;
    }

    function getFileDescription() {
        return $this->_file_descriptions[ $this->document_link->getFileType() ];
    }

    function getIcon() {
        $icon_descriptor = 'AMP_ICON_' . strtoupper( $this->document_link->getFileType() );
        if (!defined( $icon_descriptor )) return false;
        $icon_file = AMP_LOCAL_PATH . AMP_IMAGE_PATH . constant( $icon_descriptor );
        if ( !file_exists_incpath($icon_file) ) return false;
        $icon_url = AMP_CONTENT_URL_IMAGES . constant( $icon_descriptor );
        
        return '<IMG SRC="'.$icon_url.'" '. $this->_HTML_makeAttributes( $this->getIconAttr() ) .'>';
    }

    function _HTML_docLink() {

        $linkhtml = $this->getIcon() . " Download as " . $this->getFileDescription();

        return $this->_HTML_link( $this->document_link->getURL(), $linkhtml );
    }

    function _HTML_end() {
        return  "</div>\n</td></tr></table>";
    }
}
?>
