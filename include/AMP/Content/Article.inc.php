<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );
require_once ( 'AMP/Content/Article/Display.inc.php' );

define ('AMP_CONTENT_STATUS_LIVE', 1);
define ('AMP_CONTENT_STATUS_DRAFT', 0);

define ('AMP_CONTENT_CLASS_DEFAULT' , 1 );
if (!defined( 'AMP_CONTENT_CLASS_FRONTPAGE' ))      define ('AMP_CONTENT_CLASS_FRONTPAGE' , 2 );
if (!defined( 'AMP_CONTENT_CLASS_SECTIONHEADER' ))  define ('AMP_CONTENT_CLASS_SECTIONHEADER' , 8 );
if (!defined( 'AMP_CONTENT_CLASS_NEWS' ))           define ('AMP_CONTENT_CLASS_NEWS' , 3 );
define ('AMP_CONTENT_CLASS_MORENEWS' , 4 );
if (!defined( 'AMP_CONTENT_CLASS_PRESSRELEASE'))    define ('AMP_CONTENT_CLASS_PRESSRELEASE' , 10 );
define ('AMP_CONTENT_CLASS_USERSUBMITTED' , 9 );
if (!defined( 'AMP_CONTENT_CLASS_ACTIONITEM'))      define ('AMP_CONTENT_CLASS_ACTIONITEM' , 5 );
if (!defined( 'AMP_CONTENT_CLASS_BLOG' ))           define ('AMP_CONTENT_CLASS_BLOG', '20');
if (!defined( 'AMP_CONTENT_CLASS_SECTIONFOOTER'))      define ('AMP_CONTENT_CLASS_SECTIONFOOTER' , false );


if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_DEFAULT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_DEFAULT' , 'sidebar_right') ;
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_LEFT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_LEFT' , 'sidebar_left') ;
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_RIGHT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_RIGHT' , 'sidebar_right') ;

if (!defined( 'AMP_ARTICLE_DISPLAY_DEFAULT'))   define( 'AMP_ARTICLE_DISPLAY_DEFAULT', 'Article_Display' );
if (!defined( 'AMP_ARTICLE_DISPLAY_FRONTPAGE')) define( 'AMP_ARTICLE_DISPLAY_FRONTPAGE', 'ArticleDisplay_FrontPage' );
if (!defined( 'AMP_ARTICLE_DISPLAY_NEWS'))      define( 'AMP_ARTICLE_DISPLAY_NEWS', 'ArticleDisplay_News' );
if (!defined( 'AMP_ARTICLE_DISPLAY_PRESSRELEASE')) define( 'AMP_ARTICLE_DISPLAY_PRESSRELEASE', 'ArticleDisplay_PressRelease' );
if (!defined( 'AMP_ARTICLE_DISPLAY_BLOG')) define( 'AMP_ARTICLE_DISPLAY_BLOG', 'ArticleDisplay_Blog' );

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
        if (!$this->getData( 'linkover' )) return false;
        if (! ($target = $this->getData( 'link' ))) return false;
        return $target;
    }

    function getURL() {
        if ($url = $this->getRedirect() ) return $url;
        if (!$this->id ) return false;
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
        if (!$this->getData( 'picuse' )) return false;
        return $this->getData( 'picture' );
    }

    function getArticleDate() {
        if (!$this->isPublicDate()) return false;
        $date_value =  $this->getData('date');
        if ($date_value == AMP_NULL_DATE_VALUE) return false;
        return $date_value;
    }

    function isPublicDate() {
        //frontpage articles have the opposite 'display date' logic as standard
        //articles
        //this is the dumbest hack ever, but until we
        //re-tool the backend forms, I have no choice

        if ($this->getClass() != AMP_CONTENT_CLASS_FRONTPAGE ) return !($this->getData( 'usedate' ));
        return $this->getData( 'usedate' );
    }

    function getItemDate() {
        return $this->getArticleDate();
    }

    function getItemDateChanged() {
        return $this->getData( 'updated');
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
    }

    function readVersion( $version_id ) {
        require_once ( 'AMP/Content/Article/Version.inc.php' );
        $version = &new Article_Version( $this->dbcon, $version_id );
        if (!$version->hasData()) return false;

        $this->setData( $version->getData() );
    }

}


?>
