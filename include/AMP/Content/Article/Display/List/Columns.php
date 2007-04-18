<?php
require_once('AMP/Content/Article/Display/List.php');

class AMP_Content_Article_Display_List_TwoColumn extends AMP_Content_Article_Display_List {

	var $_display_columns = 2;

	function AMP_Content_Article_Display_List_TwoColumn( $source= false, $criteria = array()) {
		$this->__construct($source, $criteria);
	}

}

class AMP_Content_Article_Display_List_ThreeColumn extends AMP_Content_Article_Display_List {

	var $_display_columns = 3;

	function AMP_Content_Article_Display_List_ThreeColumn( $source= false, $criteria = array()) {
		$this->__construct($source, $criteria);
	}
}

?>
