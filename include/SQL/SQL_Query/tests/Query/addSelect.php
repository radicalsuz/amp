<?php
//
//  $Id$
//

class tests_Query_addSelect extends tests_UnitTest
{

    function test_onePara()
	{
	    $query =& new SQL_Query('table');
		$query->addSelect('myPara');
		
		$this->assertEquals(array('myPara'),$query->getSelect());
	}

    function test_manyParas()
	{
	    $query =& new SQL_Query('table');
		$query->addSelect('myPara','myPara1','myPara2','myPara3');
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3'),$query->getSelect());
	}

    function test_arrayPara()
	{
	    $query =& new SQL_Query('table');
		$query->addSelect(array('myPara','myPara1','myPara2','myPara3'));
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3'),$query->getSelect());
	}

    function test_mixedPara()
	{
	    $query =& new SQL_Query('table');
		$query->addSelect('myPara','myPara1');
		$query->addSelect(array('myPara2','myPara3'));
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3'),$query->getSelect());
	}

    function test_mixedAlotPara()
	{
	    $query =& new SQL_Query('table');
		$query->addSelect('myPara','myPara1');
		$query->addSelect(array('myPara2','myPara3'));
		$query->addSelect(array('myPara4'));
		$query->addSelect('myPara5');
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3','myPara4','myPara5'),$query->getSelect());
	}

}

?>
