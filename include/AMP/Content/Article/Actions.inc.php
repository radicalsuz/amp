<?php

require_once ( 'AMP/Content/Map/Select.inc.php' );

class ArticleActions {

    var $submitGroup = "submitAction";

    var $actions = array(
        'publish' => 'id', 
        'unpublish' => 'id', 
        'delete' => 'id', 
        'reorder' => 'pageorder', 
        'move' => array( 'id', 'move_destination' )
        );
  
    var $source;

    function ArticleActions( &$source ) {
        $this->source = &$source;
    }

    function output() {
        $output = "";
        foreach( $this->actions as $action => $args ) {
            $button_method = '_HTML_button_' . ucfirst( $action );
            if (!method_exists( $this, $button_method )) $button_method = '_HTML_button_default';
            $output .= $this->$button_method( $action );
        }
        return $output;
    }

    function setSubmitGroup( $group_name ) {
        $this->submitGroup = $group_name;
    }

    function isAction( $action ) {
        return array_key_exists( $action, $this->actions ) ;
    }

    function getRequestedValues( $action, &$value_set ) {
        if ( is_string($this->actions[ $action ]) ) return $value_set[ $this->actions[ $action ] ];
        return array_combine_key( $this->actions[ $action ], $value_set );
    }

    function _HTML_button_default( $action ) {
        return "<input type='submit' name='". $this->submitGroup ."[" . $action ."]' value='" . ucfirst( $action ) ."'>";
    }

    function _HTML_button_Reorder( $action ) {
        return "<input type='submit' name='". $this->submitGroup ."[" . $action ."]' value='Change Order'>";
    }

    function _HTML_button_Move( $action ) {
        return "<input type='button' name='showMove' onclick='window.change_any(\"article_moveMenu\");' value='Move'>"
                . '<div class="AMPComponent_hidden" id="article_moveMenu">' . $this->_HTML_selectDestination() . '</div>';
    }

    function _HTML_selectDestination() {
        $section_select =   '<select class="searchform_element" name="move_destination">' .
                            ContentMap_Select::getIndentedOptions_withNull() . '</select>';
        return "<span class='searchform_label'>Move To: &nbsp;</span>". $section_select .
                "&nbsp;<input type='submit' class='searchform_element' name='". $this->submitGroup ."[move]' value='Move'>&nbsp;" .
                "<input type='button' class='searchform_element' name='hideMove' value='Cancel' onclick='window.change_any(\"article_moveMenu\");'>";
    }

    function commitReorder( $order_array ) {
        if (empty( $order_array ) || !is_array( $order_array )) return false;
        $ordered_pages = 0;
        foreach ($order_array as $id => $order_value ) {
            $criteria = "id = ". $id;
            $update = array( "pageorder=NULL" );
            if ($order_value) $update = array("pageorder=".$order_value);
            $ordered_pages += ($this->source->updateData( $update, $criteria ));
        }
        return $ordered_pages;
    }

    function commitMove( $value_array ) {
        if ((!isset($value_array['id'])) || empty( $value_array['id'])) return false;
        if (!(isset($value_array['move_destination']) && $value_array['move_destination'])) return false;
        $criteria = "id in( " . join( ",", $value_array['id']) . ")";
        $update = array("type=" . $value_array['move_destination']);
        return $this->source->updateData( $update, $criteria );

    }


    function commitPublish( $id_array ) {
        if (empty( $id_array ) || !is_array( $id_array )) return false;
        $criteria = "id in( " . join( ",", $id_array ) . ")";
        $update = array("publish=1");
        return $this->source->updateData( $update, $criteria );

    }
    function commitUnpublish( $id_array ) {
        if (empty( $id_array ) || !is_array( $id_array )) return false;
        $criteria = "id in( " . join( ",", $id_array ) . ")";
        $update = array("publish=0");
        return $this->source->updateData( $update, $criteria );

    }

    function commitDelete( $id_array ) {
        if (empty( $id_array ) || !is_array( $id_array )) return false;
        $criteria = "id in( " . join( ",", $id_array ) . ")";
        return $this->source->deleteData( $criteria );

    }




}

?>
