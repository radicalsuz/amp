<?php
//
// $Id$
//

require_once 'SQL/Query.php';

/**
* This class provides an object oriented interface to build an insert query.
*
* @package SQL_Query
* @author Wolfram Kriesing <wolfram@kriesing.de>
* @version 2003/08/26
*/
class SQL_Query_Insert extends SQL_Query
{    
    /**
    * This property contains all the values that shall be inserted into
    * a table, every value contains the column name as the key.
    * @var array the values to insert into the table
    */
    var $values = array();
      
    function SQL_Query_Insert( $table=null)
    {
        parent::SQL_Query( $table, 'insert');
    }    
    
    /**
    * This method only serves the purpose to empty the select part.
    *
    * @return void
    * @access public
    */
    function resetValues()
    {
        $this->values = array();
    }

    /**
    * Add values to the insert query. You can pass multiple parameters, each parameter
    * contains one set of values, i.e. this code
    * <code>
    * $query =& new SQL_Query_Insert('person');
    * $query->addValues(array('name'=>'"cain"','phone'=>123456),
    *                   array('name'=>'"foo"','phone'=>345678));
    * $query->addValues(array('name'=>'"foo1"','phone'=>911));
    * </code>
    * depending on the renderer this might result in a rendered query that looks like this:<br>
	* <pre>
    * INSERT INTO person (name,phone,surname) VALUES ("cain",123456), ("foo",345678), ("foo1",911)
	* </pre>
    *
    * @param mixed any parameter passed is added to the values, you can pass as many parameters
    *  as you want.
    */
    function addValues( $values)
    {
        $args = func_get_args();
        foreach ($args as $aArg) {
            $this->values[] = $aArg;
        }
    }

    /**
    * Return the select part as it is stored internally.
    *
    * @access public
    * @return array this array contains all the parts that were added to the select part
    */
    function getValues()
    {
        return $this->values;
    }

}

?>
