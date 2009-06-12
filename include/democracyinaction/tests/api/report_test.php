<?php

require_once(dirname(__FILE__) . '/../test_helper.php');
require_once(dirname(__FILE__) . '/api_test_helper.php');

class TestReport extends ApiTestCase {
    function testReport() {
        list($status, $data) = 
            authenticated_get('/api/getObjects.sjs?object=report&limit=1');
        var_dump($data);
        $this->assertEqual($status, 200);
        $this->assertWantedPattern('/<data organization_KEY="1">/', $data);
    }
}

?>
