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
    var $_class_name = 'Article';

    var $_version_status = false;

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
        $this->_addAllowedKey( 'sections_related' );
        $this->_addAllowedKey( 'url' );
        $this->_addAllowedKey( 'tags' );
    }

    function &getDisplay() {
        $classes = filterConstants( 'AMP_CONTENT_CLASS' );
        $display_def_constant = 'AMP_ARTICLE_DISPLAY_' . array_search( $this->getClass() , $classes );

        $display_class = AMP_ARTICLE_DISPLAY_DEFAULT;
        if (defined( $display_def_constant )) $display_class = constant( $display_def_constant );

        if (!class_exists( $display_class )) $display_class = AMP_ARTICLE_DISPLAY_DEFAULT;
        $result = &new $display_class( $this );
        return $result;
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
        //if (!$this->getData('usemore')) return false;
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
        $empty_value = false;
        if (! ($img_path = $this->getImageFileName())) return $empty_value;
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
        $empty_value = false;
        if (!$this->allowsComments()) return $empty_value;
        require_once ( 'AMP/Content/Article/Comments.inc.php' );
        $comment_set = &new ArticleCommentSet( $this->dbcon, $this->id );
        return $comment_set;
        
        //require_once ( 'AMP/Content/Article/Comment/ArticleComment.php' );
        //$comment_source = &new AMP_Content_Article_Comment_List( );
    }

    function getDocumentLink() {
        return $this->getData('doc');
    }

    function getDocLinkType() {
        return $this->getData('doctype');
    }

    function &getDocLinkRef() {
        require_once ( 'AMP/Content/Article/DocumentLink.inc.php' );
        $empty_value = false;
        if (!($doc = $this->getDocumentLink() )) return $empty_value;
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
        $this->legacyFieldname( $data, 'shortdesc', 'blurb' );

        $this->legacyFieldname( $data, 'type', 'section' );
        $this->legacyFieldname( $data, 'picture', 'image' );

        if ( isset( $data['link']) && $data['link'] && !isset( $data['linkover'])) {
            $this->mergeData( array( 'linkover' => 1));
        }

        if ( !isset( $data[ 'url' ] ) && ( $article_url = $this->getURL( )) ){
            $this->mergeData( array( 'url' => $article_url ));
        }

    }

    function _save_create_actions( $data ){
        if ( !( isset( $data['datecreated']) && $data['datecreated'] )){
            $data['datecreated'] = date( 'Y-m-d');
        }
        if ( !( isset( $data['enteredby']) && $data['enteredby'])){
            $data['enteredby'] = AMP_SYSTEM_USER_ID ;
        }
        return $data;

    }

    function _save_update_actions( $data ) {
        $data['updatedby'] = AMP_SYSTEM_USER_ID ;
        $data['updated'] = date( 'Y-m-d H:i:s');
        
        return $data;

    }

    function readVersion( $version_id ) {
        require_once ( 'AMP/Content/Article/Version.inc.php' );
        $version = &new Article_Version( $this->dbcon, $version_id );
        if (!$version->hasData()) return false;

        $this->setData( $version->getData() );
        $this->_version_status = true;
    }

    function saveVersion( ){
        require_once ( 'AMP/Content/Article/Version.inc.php' );
        $version = &new Article_Version( $this->dbcon );
        $version->setData( $this->getData( ));
        return $version->save( );
    }

    function setDefaults( ){
        $this->mergeData( array( 
            'type'      => AMP_CONTENT_MAP_ROOT_SECTION,
            'linkover'  => 1,
            'uselink'   => 1,
            'class'     => AMP_CONTENT_CLASS_DEFAULT,
            'enteredby' => AMP_SYSTEM_USER_ID,
            'datecreated'   => date( 'Y-m-d' )
        ));
    }

    function getNewAliasName( ){
        return $this->getData( 'new_alias_name' );
    }

    function getSectionsRelated() {
        if ( $internal_value = $this->_getSectionsRelatedBase( )) return $internal_value;
        return $this->_getSectionsRelatedDB( );
    }

    function _getSectionsRelatedBase( ){
        return $this->getData( 'sections_related');
    }

    function _getSectionsRelatedDB( ){
        $related_sections = &AMPContentLookup_SectionsByArticle::instance( $this->id );
        if ( !$related_sections ) return false;

        $this->mergeData( array(  'sections_related' => array_keys( $related_sections ) ));
        return array_keys( $related_sections );
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
        $this->_save_aliases( );
        $this->_save_sections_related( );
        $this->_save_tags( );
    }

    function _save_sections_related( ){
        $sections_related = $this->_getSectionsRelatedBase( );
        $active_related = $this->_getSectionsRelatedDB( ) ;
        if ( !$active_related && !$sections_related ) return false;
        if ( $active_related ) {
            $deleted_items = array_diff( $active_related, $sections_related );
            $new_items = array_diff( $sections_related, $active_related );
        } else {
            $deleted_items = array( );
            $new_items = $sections_related;
        }
        if ( empty( $deleted_items ) && empty( $new_items )) return false;

        require_once( 'AMP/Content/Section/RelatedSet.inc.php');
        $related_section_set = &new SectionRelatedSet( $this->dbcon );

        if ( !empty( $deleted_items )) {
            $delete_crit = $this->_makeRelatedSectionCriteria( $deleted_items );
            $related_section_set->deleteData( $delete_crit );
            foreach( $deleted_items as $section_id ) {
                AMPContentLookup_RelatedArticles::clear_cache( $section_id );
            }
        }
        if ( !empty( $new_items )) {
            foreach( $new_items as $section_id ) {
                $insert_values = array( 'typeid' => $section_id , 'articleid' => $this->id );
                $related_section_set->insertData( $insert_values );
                AMPContentLookup_RelatedArticles::clear_cache( $section_id );
            }

        }
        AMPContentLookup_SectionsByArticle::clear_cache( $this->id );
    }

    function _save_tags( ) {
        if ( $this->isVersion( )) return;
        return AMP_update_tags( $this->_getTagsBase( ), false, $this->id, AMP_SYSTEM_ITEM_TYPE_ARTICLE );
    }

    function getTags( ) {
        $tags = $this->_getTagsBase( );
        if ( !$tags ) {
            $this->_getTagsDB( );
        }
        return $tags;
    }

    function _getTagsBase( ) {
        return $this->getData( 'tags');
    }

    function _getTagsDB( ) {
        if ( !isset( $this->id ) && $this->id ) return false;
        $tag_lookup = AMPSystem_Lookup::instance( 'tagsByArticle', $this->id );
        if ( $tag_lookup ) {
            $tags = array_keys( $tag_lookup );
            $this->mergeData( array( 'tags' => $tags ));
        }

    }

    function _makeRelatedSectionCriteria( $section_id_array ) {
        if ( empty( $section_id_array ) || !is_array( $section_id_array )) return false;
        return 'typeid in ( '.join( ',', $section_id_array ).' ) and articleid=' . $this->id;
    }

    function _save_aliases( ){
        if ( !( $alias_name = $this->getNewAliasName( ))) return false;
        $alias_name = urlencode( $alias_name );
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

    function move( $section_id = false, $class_id = false, $related_section_id = false ) {
        $move_action = false;
        if ( $section_id && $section_id != $this->getSection( )) {
            $this->setSection( $section_id );
            $move_action = true;
        }
        if ( $related_section_id ) {
            $move_action = $move_action || ( $this->relate( $related_section_id ));
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

    function tag( $tag_id = false, $tag_names = false ) {
        if ( $tag_names ) {
            require_once( 'AMP/Content/Tag/Tag.php');
            $new_tag_set = AMP_Content_Tag::create_many( $tag_names );
            $new_tag_results = true;
            foreach( $new_tag_set as $new_tag_id ) {
                $new_tag_results = $new_tag_results && $this->tag( $new_tag_id );
            }
            if ( !$tag_id ) return $new_tag_results;
        }

        if ( !$tag_id ) return false;
        $related_tags = AMPSystem_Lookup::instance( 'tagsByArticle', $this->id );
        if ( isset( $related_tags[ $tag_id ])) return false;

        require_once( 'AMP/Content/Tag/Item/Item.php');
        $action_item = &new AMP_Content_Tag_Item( AMP_Registry::getDbcon( ));
        $create_values = array( 'item_type' => 'article', 'item_id' => $this->id, 'user_id' => AMP_SYSTEM_USER_ID, 'tag_id' => $tag_id );
        $action_item->setData( $create_values );
        return $action_item->save( );
    }

    function relate( $section_id ) {
        if ( !$section_id ) return false;
        $db_related = $this->_getSectionsRelatedDB( );
        if ( array_search( $section_id, $db_related ) !== FALSE ) return false;

        require_once( 'AMP/Content/Section/RelatedSet.inc.php');
        $related_section_set = &new SectionRelatedSet( $this->dbcon );

        $insert_values = array( 'typeid' => $section_id , 'articleid' => $this->id );
        return $related_section_set->insertData( $insert_values );

    }

    function makeCriteriaSection( $section_id ) {
        $related_articles = &AMPContentLookup_RelatedArticles::instance( $section_id );
        if ( !$related_articles ) return $this->_makeCriteriaEquals( 'type', $section_id ) ;

        return '( ' . $this->_makeCriteriaEquals( 'type', $section_id ) 
                    . ' or id in( ' . join( ',', array_keys( $related_articles ) ) . ' ) )';
    }

    function makeCriteriaTag( $tag_id ) {
        $tagged_articles = AMPSystem_Lookup::instance( 'articlesByTag', $tag_id );
        if ( !$tagged_articles || empty( $tagged_articles )) return 'FALSE';
        return 'id in( ' . join( ',', array_keys( $tagged_articles )) . ')';
    }

    function makeCriteriaFrontpage( $value ) {
        if ( !$value ) return false;
        return 'fplink=1';
    }

    function makeCriteriaNew( $value ) {
        if ( !$value ) return false;
        return 'new=1';
    }

    function makeCriteriaClass( $class_id ){
        return $this->_makeCriteriaEquals( 'class', $class_id );
    }

    function makeCriteriaType( $section_id ) {
        return $this->makeCriteriaSection( $section_id );
    }

    function getMetaDescription( ){
        $result = $this->getData( 'metadescription');
        if ( $result ) return $result;

        return $this->getBlurb( );
    }

    function getMetaKeywords( ){
        return $this->getData( 'metakeywords');
    }
/*
    function save( $save_version = true ){
        if ( isset( $this->id ) && $this->id && $save_version ) {
            $this->saveVersion( );
        }
        if ( !iss)
        return parent::save( );
    }
    */

    function get_url_edit( ) {
        return AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE_EDIT, array( 'id=' . $this->id ) );
    }

    function isVersion( ) {
        return $this->_version_status;
    }
}


?>
