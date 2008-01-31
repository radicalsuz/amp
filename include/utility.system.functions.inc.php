<?php
/**
 * Check if a file exists in the include path
 *
 * @version      1.2
 * @author       Aidan Lister <aidan@php.net>
 * @param        string $file The name of the file to look for
 * @return       bool True if the file exists, False if it does not
 */

if ( !function_exists( 'file_exists_incpath' ) ) {

    function file_exists_incpath ($file) {
        if ( file_exists( $file )) return $file;
        $paths = explode(PATH_SEPARATOR, get_include_path());

        foreach ($paths as $path)
        {
            // Formulate the absolute path
            $fullpath = $path . DIRECTORY_SEPARATOR . $file;

            // Check it
            if (file_exists($fullpath)) {
                return $fullpath;
            }
        }

        return false;
    }
}

if (!function_exists('find_local_path')) {
function find_local_path () {
    if (function_exists('apache_lookup_uri')) {

        $localInfo = apache_lookup_uri( '/custom/' );
        $localPath = preg_replace( "/(.*)\/custom.*$/", "\$1", $localInfo->filename );
        
    } elseif ( isset( $_SERVER['AMP_LOCAL_PATH'])) {
        $localPath = $_SERVER['AMP_LOCAL_PATH'];
    }

    $customPath = isset( $localPath )? $localPath . DIRECTORY_SEPARATOR . 'custom' : '';

    $searchPath = '.';
    $depth = 0;
   
    while ( !is_dir($customPath) && $depth++ < 4 ) {
        $customPath = $searchPath . DIRECTORY_SEPARATOR . 'custom';
        $localPath = realpath( $searchPath );
        $searchPath = '..' . DIRECTORY_SEPARATOR . $searchPath;
    }

    if ($depth >= 4) return null;
	if ( DIRECTORY_SEPARATOR != '/' ) {
		$localPath = str_replace( '/', DIRECTORY_SEPARATOR, $localPath );
	}
	
	return $localPath;
}
}

function AMP_init_local_path( ) {

    // If we don't yet have a local path, find one.
    if (!defined('AMP_LOCAL_PATH'))
         define( 'AMP_LOCAL_PATH', find_local_path() );

    if ( AMP_LOCAL_PATH ) {
        $incpath = ini_get('include_path');
        // Search local paths for includes.
        ini_set( 'include_path', AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . "custom" . PATH_SEPARATOR .
                                 AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . "lib" . PATH_SEPARATOR .
                                 ini_get('include_path') );
    }

}
?>
