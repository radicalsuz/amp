<?php
//
//  $Id$
//

class tests_Query_reset extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->addSelect('id','name');
		$query->reset();
		
		$this->assertEquals(new SQL_Query('table'),$query);
	}


}

?>
