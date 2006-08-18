<?php

class AMP_Display_Template {

    var $_template;
    var $_properties;
    var $_output;


    //var $_allowed_methods = array( );
    //var $_confirmed_methods = array( );
    //var $_methods_output= array( );
    //var $_method_args = array( );

    var $_helpers;
    var $_tokens_active;

    var $_path_default = 'AMP/Display/';
    var $_path_current = 'AMP/Display/';
    var $_extension = 'thtml';
    var $_displays_partial = array( );
    var $_headings = array( );

    function AMP_Display_Template( $template_path = null ) {
        $this->__construct( $template_path );
    }

    function __construct( $template_path = null ) {
        if ( isset( $template_path )) $this->set_path_template( $template_path );
    }

    function set_path_template( $path ){

        $this->_template = $this->_load_template_file( $path );
        $this->_path_current = substr( $path, 0, strlen( $path ) - strlen( basename( $path )));
        $dotspot = strrpos( basename( $path ), ".");
        if ( $dotspot ) $this->_extension = substr( basename( $path ), $dotspot + 1 );
        preg_match_all( "/%([\w\s]+)(%|$)/", $this->_template, $token_results, PREG_PATTERN_ORDER );
        $this->_tokens_active = $token_results[1];
    }

    function _load_template_file( $request_path ) {
        $path_verified = false;
        $path_exists = file_exists_incpath( $request_path );

        if ( $path_exists ){
            $path_verified = $path_exists; 
        } else {
            $test_path = $this->_path_default . basename( $request_path );
            if ( $path_exists = file_exists_incpath( $test_path )) {
                $path_verified = $path_exists;
            }
        }

        if ( file_exists( $path_verified )) return file_get_contents( $path_verified );
        /*
        $paths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($paths as $path)
        {
            // Formulate the absolute path
            $fullpath = $path . DIRECTORY_SEPARATOR . $path_verified;

            // Check it
            if (file_exists($fullpath)) {
                return file_get_contents( $fullpath );
            }
        }
        return false;
        */
    }

    function has_template( ){
        return $this->_template;
    }

    function set_property( $name, $value ){
        $this->_properties[ $name ] = $value;
    }

    function set_properties( $values ){
        foreach( $values as $name => $value ){
            $this->set_property( $name, $value );
        }
    }

    function set_method( $key, $method_desc, $args = array( ) ) {
        $this->_allowed_methods[ $key ] = $method_desc;
        $this->_method_args[ $key ] = $args;
    }

    function execute( ){
        $this->_replace_tokens( );
        return $this->_output;
    }

    function _replace_tokens( ){
        $replace_set = array( );
        foreach( $this->_tokens_active as $token ){
            if ( !isset( $this->_properties[ $token ])) continue;
            $key = '%' . $token . '%';
            $replace_set[ $key ] = $this->_properties[ $token ];
        }
        $this->_output = str_replace( array_keys( $replace_set ), array_values( $replace_set ), $this->_template );
        $this->_execute_renderers( );
        $this->_execute_helpers( );
    }

    function _execute_renderers( ){
        $render_requests = array( );
        //$request_types = array( 'heading', 'conditional' );
        $render_request_types = array( 'render_partial_conditional', 'render_partial_heading', 'render_partial_loop', 'render_partial');
        foreach( $this->_tokens_active as $token ){
            if ( strpos( $token, 'render_partial_' ) === FALSE ) continue;
            $token_key = '%' . $token . '%';
            foreach( $render_request_types as $request_type ){
                if ( strpos( $token, $request_type ) !== 0 ) continue;
                $key = substr( $token, strlen( $request_type ) + 1 );
                $local_method = '_' . $request_type;
                $render_requests[ $token_key ] = $this->$local_method( $key );
                break;
            }
            /*
            if ( strpos( $token, 'render_partial_conditional_' ) === 0 ){
                $key = substr( $token, 27 );
                $render_requests[ $token_key ] = $this->_render_partial_conditional( $key );
                continue;
            }
            if ( strpos( $token, 'render_partial_heading_' ) === 0 ){
                $key = substr( $token, 23 );
                $render_requests[ $token_key ] = $this->_render_partial_heading( $key );
                continue;
            }
            //default
            $key = substr( $token, 15 );
            $render_requests[ $token_key ] = $this->_render_partial( $key );
            */

        }
        $this->_output = str_replace( array_keys( $render_requests ), array_values( $render_requests ), $this->_output );
    }

    function _render_partial_conditional( $key ){
        if ( isset( $this->_properties[ $key ]) && $this->_properties[ $key ]) {
            return $this->_render_partial( $key );
        }
    }

