<?php

require_once(dirname(__FILE__) . '/../test_helper.php');
require_once(dirname(__FILE__) . '/api_test_helper.php');

class TestGet extends ApiTestCase {
    function testAuthenticated() {
        list($status, $by_query_data) = 
            authenticated_get('/api/getObjects.sjs?object=supporter&limit=1');
        $this->assertEqual($status, 200);
        $this->assertWantedPattern('/<data organization_KEY="1">/', $by_query_data);

        $by_query_expected = 
            file_get_contents(dirname(__FILE__).'/../fixtures/get/getObjects.sjs.xml');

        $this->assertSameXMLStructure($by_query_data, $by_query_expected);

        $xml = new SimpleXMLElement($by_query_data);
        $supporters = $xml->xpath('/data/supporter/item');
        $supporter = $supporters[0];
        $key = $supporter->supporter_KEY;

        $by_key_expected = file_get_contents(dirname(__FILE__).'/../fixtures/get/getObject.sjs.xml');
        list($status, $by_key_data) =
            authenticated_get('/api/getObject.sjs?object=supporter&key='.$key);
        $this->assertSameXMLStructure($by_key_data, $by_key_expected);
    }
}

?>
