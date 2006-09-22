<?php

require_once( 'AMP/Content/Display/HTML.inc.php');

class DocumentLink  {

    var $_filename;
    var $_filetype;
    var $_document_folder = AMP_CONTENT_DOCUMENT_PATH;

    var $_default_display = 'ArticleDocumentLink_Display';
    var $_mime_translations = array( 
        'application/octet-stream' => 'file',
        'application/pdf' => 'pdf',
        'application/msword' => 'word',
        'image/jpeg' => 'img',
        'image/gif' => 'img',
        'image/tiff' => 'img',
        'image/png' => 'img',
        'video/mpeg' => 'file',
        'video/x-ms-wmv' => 'wmv',
        'video/x-flv' => 'flv',
        'application/x-quicktime' => 'mov',
        'application/quicktime' => 'mov',
        'video/quicktime' => 'mov'
        );

    function DocumentLink( $filename = null ) {
        if ( isset( $filename )) $this->init( $filename );
    }

    function init( $filename ) {
        $this->setFile( $filename );
    }

    function setFile( $filename, $filetype = AMP_CONTENT_DOCUMENT_TYPE_DEFAULT ) {
        $this->_filename = $filename;
        $this->setFileType( $filetype );
        $this->verifyFileType( );
    }

    function verifyFileType( ) {
        if ( !function_exists( 'mime_content_type')) return false;
		$file_path = $this->getPath();
		if (is_dir($file_path)) return false;
        if ( !( $mime_filetype = mime_content_type( $file_path )) ) return false;
		//yet another microsoft-required hack
		if (strtolower(substr($file_path, strlen($file_path)-3)) == 'wmv' ){
			$mime_filetype = 'video/x-ms-wmv';
		}

        $this->setFileType( $this->_simpleFileType( $mime_filetype ));
    }

    function _simpleFileType( $mime_filetype ) {
        if ( !isset( $this->_mime_translations[ $mime_filetype ])) return AMP_CONTENT_DOCUMENT_TYPE_DEFAULT;
        return $this->_mime_translations[ $mime_filetype ];
    }

    function setFileType( $filetype ) {
        if ( !$filetype ) $filetype = AMP_CONTENT_DOCUMENT_TYPE_DEFAULT;
        $this->_filetype = $filetype;
    }

    function display( $display_type = null ) {
        $requested_display = $this->_default_display;
        if ( isset( $display_type ) && class_exists( $this->_default_display.ucfirst( $display_type ))) {
            $requested_display = $this->_default_display . ucfirst( $display_type );
        }
        $display = & new $requested_display( $this );
        return $display->execute();
    }

    function getFileName() {
        if ( !isset( $this->_filename)) return false;
        return $this->_filename;
    }

    function getFileType() {
        if ( !isset( $th))
        return $this->_filetype;
    }

    function getURL() {
        return AMP_CONTENT_URL_DOCUMENTS . $this->getFileName();
    }

    function getPath() {
        return AMP_pathFlip( AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . $this->_document_folder . DIRECTORY_SEPARATOR . $this->getFileName() );
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
        AMP_CONTENT_DOCUMENT_TYPE_MOV   =>  'Quicktime Movie',
        AMP_CONTENT_DOCUMENT_TYPE_WMV   =>  'Windows Media',
        AMP_CONTENT_DOCUMENT_TYPE_FLV   =>  'Flash Video',
        AMP_CONTENT_DOCUMENT_TYPE_DEFAULT =>  'File',
        AMP_CONTENT_DOCUMENT_TYPE_IMAGE =>  'Image File' 
        );
    

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
        $icon_url = AMP_CONTENT_URL_ICONS . constant( $icon_descriptor );
        
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
class ArticleDocumentLink_DisplayDiv extends ArticleDocumentLink_Display {

    function ArticleDocumentLink_DisplayName( $document_link ) {
        $this->setDocument( $document_link );
    }

    function _HTML_docLink() {

        $linkhtml = $this->getIcon() . " ". basename( $this->document_link->getPath( ) );

        return $this->_HTML_inDiv( 
                    $this->_HTML_link( $this->document_link->getURL(), $linkhtml ),
                    array( 'class'=>'filelink')
                    );
    }

    function _HTML_start( ) {
        return $this->_HTML_newline( );
    }

    function _HTML_end( ) {
        return false;
    }




}
?>
