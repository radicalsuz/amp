<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author:  Wolfram Kriesing <wolfram@kriesing.de>                      |
// +----------------------------------------------------------------------+
//
//  $Id: Function.php,v 1.3 2003/10/05 20:41:57 wk Exp $
//

/**
* 
* The spec defines 
*
* SUBSTRING <left paren> <bit value expression> FROM <start position>
*   [ FOR <string length> ] <right paren>

CONVERT <left paren> <character value expression>
    USING <form-of-use conversion name> <right paren>

{ UPPER | LOWER } <left paren> <character value expression> <right paren>    

CONVERT (char_value target_char_set USING form_of_use source_char_name)
 
TRANSLATE(char_value target_char_set USING translation_name)


*
*
*
* @package Query_Function
* @author Wolfram Kriesing <wolfram@kriesing.de>
*/
class Query_Function
{
    /**
    * @var array this array contains at least one function
    */
    var $_function = array();

    /**
    * This property is set to true if the set-quantifier as 
    * defined in the sql-spec is DISTINCT, since it might only be
    * ALL | DISTINCT and ALL is default we call it distinct.
    * The spec defines that the DISTINCT appears like this in a GROUP-function:
    * <pre>
    * &lt;general set function&gt; ::=
    *   &lt;set function type&gt;
    *       &lt;left paren&gt; [ &lt;set quantifier&gt; ] &lt;value expression&gt; &lt;right paren&gt;
    *
    * &lt;set function type&gt; ::=
    *   AVG | MAX | MIN | SUM | COUNT
    * </pre>
    * To ensure that the DISTINCT is not used for other functions than
    * those mentioned above the user has to know when to use it!
    *
    * @var boolean true if the select shall be distinct
    */
    var $distinct = false;

    var $_aliases = array(
#                        'strtoupper'    =>  
                        );
    
    /**
    *
    *
    * @param string the name of the function, i.e. 'strtoupper', 'count', ...
    * @param 
    */
    function Query_Function( $type, $params)
    {

    }

    /**
    * Remove DISTINCT if set.
    *
    * @return void
    */
    function resetDistinct()
    {
        $this->distinct = false;
    }
        
    /**
    * Add DISTINCT to the beginning of the function.
    *
    * @return void
    */
    function setDistinct()
    {
        $this->distinct = true;
    }
    
    /**
    * Tell if the query is DISTINCT.
    *
    * @return boolean true if the query shall be distinct
    */
    function getDistinct()
    {
        return $this->distinct;
    }
    
}

?>
