<?php
//
// $Id$
//

require_once 'SQL/Condition.php';

/**
* This class is only responsible for providing an interface to collect all the elements
* of a query, such as the select, where, group, etc. parts. The data are saved into properties.
* There is a reset* and a add* method for each part of the query. The set* method() as
* QueryTool v0.x used to have it is not really needed and does only cause confusion, since you 
* can easily mix add* and set* which sometimes only causes debugging effort.
*   
* thanks to those guys who wrote this great book
* [1] http://www.oldenbourg.de/frame0.htm?http://www.oldenbourg.de/cgi-bin/rotitel?T=25706
*
* at least the sql92 spec
* [2]http://www.contrib.andrew.cmu.edu/~shadow/sql/sql1992.txt
*
* @package SQL_Query
* @author Wolfram Kriesing <wk@visionp.de>
* @version 2003/05/23
*/
class SQL_Query
{

    /**
    * @var string the query type, i.e. 'select', 'insert', etc.
    */
    var $type = 'select';

    /**
    * @var array contains the strings that are given for selecting, i.e.
    *  '*' or 'id' or 'name' or 'name AS userName' or 'surname' or
    *  'SUBSTRING(name FROM 1 FOR 5 COLLATE whatever) AS partOfName' or 'COUNT(x)'
    *  every value in the array contains ONE value that needs to be selected
    */
    var $select = array();

    /**
    * @var array contains the columns that shall not be selected
    */
    var $dontSelect = array();

    /**
    * @var mixed the table, table and alias or the join this class is currently working on, 
    *  note that this class only works on one table, to use joined queries
    *  pass a join instance
    *  If this value is an array it is: array(tableName|join-object[,aliasName]) 
    */
    var $from = array();

    /**
    *   @var array every value of this array contains one expression
    *   @todo i am not sure how to do this, since an expression might be quite complex and use parentheses, so there
    *    are groups ... and how are they connected, via AND or OR, ...
    */
    var $where = null;

    /**
    * @var array each value in this array is an array itself,
    *  it might be either only array(column name), which means it is sorted ascending, 
    *  which is the default. Or if the second element is true it means descending. 
    *  I.e. array('name') or array('name',false) =ascending, or array('name',true) =descending
    *  and if a collate-clause shall be added it is simply the third field in the array, since
    *  this is not a very usual case i think we can 'abandon' it there.
    *  each field has a meaning, this way accessing them directly is easier in using this structure
    *    
    * copied from [2], that's how the order clause is specified. this is only sql92!!!
    * I dont know what diff there is to sql99 but i will look one day ... i hope there is none :-)
    * <pre>
    *     &lt;order by clause&gt; ::=
    *          ORDER BY &lt;sort specification list&gt;
    *
    *     &lt;sort specification list&gt; ::=
    *          &lt;sort specification&gt; [ { &lt;comma&gt; &lt;sort specification&gt; }... ]
    *
    *     &lt;sort specification&gt; ::=
    *          &lt;sort key&gt; [ &lt;collate clause &gt; ] [ &lt;ordering specification&gt; ]
    *
    *
    *     &lt;sort key&gt; ::=
    *            &lt;column name&gt;
    *          | &lt;unsigned integer&gt;
    *
    *     &lt;ordering specification&gt; ::= ASC | DESC	
    *
    *     &lt;collate clause&gt; ::= COLLATE &lt;collation name&gt;  
    * </pre>
    */
    var $order = array();

    /**
    * @var array each value of the array contains the column name or expression that shall be added to the group clause
    */
    var $group = array();

    /**
    *   @var array each value of the array contains the column name or expression that shall be added to the having clause
    */
    var $having = array();

    /**
    *   @var string the limit clause of the query
    */
    var $limit = array();

    function SQL_Query( $table=null, $type='select')
    {
        if ($table!=null) {
            $this->setFrom($table);
        }
        $this->setType($type);
    }

    /**
    * Sets the query type, which is either 'select' which is default, 'insert',
    * 'delete', etc.
    * 
    * @param string the query type
    */
    function setType( $type)
    {
        $this->type = strtolower($type);
    }
    
    /**
    * Gets the query type, which is either 'select' which is default, 'insert',
    * 'delete', etc., all types are always lower case!
    * 
    * @return string the query type
    */
    function getType()
    {
        return $this->type;
    }
    
