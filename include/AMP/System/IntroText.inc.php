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

 class AMPSystem_IntroText extends AMPSystem_Data_Item {

    var $textdata;
    var $_textdata_keys;
    var $id;
    var $datatable = 'moduletext';
    var $name_field = 'name';
    var $_exact_value_fields = array( 'modid' );
    var $_class_name = 'AMPSystem_IntroText';
    var $display_class = 'Article_Public_Detail_Page';

    function AMPSystem_IntroText ( &$dbcon, $text_id=null ) {
        $this->init( $dbcon, $text_id );
    }
    
    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
        $this->legacyFieldname( $data, 'modid', 'tool_id' );
        $this->legacyFieldname( $data, 'type', 'section' );
    }

    function &getDisplay() {
        if( AMP_RENDER_DISPLAY_CLASS_PUBLICPAGE != $this->display_class ) {
            require_once ( 'AMP/Content/Article/Display/Introtext.inc.php' );
            $this->display_class = AMP_RENDER_DISPLAY_CLASS_PUBLICPAGE ? AMP_RENDER_DISPLAY_CLASS_PUBLICPAGE : 'ArticleDisplay_IntroText';
        } else {
            require_once ( 'AMP/Content/Article/Public/Detail/Page.php' );
        }
        $display_class = $this->display_class;
        $display = & new $display_class( $this );
        return $display;
    }

    function _blankIdAction( ){
        $this->_setSourceIncrement( 100 );
    }


    function getSection() {
        return $this->getData( 'type' );
    }

    function getParent( ) {
        return $this->getSection( );
    }

    function getImageFile( ) {
        return false;
    }

    function getImageData( ) {
        return array( );
    }

    function getSectionsRelated( ) {
        return false;
    }

    function hasAncestor( $section_id ) {
        $map = AMPContent_Map::instance( );
        $ancestors = $map->getAncestors( $this->getParent( ));
        return isset( $ancestors[$section_id]) ;
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

    function getItemDate( ) {
        return $this->getData( 'date');
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
        if ( !( isset( $this->id ) && $this->id )) return AMP_SYSTEM_URL_PUBLIC_PAGE;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_PUBLIC_PAGE, array( 'id='. $this->id ));
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'tool_id');
    }

    function getMediaUrl( ) {
        return false;
    }

    function getMediaHtml( ) {
        return false;
    }

    function getClass( ) {
        return AMP_CONTENT_CLASS_DEFAULT;
    }

    function makeCriteriaTool( $tool_id ) {
        return $this->_makeCriteriaEquals( 'modid', $tool_id );
    }

}

?>
