<?php
//
//  $Id$
//

/**
*
*/
class tests_Renderer_Common_renderGroup extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Common';

    function test_simple()
    {
        $query =& new SQL_Query();
        $query->addGroup('id');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('id',$ren->renderGroup());
    }
    
    function test_twoParas()
    {
        $query =& new SQL_Query();
        $query->addGroup('name','MAX(num)');
        $ren =& new $this->_renderer($query);
        $this->assertStringEquals('name , MAX(num)',$ren->renderGroup());
    }
}

?>
