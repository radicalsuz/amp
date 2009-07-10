<?php

class AMP_System_List_Toolbar {
    var $submitGroup = "submitAction";

    var $_actions = array();
    /* examples:
    var $_actions = array(
        'publish',
        'unpublish',
        'delete',
        );
    */
    var $_headerContent = array( AMP_TEXT_WITH_SELECTED );
    var $_footerContent = array( );

    var $_display;
    var $_rendered_footerContent = false;

    var $_tabs = array( );
    var $_tab_closure = false;

    function AMP_System_List_Toolbar ( &$display ){
        $this->_display = &$display;
        $this->submitGroup = 'submitAction' . $display->getName( );
    }

    function output() {
        $output = "";
        foreach( $this->_actions as $action ) {
            $output .= $this->renderAction( $action );
        }
        $this->init_javascript( );
        $renderer = AMP_get_renderer( );
        return    
            $renderer->div( 
                $this->renderToolbarStart( )
                . $output
                . $this->renderToolbarEnd( )
                , array( 'class' => 'toolbar' )
                );
    }

    function init_javascript( ) {
        $header = &AMP_get_header( );
        $block_form_submit = <<<SCRIPT
jq = jQuery.noConflict( );
jq( '.list_block form').keypress(  function( ev ) {
    if( ev.keyCode == 13 ) { return false; }
});
SCRIPT;
        $header->addJavascriptOnload( $block_form_submit );
    }

    function execute( ){
        return $this->output( );
    }

    function getSubmitGroupName( ){
        return $this->submitGroup;
    }

    function renderAction( $action ){
        $button_method_1 = 'render' . ucfirst( $action );
        $button_method_2 = 'render_toolbar_' . ( $action );
        $button_method = method_exists( $this->_display, $button_method_2 ) ? $button_method_2 : $button_method_1 ;
        if (!method_exists( $this->_display, $button_method )) return $this->renderDefault( $action );
        return $this->_display->$button_method( $this );
    }

    function renderDefault( $action ){
        $renderer = AMP_get_renderer( );
        $display_text = ucwords( str_replace( '_', ' ', $action ));
        return "<input type='submit' name='". $this->submitGroup ."[" . $action ."]' value='" . $display_text ."'>\n" . $renderer->space( );
    }

    function renderToolbarStart( ){
        return join( '', $this->_headerContent);
    }
    function renderToolbarEnd( ){
        if ( $this->_rendered_footerContent ) return false;
        $this->_rendered_footerContent = true;
        return join( '', $this->_footerContent);
    }
    function addStartContent( $content, $key = null ){
        if ( isset( $key )) return $this->_headerContent[$key] = $content;
        $this->_headerContent[] = $content;
    }
    function addEndContent( $content, $key = null ){
        if ( isset( $key )) return $this->_footerContent[$key] = $content;
        $this->_footerContent[] = $content;
    }

    function addAction( $action_name ){
        $this->_actions[] = $action_name;
    }

    function setActionGlobal( $action_name ){
        //interface
    }

    function setSubmitGroup(  $name ){
        $this->submitGroup = $name;
    }

    function add_panel( $action, $contents = array( )) {
        $renderer = AMP_get_renderer( );
        $tab_name = $action . '_targeting';
        //$this->_tabs[$action] = $tab_name;
        if ( is_array( $contents )) {
            $contents = join( $renderer->space( ), $contents );
        }

        $tab_contents = 
                $renderer->div( 
                    $contents
                    . $renderer->space( ) 
                    . $this->renderDefault( $action ) 
                    . $renderer->space( ) 
                    . $renderer->input( 'hide_'.$action.'_panel', 'Cancel', array( 'type'=>'button', 'onClick'=>'new Effect.DropOut( "'.$tab_name.'" );')) 
                    . $renderer->space( ),
                    array( 
                            'class' => 'panel', 
                            'style' => 'display:none;', 
                            'id' => $tab_name 
                            )
                    );
            $this->addEndContent( $tab_contents, $tab_name);
        $display_text = ucwords( str_replace( '_', ' ', $action ));

        return  
            $renderer->input( 'show_'. $action .'_panel', $display_text, 
                    array(  'type' => 'button', 'onclick' => '$$( ".toolbar .panel").invoke( "hide");AMP_show_panel( "'.$tab_name.'" );' ))
            . $renderer->space( );

    }

    function addTab( $action, $contents=array( ) ) {
        return $this->add_panel( $action, $contents );

            /*
        $renderer = AMP_get_renderer( );
        $tab_name = $action . '_targeting';
        $this->_tabs[$action] = $tab_name;
        if ( is_array( $contents )) {
            $contents = join( $renderer->space( ), $contents );
        }
            
            $renderer->inDiv( 
                    '<a name="'. $tab_name .  '"></a>'
                    . $contents . "\n" 
                    . $renderer->space( )
                    . $this->renderDefault( $action )
                    . "<input type='button' name='hide".ucfirst( $action ) . "' value='Cancel' onclick='window.change_any( \"".$tab_name."\");'>\n"
                    . $renderer->space( ),
                    array( 
                        'class' => 'AMPComponent_hidden', 
                        'id' => $tab_name )
                );

        $this->addEndContent( $tab_contents, $tab_name);
        $this->_initListTabs( );

        return  "<input type='button' name='show".ucfirst( $action )."' value='".ucfirst( $action ) . "' "
                . "onclick='window.clearListTabs(\"".$tab_name."\" );window.change_any( \"".$tab_name."\" );"
                . "window.scrollTo( 0, document.anchors[\"".$tab_name."\"].y );'>\n". $renderer->space( );
                */

    }
    /*
    function addTab_old( $action, $contents=array( ) ) {
        $renderer = AMP_get_renderer( );
        $tab_name = $action . '_targeting';
        $this->_tabs[$action] = $tab_name;
        if ( is_array( $contents )) {
            $contents = join( $renderer->space( ), $contents );
        }

        $tab_contents = 
            $renderer->inDiv( 
                    '<a name="'. $tab_name .  '"></a>'
                    . $contents . "\n" 
                    . $renderer->space( )
                    . $this->renderDefault( $action )
                    . "<input type='button' name='hide".ucfirst( $action ) . "' value='Cancel' onclick='window.change_any( \"".$tab_name."\");'>\n"
                    . $renderer->space( ),
                    array( 
                        'class' => 'AMPComponent_hidden', 
                        'id' => $tab_name )
                );

        $this->addEndContent( $tab_contents, $tab_name);
        $this->_initListTabs( );

        return  "<input type='button' name='show".ucfirst( $action )."' value='".ucfirst( $action ) . "' "
                . "onclick='window.clearListTabs(\"".$tab_name."\" );window.change_any( \"".$tab_name."\" );"
                . "window.scrollTo( 0, document.anchors[\"".$tab_name."\"].y );'>\n". $renderer->space( );

    }
    */

    function _initListTabs( ) {
        $list_popup_script = '';
        foreach( $this->_tabs as $tab_name ) {
            $list_popup_script .= "if ( exempt_tab_name != '".$tab_name."' && ( $(\"".$tab_name."\").style.display==\"block\") ) window.change_any( \"".$tab_name."\");\n";
        }
        $script  = "\nfunction clearListTabs( exempt_tab_name ) {\n"
                    . $list_popup_script 
                    . "\n}";
        $header = &AMP_get_header( );
        $header->addJavascriptDynamic( $script, 'list_tabs_clear' );
    }
}

?>
