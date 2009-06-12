<?php

require_once(dirname(__FILE__) . '/../test_helper.php');
require_once(dirname(__FILE__) . '/api_test_helper.php');

class TestAuth extends UnitTestCase {
    function testInvalidCredentials() {
        list($status, $data) = 
            post(authentication_url(), array('email' => 'nobody', 'password' => 'nopassword'));
        #has an interesting status code
        $this->assertEqual($status, 200); #prefer #4xx, maybe 401?
        #has interesting error message
        $this->assertWantedPattern('/Invalid login/', $data);
        #looks like we expect
        $expected = file_get_contents(dirname(__FILE__).'/../fixtures/auth/invalid.xml');
        $this->assertEqual($data, $expected);
    }
    function testValidCredentials() {
        list($status, $data) = 
            post(authentication_url(), array('email' => test_user(), 'password' => test_pass()));
        #has an interesting status code
        $this->assertEqual($status, 200);
        #has interesting success message
        $this->assertWantedPattern('/Successful Login/', $data);
        #looks like we expect
        $expected = file_get_contents(dirname(__FILE__).'/../fixtures/auth/valid.xml');
        $this->assertEqual($data, $expected);
    }
}
?>
