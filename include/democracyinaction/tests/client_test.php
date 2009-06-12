<?php

require_once(dirname(__FILE__) . '/../client.php');
require_once(dirname(__FILE__) . '/test_helper.php');
require_once(dirname(__FILE__) . '/api/api_test_helper.php');

class TestClient extends UnitTestCase {
    function testSend() {
        $client = new DemocracyInAction_Client();
        list($status, $data) = 
            $client->send(authentication_url(), 
                          array('email' => test_user(), 'password' => test_pass()), 
                          'post');
        $this->assertWantedPattern('/Successful Login/', $data);

        $url = 'https://'.test_node().'/api/getObjects.sjs';
        list($status, $data) = 
            $client->send($url, 
                          array('object' => 'supporter', 'limit' => 1));
        $this->assertWantedPattern('/organization_KEY="1"/', $data);
    }
}
