<?php
require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'AMP/Content/Article/Set.inc.php' );
require_once( 'AMP/Content/Section.inc.php' );
require_once( 'AMP/Content/Article.inc.php' );
require_once( 'RSSWriter/AMP_RSSWriter.php' );
require_once( 'AMP/Content/Page/Urls.inc.php' );
require_once( 'AMP/Content/Article/Public/List.php');
require_once( 'AMP/Content/Article/Public/Detail.php');

class AMPContent_RSSFeed extends AMPSystem_Data_Item {

    var $datatable = 'rssfeed';
    var $name_field =  'title';
    var $sourceSet;
    var $_class_name = "AMPContent_RSSFeed";
    var $_display;

    var $feed_metaData = array(
        'language' => 'en-us',
        'webmaster' => AMP_SITE_ADMIN );
    var $sourceItem_class = 'Article';

    function AMPContent_RSSFeed ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        if ( isset( $id )) $this->getDataSource();
    }

    function getDescription() {
        return $this->getData( 'description' );
    }

    function getBlurb( ) {
        return $this->getDescription( );
    }

    function getTitle() {
        return $this->getName();
    }
    function getLimit() {
        if(!$limit = $this->getData('sqllimit')) return 15;
        return $limit;
    }
    function getSort() {
        if(!($sortdef= $this->getData('orderbysql'))) return 'date desc';
        return $sortdef ." ". $this->getData('orderbyorder');
    }
    function getCombineLogic() {
        return $this->getData( 'combine_logic' );
    }
    function getCriteria() {
        $total_crit = array();
		$article_search = new Article( $this->dbcon );
        if ($crit = $this->getData('sqlwhere')) {
            if ($bad_spot =  strpos( $crit, "or typeid" )) {
                $crit = substr( $crit, 0, $bad_spot ) . ")"; 
            }
            $total_crit[] = $crit;
        }
        if ( $id = $this->getData('class_id')) {
            #$total_crit[] = "class =".$id;
            $total_crit[] = $article_search->makeCriteriaClass($id);
        }
        if ( $id = $this->getData('section_id')) {
            $section = &new Section( $this->dbcon, $id );
            $section_crit = $section->getDisplayCriteria();
			$crit = join( ' AND ', $article_search->makeCriteria($section_crit));
            $total_crit[] = $crit;
        }
        if (empty( $total_crit)) return false;
        return "( ". join( " ".$this->getCombineLogic()." ", $total_crit ). " ) AND " . $article_search->makeCriteriaDisplayable();

    }

    function getDataSource() {
        $articles = &new ArticleSet( $this->dbcon );
        $articles->addCriteria( $this->getCriteria() );
        #$articles->addCriteria( 'publish=1' );
        $articles->setSort ( $this->getSort() );
        $articles->setLimit( $this->getLimit() );
        $articles->readData();
        $this->sourceSet = &$articles;
    }

    function &getDisplay() {
        $feed_name = ($name = $this->getTitle()) ? $name : AMP_SITE_NAME;
        $this->feed_metaData['lastBuildDate'] = date('D, d M Y  H:i:s');
        $this->feed_metaData['generator'] = 'Activist Mobilization Platform '.AMP_SYSTEM_VERSION_ID;
        if ( ! $this->sourceSet->isReady() ) return false;
        $display = &new AMP_RSSWriter( AMP_SITE_URL, $feed_name, AMP_SITE_META_DESCRIPTION, $this->feed_metaData ); 
        $articleSet = $this->sourceSet->instantiateItems( $this->sourceSet->getArray(), $this->sourceItem_class );
        foreach( $articleSet as $article ) {
            $articleMeta=array();
            if ($this->getData('include_full_content')) {
                $this->_display = &new Article_Public_Detail( $article );
            } else {
                $this->_display = &new Article_Public_List( false, array(), 1 );
            }

            $url = $article->getURL();
            if ( strpos( $url, "http://" ) === FALSE ) $url = AMP_SITE_URL . $url;

            $articleMeta['description'] = $this->_makeDescription( $article );
            if ($itemdate = $article->getItemDate() ) {
                #$articleMeta['pubDate'] = strtotime( $itemdate );
                #$articleMeta['dc:date'] = date('Y-m-d', strtotime( $itemdate ));
                $articleMeta['pubDate'] = date('D, d M Y', strtotime( $itemdate ));
            }
#            $articleMeta['guid'] = $url;

            $display->addItem( $url, $article->getTitle(), $articleMeta);
        }
        return $display;
    }

    function _makeDescription( &$source ) {
        if ($this->getData('include_full_content')) {
            $output = $this->_display->render_body( $source );
        } else {
            $output = AMP_trimText( $this->_display->render_blurb( $source ), 9000 );
        }
        return $output;
    }

    function setSection( $section_id ){
        return $this->mergeData( array( 'section_id' => $section_id ) );
    }

    function setClass( $class_id ){
        return $this->mergeData( array( 'class_id' => $class_id ) );
    }

    function setCombineLogic( $logic_value = 'AND'){
        return $this->mergeData( array( 'combine_logic' => $logic_value ) );
        
    }

    function getSection( ) {
        return $this->getData( 'section_id');
    }

    function getURL( ) {
        return $this->get_live_url( "rssfeed") ;
    }

    function get_url_edit( ) {
        return $this->get_system_url( "rssfeed");
    }

    function makeCriteriaDisplayable( ) {
        return '';
    }

}

?>