    /**
    * Set the table or a join, this query is built for.
    *
    * @param mixed string - the table name, or
    *  array - the table and its alias, like this: array('table','alias'), or
    *  join-object - this is an instance of SQL_Query_Join.
    *  Since there are those multiple options, we can not pass them as reference 
    *  with PHP4, its not nice, but that's how it currently is.
    */
    function setFrom($from)
    {
        if (is_array($from)) {
            $from = array(current($from),key($from));
        }
        if (is_string($from)) {
            settype($from,'array');
        }
        $this->from = $from;
    }

    /**
    * Return the FROM part that is set for the query.
    *
    * @return mixed the from part, see setFrom()
    */
    function getFrom()
    {
        return $this->from;
    }

    /**
    *   This method only serves the purpose to empty the select part.
    *
    *   @return void
    *   @access public
    */
    function resetSelect()
    {
        $this->select = array();
    }

    /**
    *   Add columns or expressions to the select part of the query.
    *   Passing no value to this method adds a '*' to the select query, which is normally the default.
    *   Example usage:
    *   <code>
    *   $obj->addSelect('id','name','surname'); // select the three given columns OR
    *   $obj->addSelect(array('id','name','surname')); // select the three given columns
    *   </code>
    *
    *   @param mixed any parameter passed is added to the select query, you can pass as many parameters
    *    as you want. To select multiple columns add their names as a parameter each.
    */
    function addSelect($string='*')
    {
        if (func_num_args()>1) {
            $args = func_get_args();
        } else {
            $args = is_array($string)?$string:array($string);
        }
        foreach ($args as $aVal) {
            $this->select[] = $aVal;
        }
        array_unique($this->select);
    }	

    /**
    * Return the select part as it is stored internally.
    *
    * @access public
    * @return array this array contains all the parts that were added to the select part
    */	
    function getSelect()
    {
        return $this->select;
    }

    /**
    * This method only serves the purpose to empty the dontSelect part.
    *
    * @return void
    * @access public
    */
    function resetDontSelect()
    {
        $this->dontSelect = array();
    }	

    /**
    *   Add columns or expressions to the select part of the query.
    *   Passing no value to this method adds a '*' to the select query, which is normally the default.
    *   Example usage:
    *   <code>
    *   $obj->addSelect('id','name','surname'); // select the three given columns
    *   </code>
    *
    *   @param mixed strings - any parameter passed is added to the select query, you can pass as many parameters
    *    as you want. To select multiple columns add their names as a parameter each.
    *    array - or only one parameter which is an array and contains multiple column names
    */
    function addDontSelect($string)
    {
        if (func_num_args()>1) {
            $args = func_get_args();
        } else {
            $args = is_array($string)?$string:array($string);
        }
        foreach ($args as $aVal) {
            $this->dontSelect[] = $aVal;
        }
    }

    /**
    * Return the dont-select part as it is stored internally.
    *
    * @access public
    * @return array this array contains all the parts that were added to the dont-select part
    */
    function getDontSelect()
    {
        return $this->dontSelect;
    }

    /**
    *   This method resets the where part of the query.
    *   
    *   @return void
    */
    function resetWhere()
    {
        $this->where = null;
    }

    /**
    *   This method adds conditions to the where part of the query.
    *   usage example:
    *   <code>   
    *   $query->addWhere('name','LIKE','"%any%"');
    *
    *   // to build this where clause:    name LIKE "%any%" OR name LIKE '%none%'
    *   // do it like this:
    *   $cond1 = $query->condition('name','LIKE','"%any%"');
    *   $cond2 = $query->condition('name','LIKE','"%none%"');
    *   $query->addWhere($cond1,'OR',$cond2);
    *
    *   // to build this: (name LIKE 'n%' AND name LIKE 'a%') OR name LIKE 's%'
    *   // this is the prefered way
    *   $cond = $query->condition('name','LIKE','"n%"');
    *   $cond->add('name','LIKE','"a%"','AND');
    *   $query->addWhere($cond,'OR',$query->condition('name','LIKE','"%s"'));
    *   // OR like this, it works too
    *   $c1 = $query->condition('name','LIKE','"n%"');
    *   $c2 = $query->condition('name','LIKE','"a%"');
    *   $query->addWhere($query->condition($c1,'AND',$c2),'OR',$query->condition('name','LIKE','"%s"'));
    *   </code>
    *   Since the condition parameters can also be strings we can not make them
    *   accept references only :-( so we must live with copying the condition-objects
    *   in case they are passed instead of a string. But this seems no big problem
    *   i think.
    *   
    *   @param mixed a
    *   @return void
    */	
    function addWhere($cond1,$operator=null,$cond2=null,$globalOperator='AND')
    {
        if (!$this->where) {
            $this->where =& new SQL_Condition($cond1,$operator,$cond2);
        } else {
            $this->where->add($cond1,$operator,$cond2,$globalOperator);
        }
    }

