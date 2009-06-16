<?php

require_once(dirname(__FILE__) . '/../parser.php');
require_once(dirname(__FILE__) . '/test_helper.php');

class TestParser extends UnitTestCase {
    function testParseObject() {
        $xml = 
            file_get_contents(dirname(__FILE__).'/fixtures/get/getObject.sjs.xml');
        $result = DemocracyInAction_Parser::parse_object($xml);
        $this->assertEqual('Web', $result['Source']);
    }
}
