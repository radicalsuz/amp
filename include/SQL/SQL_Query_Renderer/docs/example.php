<?php
/* interesting reads
    http://docs.xaraya.com/docs/rfcs/rfc0035.html
	
	this is what it will become one day
	http://www.korzh.com/delphi/sq/images/ssmain1.gif
*/

//
//  a join from ContaX
//
/* QT0.9x its like this
$this->DB_QueryTool(TABLE_CONTACT);
$this->autoJoin( array(TABLE_CONTACT2TREE,TABLE_CONTACTTREE) );
$this->setLeftJoin(TABLE_EMAIL,TABLE_EMAIL.'.contact_id=id AND '.TABLE_EMAIL.'.primaryMail=1');
$this->setWhere(TABLE_CONTACTTREE.".user_id=$userId");
$this->setOrder('surname,name');
*/
ini_set('error_reporting',E_ALL);
ini_set('include_path',realpath(dirname(__FILE__).'/../../..'));

#define('DB_DSN',    'mysql://root@localhost/test');
$tableStructures = array(
                        
                        );

require_once 'SQL/Query.php';
require_once 'SQL/Query/Join.php';
require_once 'SQL/Query/Condition.php';

/*$aliases = array(
				TABLE_CONTACT       =>  'contact'
				,TABLE_CONTACT2TREE  =>  'contact2tree'
				,TABLE_CONTACTTREE   =>  'contactTree'
				);
*/				
#DB_QueryTool::addAliases($aliases);

$query =& new SQL_Query_Join('contact');
$query->autoJoin('contact2tree','contactTree');
$query->addLeftJoin('email',new SQL_Query_Condition('email.contact_id','=','id','AND','email.primaryMail','=',1));
$query->addWhere('contactTree.user_id','=',7);
$query->addOrder('surname,name');



// this is just to demonstrate nested where clauses, the query doesnt really make sense, at least not to me :-)
// SELECT * FROM user WHERE (name LIKE 'n%' AND name LIKE 'a%') OR name LIKE 's%'

$query =& new SQL_Query('user');
$c1 = $query->condition('name','LIKE','"n%"');
$c1->add('name','LIKE','"a%"','AND');
$query->addWhere($c1,'OR',$query->condition('name','LIKE','"%s"'));


require_once 'SQL/Query/Renderer.php';
$render =& SQL_Query_Renderer::factory($query,$tableStructures);
print $render->toString();

print "<pre>";
print_r($query);
?>
