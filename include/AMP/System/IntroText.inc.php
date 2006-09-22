<?php

/**************
 *  AMPSystem_IntroText 
 *  represents introductory and response texts
 *  used by the Modules system
 *
 *  AMP 3.5.0
 *  2005-27-06
 *
 *  Author: austin@radicaldesigns.org
 *
 *****/
require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Article/Display/Introtext.inc.php' );

 class AMPSystem_IntroText extends AMPSystem_Data_Item {

    var $textdata;
    var $_textdata_keys;
    var $id;
    var $datatable = 'moduletext';
    var $name_field = 'name';
    var $_exact_value_fields = array( 'modid' );

    function AMPSystem_IntroText ( &$dbcon, $text_id=null ) {
        $this->init( $dbcon, $text_id );
    }
    
    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
        $this->legacyFieldname( $data, 'modid', 'tool_id' );
    }

    function &getDisplay() {
        $display = & new ArticleDisplay_IntroText( $this );
        return $display;
    }

    function _blankIdAction( ){
        $this->_setSourceIncrement( 100 );
    }


    function getSection() {
        return $this->getData( 'type' );
    }

    function getTemplate() {
        return $this->getData( 'templateid' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }

    function getSubTitle() {
        return $this->getData( 'subtitle' );
    }


    function getBody() {
        return $this->getData( 'body' );
    }

    function isHtml() {
        return $this->getData( 'html' );
    }

    function mergeBodyFields( $fielddata ) {
        $replace_values = AMP_makeMergeFields( array_keys($fielddata) );
        return str_replace( $replace_values, $fielddata, $this->getBody() );
        #return ereg_replace( "%\w+%", "", $merged );
    }

    function getImageRef() {
        //for now, no images for introtexts
        return false;
    }
    function getDocLinkRef() {
        return false;
    }

    function getAuthor() {
        return false;
    }

    function getSource() {
        return false;

    }

    function getSourceURL() {
        return false;
    }

    function getSidebar() {
        return false;
    }

    function getContact() {
        return false;
    }
    function getArticleDate() {
        return false;
    }
    function getComments() {
        return false;
    }

    function getBlurb( ){
        return false;
    }

    function getToolName( ){
        $tool_id = $this->getToolId( );
        if ( !$tool_id ) return false;
        $tool_names = &AMPSystem_Lookup::instance( 'tools');
        if ( isset( $tool_names[ $tool_id ])) return $tool_names[ $tool_id ];
        return false;
    }
    function getToolId( ){
        return $this->getData( 'tool_id');
    }

    function getPageLink( ){
        return $this->getData( 'searchtype');
    }

    function getURL( ){
        return $this->getPageLink( );
    }

    function setTemplate( $template_id ){
        return $this->mergeData( array( 'templateid' => $template_id ));
    }

    function get_url_edit( ) {
        if ( !isset( $this->id ) && $this->id ) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_PUBLIC_PAGE, array( 'id='. $this->id ));
    }

}

?>
