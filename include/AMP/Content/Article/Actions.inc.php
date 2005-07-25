<?php

class ArticleActions {

    function ArticleActions() {
    }

    function output() {
        return 
            $this->_HTML_button_Publish() .
            $this->_HTML_button_Unpublish() .
            $this->_HTML_button_Delete() .
            $this->_HTML_button_ChangeOrder() .
            $this->_HTML_moveArticles() ;
    }

    function _HTML_button_Publish() {
        return "<input type='submit' name='submitListAction[publish]' value='Publish'>";
    }

    function _HTML_button_Unpublish() {
        return "<input type='submit' name='submitListAction[publish]' value='Unpublish'>";
    }

    function _HTML_button_ChangeOrder() {
        return "<input type='submit' name='submitListAction[order]' value='Change Order'>";
    }

    function _HTML_button_Delete() {
        return "<input type='submit' name='submitListAction[delete]' value='Delete'>";
    }

    function _HTML_moveArticles() {
        return "<input type='button' name='showMove' onclick='window.change_any(\"article_moveMenu\");' value='Move'>"
                . '<div class="AMPComponent_hidden" id="article_moveMenu">' . $this->_HTML_selectDestination() . '</div>';
    }

    function _HTML_selectDestination() {
        $map = &AMPContent_Map::instance();
        $options = $map->selectOptions();
        array_unshift( $options, 'Select Section To Move To'  );
        return "Move To:". AMP_buildSelect( "move_destination", $options ) .
                "<input type='submit' name='submitListAction[move]' value='Move'> &nbsp;" .
                "<input type='button' name='hideMove' value='Cancel' onclick='window.change_any(\"article_moveMenu\");'>";
    }



}

?>
