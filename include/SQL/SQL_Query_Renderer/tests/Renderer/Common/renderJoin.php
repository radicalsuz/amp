<?php
//
//  $Id$
//

require_once 'SQL/Query/Join.php';

/**
* Test the renderJoin() method of the renderer.
*
*
*
*/
class tests_Renderer_Common_renderJoin extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Common';

    var $_join = 'SQL_Query_Join';

    /**
    * Test the simplest join of all, an inner join of two tables.
    *
    */
    function test_inner2tables()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 INNER JOIN table2 ON',$renderer->renderJoin());
    }

    /**
    * Test the an inner join of four tables.
    * Here is important to note that table3 and 4 are joined without a comma ','!
    */
    function test_inner4tables()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),null);
        $join->addJoin('table3',null);
        $join->addJoin('table4',null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 INNER JOIN table2 ON INNER JOIN table3 ON INNER JOIN table4 ON',
                                    $renderer->renderJoin());
    }

    function test_inner5tables()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),null);
        $join->addJoin('table3',null);
        $join->addJoin(array('table4','table5'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 INNER JOIN table2 ON INNER JOIN table3 ON '.
                                    ', table4 INNER JOIN table5 ON',
                                    $renderer->renderJoin());
    }

    function test_leftJoin()
    {
        $join =& new $this->_join();
        $join->addLeftJoin(array('table1','table2'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 LEFT JOIN table2 ON',
                                    $renderer->renderJoin());
    }

    function test_twoLeftJoins()
    {
        $join =& new $this->_join();
        $join->addLeftJoin(array('table1','table2'),null);
        $join->addLeftJoin('table3',null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 LEFT JOIN table2 ON LEFT JOIN table3 ON',
                                    $renderer->renderJoin());
    }

    function test_twoLeftJoins1()
    {
        $join =& new $this->_join();
        $join->addLeftJoin(array('table1','table2'),null);
        $join->addLeftJoin(array('table3','table4'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 LEFT JOIN table2 ON , table3 LEFT JOIN table4 ON',
                                    $renderer->renderJoin());
    }

    function test_rightJoin()
    {
        $join =& new $this->_join();
        $join->addRightJoin(array('table1','table2'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 RIGHT JOIN table2 ON',
                                    $renderer->renderJoin());
    }

    function test_twoRightJoins()
    {
        $join =& new $this->_join();
        $join->addRightJoin(array('table1','table2'),null);
        $join->addRightJoin('table3',null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 RIGHT JOIN table2 ON RIGHT JOIN table3 ON',
                                    $renderer->renderJoin());
    }

    function test_twoRightJoins1()
    {
        $join =& new $this->_join();
        $join->addRightJoin(array('table1','table2'),null);
        $join->addRightJoin(array('table3','table4'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 RIGHT JOIN table2 ON , table3 RIGHT JOIN table4 ON',
                                    $renderer->renderJoin());
    }

    function test_rightAndLeftJoin()
    {
        $join =& new $this->_join();
        $join->addRightJoin(array('table1','table2'),null);
        $join->addLeftJoin('table3',null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 RIGHT JOIN table2 ON LEFT JOIN table3 ON',
                                    $renderer->renderJoin());
    }

    function test_allJoins()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),null);
        $join->addRightJoin(array('table3','table4'),null);
        $join->addLeftJoin(array('table5','table6'),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 INNER JOIN table2 ON , table3 RIGHT JOIN table4 ON ,'.
                                    'table5 LEFT JOIN table6 ON ',
                                    $renderer->renderJoin());
    }

    function test_nestedJoin()
    {
        $join1 =& new $this->_join();
        $join1->addJoin(array('table1','table2'),null);
        $join =& new $this->_join();
        $join->addJoin(array('table3',$join1),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table3 INNER JOIN ( table1 INNER JOIN table2 ON ) ON',
                                    $renderer->renderJoin());
    }

    function test_nestedJoinWithAlias()
    {
        $join1 =& new $this->_join();
        $join1->addJoin(array('table1','table2'),null);
        $join =& new $this->_join();
        $join->addJoin(array('table3','j1'=>$join1),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table3 INNER JOIN ( table1 INNER JOIN table2 ON ) j1 ON',
                                    $renderer->renderJoin());
    }

    function test_nestedJoinAllAliased()
    {
        $join1 =& new $this->_join();
        $join1->addJoin(array('t1'=>'table1','t2'=>'table2'),null);
        $join =& new $this->_join();
        $join->addJoin(array('t3'=>'table3','j1'=>$join1),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table3 t3 INNER JOIN ( table1 t1 INNER JOIN table2 t2 ON ) j1 ON',
                                    $renderer->renderJoin());
    }

    function test_nestedJoin2levels()
    {
        $join1 =& new $this->_join();
        $join1->addJoin(array('table1','table2'),null);
        $join2 =& new $this->_join();
        $join2->addJoin(array($join1,'table3'),null);
        $join =& new $this->_join();
        $join->addJoin(array('table4',$join2),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table4 INNER JOIN '.
                                '( ( table1 INNER JOIN table2 ON ) INNER JOIN table3 ON ) ON',
                                    $renderer->renderJoin());
    }

    /**
    * Make a nested join of 2 levels with some tables/joins aliased.
    *
    */
    function test_nestedJoin2levelsSomeAliased()
    {
        $join1 =& new $this->_join();
        $join1->addJoin(array('t1'=>'table1','table2'),null);
        $join2 =& new $this->_join();
        $join2->addJoin(array('j1'=>$join1,'table3'),null);
        $join =& new $this->_join();
        $join->addJoin(array('t4'=>'table4',$join2),null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table4 t4 INNER JOIN '.
                                '( ( table1 t1 INNER JOIN table2 ON ) j1 INNER JOIN table3 ON ) ON',
                                    $renderer->renderJoin());
    }

    /**
    * This test all kind of joins with one and two tables ... this is really long :-)
    *
    */
    function test_allJoinsAllKinds()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),null);
        $join->addRightJoin(array('table3','table4'),null);
        $join->addLeftJoin(array('table5','table6'),null);
        $join->addJoin('table7',null);
        $join->addRightJoin('table8',null);
        $join->addLeftJoin('table9',null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 INNER JOIN table2 ON , table3 RIGHT JOIN table4 ON ,'.
                                    'table5 LEFT JOIN table6 ON INNER JOIN table7 ON '.
                                    'RIGHT JOIN table8 ON LEFT JOIN table9 ON ',
                                    $renderer->renderJoin());
    }

    /**
    * This test all kind of joins with one and two tables ... 
    * and two nested joins which also have aliases, so this is kinda very strange
    * a mixture of lot of the things tested above
    *
    */
    function test_allJoinsAllKindsAndNested()
    {
        $join1 =& new $this->_join();
        $join1->addLeftJoin(array('jt1'=>'jtable1','jtable2'),null);

        $join2 =& new $this->_join();
        $join2->addRightJoin(array('j1'=>$join1,'jtable3'),null);

        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),null);
        $join->addRightJoin(array('table3','table4'),null);
        $join->addLeftJoin(array('table5',$join2),null);
        $join->addJoin('table7',null);
        $join->addRightJoin('table8',null);
        $join->addLeftJoin('table9',null);

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertStringEquals('table1 INNER JOIN table2 ON , table3 RIGHT JOIN table4 ON ,'.
                                    'table5 LEFT JOIN '.
                                    '( ( jtable1 jt1 LEFT JOIN jtable2 ON ) j1 RIGHT JOIN jtable3 ON )'.
                                    ' ON INNER JOIN table7 ON '.
                                    'RIGHT JOIN table8 ON LEFT JOIN table9 ON ',
                                    $renderer->renderJoin());
    }

    //
    // check if the renderer puts the spaces properly after the ON-clause
    //

    function test_properSpacingTwoJoins()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),new SQL_Condition('table1.x','=','table2.y'));
        $join->addJoin(array('table3'),new SQL_Condition('table3.z','=','table1.x'));

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertRegExp('~^table1 INNER JOIN table2 ON table1.x = table2.y '.
                            'INNER JOIN table3 ON table3.z = table1.x$~i',
                            $renderer->renderJoin());
    }

    function test_properSpacingThreeJoins()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),new SQL_Condition('table1.x','=','table2.y'));
        $join->addJoin(array('table3'),new SQL_Condition('table3.z','=','table1.x'));
        $join->addLeftJoin(array('table4'),new SQL_Condition('table4.z','=','table2.x'));

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertRegExp('~^table1 INNER JOIN table2 ON table1.x = table2.y '.
                            'INNER JOIN table3 ON table3.z = table1.x '.
                            'LEFT JOIN table4 ON table4.z = table2.x$~i',
                            $renderer->renderJoin());
    }

    function test_properSpacingTwoJoinsCommaSeperated()
    {
        $join =& new $this->_join();
        $join->addJoin(array('table1','table2'),new SQL_Condition('table1.x','=','table2.y'));
        $join->addJoin(array('table3','table4'),new SQL_Condition('table3.z','=','table4.x'));

        $renderer =& new $this->_renderer(new SQL_Query($join));

        $this->assertRegExp('~^table1 INNER JOIN table2 ON table1.x = table2.y,'.
                            'table3 INNER JOIN table4 ON table3.z = table4.x$~i',
                            $renderer->renderJoin());
    }

}

?>
