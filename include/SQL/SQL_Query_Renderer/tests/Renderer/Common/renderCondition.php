<?php
//
//  $Id$
//

require_once 'SQL/Condition.php';
require_once 'SQL/Query/Renderer/Common.php';


/**
* Test the renderFrom() method of the renderer.
* Actually we dont need to test the join rendering here, since renderJoin() does that
*
*
*/
class tests_Renderer_Common_renderCondition extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Common';

    function test_empty()
    {
        $ren =& new $this->_renderer(new SQL_Query(''));

        $cond = null;
        $this->assertEquals(null,$ren->renderCondition());
    }

    function test_one()
    {
        $query =& new SQL_Query('city');
        $query->addWhere('1','<>','2');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals('1 <> 2',$ren->renderCondition());
    }

    function test_oneParenthesised()
    {
        $cond =& new SQL_Condition();
        $cond->add(1,'=',1);
        $query =& new SQL_Query('city');
        $query->addWhere($cond,'<>','2');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals('( 1 = 1 ) <> 2',$ren->renderCondition());
    }

    function test_two()
    {
        $query =& new SQL_Query('city');
        $query->addWhere('1','<>','2');
        $query->addWhere('3','=','4','OR');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals('1 <> 2 OR 3 = 4',$ren->renderCondition());
    }

    function test_three()
    {
        $query =& new SQL_Query('city');
        $query->addWhere('1','<>','2');
        $query->addWhere('3','=','4','OR');
        $query->addWhere('5','=','6','XOR');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals('1 <> 2 OR 3 = 4 XOR 5 = 6',$ren->renderCondition());
    }

    function test_nested()
    {
        $query =& new SQL_Query('city');
        $query->addWhere('1','<>','2');
        $query->addWhere('3','=',$query->condition('me','=','somestring'),'OR');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals('1 <> 2 OR 3 = ( me = somestring )',$ren->renderCondition());		
    }

    function test_twoNested()
    {
        $query =& new SQL_Query('city');
        $query->addWhere('1','<>','2');
        $query->addWhere($query->condition('you','=','other'),'=',
                        $query->condition('me','=','somestring'),'OR');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals(  '1 <> 2 OR '.
                                    '( you = other ) = ( me = somestring )',
                                    $ren->renderCondition());
    }

    function test_twoLevelsNested()
    {
        $query =& new SQL_Query('city');
        $query->addWhere($query->condition('you','=',
                        $query->condition('me','=','somestring')),'=','xx');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals(  '( you = ( me = somestring ) ) = xx',
                                    $ren->renderCondition());
    }

    function test_quotedField()
    {
        $query =& new SQL_Query('city');
        $query->addWhere('name','<>','\'wolfram\'');

        $ren =& new $this->_renderer($query);

        $this->assertStringEquals('name <> \'wolfram\'',$ren->renderCondition());
    }

    function test_conditionOnly()
    {
        $cond =& new SQL_Condition('name','<>','x');

        $ren =& new $this->_renderer(new SQL_Query());

        $this->assertStringEquals('name <> x',$ren->renderCondition($cond));
    }

    /**
    * Be sure there is a space between the condition and the operators, 
    * this is important i.e. for IN as demonstrated here.
    * And this method checks that an IN-parameter which is a string is properly
    * handled.
    */
    function test_spacesAndIn()
    {
        $cond =& new SQL_Condition('id','IN','(1,2)');

        $ren =& new $this->_renderer(new SQL_Query());

        $this->assertEquals('id IN (1,2)',$ren->renderCondition($cond));
    }

    /**
    * Test that the renderer also expands a IN parameter which is an array
    *
    */
    function test_inCondition()
    {
        $cond =& new SQL_Condition('id','IN',array(1,2));

        $ren =& new $this->_renderer(new SQL_Query());

        $this->assertEquals('id IN (1,2)',$ren->renderCondition($cond));
    }

    /**
    * test that if only one parameter is given to the condition if it is rendered properly
    *
    */
    function test_oneParamInConstructor()
    {
        $cond =& new SQL_Condition(new SQL_Condition('id','<>',1));

        $ren =& new $this->_renderer(new SQL_Query());

        $this->assertStringEquals('( id <> 1 )',$ren->renderCondition($cond));
    }

    /**
    * test if the condition renders properly when two parameters are given
    *
    */
    function test_twoParams()
    {
        $cond =& new SQL_Condition('id','=',2);
        // we actually check this here
        $cond->add(new SQL_Condition('id','<>',1),'OR');

        $ren =& new $this->_renderer(new SQL_Query());

        $this->assertStringEquals('id = 2 OR ( id <> 1 )',$ren->renderCondition($cond));
    }

}

?>