    function _render_partial_heading( $key ){
        if ( isset( $this->_properties[ $key ]) 
                && (  !isset( $this->_headings[ $key ]) 
                    || strtolower( $this->_properties[ $key ] ) != strtolower( $this->_headings[ $key ]))) {
            $this->_headings[ $key ]  = $this->_properties[ $key ];
            $result = $this->_render_partial( 'heading.'. $key );
            if ( $result ) return $result;
            
            //fallback use default heading template
            $this->set_property['heading'] = $this->_headings[ $key ];
            return $this->_render_partial( 'heading' );
        }
    }

    function _render_partial_loop( $key ) {
        if ( !is_array( $this->_properties[ $key ])) return false;

        $output = false;
        $display = &$this->_load_partial( $key );
        foreach( $this->_properties[$key] as $id => $source ){
            if ( is_array( $source )) {
                $display->set_properties( $source );
            }
            if ( is_object( $source )) {
                $display->set_properties( $source->getData( ) );
            }
            $output .= $display->execute( );
        }
        return $output;
    }

    function _render_partial( $key ){
        $display = &$this->_load_partial( $key );
        if ( !$display->has_template( )) return false;
        return $display->execute( );
    }

    function &_load_partial( $key ) {
        $file_name = $this->_make_partial_filename( $key );
        $partial_display = & new AMP_Display_Template( $file_name );
        $partial_display->inherit( $this );
        return $partial_display;
    }

    function get_properties( ){
        return $this->_properties;
    }

    function get_helpers( ){
        return $this->_helpers;
    }

    function add_helper( &$helper, $key  ){
        $this->_helpers[ $key ] = &$helper;
    }

    function inherit( &$display ){
        $this->set_properties( $display->get_properties( ));
        foreach( $display->get_helpers( ) as $key => $helper ) {
            $this->add_helper( $helper, $key );
        }
    }

    function _execute_helpers( ){
        $helper_output = array( );
        foreach( $this->_tokens_active as $token ) {
            if ( strpos( $token, 'helper_' ) === FALSE ) continue;

            $token_key = '%' . $token . '%';
            $helper_request = split( '_', $token );
            $helper_key = $helper_request[1];
            if ( !isset( $this->_helpers[ $helper_key ])) continue;

            $helper_method = join( '_', array_slice( $helper_request, 2 ));
            if ( !method_exists( $this->_helpers[ $helper_key ], $helper_method )) continue;

            $active_helper = &$this->_helpers[ $helper_key ];
            $helper_output[ $token_key ] = $active_helper->$helper_method( $this->get_properties( ));
        }
        $this->_output = str_replace( array_keys( $helper_output ), array_values( $helper_output ), $this->_output );
    }

    function _make_partial_filename( $key ) {
        return $this->_path_current . $key . '.inc.' . $this->_extension;
    }

/*
    function _populate_values( ){
        $this->_populate_properties( );
        $this->_populate_methods( );
    }

    function _populate_properties( ){
        $this->_output = str_replace( array_keys( $this->_properties ), array_values( $this->_properties ), $this->_template );
    }

    function _populate_methods( ){
        foreach( $this->_allowed_methods as $key => $method_def ){
            if ( !( $action_type = $this->_verify_method_def( $key ))) continue;
            $this->_methods_output[$key] = call_user_func_array( $action_type, $this->get_method_args( $key ) );
        }

        $this->_output = str_replace( array_keys( $this->_methods_output), array_values( $this->_methods_output), $this->_output );
    }

    function _verify_method_def( $key, $method_def ) {
        if ( isset( $this->_confirmed_methods[ $key ])) return $this->_confirmed_methods[ $key ];

        // free function
        $result = ( is_string( $method_def ) && function_exists( $method_def ));
        if ( $result ) return $this->_confirm_method( $key, $method );

        //class method
        $result = ( is_array( $method_def ) && class_exists( $method_def[0]) && method_exists( $method_def[0], $method_def[1] ));
        if ( $result ) return $this->_confirm_method( $key, array( $method_def[0], $method_def[1] );

        //actual object
        $result = ( is_array( $method_def ) && is_object( $method_def[0]) && method_exists( $method_def[0], $method_def[1] ));
        if ( $result ) return $this->_confirm_method( $key, array( $method_def[0], $method_def[1] );

        //confirm failed
        return $this->_confirm_method( $key, false );
    }

    function _confirm_method( $key, $method_simple ){
        $this->_confirmed_methods[$key] = $method_simple;
        return $method_simple;
    }

    function get_method_args( $key ) {
        $results = array( );
        foreach( $this->_method_args[ $key ] as $value ){
            $results[$key] = $value;
            if ( isset( $this->_properties[$value])) $results[$key] = $this->_properties[$value];
        }
        return $results;
    }
    */

}

?>
