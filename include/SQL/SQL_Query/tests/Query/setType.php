<?php
//
//  $Id$
//

require_once 'SQL/Query.php';

class tests_Query_setType extends tests_UnitTest
{
    // check that the type can be set via the constructor
    function test_viaConstructor()
    {
        $query =& new SQL_Query( 'table', 'insert');
        $this->assertEquals('insert',$query->getType());
    }

    // set the type via the setType method
    function test_setType()
    {
        $query =& new SQL_Query('');
        $query->setType('delete');

        $this->assertEquals('delete',$query->getType());
    }

}

?>
