<?php
//
//  $Id$
//

class tests_UnitTest extends PhpUnit_TestCase
{
    function setUp()
    {
        $this->setLooselyTyped(true);
    }

    function tearDown()
    {
    }

    function assertStringEquals($expected,$actual,$msg='')
    {
        $expected = '~^\s*'.preg_replace('~\s+~','\s*',trim(preg_quote($expected))).'\s*$~i';
        $this->assertRegExp($expected,$actual,$msg);
    }



}

?>
