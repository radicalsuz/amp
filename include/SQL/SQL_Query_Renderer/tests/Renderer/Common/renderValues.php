<?php
//
//  $Id$
//

require_once 'SQL/Query/Insert.php';

/**
*
*/
class tests_Renderer_Common_renderValues extends tests_UnitTest
{

    var $_renderer = 'SQL_Query_Renderer_Common';

    // standard simple values test
    function test_simple()
    {
        $insert =& new SQL_Query_Insert('table');
        $values = array(
                    array('name'=>'"cain"','surname'=>'"none"')
                    ,array('name'=>'"foo"','surname'=>'"bar"')
                        );
        $insert->addValues($values[0]);
        $insert->addValues($values[1]);
        $renderer =& new $this->_renderer($insert);
        $this->assertStringEquals(  '( name , surname ) VALUES '.
                                    '( "cain" , "none" ) , ( "foo" , "bar" )' 
                                    ,$renderer->renderValues());
    }    
    
    // the renderer checks all the value-sets that are given to have the same colummns
    // if not all are given it assumes their value to be NULL
    function test_missingCols()
    {
        $insert =& new SQL_Query_Insert('table');
        $values = array(
                    array('name'=>'"cain"','surname'=>'"none"')
                    ,array('name'=>'"foo"') // here the surname will be NULL
                        );
        $insert->addValues($values[0]);
        $insert->addValues($values[1]);
        $renderer =& new $this->_renderer($insert);
        $this->assertStringEquals(  '( name , surname ) VALUES '.
                                    '( "cain" , "none" ) , ( "foo" , NULL )' 
                                    ,$renderer->renderValues());
    }    
    
}

?>
