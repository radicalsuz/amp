<?php

define ('AMP_PHP_START_TAG' , '<?php' );
define ('AMP_PHP_END_TAG' , '?>' );

class NavEngine_HTML extends NavEngine {

    var $_engine_type = 'HTML';

    function NavEngine_HTML( &$nav ) {
        $this->init( $nav );
    }

    function execute() {
        if (!($html = $this->nav->getBaseHTML())) return false;
        return $this->_activateIncludes( $html );
    }

    function _activateIncludes( $html ) {
        $start = $this->_findPhpStartTag( $html );
        if ($start === FALSE) return $html;
        $start = $start + strlen( AMP_PHP_START_TAG );

        $end = $this->_findPhpEndTag( $html, $start );
        if ($end === FALSE) return $html;

        $result = $this->_processPhpInclude( substr( $html, $start, $end-$start) );

        $block_end = $end + strlen( AMP_PHP_END_TAG );
        $block_start = $start - strlen( AMP_PHP_START_TAG );
        $current_html = $this->_replacePhpBlock( $html, $result, $block_start, $block_end );

        return $this->_activateIncludes( $current_html );
    }

    function _processPhpInclude( $code ) {
        if (!($filename = $this->_getIncludeFilename( $code ))) return false;

        extract ($this->_globalVars());
        extract ($this->_allowedVars( $code ));

        ob_start();
        include( $filename );
        $include_value = ob_get_contents();
        ob_end_clean();

        return $include_value;
    }

    function _globalVars() {
        $page = AMPContent_Page::instance();
        return array(
            'base_path' =>  ( AMP_BASE_PATH . DIRECTORY_SEPARATOR ),
            'dbcon'     =>  &$page->dbcon,
            'MM_type'   =>  $page->section_id,
            'MM_parent' =>  $page->section->getParent(),
            'MM_typename'=> $page->section->getName(),
            'MM_website_name'=> AMP_SITE_NAME,
            'Web_url'		=> 	AMP_SITE_URL,
            'MM_region' =>  $GLOBALS['MM_region'],
            'list'      =>  $page->isList(),
            'id'        =>  $page->article_id 
        );
    }

    function _findPhpStartTag( $html, $offset = 0 ) {
        return strpos( $html, AMP_PHP_START_TAG );
    }

    function _findPhpEndTag( $html, $offset = 0 ) {
        return strpos( $html, AMP_PHP_END_TAG );
    }

    //these variables are still allowed in navs for legacy compatibility
    //please don't introduce new ones
    function _allowedVars( $code ) {
        $result = array();
        $allowed_vars = array( 'navalign', 'regionlink' );

        foreach( $allowed_vars as $varname ) {
            $start = strpos( $code, '$' . $varname );
            if ($start === FALSE) continue;
            $end = strpos( $code, ';', $start)+1;
            if ($end === FALSE) continue;

            $statement = substr($code, $start, $end - $start );
            eval( $statement ); 
            $result[ $varname ] = $$varname;
        }
        return $result;
    }

    function _getIncludeFilename( $code ) {
        $include_args = preg_replace("/.*include\s*[\(\s*]?\s*\"?([^\)\"\s]*)\"?[\)\s*]?.*/", "\$1", $code );
        $incl = str_replace('"','',$include_args);
        $customfile = AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR . $incl;
        if (file_exists( $customfile )) return $customfile;
        
        $basefile = 'AMP/Nav/'.$incl;
        if (file_exists_incpath( $basefile )) return $basefile;
        if (file_exists_incpath( $incl )) return $incl;
        return false;
    }

    function _replacePhpBlock( $original, $insert, $start, $end ) {
        return substr( $original, 0, $start) . $insert . substr($original, $end);
    }

}

class NavEngine {

    var $nav;
    var $_engine_type = 'default';

    function NavEngine( &$nav ) {
        $this->init( $nav );
    }

    function init( &$nav ) {
        $this->nav = &$nav;
    }

    function getEngineType() {
        return $this->_engine_type;
    }

}
?>
