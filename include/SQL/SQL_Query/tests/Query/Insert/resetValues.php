<?php
//
//  $Id$
//

require_once 'SQL/Query/Insert.php';

class tests_Query_Insert_resetValues extends tests_UnitTest
{

    function test_default()
    {
        $query =& new SQL_Query_Insert('table');
        $query->addValues(array('name'=>'cain'));
        $query->resetValues();

        $this->assertEquals(array(),$query->getValues());
    }


}

?>
