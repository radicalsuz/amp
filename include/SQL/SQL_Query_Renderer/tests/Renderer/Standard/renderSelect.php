<?php
//
//  $Id$
//

require_once 'SQL/Query/Renderer/Standard.php';

class tests_Renderer_Standard_renderSelect extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Standard';

    var $_query = 'SQL_Query';

    function test_all()
    {
        $query =& new $this->_query('');
        $query->addSelect('*','and this','and that');

        $renderer =& new $this->_renderer($query);

        $this->assertEquals('*,and this,and that',$renderer->renderSelect());
    }

    function test_oneDontSelect()
    {
        $query =& new $this->_query('');
        $query->addSelect('*','and this','and that');
        $query->addDontSelect('and this');

        $renderer =& new $this->_renderer($query);

        $this->assertEquals('*,and that',$renderer->renderSelect());
    }
}

?>
