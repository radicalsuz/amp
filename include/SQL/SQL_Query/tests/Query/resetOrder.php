<?php
//
//  $Id$
//

class tests_Query_resetOrder extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addOrder(array('col',true));
		$query->resetOrder();
		
		$this->assertEquals(array(),$query->getOrder());
	}


}

?>
