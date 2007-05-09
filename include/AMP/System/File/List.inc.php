<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/System/File/File.php');
require_once( 'AMP/Content/Page/Urls.inc.php');
require_once( 'AMP/System/List/Observer.inc.php');

class AMP_System_File_List extends AMP_System_List_Form {

    var $_path_files;
    var $suppress = array( 'header'=>true , 'editcolumn' =>true );
    var $col_headers = array( 
        'File Name' => 'name',
        'Date Uploaded' => 'time' );
    var $_source_object = 'AMP_System_File';
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_actions = array( 'delete' );
    var $_url_add = AMP_SYSTEM_URL_DOCUMENT_UPLOAD;
    var $name_field = 'name';

    function AMP_System_File_List( ) {
        $this->_path_files = AMP_LOCAL_PATH . AMP_CONTENT_URL_DOCUMENTS;
        $listSource = &new $this->_source_object( );
        $this->_init_pager( $listSource );
        $source = $listSource->search( $this->_path_files );
        $this->init( $source );
    }

    function _after_init( ){
        $this->addTranslation( 'time', '_makePrettyDate');
    }

    function _getSourceRow( ){
        $row_data = parent::_getSourceRow( );
        if ( $row_data ) $row_data['id'] = strip_tags( $row_data['name'] );
        return $row_data;
    }

    function _getUrlEdit( $row_data ){
        $base_url = ( substr( AMP_SITE_URL, -1 ) == '/' ) ? substr( AMP_SITE_URL, 0, -1 ) : AMP_SITE_URL;
        return $base_url . AMP_CONTENT_URL_DOCUMENTS . $row_data['name'];
    }

    function _getNameColumnFormat( $row_data ) {
        if ( !( isset( $this->name_field ) && isset( $row_data[$this->name_field ]))) return false; 
    //    if ( isset( $this->suppress['editcolumn']) && $this->suppress['editcolumn']) return $row_data[ $this->name_field ];

        return      "<A HREF='". $this->_getUrlEdit( $row_data ) ."' title='" . AMP_TEXT_EDIT_ITEM . "' target='".$this->_getEditLinkTarget( )."'>" 
                    . $row_data[ $this->name_field ]
                    . '</a>';
    }

    /*
    function _setSortArticleLinks( $sort_direction ){
        //do nothing -- what should this do? -AP 2006-03-30
    }

    function articleLinks( $source, $column_name ) {
        $all_article_links = &AMPContent_Lookup::instance( 'articlesbyDocument');
        $linked_articles = array_keys( $all_article_links, $source->getName( ));
        if ( empty( $linked_articles )) return false;

        $article_titles = &AMPContent_Lookup::instance( 'articles' );
        $linked_article_titles = array_combine_key( $linked_articles, $article_titles );
        $renderer = &$this->_getRenderer( );
        $output = false;
        foreach( $linked_article_titles as $id => $title ){
            $output .= $renderer->link( 
                        AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE_EDIT, 'id='.$id), 
                        AMP_trimText( $title, 25 ),
                            array( 'title' => $title )) 
                        . $renderer->newline( );
        }
        return $output;
    }
    */

}

?>
