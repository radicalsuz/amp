<?php
//
//  $Id$
//

class tests_Query_addWhere extends tests_UnitTest
{

    function test_firstCondition()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		
		$this->assertEquals(new SQL_Condition('1','<>','2'),$query->getWhere());
	}

    function test_twoConditions()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->addWhere('3','<>','4');
		
		$cond =& new SQL_Condition('1','<>','2');
		$cond->add('3','<>','4');
		
		$this->assertEquals($cond,$query->getWhere());
	}

    function test_nestedConditions()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>',new SQL_Condition('3','<>','4'));
		
		$cond =& new SQL_Condition('1','<>',new SQL_Condition('3','<>','4'));
		
		$this->assertEquals($cond,$query->getWhere());
	}

    function test_oneParam()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere(new SQL_Condition(1,'=',1));
		
		$this->assertEquals(new SQL_Condition(new SQL_Condition(1,'=',1)),$query->getWhere());
	}

    function test_twoParams()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere(2,'=',2);
		// test this ...
		$query->addWhere(new SQL_Condition(1,'=',1),'OR');
		
		$cond =& new SQL_Condition(2,'=',2);
		$cond->add(new SQL_Condition(1,'=',1),'OR');
		$this->assertEquals($cond,$query->getWhere());
	}

}

?>
