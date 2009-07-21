<?php
require_once( 'AMP/System/Base.php');

class AMP_System_File {

    var $_path;
    var $_basename;
    var $_extension;

    var $_sort_property;
    var $_sort_direction = AMP_SORT_ASC;
    var $_sort_method = "";
    var $_sort_glob_property = 'name';
    var $_sort_glob_direction = AMP_SORT_ASC;
    var $_source_criteria = array( );

    var $_observers = array( );
    var $id;
    var $_class_name = 'AMP_System_File';
    var $_parent_folder;
    var $_special_parent_folders = array( "img/original", "img/pic", "img/crop", "img/thumb", "downloads"  );

    var $_mimetype;

    //mimetype cacheing is a performance optimization for large file lists
    var $_mimetype_cache;


    var $_search_offset;
    var $_search_limit;
#    var $_search_total;

    function AMP_System_File( $file_path = null ){
        if ( isset( $file_path ) && !is_object( $file_path )) $this->setFile( $file_path );
    }

    function setFile( $tainted_file_path ){
		$file_path = AMP_pathFlip( $tainted_file_path );
        $this->_path = $file_path;
        $this->_basename = basename( $file_path );
        $this->_extension = $this->findExtension( $file_path );
        $folders = split( DIRECTORY_SEPARATOR, dirname( $file_path ) );
        $this->_parent_folder = array_pop( $folders );

        $this->id = $this->getName( );
    }

    function findExtension( $file_path ){
        if (!( $dotspot = strrpos( $file_path, "." ))) return false;
        return strtolower( substr( $file_path, $dotspot+1) );
        
    }

    function getTime( ){
        return filemtime( $this->getPath( ) );
    }

    function getItemDate( ){
        return date( 'M d, Y', $this->getTime( ));
    }

    function getName( ){
        if( $this->inSpecialSubfolder( )) {
            return $this->_parent_folder . DIRECTORY_SEPARATOR . $this->_basename;
        }

        return $this->_basename;
    }

    function inSpecialFolder( ) {
        return preg_match( "/(" . str_replace( "/", "\/", join( "|", $this->_special_parent_folders)). ")/", $this->_path );
    }

    function inSpecialSubfolder( ) {
        if ( !$this->inSpecialFolder( )) return false;
        return !preg_match( "/(" . str_replace( "/", "\/", join( "|", $this->_special_parent_folders)). ")$/", dirname( $this->_path ) );

    }

    function getFilename( ) {
        return $this->_basename;

    }

    function getSubfolder( ) {
        return $this->_parent_folder;
    }

    function getExtension( ){
        return $this->_extension;
    }

    function getPath( ){
        return $this->_path;
    }

    function set_mimetype( $mimetype_value = null ){
        if ( isset( $mimetype_value ) ) return $this->_mimetype = $mimetype_value;
        if ( !( function_exists( 'mime_content_type' ))) return false;

        $mime_filetype = false;
        //if ( !( $mime_filetype = $this->lookup_mimetype( ))) {
		$file_path = $this->getPath();
		if (is_dir($file_path)) return false;
		$mime_filetype = mime_content_type( $file_path );
        //    $this->cache_mimetype( $mime_filetype );
        //}

        if ( !$mime_filetype ) return false;

        return $this->_mimetype = $mime_filetype ;
    }

    function get_mimetype( ){
        return $this->_mimetype;
    }

    function find( $criteria=array( ), $result_class=null ) {
        $criteria = array_merge( $this->_source_criteria, $criteria );
        if ( isset( $result_class )) $this->_class_name = $result_class;
        $path = isset( $criteria['path']) && $criteria['path'] ? $criteria['path'] : false;
        $pattern = isset( $criteria['pattern']) && $criteria['pattern'] ? $criteria['pattern'] : '*';
        unset( $criteria['path'] );
        unset( $criteria['pattern'] );
        $result = $this->search( $path, $pattern );
        if( empty( $criteria )) return $result;
        if( !$result ) return false;
        return $this->filter( $criteria, $result );

    }

    function create_glob_expression( $criteria = array( )) {
        if( empty( $criteria)) return "*";
        $path = isset( $criteria['path']) && $criteria['path'] ? $criteria['path'] : false;
        $pattern = isset( $criteria['pattern']) && $criteria['pattern'] ? $criteria['pattern'] : '*';
        if ( $path && ( substr( $path, -1 ) !== DIRECTORY_SEPARATOR )) $path .= DIRECTORY_SEPARATOR;
        $path = $this->add_subfolders_glob_to_path( $path );

        return $path . $pattern;

    }

