<?php
//
//  $Id$
//

class tests_Query_hashKey extends tests_UnitTest
{

    function test_default()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->addSelect('id','name');
		
		$query1 = $query;
		
		$this->assertEquals($query1->hashKey(),$query->hashKey());
	}

    function test_differentQueries()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->addSelect('id','name');
		
		$query1 = $query;
		$query1->addGroup('id');
		
		$this->assertFalse($query1->hashKey()==$query->hashKey());
	}

	// those queries are slightly different but the hashkey shall be different
    function test_slightlyDifferentQueries()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->addSelect('id','name');
		
		$query1 = $query;
		$query->resetWhere();
		$query->addWhere('2','<>','1');
		
		$this->assertFalse($query1->hashKey()==$query->hashKey());
	}

	// those queries are slightly different but the hashkey shall be different
	// even the different select-orders, since this might make a difference
    function test_slightlyDifferentQueries1()
	{
	    $query =& new SQL_Query('table');
		$query->addWhere('1','<>','2');
		$query->addSelect('id','name');
		
		$query1 = $query;
		$query->resetSelect();
		$query->addSelect('name','id');
		
		$this->assertFalse($query1->hashKey()==$query->hashKey());
	}


}

?>
