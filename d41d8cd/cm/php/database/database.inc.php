<?php
namespace Cm;
require_once dirname(__FILE__)."/../core/core.inc.php";

class DbException extends \Exception {

};


//https://www.ibm.com/support/knowledgecenter/SSEPEK_10.0.0/com.ibm.db2z10.doc.codes/src/tpc/db2z_sqlstatevalues.dita

//error codes 23xxx
class DbConstraintViolation extends DbException {}



require_once __DIR__.'/dbconnection.inc.php';
require_once __DIR__.'/dbquery.inc.php';


?>
