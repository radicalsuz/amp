<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Badge extends AMPSystem_Data_Item {

    var $datatable = "badges";
    var $name_field = "name";

    function AMP_Content_Badge ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

	function getInclude() {
		return $this->getData('include');
	}

	function getIncludeFunction() {
		return $this->getData('include_function');
	}

    function getIncludeFunctionArguments( ) {
        $value = $this->getData( 'include_function_args');
        if ( !$value ) return $this->getDefaultArguments( );
        $tuples = split( "&&", $value );

        $values = array( );
        foreach( $tuples as $tuple ) {
            $tuple_segments = split( '=', $tuple );
            if ( count( $tuple_segments ) > 2 ) continue;

            $values[ $tuple_segments[0]] = $tuple_segments[1];
        }

        return array_merge( $this->getDefaultArguments( ), $values );
    }

    function getDefaultArguments( ) {
        require_once( 'AMP/Content/Page.inc.php');
        $page = AMPContent_Page::instance( );
        $values['section'] = $page->getSectionId( );
        $values['class'] = $page->getClassId( );
        $values['article'] = $page->getArticleId( );
        $values['intro_id'] = $page->getIntroId( );

        foreach( $values as $key => $value ) {
            if ( !$value ) {
                unset ( $values[ $key ] );
            }
        }

        return $values;

    }

	function getHtml() {
		return $this->getData('html');
	}

	function getGallery() {
		return $this->getData('gallery');
	}

	function execute() {
		$output = false;
		if ($output = $this->render_php_include()) return $output;
		if ($output = $this->render_html()) return $output;
		if ($output = $this->render_gallery()) return $output;
		return $output;
	}

    function output( ) {
        return $this->execute( );
    }

	function render_php_include() {
		if (!($include_filename = $this->getInclude() )) return false;
		if (!file_exists_incpath($include_filename)) {
			trigger_error( sprintf( AMP_TEXT_ERROR_FILE_EXISTS_NOT, $include_filename )) ;
			return false;
		} 

		if ($include_function = $this->getIncludeFunction()) {
			include_once( $include_filename );
			if (is_callable($include_function)) {
                $arguments = $this->getIncludeFunctionArguments( );
				return $include_function( $arguments );
			} else {
				trigger_error( AMP_TEXT_ERROR_NOT_DEFINED, $include_filename, $include_function );
				return false;
			}
			
		} 

		//default, just include the file
		ob_start();
		include( $include_filename );
		$include_value = ob_get_contents();
		ob_end_clean();

		return $include_value;
	}

	function getBody() {
		return $this->getHtml();
	}

	function render_html() {
		return $this->getHtml();
	}

	function render_gallery() {
		//stub, fix later 2007-03-19 AP
		return false;
	}

	function getURL() {
		if (!$this->id) return false;
		return AMP_url_add_vars( AMP_CONTENT_URL_BADGE, "id=".$this->id);
	}

	function get_url_edit() {
		if (!$this->id) return false;
		return AMP_url_add_vars( AMP_SYSTEM_URL_BADGE, "id=".$this->id);
	}

    function getStatus( ) {
        return $this->isLive( ) ? AMP_PUBLISH_STATUS_LIVE : AMP_PUBLISH_STATUS_DRAFT;
    }

}

?>
