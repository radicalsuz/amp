<?php

class AMP_Renderer_CSV {

    var $delimiter = ',';
	var $file_extension = 'csv';
	var $_delimiter_sets = array( 
				'csv'=> array( 'delimiter' => ',', 'extension' => 'csv' ),
				'tab'=> array( 'delimiter' => "\t", 'extension' => 'txt' ));

    function format($dataset=null, $quot='"') { 
        if (!(isset($dataset) && $dataset && is_array($dataset))) {
			trigger_error( sprintf(AMP_TEXT_ERROR_NO_SELECTION, AMP_pluralize( AMP_TEXT_ITEM_NAME )));
			return false;
		} 

        $str='';
        $escape_function = "escapeforcsv";
        foreach ($dataset as $row) {
            array_walk( $row, array($this, $escape_function, $this->delimiter) );
            $str .= implode($this->delimiter, $row) . "\n";
        }
        return $str;
    } 

	function header( $filename ) {
        if (AMP_DISPLAYMODE_DEBUG) return;  
		if (!strpos($filename, $this->file_extension)) {
			$filename .= '.'. $this->file_extension;
		}
		header("Content-type: application/".$this->file_extension);
		header("Content-Disposition: attachment; filename=".$filename);
	}

    function escapeforcsv( &$value, $key, $field_delimiter=',', $quot='"')  {
        $has_fd =(strchr($value, $field_delimiter)!==FALSE); 
        $has_quot = (strchr($value, $quot)!==FALSE);
        $has_leading_space = (substr($value, 0,1)==" ");
        $has_cr = ((strchr($value, "\n")!==FALSE)||(strchr($value,"\r")!==FALSE));
        if ($has_quot) $value = str_replace($quot, $quot.$quot, $value);
        if  (($has_fd || $has_quot) || ($has_cr || $has_leading_space) ) { 
            $value = $quot . $value . $quot;
        }
    }

	function use_tab() {
		$this->delimiter = $this->_delimiter_sets['tab']['delimiter'];
		$this->file_extension = $this->_delimiter_sets['tab']['extension'];
	}

	function use_csv() {
		$this->delimiter = $this->_delimiter_sets['csv']['delimiter'];
		$this->file_extension = $this->_delimiter_sets['csv']['extension'];
	}
}

?>
