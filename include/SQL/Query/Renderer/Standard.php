<?php

require_once 'SQL/Query/Renderer/Common.php';

/**
* This class simply renders a query, it has no extra logic, it only 
* really converts the SQL_Query-object into a Query-string which could be
* sent straight to the DB.
* For example:
* <code>
* $query =& new SQL_Query('table');
* $query->addSelect('*');
*
* $renderer =& new SQL_Query_Renderer_Standard($query);
* $queryAsString = $renderer->render();
* </code>
* results in $queryAsString = 'SELECT * FROM table'
*   
* @package SQL_Query_Renderer
* @author Wolfram Kriesing <wk@visionp.de>
*/
class SQL_Query_Renderer_Standard extends SQL_Query_Renderer_Common
{
    /**
    * Render the select part. 
    *
    * @return string the rendered select part of the query
    */
    function renderSelect()
    {
        $select = $this->_query->getSelect();
        $dontSelect = $this->_query->getDontSelect();
        return implode(',',array_diff($select,$dontSelect));
    }

}

?>
