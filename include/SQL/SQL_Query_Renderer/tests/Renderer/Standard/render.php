<?php
//
//  $Id$
//

require_once 'SQL/Query/Join.php';
require_once 'SQL/Query/Insert.php';

/**
*
*
*/
class tests_Renderer_Standard_render extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Standard';

    // test the select with all possible parts of the query, like group, order, ....
    function test_selectWithAllParts()
    {
        $query =& new SQL_Query('city');
        $query->addSelect('*','name','id');
        $query->addDontSelect('name');
        $query->addWhere('id','<>',42);
        $query->addOrder(array('country_id',true));
        $query->addGroup('surname');
        
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals(  'SELECT *,id FROM city '.
		                            'WHERE id <> 42 GROUP BY surname ORDER BY country_id DESC',
									$ren->render());
    }

    function test_selectAllFromOneTable()
    {
        $query =& new SQL_Query('city');
        $query->addSelect('*');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('SELECT * FROM city',$ren->render());
    }

    function test_selectOneFromOneTable()
    {
        $query =& new SQL_Query('city');
        $query->addSelect('name');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('SELECT name FROM city',$ren->render());
    }

    function test_selectAllFromOneTableWithAlias()
    {
        $query =& new SQL_Query(array('c'=>'city'));
        $query->addSelect('*');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('SELECT * FROM city c',$ren->render());
    }

    function test_selectAllFromOneTableWithWhere()
    {
        $query =& new SQL_Query(array('c'=>'city'));
        $query->addSelect('*');
        $query->addWhere('id','<',7);
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('SELECT * FROM city c WHERE id < 7',$ren->render());
    }

    function test_selectSomeColsFromOneTable()
    {
        $query =& new SQL_Query(array('c'=>'city'));
        $query->addSelect('id');
        $query->addSelect('name');
        $query->addSelect('phonePrefix AS phonePref');
        $query->addSelect('SOME_FUNCTION(name,3,4) AS resultOfSomeFunc');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals(  'SELECT id , name , phonePrefix AS phonePref , '.
                                    'SOME_FUNCTION(name,3,4) AS resultOfSomeFunc FROM city c',
                                    $ren->render());
    }

    // check if the insert query is properly built
    // we also use some method calls which should not have an effect on 
    // the insert query that will be built, like addSelect() i.e. since there
    // is no select-part for an insert query
    function test_insertSimple()
    {
        $query =& new SQL_Query_Insert('city', 'insert');
        $query->addSelect('no matter what');
        $query->addValues(  array('country_id'=>1,'name'=>'"cain"','phonePrefix'=>'"0049"'),
                            array('country_id'=>2,'name'=>'"foo"','phonePrefix'=>'"0034"'));
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals(  'INSERT INTO city (country_id,name,phonePrefix) '.
                                    'VALUES (1,"cain","0049") , (2,"foo","0034")',
                                    $ren->render());
    }

	//
	// render a DELETE-queries
	// by definition a searched-delete query might have a where clause, first we test
	// it without WHERE, then with WHERE. The positioned-delete queries are to be done
	//
	function test_deleteSearchedWithoutWhere()
	{
	    $query =& new SQL_Query( 'city', 'delete');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('DELETE FROM city',$ren->render());
	}
	
	function test_deleteSearchedWithWhere()
	{
	    $query =& new SQL_Query( 'city', 'delete');
		$query->addWhere('id','=',1);
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('DELETE FROM city WHERE id = 1',$ren->render());
	}
	    
}

?>
