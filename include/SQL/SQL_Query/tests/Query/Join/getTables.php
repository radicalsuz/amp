<?php
//
//  $Id$
//

class tests_Query_Join_getTables extends tests_UnitTest
{
    function test_oneTableAsString()
	{
	    $join =& new SQL_Query_Join();
		$join->addJoin('table1',null);
		
		$this->assertEquals(array(array('table1')),$join->getTables());
	}

    function test_twoTablesAsArray()
	{
	    $join =& new SQL_Query_Join();
		$join->addJoin(array('table1','table2'),null);
		
		$this->assertEquals(array(array('table1'),array('table2')),$join->getTables());
	}

    function test_twoTablesWithOneAlias()
	{
	    $join =& new SQL_Query_Join();
		$join->addJoin(array('t'=>'table1','table2'),null);
		
		$this->assertEquals(array(array('table1','t'),array('table2')),$join->getTables());
	}

    function test_twoTablesWithTwoAlias()
	{
	    $join =& new SQL_Query_Join();
		$join->addJoin(array('t'=>'table1','t2'=>'table2'),null);
		
		$this->assertEquals(array(array('table1','t'),array('table2','t2')),$join->getTables());
	}

    function test_nestedJoins()
	{
	    $join1 =& new SQL_Query_Join();
		$join1->addJoin(array('1table','1table2'),null);
	    
		$join =& new SQL_Query_Join();
		// the alias 't2' is not important here and getTables() doesnt return it either
		$join->addJoin(array('t'=>'table1','t2'=>$join1),null);

		$this->assertEquals(array(array('table1','t'),array('1table'),array('1table2')),$join->getTables());
	}

    function test_nestedJoinsAndAliases()
	{
	    $join1 =& new SQL_Query_Join();
		$join1->addJoin(array('1t'=>'1table','1t2'=>'1table2'),null);
	    
	    $join2 =& new SQL_Query_Join();
		$join2->addJoin(array('2t'=>'2table','2t2'=>$join1),null);
	    
		$join =& new SQL_Query_Join();
		// the alias 't2' is not important here and getTables() doesnt return it either
		$join->addJoin(array('t'=>'table1','t2'=>$join2),null);

		$expected = array(
		                array('table1','t'),
						array('2table','2t'),
						array('1table','1t'),array('1table2','1t2'));
		$this->assertEquals($expected,$join->getTables());
	}
}

?>
