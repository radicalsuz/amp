<?php
//
//  $Id$
//

require_once 'SQL/Query/Join.php';

/**
* Test the renderFrom() method of the renderer.
* Actually we dont need to test the join rendering here, since renderJoin() does that
*
*
*/
class tests_Renderer_Common_renderFrom extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Common';

    // test empty from part, which simply returns an empty string, but
    // we also have to be sure that it doesnt fail :-)
    function test_empty()
    {
        $query =& new SQL_Query();
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('',$ren->renderFrom());
    }

    // test rendering the FROM-part with only one table name given
    function test_tableNameOnly()
    {
        $query =& new SQL_Query('table');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('table',$ren->renderFrom());
    }

    // test FROM with one table with a given alias 
    function test_withAlias()
    {
        $query =& new SQL_Query(array('t'=>'table'));
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('table t',$ren->renderFrom());
    }

    // test the optional parameter.
    // so here we only need to test if the parameter is being used if given
    function test_optionalParameter()
    {
        $ren =& new $this->_renderer(new SQL_Query());
        $query =& new SQL_Query( 'table1');
        $this->assertStringEquals( 'table1', $ren->renderFrom( $query->getFrom()));
    }

    // here we only have to test, that a join is detected as such and
    // rendered as one, there is a seperate test for renderJoin, which 
    // handles all the various variations of joins to see if they all get rendered properly
    // so we only check that its seen as a join :-) much said for little 
    function test_join()
    {
        $query =& new SQL_Query();
        $join =& new SQL_Query_Join();
        $join->addJoin(array('t'=>'table','table1'),new SQL_Condition('table.x','=','table1.x'));
        $query->setFrom($join);
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('table t INNER JOIN table1 ON table.x = table1.x',$ren->renderFrom());
    }

}

?>
