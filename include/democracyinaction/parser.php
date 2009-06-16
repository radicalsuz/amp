<?php

class DemocracyInAction_Parser {

    function parse($xml, $resultset) {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

        xml_set_object($parser, $resultset);
        xml_set_character_data_handler($parser, 'cdata_handler');
        xml_set_element_handler($parser, 'start_handler', 'end_handler');
        xml_parse($parser, $xml);

        xml_parser_free($parser);
        return $resultset->results;
    }

    function parse_object($xml) {
        $result = DemocracyInAction_Parser::parse_objects($xml);
        return $result[0];
    }

    function parse_objects($xml) {
        return DemocracyInAction_Parser::parse($xml, new DemocracyInAction_ObjectResultSet);
    }

    function parse_report($xml) {
        return DemocracyInAction_Parser::parse($xml, new DemocracyInAction_ReportResultSet);
    }
}

class DemocracyInAction_ObjectResultSet {
    function cdata_handler($parser, $data) {
        if($this->parsing_item) {
            $this->results[count($this->results)-1][$this->tag] .= trim($data);
        }
    }

    function start_handler($parser, $name) {
        if('item' == $name) {
            $this->parsing_item = true;
            $this->results[] = array();
        }
        $this->tag = $name;
    }

    function end_handler($parser, $name) {
        if('item' == $name) {
            $this->parsing_item = false;
            $index = count($this->results)-1;
            $key = $this->results[$index]['key'];
        }
    }
}

class DemocracyInAction_ReportResultSet {
    function cdata_handler($parser, $data) {
        if($this->parsing_row) {
            $this->results[count($this->results)-1][$this->tag] .= trim($data);
        }
    }

    function start_handler($parser, $name) {
        if('row' == $name) {
            $this->parsing_row = true;
            $this->results[] = array();
        }
        $this->tag = $name;
    }

    function end_handler($parser, $name) {
        if('row' == $name) {
            $this->parsing_row = false;
            $index = count($this->results)-1;
            $key = $this->results[$index]['key'];
        }
    }
}

?>
