<?php
//
//  $Id$
//

/**
* Test the renderOrder() method of the renderer.
*
*
*/
class tests_Renderer_Common_renderOrder extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Common';

    function test_empty()
    {
        $query =& new SQL_Query();
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('',$ren->renderOrder());
    }

    function test_oneAscending()
    {
        $query =& new SQL_Query();
		$query->addOrder('name');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name',$ren->renderOrder());
    }

    function test_oneDescending()
    {
        $query =& new SQL_Query();
		$query->addOrder(array('name',true));
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name DESC',$ren->renderOrder());
    }

    function test_twoAscending()
    {
        $query =& new SQL_Query();
		$query->addOrder('name','surname');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name , surname',$ren->renderOrder());
    }

    function test_twoDescending()
    {
        $query =& new SQL_Query();
		$query->addOrder(array('name',true),array('surname',true));
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name DESC , surname DESC',$ren->renderOrder());
    }

    function test_twoMixed()
    {
        $query =& new SQL_Query();
		$query->addOrder(array('name',false),array('surname',true));
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name , surname DESC',$ren->renderOrder());
    }

    function test_twoMixedAndCollate()
    {
        $query =& new SQL_Query();
		$query->addOrder(array('name',false,'x'),array('surname',true,'y'));
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name COLLATE x, surname COLLATE y DESC',$ren->renderOrder());
    }

}

?>