    /**
    * Return the current where condition.
    *
    * @return mixed mostly an instance of SQL_Condition, or null if no condition is set
    */
    function getWhere()
    {
        return $this->where;
    }

    /**
    *   This method is just a shortcut to build conditions easier.
    *   Example usage:
    *   <code>
    *   $cond  = $query->condition('name','=','Foo');
    *   $cond1 = $query->condition('name','=','Bar');
    *   $query->addWhere($cond,'OR',$cond1);
    *   // is the same as this
    *   $cond  = new SQL_Condition('name','=','Foo');
    *   $cond1 = new SQL_Condition('name','=','Foo');
    *   $query->addWhere($cond,'OR',$cond1);
    *   </code>
    *
    */
    function &condition($cond1,$operator,$cond2)
    {
        return new SQL_Condition($cond1,$operator,$cond2);
    }

    /**
    *   This method resets the order part of the query.
    *   
    *   @return void
    */
    function resetOrder()
    {
        $this->order = array();
    }

    /**
    * This method adds one or many pieces to the order part of the query.
    * example usage:
    * <code>
    * $query->addOrder('column');       // simply sort by this column in ascending order
    *
    * $query->addOrder(array('column',true));  // sort by 'column' descending
    *
    * // sort by all given cols, but col2 descending
    * // this should result in this: ORDER BY col1, col2 DESC
    * $query->addOrder('col1',array('col2',true));  
    *
    * // add a collate clause
    * // this should become: ORDER BY col COLLATE col1 DESC, col2
    * $query->addOrder(array('col',true,'col1'),'col2');  
    *
    * // add a collate clause
    * // this should become: ORDER BY col COLLATE col1 ASC, col2
    * $query->addOrder(array('col',false,'col1'),'col2');  
    * </code>   
    *
    * @see order
    * @param mixed either a simply string or an array
    * @return void
    */
    function addOrder($order)
    {
        foreach (func_get_args() as $aArg) {
            if (is_array($aArg)) {
                $this->order[] = $aArg;
            } else {
                // set the false, to signalize that it is descending
                $this->order[] = array($aArg,false);
            }
        }
    }

    /**
    * Return the order part of the query.
    *
    * @return array 
    */
    function getOrder()
    {
        return $this->order;
    }

    /**
    * This method resets the group part of the query.
    *   
    * @return void
    */	
    function resetGroup()
    {
        $this->group = array();
    }

    /**
    * This method adds one or many pieces to the group part of the query.
    *   
    * @param string the column name or expression to be added to the group part of the query
    * @return void
    */
    function addGroup($group)
    {
        foreach (func_get_args() as $v) {
            $this->group[] = $v;
        }
    }

    /**
    * Return the group part of the query.
    *
    * @return array 
    */
    function getGroup()
    {
        return $this->group;
    }

    /**
    * This method resets the having part of the query.
    *   
    * @return void
    */	
/*	function resetHaving()
    {
        $this->having = array();
    }
*/	
    /**
    * This method adds one or many pieces to the group part of the query.
    *   
    * @param string the column name or expression to be added to the group part of the query
    * @return void
    */	
/*	function addHaving($having)
    {
        foreach (func_get_args() as $v) {
            $this->having[] = $v;
        }
    }
*/
    /**
    * Return the having part of the query.
    *
    * @return array 
    */
/*	function getHaving()
    {
        return $this->having;
    }
*/			
    /**
    *   Reset the limit part of the query.
    *
    *   @access public
    *   @return void
    */
/*	function resetLimit()
    {
        $this->limit = array();
    }
*/	
    /**
    *   Set the limit clause of the array.
    *
    *   @param int the row to start at
    *   @param int the max. number of rows to return
    *   @access public
    *   @return void
    */
/*	function setLimit($from,$count)
    {
        $this->limit = array($from,$count);
    }
*/	
    /**
    * Return the limit clause of the query.
    *
    * @return array 
    */
/*	function getLimit()
    {
        return $this->limit;
    }
*/    
       
    /**
    *   Reset all parts of the query.
    *   This method simply searches all the class methods, that start with
    *   'reset' and calls them.
    *
    */
    function reset()
    {
        foreach (get_class_methods(__CLASS__) as $method) {
            if ($method!='reset' && strpos($method,'reset')===0) {
                $this->$method();
            }
        }
    }

    /**
    *   This method returns the hash of this object. 
    *   This is very useful for caching to detect if this query is already cached.
    *   
    *
    */
    function hashKey()
    {
        return md5(serialize($this));
    }

}

?>
