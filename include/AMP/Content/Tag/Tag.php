<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Tag extends AMPSystem_Data_Item {
    var $datatable = "tags";
    var $name_field = "name";

    var $_class_name = 'AMP_Content_Tag';
    //var $_contents_criteria = array( );
    //var $_contents_class = 'ArticleSet';
    var $_exact_value_fields = array( 'image' );
    
    function AMP_Content_Tag( &$dbcon, $id = null ){
        $this->__construct( $dbcon, $id );
    }

    function __construct( &$dbcon, $id = null ) {
        if ( isset( $id ) && !is_numeric( $id )) {
            $tags = AMP_lookup( 'tagsSimple');
            $matches = array_keys( $tags, strtolower( $id ) ) ;
            if ( $matches ) $id = current( $matches );
        }
        $this->init( $dbcon, $id );
    }

    function getBlurb( ){
        return $this->getData( 'description');
    }

    function getImageFilename( ){
        return $this->getData( 'image');
    }

    function &getImageRef() {
        $empty_value = false;
        if (! ($img_path = $this->getImageFileName())) return $empty_value;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getImageFile( ) {
        if (! ($img_name = $this->getImageFileName())) return false;
        require_once( 'AMP/System/File/Image.php');
        $image = new AMP_System_File_Image( AMP_image_path( $img_name ));
        return $image;

    }

    function getItemDate() {
        //interface
        return false;
    }
    function getTitle () {
        return $this->getName();
    }

    function get_url_edit( ) {
        if ( !isset( $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_TAG, array( 'id=' . $this->id ) );
    }

    function getURL( ) {
        if ( !isset( $this->id )) return false;
        return AMP_Url_AddVars( AMP_CONTENT_URL_TAG, array( 'id=' . $this->id ) );
    }

    function makeCriteriaTag( $tag_name ) {
        $key = AMP_Content_Tag::findByName( $tag_name );
        if ( !$key ) return 'FALSE';
        return 'id=' . $key;
    }

    function makeCriteriaDisplayable( ) {
        return $this->makeCriteriaLive( );
    }

    /**
     * This function searches an array of tags for a given key.  If the key is supplied, it is added to the lookup. 
     * 
     * @param mixed $raw_tag_name 
     * @param mixed $add_tag_key 
     * @access public
     * @return void
     */
    function findByName( $raw_tag_name, $add_tag_key = false ) {
        static $simple_tag_names = false;
        if ( !$simple_tag_names ) {
            $simple_tag_names = AMPSystem_Lookup::instance( 'tagsSimple' );
            if ( !$simple_tag_names ) {
                $simple_tag_names = array( );
            }
        }

        
        $tag_name = strtolower( trim( $raw_tag_name ));
        if ( $add_tag_key ) {
            $simple_tag_names[$add_tag_key ] = $tag_name;
        }
        return array_search( $tag_name, $simple_tag_names );
    }

    function create_many( $tags_text ) {
        if ( !$tags_text ) return false;
        $tags_set = preg_split( '/\s?,\s?/', $tags_text );
        $new_tags_verified = array( );

        foreach( $tags_set as $raw_new_tag ) {
            $new_tag = trim( $raw_new_tag );
            if ( !$new_tag ) continue;

            //create new tag
            $new_tag_id = AMP_Content_Tag::create( $new_tag );
            if ( !$new_tag_id ) continue;
            
            //add the id to the results list
            $new_tags_verified[] = $new_tag_id;
        }
        return $new_tags_verified;
    }

    function create( $tag_name, $description = false ) {
        if ( !trim( $tag_name )) return false;
        $existing_id = AMP_Content_Tag::findByName( $tag_name );

        if ( $existing_id ) return $existing_id;
        return AMP_Content_Tag::_create( trim( $tag_name ));
    }

    function _create( $tag_name, $description = false ) {
        $tag = &new AMP_Content_Tag( AMP_Registry::getDbcon( ));
        $tag->setDefaults( );
        $tag->mergeData( array( 'name' => $tag_name , 'description' => $description ));
        $result = $tag->save( );
        if ( !$result ) return false;

        AMP_Content_Tag::findByName( $tag_name, $tag->id );
        return $tag->id;

    }

    function setDefaults( ) {
        $this->mergeData( array( 
            'publish' => 1
            ));

    }

    function getDisplay( ) {
        /*
        require_once( 'AMP/Content/Tag/Public/Intro.php');
        $intro = new AMP_Content_Tag_Public_Intro( $this );
        $intro->display_content = false;

        $display = &AMPContent_Manager::instance( );
        $display->add( $intro, AMP_CONTENT_DISPLAY_KEY_INTRO );
        */
        $tag_displays = AMP_lookup( 'tag_displays');
        $display_class = isset( $tag_displays[ $this->id ] ) ? $tag_displays[ $this->id ] : AMP_CONTENT_TAG_DISPLAY_DEFAULT;
        require_once( 'AMP/Content/Article/Public/List.php');
        if( !class_exists( $display_class )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $display_class ));
            $display_class = AMP_CONTENT_TAG_DISPLAY_DEFAULT;
        }

        return new $display_class( AMP_Registry::getDbcon( ), array( 'tag' => $this->id ));
    }

    
    function getDisplayIntro( ) {
        require_once( 'AMP/Content/Tag/Public/Intro.php');
        return new AMP_Content_Tag_Public_Intro( $this );
    }
    
/*
    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function &getDisplay() {
        require_once( 'AMP/Content/Tag/Display.inc.php');
        return new AMP_Content_Tag_Display( $this );
    }


    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new AMPTag_ContentSet( $this->dbcon, $this->id );
        $this->_contents->filter( "live" );

        return $this->_contents;
    }

    function addContentsCriteria( $criteria ) {
        if ( array_search( $criteria, $this->_contents_criteria ) !== FALSE ) return true;
        $this->_contents_criteria[] = $criteria;
    }
*/



}
?>
