<?php
//
//  $Id$
//

class tests_Query_resetGroup extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addGroup('id');
		$query->resetGroup();
		
		$this->assertEquals(null,$query->getGroup());
	}


}

?>
