<?php
//
//  $Id$
//

require_once 'SQL/Query/Insert.php';

class tests_Query_Insert_addValues extends tests_UnitTest
{

    function test_one()
    {
        $query =& new SQL_Query_Insert('table');
        $values = array('name'=>'cain','country_id'=>1,'phonePrefix'=>'0049');
        $query->addValues($values);
        $this->assertEquals(array($values),$query->getValues());
    }

    function test_multiple()
    {
        $query =& new SQL_Query_Insert('table');
        $query->addValues(  array('name'=>'cain','surname'=>'Doodi'),
                            array('name'=>'foo','surname'=>'bar'));
        $query->addValues(  array('name'=>'foo1','surname'=>'bar1'));
        
        $expected = array(  array('name'=>'cain','surname'=>'Doodi'),
                            array('name'=>'foo','surname'=>'bar'),
                            array('name'=>'foo1','surname'=>'bar1'));
        $this->assertEquals($expected,$query->getValues());
    }

}

?>
