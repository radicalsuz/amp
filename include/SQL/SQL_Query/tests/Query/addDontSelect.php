<?php
//
//  $Id$
//

class tests_Query_addDontSelect extends tests_UnitTest
{

    function test_onePara()
	{
	    $query =& new SQL_Query('table');
		$query->addDontSelect('myPara');
		
		$this->assertEquals(array('myPara'),$query->getDontSelect());
	}

    function test_manyParas()
	{
	    $query =& new SQL_Query('table');
		$query->addDontSelect('myPara','myPara1','myPara2','myPara3');
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3'),$query->getDontSelect());
	}

    function test_arrayPara()
	{
	    $query =& new SQL_Query('table');
		$query->addDontSelect(array('myPara','myPara1','myPara2','myPara3'));
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3'),$query->getDontSelect());
	}

    function test_mixedPara()
	{
	    $query =& new SQL_Query('table');
		$query->addDontSelect('myPara','myPara1');
		$query->addDontSelect(array('myPara2','myPara3'));
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3'),$query->getDontSelect());
	}

    function test_mixedAlotPara()
	{
	    $query =& new SQL_Query('table');
		$query->addDontSelect('myPara','myPara1');
		$query->addDontSelect(array('myPara2','myPara3'));
		$query->addDontSelect(array('myPara4'));
		$query->addDontSelect('myPara5');
		
		$this->assertEquals(array('myPara','myPara1','myPara2','myPara3','myPara4','myPara5'),$query->getDontSelect());
	}

}

?>
