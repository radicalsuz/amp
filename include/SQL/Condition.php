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
//  $Id: Condition.php,v 1.2 2003/09/22 20:49:56 wk Exp $
//

/**
* Use this class to build a where clauses.
* This class is only responsible for providing an interface to collect all the elements
* for a where-clause.
* 
*   
* <code>
* // build this: (name LIKE 'n%' AND name LIKE 'a%') OR name LIKE 's%'
* $c1 = new Query_Condition('name','LIKE','"n%"');
* $c1->add('name','LIKE','"a%"','AND');
* $query =& new SQL_Query();
* $query->addWhere($c1,'OR',new SQL_Query_Condition('name','LIKE','"%s"'));
* </code>
*
* @package Query_Condition
* @author Wolfram Kriesing <wk@visionp.de>
*/
class SQL_Condition
{
    /**
    * @var array this array contains at least one condition
    */
    var $condition = array();

    /**
    * Constructor that also lets you initialize the condition.
    * This method returns the instance of this class and also
    * allows for building a simple expression.
    * Some examples to demonstrate how this constructor can be used:
    * <code>
    * // simply get an instance of this class
    * $c1 = new Query_Condition();
    *
    * // get the instance and initialize the expression 
    * $c1 = new Query_Condition('name','LIKE','"n%"');
    * </code>
    *
    * @see add()
    * @param string the first term
    * @param string the operator, such as '=', '<', 'LIKE', ...
    * @param string the second term that the first shall be compared to
    * @return void
    */
    function SQL_Condition($cond1=null,$operator=null,$cond2=null)
    {
        if ($cond1!=null) {
            $this->add($cond1,$operator,$cond2);
        }
    }

    /**
    * Extend the condition by adding another condition.
    * This is useful when you want to build something like 
    * (name LIKE 'n%' OR name LIKE 'a%' OR name LIKE 'c%')
    * <code>
    * $c1 = new SQL_Query_Condition('name','LIKE','"n%"');
    * $c1->add('name','LIKE','"a%"','OR');
    * $c1->add('name','LIKE','"c%"','OR');
    * // or if you only want to add a condition to an existing condition
    * $c2 = new SQL_Query_Condition('name','LIKE','"n%"');
    * $c2->add($c1,'OR');
    * </code>
    *
    * @param mixed the first paramter for the condition
    * @param string the operator for the condition, i.e. '<>', 'OR', 'IN', 'LIKE', ...
    * @param mixed the first paramter for the condition
    * @param string the operator that shall be used to connect this expression to the
    *  existing part 
    * @return void
    */
    function add($cond1,$operator,$cond2=null,$globalOperator='AND')
    {
        if (!sizeof($this->condition)) {
            if ($cond2==null) {
                $this->condition[] = array($cond1);
            } else {
                $this->condition[] = array($cond1,$operator,$cond2);
            }
        } else {
            if ($cond2==null) {
                $this->condition[] = array($cond1,$operator);
            } else {
                $this->condition[] = array($cond1,$operator,$cond2,$globalOperator);
            }
        }
    }

    /**
    * Return the current condition object.
    *
    * @return array the current condition built using call(s) to add()
    */
    function get()
    {
        return $this->condition;
    }
}

?>
