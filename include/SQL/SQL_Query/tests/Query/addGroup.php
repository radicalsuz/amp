<?php
//
//  $Id$
//

class tests_Query_addGroup extends tests_UnitTest
{

    function test_one()
    {
        $query =& new SQL_Query('table');
        $query->addGroup('id');

        $this->assertEquals(array('id'),$query->getGroup());
    }

    function test_two()
    {
        $query =& new SQL_Query('table');
        $query->addGroup('id','name');

        $this->assertEquals(array('id','name'),$query->getGroup());
    }

    function test_twoSeperatly()
    {
        $query =& new SQL_Query('table');
        $query->addGroup('id');
        $query->addGroup('name');

        $this->assertEquals(array('id','name'),$query->getGroup());
    }

    function test_twoAndReset()
    {
        $query =& new SQL_Query('table');
        $query->addGroup('id');
        $query->resetGroup();
        $query->addGroup('name');
        $query->addGroup('id');

        $this->assertEquals(array('name','id'),$query->getGroup());
    }

    function test_twoAndExpression()
    {
        $query =& new SQL_Query('table');
        $query->addGroup('name');
        $query->addGroup('STRLEN(name)');

        $this->assertEquals(array('name','STRLEN(name)'),$query->getGroup());
    }

}

?>
