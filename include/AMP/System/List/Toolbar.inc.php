<?php
define( 'AMP_TEXT_WITH_SELECTED', 'With Selected:&nbsp;');

class AMP_System_List_Toolbar {
    var $submitGroup = "submitAction";

    var $_actions = array();
    /*
    var $_actions = array(
        'publish',
        'unpublish',
        'delete',
        );
    */
    var $_headerContent = AMP_TEXT_WITH_SELECTED;
    var $_footerContent;

    var $_display;

    function AMP_System_List_Toolbar ( &$display ){
        $this->_display = &$display;
        $this->submitGroup = 'submitAction' . $display->getName( );
    }

    function output() {
        $output = "";
        foreach( $this->_actions as $action ) {
            $output .= $this->renderAction( $action );
        }
        return $this->renderToolbarStart( )
                . $output
                . $this->renderToolbarEnd( );
    }

    function execute( ){
        return $this->output( );
    }

    function getSubmitGroupName( ){
        return $this->submitGroup;
    }

    function renderAction( $action ){
        $button_method = 'render' . ucfirst( $action );
        if (!method_exists( $this->_display, $button_method )) return $this->renderDefault( $action );
        return $this->_display->$button_method( $this );
    }

    function renderDefault( $action ){
        return "<input type='submit' name='". $this->submitGroup ."[" . $action ."]' value='" . ucfirst( $action ) ."'>";
    }

    function renderToolbarStart( ){
        return $this->_headerContent;
    }
    function renderToolbarEnd( ){
        return $this->_footerContent;
    }
    function _addToolbarStartContent( $content ){
        $this->_headerContent .= $content;
    }
    function _addToolbarEndContent( $content ){
        $this->_footerContent .= $content;
    }

    function addAction( $action_name ){
        $this->_actions[] = $action_name;
    }

    function setSubmitGroup(  $name ){
        $this->submitGroup = $name;
    }
}

?>
