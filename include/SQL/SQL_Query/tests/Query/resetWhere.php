<?php
//
//  $Id$
//

class tests_Query_resetWhere extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->resetWhere();
		
		$this->assertEquals(null,$query->getWhere());
	}


}

?>
