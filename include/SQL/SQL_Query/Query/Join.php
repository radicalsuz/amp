<?php

/**
*   
*   
* thanks to those guys who wrote this great book<br/>
* [1] http://www.oldenbourg.de/frame0.htm?http://www.oldenbourg.de/cgi-bin/rotitel?T=25706<br/>
* at least the sql92 spec<br/>
* [2] http://www.contrib.andrew.cmu.edu/~shadow/sql/sql1992.txt<br/>
* <br>
* SQL 92 defines a join as this, from [2]
* it needs to be verified if all kinds of joins can be represented by the current strucutre of this class
* <br/>
* <pre>
*         &lt;joined table&gt; ::=
*                &lt;cross join&gt;
*              | &lt;qualified join&gt;
*              | &lt;left paren&gt; &lt;joined table&gt; &lt;right paren&gt;
*
*         &lt;cross join&gt; ::=
*              &lt;table reference&gt; CROSS JOIN &lt;table reference&gt;
*
*         &lt;qualified join&gt; ::=
*              &lt;table reference&gt; [ NATURAL ] [ &lt;join type&gt; ] JOIN
*                &lt;table reference&gt; [ &lt;join specification&gt; ]
*
*         &lt;join specification&gt; ::=
*                &lt;join condition&gt;
*              | &lt;named columns join&gt;
*
*         &lt;join condition&gt; ::= ON &lt;search condition&gt;
*
*         &lt;named columns join&gt; ::=
*              USING &lt;left paren&gt; &lt;join column list&gt; &lt;right paren&gt;
*
*         &lt;join type&gt; ::=
*                INNER
*              | &lt;outer join type&gt; [ OUTER ]
*              | UNION
*
*         &lt;outer join type&gt; ::=
*                LEFT
*              | RIGHT
*              | FULL
*
*         &lt;join column list&gt; ::= &lt;column name list&gt;
* </pre>
*
we also have to be able to build complicated joins like that. the following ones might make
no sense, but are executable. I just added them to show how complicated only the join expression might
become :-)

// simple nested join clauses
SELECT * FROM 
uuser u LEFT JOIN 
    (time t RIGHT JOIN
        (time t1 RIGHT JOIN uuser u2 ON t1.user_id=u2.id) 
    ON t.user_id=u2.id)
ON u.id=t.user_id 

// this query is fully outspelled, with all the JOIN-clauses and no commas in the join
select * from 
uuser u
INNER JOIN time t ON u.id=t.user_id
INNER JOIN time t1 ON t1.id=u.id

// this has join clauses and a comma seperation
select * from 
uuser u INNER JOIN time t ON u.id=t.user_id,
uuser u1 INNER JOIN time t1 ON t1.id=u1.id

// this assigns an alias to a join clause
SELECT * FROM 
uuser u LEFT JOIN 
    (time t RIGHT JOIN uuser u1 ON t.user_id=u1.id) j1  // here we give an alias to a join expression
ON u.id=j1.user_id

// building this would be done like this
// 1. build this: time t RIGHT JOIN uuser u1 ON t.user_id=u1.id
$join1 =& new SQL_Query_Join();
$join1->addRightJoin(array('t'=>'time','u1'=>'uuser'),new SQL_Condition('t.user_id','=','u1.id'));

// 2. build the actual join
// uuser u LEFT JOIN (<$join1>) j1 ON u.id=j1.user_id
$join =& new SQL_Query_Join();
$join->addLeftJoin(array('u'=>'uuser','j1'=>&$join1)),
            new SQL_Condition('u.id','=','j1.user_id'));



or even more complex ....
SELECT * FROM 
uuser u LEFT JOIN 
    (time t RIGHT JOIN
        (time t1 RIGHT JOIN uuser u2 ON t1.user_id=u2.id) 
    ON t.user_id=u2.id)
ON u.id=t.user_id, 
time, 
uuser u3 LEFT JOIN time t3 ON t3.user_id=u3.id, 
uuser u4 LEFT JOIN time t4 ON t4.user_id=u4.id

to build it we would call the following:

// we build it inside out
// 1. build this: time t1 RIGHT JOIN uuser u2 ON t1.user_id=u2.id
$join1 =& new SQL_Query_Join();
$join1->addRightJoin(array('t1'=>'time','u2'=>'uuser'),new SQL_Condition('t1.user_id','=','u2.id'));

// 2. (time t RIGHT JOIN (<$join1>) ON t.user_id=u2.id)
$join2 =& new SQL_Query_Join();
$join2->addRightJoin(array('t'=>'time',$join1),new SQL_Condition('t.user_id','=','u2.id'));

// 3. uuser u LEFT JOIN (<$join2>) ON u.id=t.user_id
$join3 =& new SQL_Query_Join();
$join3->addRightJoin(array('u'=>'uuser',$join2),new SQL_Condition('u.id','=','t.user_id'));

// 4.   time, 
//      uuser u3 LEFT JOIN time t3 ON t3.user_id=u3.id,
//      uuser u4 LEFT JOIN time t4 ON t4.user_id=u4.id
$join3->addJoin('time');
$join3->addLeftJoin(array('u3'=>'uuser','t3'=>'time'),new SQL_Condition('t3.user_id','=','u3.id'));
$join3->addLeftJoin(array('u4'=>'uuser','t4'=>'time'),new SQL_Condition('t4.user_id','=','u4.id'));

* @package SQL_Query
* @author Wolfram Kriesing <wk@visionp.de>
*/
class SQL_Query_Join
{

