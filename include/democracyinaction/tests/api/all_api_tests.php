<?php

require_once(dirname(__FILE__) . '/../test_helper.php');

class AllAPITests extends TestSuite {
    function AllAPITests() {
        $this->TestSuite('All tests');
        $this->addFile(dirname(__FILE__).'/auth_test.php');
        $this->addFile(dirname(__FILE__).'/get_test.php');
    }
}
?>
