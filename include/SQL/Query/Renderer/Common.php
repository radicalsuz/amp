<?php

/**
* This class provides all the common functionality a renderer contains,
* such as saving the query into a property, etc.
* The specific rendering is implement in a class that (normally) extends 
* this one.
*
* @package SQL_Query_Renderer
* @author Wolfram Kriesing <wolfram@kriesing.de>
*/
class SQL_Query_Renderer_Common
{

    /**
    * @var object this is a SQL_Query object, should be saved in here as a reference
    */
    var $query = null;
    
    /**
    *
    *
    *   @param object this should be SQL_Query object
    */
    function SQL_Query_Renderer_Common( &$query)
    {
        $this->_query =& $query;
    }

    function render()
    {
        switch (strtolower($this->_query->getType())) {
            case 'insert':
                $query = sprintf('INSERT INTO %s %s'
                                    ,$this->renderFrom()
                                    ,$this->renderValues());
                break;
            case 'update':
                $query = sprintf('UPDATE %s %s'
                                    ,$this->renderFrom()
                                    ,$this->renderSet());
                break;
            case 'delete':
                $where = $this->renderCondition();
                $query = sprintf('DELETE FROM %s %s'
                                    ,$this->renderFrom()
                                    ,$where?('WHERE '.$where):'');
                break;
            default:
                $where = $this->renderCondition();
                $group = $this->renderGroup();
                $order = $this->renderOrder();
                $query = sprintf('SELECT %s FROM %s',$this->renderSelect(),$this->renderFrom());
                $query.= $where?(' WHERE '.$where):'';
                $query.= $group?(' GROUP BY '.$group):'';
                $query.= $order?(' ORDER BY '.$order):'';
                break;
        }
        return $query;
    }


    /**
    * Render the FROM part of the query, by taking any kind of joins into account.
    *
    * @version 2003/06/06
    * @access public
    * @return string the string added behind FROM
    */
    function renderFrom( $from=null)
    {
        if ($from==null) {
            $from = $this->_query->getFrom();
        }
        $ret = '';
        if (is_array($from) && sizeof($from)) {
            if (sizeof($from)==1) {
                $ret = $from[0];
            } else {
                $ret = $from[0].' '.$from[1];
            }
        } elseif (is_a($from,'SQL_Query_Join')) {
            $ret = $this->renderJoin($from);
        }

        return $ret;
    }    
    
    /**
    * Render a join given by an instance of SQL_Query_Join.
    * It renders the joins in the following way:
    * If one table is given, it simply adds the join to the current join string, like this:
    * <join-type> JOIN table1 ON <condition>
    * If two tables are given, this is being built:
    * table1 <join-type> JOIN table2 ON <condition>
    * One table is added to the existing join string with a space,
    * two table-joins are added with a ',' (comma).
    *
    * @param object an instance of SQL_Query_Join
    * @return string the join rendered as a valid SQL string
    */
    function renderJoin( $join=null)
    {
        if ($join==null) {
            $join = $this->_query->getFrom();
        }
        $joins = array();
        $curJoin = '';
        foreach ($join->getJoins() as $aJoin) {
            // if two tables are given do the following: table1 XXXX JOIN table2 ON condition
            if (sizeof($aJoin['tables'])>1) {
                if ($curJoin) {
                    $joins[] = $curJoin;
                    $curJoin = '';
                }
                $curJoin .= $this->renderJoinedTable($aJoin['tables'][0]);
                $curJoin .= ' '.strtoupper($aJoin['type']).' JOIN ';
                $curJoin .= $this->renderJoinedTable($aJoin['tables'][1]);
            } else {
                // if only one table is given do this: XXXX JOIN table1 ON condition
                $curJoin .= ' '.strtoupper($aJoin['type']).' JOIN ';
                $curJoin .= $this->renderJoinedTable($aJoin['tables'][0]);
            }
            $curJoin .= ' ON '.$this->renderCondition($aJoin['condition']);
        }
        if ($curJoin) {
            $joins[] = $curJoin;
        }
        return implode(',',$joins);
    }

    /**
    * This method renders the given table, which might also be an instance
    * of SQL_Query_Join, or simply the table name.
    *
    * @param mixed 
    */
    function renderJoinedTable( &$table)
    {
        $aJoin = '';
        if (is_string($table)) {    // simply a string is the table name
            $aJoin = $table;
        } elseif (is_array($table)) {   // an array is the table name (which can also be a join-object) and the alias
            // if the table name is a join-object render it
            if (is_a($table[0],'SQL_Query_Join')) {
                $aJoin = '('.$this->renderJoin($table[0]).')';
            } else {
                $aJoin = $table[0];
            }
            // add the alias if given
            if (isset($table[1])) {
                $aJoin .= ' '.$table[1];
            }
        } elseif (is_a($table,'SQL_Query_Join')) {  // if it is only a join object render it
            $aJoin = '('.$this->renderJoin($table).')';
        }
        return $aJoin;
    }
    
