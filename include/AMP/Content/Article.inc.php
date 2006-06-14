<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );
require_once ( 'AMP/Content/Article/Display.inc.php' );
require_once ( 'AMP/Content/Config.inc.php');

/**
 * Article 
 * 
 * @uses AMPSystem_Data_Item
 * @package Content
 * @version 3.5.4
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Article extends AMPSystem_Data_Item {

    var $datatable = "articles";
    var $name_field = "title";
    var $_sort_auto = false;

    /**
     * Article 
     * 
     * @param       & $dbcon      a reference to the current database connection 
     * @param mixed $id           the id of the desired article 
     * @access public
     * @return void
     */
    function Article( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id);
        $this->_addAllowedKey( 'new_alias_name' );
    }

    function &getDisplay() {
        $classes = filterConstants( 'AMP_CONTENT_CLASS' );
        $display_def_constant= 'AMP_ARTICLE_DISPLAY_' . array_search( $this->getClass() , $classes );

        $display_class = AMP_ARTICLE_DISPLAY_DEFAULT;
        if (defined( $display_def_constant )) $display_class = constant( $display_def_constant );

        if (!class_exists( $display_class )) $display_class = AMP_ARTICLE_DISPLAY_DEFAULT;
        return new $display_class( $this );
    }

    function getParent() {
        return $this->getData( 'type' );
    }

    function getSection() {
        return $this->getParent();
    }

    function getAllSections() {
        $related_set = &AMPContentLookup_SectionsByArticle::instance( $this->id );
        if ( empty( $related_set )) return array( $this->getParent( ));
        $return_set = array_keys( $related_set );
        $return_set[] = $this->getParent( );
        return $return_set;
    }

    function getClass() {
        return $this->getData( 'class' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }

    function getSubTitle() {
        return $this->getData( 'subtitle' );
    }

    function getAuthor() {
        return $this->getData( 'author' );
    }

    function getBlurb() {
        return $this->getData( 'shortdesc' );
    }

    function getRedirect() {
        #if (!$this->getData( 'linkover' )) return false;
        if (! ($target = $this->getData( 'link' ))) return false;
        return $target;
    }

    function getURL() {
        if ($url = $this->getRedirect() ) return $url;
        if (!$this->id ) return false;
        return $this->getURL_default( ) ;
    }

    function getURL_default( ){
        return AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, "id=".$this->id );
    }
    
    function getContact() {
        return $this->getData( 'contact' );
    }
    function getSource() {
        if( $source = $this->getData( 'source' )) return $source;
        return $this->getSourceURL() ;
    }

    function getBody() {
        return $this->getData( 'body' );
    }

    function getSidebar() {
        return $this->getData( 'navtext' );
    }
	
	function getSidebarClass() {
        return $this->getData( 'sidebar_class' );
    }
	
    function getSourceURL() {
        return $this->getData( 'sourceurl' );
    }

    function getMoreLinkURL() {
        if (!$this->getData('usemore')) return false;
        return $this->getData( 'morelink' );
    }

    function getImageFileName() {
        #if (!$this->getData( 'picuse' )) return false;
        return $this->getData( 'picture' );
    }

    function getArticleDate() {
        if (!$this->isPublicDate()) return false;
        return $this->getAssignedDate( );
    }

    function getAssignedDate( ){
        $date_value =  $this->getData('date');
        if ($date_value == AMP_NULL_DATE_VALUE) return false;
        return $date_value;
    }

    function isPublicDate() {
        //frontpage articles have the opposite 'display date' logic as standard
        //articles
        //this is the dumbest hack ever, but until we
        //re-tool the backend forms, I have no choice

        //this hack is disabled as of build 3.5.9
        //if ($this->getClass() != AMP_CONTENT_CLASS_FRONTPAGE ) return !($this->getData( 'usedate' ));
        //return $this->getData( 'usedate' );
        return !($this->getData( 'usedate' ));
    }

    function getItemDate() {
        return $this->getArticleDate();
    }

    function getOrder( ){
        return $this->getData( 'pageorder' );
    }

    function getItemDateChanged() {
        return $this->getData( 'updated');
    }

    function getItemDateCreated() {
        return $this->getData( 'datecreated');
    }

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        $image = &new Content_Image();
        $image->setData( $this->getImageData() );
        return $image;
    }

    function getImageData() {
        return array(   'filename'  =>  $this->getImageFileName(),
                        'caption'   =>  $this->getData( 'piccap' ),
                        'alignment' =>  $this->getData( 'alignment' ),
                        'alttag'    =>  $this->getData( 'alttag' ),
                        'image_size'=>  $this->getImageClass() );
    }

    function getImageClass() {
        return $this->getData( 'pselection' );
    }

    function getShowInNavs( ){
        return $this->getData( 'uselink');
    }

    function getShowInFrontPage( ){
        return $this->getData( 'fplink');
    }

    function getShowAsNew( ){
        return $this->getData( 'new');
    }

    function getRegion( ){
        return $this->getData( 'state');
    }

    function getLastEditorId( ){
        return $this->getData( 'updatedby' );
    }

    function getCreatorId( ){
        return $this->getData( 'enteredby');
    }

    function allowsComments() {
        if ( $this->getClass( ) == AMP_CONTENT_CLASS_BLOG ) return true;
        return $this->getData( 'comments' );
    }

    function &getComments() {
        if (!$this->allowsComments()) return false;
        require_once ( 'AMP/Content/Article/Comments.inc.php' );
        return new ArticleCommentSet( $this->dbcon, $this->id );
    }

    function getDocumentLink() {
        return $this->getData('doc');
    }

    function getDocLinkType() {
        return $this->getData('doctype');
    }

    function &getDocLinkRef() {
        require_once ( 'AMP/Content/Article/DocumentLink.inc.php' );
        if (!($doc = $this->getDocumentLink() )) return false;
        $doclink = &new DocumentLink();
        $doclink->setFile( $doc, $this->getDocLinkType() );
        return $doclink;
    }

    /**
     * isNews 
     * 
     * @access public
     * @return void
     */
    function isNews() {
        if (!$this->getClass()) return false;
        if ($this->getClass()== AMP_CONTENT_CLASS_NEWS) return true;
        if ($this->getClass()== AMP_CONTENT_CLASS_MORENEWS) return true;
        return false;
    }

    /**
     * isPressRelease 
     * 
     * @access public
     * @return void
     */
    function isPressRelease() {
        if (!$this->getClass()) return false;
        return ($this->getClass()== AMP_CONTENT_CLASS_PRESSRELEASE);
    }

    function isLive() {
        return ($this->getData('publish')==AMP_CONTENT_STATUS_LIVE);
    }

    function isHtml() {
        return $this->getData( 'html' );
    }

    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
        $this->legacyFieldname( $data, 'blurb', 'shortdesc' );
        $this->legacyFieldname( $data, 'section', 'type' );
        if ( isset( $data['link']) && $data['link'] && !isset( $data['linkover'])) {
            $this->mergeData( array( 'linkover' => 1));
        }
        /*
        if ( !( 
              ( isset( $data['id']) && $data['id'] ) 
              || $this->id )) {
            if ( !( isset( $data['datecreated']) && $data['datecreated'] )){
                $this->mergeData( array( 'datecreated' => date( 'Y-m-d')));
            }
            if ( !( isset( $data['enteredby']) && $data['enteredby'])){
                $this->mergeData( array( 'enteredby' => AMP_SYSTEM_USER_ID ));
            }

        }
        */
        if ( !( isset( $data['updatedby']) && $data['updatedby'] )){
            $this->mergeData( array( 'updatedby' => AMP_SYSTEM_USER_ID ));
        }
    }

    function readVersion( $version_id ) {
        require_once ( 'AMP/Content/Article/Version.inc.php' );
        $version = &new Article_Version( $this->dbcon, $version_id );
        if (!$version->hasData()) return false;

        $this->setData( $version->getData() );
    }

    function setDefaults( ){
        $this->mergeData( array( 
            'type' => AMP_CONTENT_MAP_ROOT_SECTION,
            'linkover' => 1,
            'class' => AMP_CONTENT_CLASS_DEFAULT,
            'enteredby' => AMP_SYSTEM_USER_ID,
            'datecreated' => date( 'Y-m-d' )
        ));
    }

    function getNewAliasName( ){
        return $this->getData( 'new_alias_name' );
    }

    function clearAliasName( ){
        return $this->mergeData( array( 'new_alias_name' => false ));
    }

    function getExistingAliases( ){
        if ( !isset( $this->id )) return false;
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        return $redirect->search( $redirect->makeCriteria( array( 'target' => $this->getURL_default( ))));
    }

    function _afterSave( ){
        if ( !( $alias_name = $this->getNewAliasName( ))) return false;
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        $existing_items = $redirect->search( $redirect->makeCriteria( array( 'alias' => $alias_name )));
        if ( $existing_items ){
            foreach( $existing_items as $existing_redirect ){
                $existing_redirect->setTarget( $this->getURL( ));
                $existing_redirect->save( );
            }
            $this->clearAliasName( );
            return true;
        }
        $redirect->setDefaults( );
        $redirect->setAlias( $alias_name );
        $redirect->setTarget( $this->getURL( ));
        $this->clearAliasName( );
        return $redirect->save( );
    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'defaultOrder');
    }

    function getDefaultOrder( ){
        $item_date = $this->getAssignedDate( );
        $item_timestamp = $item_date ? strtotime ( $item_date ) :  strtotime( AMP_NULL_DATETIME_VALUE_UNIX  );
        $desc_date_value = strtotime( AMP_FUTURE_DATETIME_VALUE + 100 ) -  $item_timestamp;
        
        return
                  ':' . intval( !$this->isLive( ))
                . ':' . intval( $this->getParent( ))
                . ':' . ( intval( $this->getOrder( )) ? intval(  $this->getOrder( )) : AMP_CONTENT_LISTORDER_MAX  )
                . ':' . $desc_date_value;
    }

    function setOrder( $order ){
        return $this->mergeData( array( 'pageorder' => $order ));
    }

    function setSection( $section_id ){
        return $this->mergeData( array( 'type' => $section_id ));
    }

    function setClass( $class_id ){
        return $this->mergeData( array( 'class' => $class_id ));
    }

    function setRegion ( $region_id ){
        return $this->mergeData( array( 'state' => $region_id ));
    }


    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        if ( is_array( $new_order_value )) return false;
        $this->setOrder( $new_order_value );
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'reorder' );
        return $result;

    }

    function move( $section_id = false, $class_id = false ) {
        $move_action = false;
        if ( $section_id && $section_id != $this->getSection( )) {
            $this->setSection( $section_id );
            $move_action = true;
        }
        if ( $class_id  && $class_id != $this->getClass( )){
            $this->setClass( $class_id );
            $move_action = true;
        }
        if ( !$move_action ) return false;
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'move' );
        return $result;
    }

    function regionize( $region_id ){
        if ( $region_id == $this->getRegion( )) return false;
        $this->setRegion( $region_id );
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'regionize' );
        return $result;

    }

    function makeCriteriaSection( $section_id ) {
        return $this->_makeCriteriaEquals( 'type', $section_id );
    }

    function makeCriteriaClass( $class_id ){
        return $this->_makeCriteriaEquals( 'class', $class_id );
    }

    function makeCriteriaType( $section_id ) {
        return $this->makeCriteriaSection( $section_id );
    }
}


?>
