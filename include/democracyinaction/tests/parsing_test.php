<?php

require_once(dirname(__FILE__) . '/test_helper.php');

class TestParsing extends UnitTestCase {
    /*
    #php5 only
    function testSimpleXMLParseObject() {
        $xml = 
            simplexml_load_file(dirname(__FILE__).'/fixtures/get/getObject.sjs.xml');
        $this->assertEqual('Web', $xml->supporter->item->Source);
    }

    function testSimpleXMLParseObjects() {
        $xml = 
            simplexml_load_file(dirname(__FILE__).'/fixtures/get/getObjects.sjs.xml');
        foreach($xml->supporter->item as $item) {
            $this->assertEqual('Web', $xml->supporter->item->Source);
        }
    }
    */

    function cdata_handler($parser, $data) {
        if($this->parsing_item) {
            $this->items[count($this->items)-1][$this->tag] .= trim($data);
        }
    }
    function start_handler($parser, $name) {
        if('item' == $name) {
            $this->parsing_item = true;
            $this->items[] = array();
        }
        $this->tag = $name;
    }
    function end_handler($parser, $name) {
        if('item' == $name) {
            $this->parsing_item = false;
            $index = count($this->items)-1;
            $key = $this->items[$index]['key'];
        }
    }

    function testXMLParserForObjects() {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
#        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

        $xml = 
            file_get_contents(dirname(__FILE__).'/fixtures/get/getObjects.sjs.xml');

        xml_set_object($parser, $this);
        xml_set_character_data_handler($parser, 'cdata_handler');
        xml_set_element_handler($parser, 'start_handler', 'end_handler');
        xml_parse($parser, $xml);

        /*
        #php5 only
        foreach($this->items as &$item) {
            $this->items[$item['key']] = &$item;
        }
        */

        xml_parser_free($parser);
    }

    function testXMLParserForObject() {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

        $xml = 
            file_get_contents(dirname(__FILE__).'/fixtures/get/getObject.sjs.xml');

        xml_parse_into_struct($parser, $xml, $values, $tags);

        $data = array();
        foreach($tags['item'] as $tag) {
            $value = $values[$tag+1];
            if($value['type'] == 'complete') {
                $data[$value['tag']] = $value['value'];
            }
        }

        xml_parser_free($parser);
    }

    function testXmlParseIntoStructWithMultipleObjects() {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
#        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

        $xml = 
            file_get_contents(dirname(__FILE__).'/fixtures/get/getObjects.sjs.xml');

        xml_parse_into_struct($parser, $xml, $values, $tags);

        // loop through the structures
        foreach ($tags as $key=>$val) {
            if ($key == "item") {
                $molranges = $val;
                // each contiguous pair of array entries are the 
                // lower and upper range for each molecule definition
                for ($i=0; $i < count($molranges); $i+=2) {
                    $offset = $molranges[$i] + 1;
                    $len = $molranges[$i + 1] - $offset;
#var_dump(array_slice($values, $offset, $len));
#                    $tdb[] = parseMol(array_slice($values, $offset, $len));
                    $mvalues = array_slice($values, $offset, $len);
                    $mol = array();
                    for ($j=0; $j < count($mvalues); $j++) {
                        $mol[$mvalues[$j]["tag"]] = $mvalues[$j]["value"];
                    }
                    $this->items[] = $mol;
                }
            } else {
                continue;
            }
        }
#        var_dump($tags);
#var_dump($this->items);
    }

    function testXmlParseIntoStructLoop() {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
#        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

        $xml = 
            file_get_contents(dirname(__FILE__).'/fixtures/get/getObjects.sjs.xml');

        xml_parse_into_struct($parser, $xml, $values, $tags);

        foreach ($values as $val) {
            if($val['tag'] == 'item' && $val['type'] == 'open') {
                $this->items[] = array();
                $this->parsing_item = true;
            }
            if($val['tag'] == 'item' && $val['type'] == 'close') {
                $this->parsing_item = false;
            }
            if($this->parsing_item && $val['type'] == 'complete') {
                $this->items[count($this->items)-1][$val['tag']] = $val['value'];
            }
        }
#        var_dump($this->items);
    }

}