    /**
	* @var array stores the join this class works on
	*/
	var $_joins = array();

    /**
    * Set the table(s) and the condition to the join. 
    *
    * Example usage:
    * <code>
    * $cond =& new SQL_Condition('time.user_id','=','user.id');
    * // rendered SQL: INNER JOIN time ON <$cond>
    * $join->addJoin('time',$cond);     
    * // rendered SQL: time INNER JOIN time ON <$cond>
    * $join->addJoin(array('time','user'),$cond); 
    * // rendered SQL: time t INNER JOIN user ON <$cond>, use the alias t
    * $join->addJoin(array('t'=>'time','user'),$cond);
    * // rendered SQL: time t INNER JOIN user u ON <$cond>
    * $join->addJoin(array('t'=>'time','u'=>'user'),$cond); 
    * // rendered SQL: time t INNER JOIN (<$joinX>) j ON <$cond>
    * // where <$joinX> is this: table1 INNER JOIN table2 table1.id=table2.xid
    * $joinX =& new SQL_Query_Join();
    * $joinX->addJoin(array('table1','table2'),new SQL_Condition('table1.id','=','table2.xid'))
    * $join->addJoin(array('t'=>'time','j'=>&$joinX),$cond); 
    * </code>
    *
    * @param mixed either a string, for one table
    *  or an array which contains two tables.
    * @param object the condition that applies to this join
    * @param string the type of join, either 'inner', 'right', 'left'
    */
    function addJoin( $tables, $condition, $type='inner')
    {
        settype($tables,'array');
        $addTables = array();
        foreach ($tables as $alias=>$table) {
            if (!is_string($alias)) {
                $addTables[] = array($table);
            } else {
                $addTables[] = array($table,$alias);
            }
        }
        $addJoin = array('tables'=>$addTables,'condition'=>&$condition,'type'=>$type);
/*FIXXXME do the checks properly
        // dont add exactly the same join twice, i am pretty sure this is not a wanted behaviour ...
        if (!in_array($addJoin,$this->_joins)) {
            $this->_joins[] = $addJoin;
        }
*/		
        $this->_joins[] = $addJoin;
    }

    /**
    *
    *
    *
    */
    function addRightJoin( $tables, $condition=null)
    {
        $this->addJoin($tables,$condition,'right');
    }

    /**
    *
    *
    *
    */
    function addLeftJoin( $tables, $condition=null)
    {
        $this->addJoin($tables,$condition,'left');
    }

    /**
    * This method returns all the tables and their aliases if given.
    * It returns all the tables that are given in this join, no matter how deep they
    * are nested.
    *
    * @return array this array contains all the tables in the order as added to this join.
    *  If a table has an alias, then the element is an array itself, where key [0] contains
    *  the table name and [1] the alias name.  
    */
    function getTables()
    {
        $tables = array();
        foreach ($this->_joins as $aJoin) {
//FIXXXME test this properly for all the possible cases ...		
            foreach ($aJoin['tables'] as $aTable) {
                if (is_a($aTable[0],__CLASS__)) {
                    $tables = array_merge($tables,$aTable[0]->getTables());
                } else {
                    $tables[] = $aTable;
                }
            }
        }
        return $tables;
    }

    /**
    * Return the internal join structure as it is.
    *
    */	
    function getJoins()
    {
        return $this->_joins;
    }
}

?>
