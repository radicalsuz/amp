<?php
//
//  $Id$
//

class tests_Query_condition extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		
		$this->assertEquals(new SQL_Condition('1','<>','2'),$query->condition('1','<>','2'));
	}

}

?>