    /**
    *   
    *
    * @access     public
    * @author     Wolfram Kriesing <wk@visionp.de>
    * @param object the condition object to be rendered, if not given the where-object from 
    *  $this->_query->getWhere() is used
    * @return string the rendered where clause
    */
    function renderCondition($conditionObj=null)
    {
        if ($conditionObj==null) {
            $conditionObj = $this->_query->getWhere();
        }
        // null is the default if no condition is set
        if ($conditionObj==null) {
            return '';
        }
        $condition = $conditionObj->get();
        $ret = '';
        foreach ($condition as $cond) {
            if (!isset($cond[2])) {
                if (isset($cond[1])) {
                    $ret .= ' '.$cond[1].' ';
                }
                $ret .= $this->renderConditionPart($cond[0]);
            } else {
                // a global operator ($cond[3]) connects the condition that is in $cond to those before
                // so we have to add it before the actual condition string
                // if there is one given but no condition before, the user has made a mistake
                if (isset($cond[3])) {
                    $ret .= ' '.$cond[3].' ';
                }
                $ret .= $this->renderConditionPart($cond[0]).' '.$cond[1].' '.$this->renderConditionPart($cond[2],$cond[1]);
            }
        }
        return $ret;	
    }

    function renderConditionPart($part,$operator=null)
    {
        if (is_a($part,'SQL_Condition')) {
            $ret = '('.$this->renderCondition($part).')';
        } else {
            switch (trim(strtolower($operator))) {
                case 'in':  
                    if (is_array($part)) { // in case the paramter is already a string
                        $ret = '('.implode(',',$part).')';
                    } else {
                        $ret = $part;   
                    }
                    break;
                default:    
                    $ret = $part;   
                    break;
            }
        }
        return $ret;
    }
     
    /**
    * Render the GROUP part of a query, either given by the internal property 
    * _query or by the paramter.
    *
    * @param array the group parameter(s)
    * @return string the 
    */
    function renderGroup( $group=null)
    {
        if ($group==null) {
            $group = $this->_query->getGroup();
        }
        $ret = '';
        if (is_array($group) && sizeof($group)) {
            // this might need to be extended if there are SQL-Functions and so on wrapped into
            // objects and those need to be extracted first
            $ret = implode(',',$group);
        }
        return $ret;
    }
    
    /**
    * Render the ORDER part of a query, either given by the internal property 
    * _query or by the paramter.
    *
    * @param array the order parameter(s)
    * @return string
    */
    function renderOrder( $order=null)
    {
        if ($order==null) {
            $order = $this->_query->getOrder();
        }
        $ret = '';
        if (is_array($order) && sizeof($order)) {
		    $ret = array();
            foreach ($order as $aOrder) {
			    $aRet = '';
			    $aRet .= $aOrder[0];
			    $aRet .= isset($aOrder[2])?' COLLATE '.$aOrder[2]:'';
			    $aRet .= $aOrder[1]?' DESC':'';
				$ret[] = $aRet;
			}
			$ret = implode(',',$ret);
        }
        return $ret;
    }
    
    /**
    * Render a VALUES-part for an insert query.
    * This method does not only render the given data it also ensures that
    * all the columns that are given for any input value set are also given in the rendered string
    * for every set of values or the query would simply not work.
    * All missing values are filled with NULL.
    *
    * @param array the values to be set for a INSERT query, every array contains
    *  one group of values, where each value's key is the column name
    * @return string the rendered values
    */
    function renderValues( $values=null)
    {
        if ($values==null) {
            $values = $this->_query->getValues();
        }
        $ret = '';
        if (is_array($values) && sizeof($values)) {
            $allKeys = array();
            // sort all the arrays in $values so that they are sorted the same way by keys
            // and get all the available keys into $allKeys 
            foreach ($values as $k=>$v) {
                ksort($values[$k]);
                // get all keys (array_keys) and add them (array_merge) to allKeys
                // and to prevent that $allKeys gets to big and to ensure that every 
                // key only is stored once we make every value unique in the array (array_unique)
                $allKeys = array_unique(array_merge($allKeys,array_keys($values[$k])));
            }
            sort($allKeys);

            // go through all the value-arrays and check that each has ALL keys given,
            // it might be that one value has a certain key not given
            // than we assume that the value for this key shall be null and set it
            $allNull = array();
            foreach ($allKeys as $k) {
                $allNull[$k] = 'NULL';
            }
            // merge the null-array with the actual, to add the missing elements in case there are 
            // any, this would only add elements that dont exist yet (depending on the key)
            foreach ($values as $k=>$v) {
                $values[$k] = array_merge( $allNull, $values[$k]);
            }
                        
            // now we only have to walk through the values and build the string we shall return
            $setStrings = array();
            foreach ($values as $aValSet) {
                $aSet = array();
                foreach ($aValSet as $aVal) {   
                    $aSet[] = $aVal;
                }
                $setStrings[] = '('.implode(',',$aSet).')';
            }
            // get all the column names
            $cols = array();
            foreach ($aValSet as $aCol=>$v) {   
                $cols[] = $aCol;
            }
            $ret = '('.implode(',',$cols).') VALUES '.implode(',',$setStrings);
        }
        return $ret;
    }
    
    function renderSet()
    {
    }

}


?>
