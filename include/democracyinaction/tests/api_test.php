<?php

require_once(dirname(__FILE__) . '/../api.php');
require_once(dirname(__FILE__) . '/test_helper.php');

class TestAPI extends UnitTestCase {
    function testConstructorSetsAttributes() {
        $api = new DemocracyInAction_Client('sandbox.democracyinaction.org', 'email', 'password');
        $this->assertEqual($api->node, 'sandbox.democracyinaction.org');
        $this->assertEqual($api->email, 'email');
        $this->assertEqual($api->pass, 'password');
    }
    function testAuthenticateUrl() {
        $api = new DemocracyInAction_Client('sandbox.democracyinaction.org', 'email', 'password');
        $this->assertEqual(
            $api->authentication_url(), 
            'https://sandbox.democracyinaction.org/api/authenticate.sjs');
    }
}

?>
