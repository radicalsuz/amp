<?php
//
//  $Id$
//

require_once 'SQL/Query/Join.php';

class tests_Query_setFrom extends tests_UnitTest
{

    function test_onePara()
	{
	    $query =& new SQL_Query('');
		$query->setFrom('myTable');
		
		$this->assertEquals(array('myTable'),$query->getFrom());
	}

    function test_withAlias()
	{
	    $query =& new SQL_Query('');
		$query->setFrom(array('myT'=>'myTable'));
		
		$this->assertEquals(array('myTable','myT'),$query->getFrom());
	}

    function test_withJoin()
	{
	    $join =& new SQL_Query_Join();
	
	    $query =& new SQL_Query('');
		$query->setFrom($join);
		
		$this->assertEquals($join,$query->getFrom());
	}


}

?>
