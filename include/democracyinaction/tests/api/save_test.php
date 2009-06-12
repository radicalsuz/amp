<?php

require_once(dirname(__FILE__) . '/../test_helper.php');
require_once(dirname(__FILE__) . '/api_test_helper.php');

class TestSave extends ApiTestCase {
    function testSaveResponse() {
        list($status, $by_query_data) = 
            authenticated_get('/api/getObjects.sjs?object=supporter&limit=1');
        $xml = new SimpleXMLElement($by_query_data);
        $supporters = $xml->xpath('/data/supporter/item');
        $supporter = $supporters[0];
        $key = $supporter->supporter_KEY;

        list($status, $data) = 
            post('https://'.test_node().'/save?xml=true&object=supporter&key='.$key, array());

        $this->assertWantedPattern('/You do not have permission to modify this object/', $data);

        list($status, $data) = 
            authenticated_post(
                'https://'.test_node().'/save?xml=true&object=supporter&key='.$key, 
                array());

        $this->assertWantedPattern('/Modified entry/', $data);
    }
}