    function add_subfolders_glob_to_path( $path ) {
        $subfolders =  array_map( "basename", glob( $path . "*", GLOB_ONLYDIR ) );
        if( !empty( $subfolders )) {
            $subfolders = array_map( array( $this, "append_slash" ), $subfolders );
            array_push( $subfolders, "");
            $path .= "{".join( ",", $subfolders ) . "}";
        }
        return $path;
    }

    function append_slash( $value ) {
        return $value . DIRECTORY_SEPARATOR;
    }

    function assign_criteria( $criteria = array( )) {
        $this->_source_criteria = array_merge( $this->_source_criteria, $criteria );
    }

    function filter( $criteria, $data ) {
        $result_data = $data;
        foreach( $criteria as $key => $test ) {
            $crit_method = 'filterBy'. AMP_to_camelcase( $key );
            if( !method_exists( $this, $crit_method )) continue;
            $this->_filter_crit = $test;
            $result_data = array_filter( $result_data, array( $this, $crit_method ));
        }
        return $result_data;
    }

    function search( $folder_path = false, $filename_pattern = '*'){;
        if ( !$folder_path ) return false;

        if ( substr( $folder_path, -1 ) !== DIRECTORY_SEPARATOR ) $folder_path .= DIRECTORY_SEPARATOR;
        $complete_path = $folder_path . $filename_pattern;
        $folder_cache_key = AMP_CACHE_TOKEN_DIR . $complete_path;

        $class_name = $this->_class_name;
        $search_total = 0;
        $result_set = array( );
        $dir_contents = $this->_search_limit ? false : AMP_cache_get( $folder_cache_key );
        if ( !$dir_contents ) {

            $files = $this->sort_glob( glob( $this->create_glob_expression( array( 'path' => $folder_path, 'pattern' => $filename_pattern )), GLOB_BRACE ));
            foreach( $files as $file_name ) {
                if (!is_file( $file_name )) continue; 
    
                $search_total++;
                if ( isset( $this->_search_offset ) && ( $search_total < $this->_search_offset ) ){
                    continue;
                }
                if ( isset( $this->_search_limit ) 
                     && ( count( $result_set ) >= $this->_search_limit )) break;

                $result_set[ basename( $file_name )] = &new $class_name( $file_name );
                

            }
            //Cache Folder results for large searches
            if ( ( count( $result_set ) > 500 ) && !$this->_search_limit ) {
                AMP_cache_set( $folder_cache_key, $result_set );
            }
        } else {
            $result_set = &$dir_contents;
        }
        #$this->sort( $result_set );

        return $result_set;
    }

    function sort_glob( $file_names ) {
        $glob_sort_method = 'sort_glob_' . $this->_sort_glob_property;
        if( !isset( $this->_sort_glob_property ) || !method_exists( $this, $glob_sort_method )) return $file_names;
        if( $this->_sort_direction != AMP_SORT_ASC ) return array_reverse( $this->$glob_sort_method( $file_names ));
        return $this->$glob_sort_method( $file_names );
    }

    function sort_glob_name( $file_names ) {
        natcasesort( $file_names );
        return $file_names;
    }
    function sort_glob_time( $file_names ) {
        $timeset = array_map( 'filemtime', $file_names );
        asort( $timeset );
        return array_combine_key( array_keys( $timeset ), $file_names );
    }

    function set_sort_glob( $sort_property, $direction = false ) {
        $this->_sort_glob_property = $sort_property;
        if( $direction ) $this->_sort_direction = $direction;
    }

    function filterByRegex( $item ) {
        return preg_match( $this->_filter_crit, $item->getName( ));
    }

    function getCacheKeySearch( ){
        $current_file = basename( $this->getPath( ));
        $cut_off = 0 - strlen( $current_file );
        return AMP_CACHE_TOKEN_DIR . substr( $this->getPath( ), 0, $cut_off );
    }

    function NoLimitRecordCount( ){
        #return count( glob( $this->create_glob_expression( $this->_source_criteria )));
        $set = ( glob( $this->create_glob_expression( $this->_source_criteria ), GLOB_BRACE));
        $file_set = array_filter( $set, 'is_file' );
        $dir_set = array_filter( $set, 'is_dir' );
        $value = count( $file_set );
        return $value;
    }

