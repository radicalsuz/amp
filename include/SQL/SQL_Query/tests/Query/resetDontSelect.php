<?php
//
//  $Id$
//

class tests_Query_resetDontSelect extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addDontSelect('myPara');
		$query->resetDontSelect();
		
		$this->assertEquals(array(),$query->getDontSelect());
	}

}

?>
