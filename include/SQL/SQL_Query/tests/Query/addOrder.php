<?php
//
//  $Id$
//

class tests_Query_addOrder extends tests_UnitTest
{

    function test_simple()
	{
	    $query =& new SQL_Query('table');
		$query->addOrder('col');
	    // the second field is always asc/desc, true for descending	
		$this->assertEquals(array(array('col',false)),$query->getOrder());
	}
	
	function test_ascending()
	{	    
	    $query =& new SQL_Query('table');
		$query->addOrder(array('column',true));  // sort by 'column' descending
		
		$this->assertEquals(array(array('column',true)),$query->getOrder());
	}
		
	function test_twoOrders()
	{	    
	    $query =& new SQL_Query('table');
		// sort by all given cols, but col2 descending
		// this should result in this: ORDER BY col1, col2 DESC
		$query->addOrder('col1');  
		$query->addOrder(array('col2',true));
		
		$this->assertEquals(array(array('col1',false),array('col2',true)),$query->getOrder());
	}
		
	function test_addCollate()
	{	    
	    $query =& new SQL_Query('table');
		// add a collate clause
		// this should become: ORDER BY col COLLATE collate_name DESC, col2
		$query->addOrder(array('col',true,'col1'),'col2');  
	
		$this->assertEquals(array(array('col',true,'col1'),array('col2',false)),$query->getOrder());
	}
		
	function test_collateAndDesc()
	{	    
	    $query =& new SQL_Query('table');
		// add a collate clause
		// this should become: ORDER BY col COLLATE collate_name ASC, col2
		$query->addOrder(array('col',false,'col1'));  
		$query->addOrder('col2');

		$this->assertEquals(array(array('col',false,'col1'),array('col2',false)),$query->getOrder());
	}
		
}

?>