    function sort( &$file_set, $sort_property=null, $sort_direction = null ){
        $this->_sort_default( $file_set );
        if ( !isset( $sort_property) && !isset( $this->_sort_property)) return true;
        if ( !isset( $sort_property)) $sort_property = $this->_sort_property;

        if ( !$this->setSortMethod( $sort_property )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_SORT_PROPERTY_FAILED, $sort_property, get_class( $this ) ));
            return false;
        }

        if ( isset( $sort_direction ))  $this->_sort_direction = $sort_direction;

        uasort( $file_set, array( $this ,'_sort_compare'));
        return true;

    }

    function setOffset( $offset ){
        $this->_search_offset = $offset;
    }

    function setLimit( $qty ){
        $this->_search_limit = $qty;
    }

    function _sort_compare( $file1, $file2 ){
        if ( !( $sort_method = $this->_sort_accessor )) return 0;
        if ( $this->_sort_direction == AMP_SORT_DESC )
            #return ( $file1->$sort_method( ) < $file2->$sort_method( ) ) ? 1 : -1; 
            return ( strnatcasecmp( $file2->$sort_method( ), $file1->$sort_method( )));
        return ( strnatcasecmp( $file1->$sort_method( ), $file2->$sort_method( )));
        #return ( $file1->$sort_method( ) > $file2->$sort_method( ) ) ? 1 : -1; 
    }

    function setSortMethod( $sort_property ){
        $access_method = 'get' . ucfirst( $sort_property );
        if ( !method_exists( $this, $access_method )) return false;
        $this->_sort_accessor = $access_method;
        return true;
    }

    function _sort_default( &$file_set ){
        uksort( $file_set, "strnatcasecmp" );
    }

    function delete( ){
        $result = unlink( $this->getPath( ));
        $this->notify( 'delete');
        return true;
    }

    function notify( $action ){
        foreach( $this->_observers as $observer ){
            $observer->update( $this, $action );
        }
    }

    function addObserver( &$observer, $observer_key = null ){
        if ( isset( $observer_key )){
            $this->_observers[$observer_key] = &$observer;
            return;
        }
        $this->_observers[] = &$observer;
    }

    function get_index( $property ) {
        $set = ( glob( $this->create_glob_expression( $this->_source_criteria ), GLOB_BRACE ));
        if( !$set ) return array( );
        $file_set = array_filter( $set, 'is_file' );
        $file_set = $this->sort_glob( $file_set );
        if ( $property == 'name' ) return array_map( array( $this, 'name_with_special_subfolder' ), $file_set );
        if ( $property == 'time') {
            $format = array_fill( 0, count( $file_set), AMP_CONTENT_DATE_FORMAT );
            return array_map( 'date', $format, array_map( 'filemtime', $file_set ));
        }
    }

    function name_with_special_subfolder( $path ) {
        $image_folders = AMP_lookup( 'image_folders');
        if( empty( $image_folders )) return basename( $path );
        if( in_array( basename( dirname( $path )), $image_folders )) {
            return basename( dirname( $path )) . DIRECTORY_SEPARATOR . basename( $path );
        }
        return basename( $path );

    }

    function get_select_from_sort( $sort_property ) {
        return $sort_property;
    }

    function getURL( ){
        $local = str_replace( AMP_LOCAL_PATH, '', dirname( $this->getPath( )));
        if( strpos( $local, AMP_CONTENT_DOCUMENT_PATH ) !== FALSE ) return '/'. AMP_CONTENT_DOCUMENT_PATH . '/'. $this->getName( );
        return false;
    }

    function get_url_edit( ) {
        if ( !$this->getName( )) return AMP_SYSTEM_URL_DOCUMENT_UPLOAD;
        return $this->getURL( );
    }

    function makeCriteriaName( $name ) {
        return '*' . $name . '*';
    }

    function getErrors( ) {
        return false;
    }

     //{{{ Object based Search methods: makeCriteria

    /**
     * makeCriteria 
     * 
     * @param mixed $data 
     * @access public
     * @return void
     */
    function makeCriteria( $data ){
        $return = array( );
        if ( !( isset( $data ) && is_array( $data ))) return false;
        foreach ($data as $key => $value) {
            $crit_method = 'makeCriteria' . ucfirst( $key );

            if (method_exists( $this, $crit_method )) {
                $return[$key] = $this->$crit_method( $value );
                continue;
            }
           /*
            if ( $crit_method = $this->_getCriteriaMethod( $key )){
                $return[$key] = $this->$crit_method( $key, $value );
            }
            */

        }
        return $return;
    }

    
    //}}}

}

?>
