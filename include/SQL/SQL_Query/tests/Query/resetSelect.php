<?php
//
//  $Id$
//

class tests_Query_resetSelect extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addSelect('myPara');
		$query->resetSelect();
		
		$this->assertEquals(array(),$query->getSelect());
	}


}

?>
