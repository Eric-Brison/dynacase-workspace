<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: admin.php,v 1.1 2006/07/21 15:27:41 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");



/**
 * view spaces to administrates them
 * @param Action &$action current action
 */
function admin(&$action) {  
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $fdoc=new_doc($dbaccess,"WORKSPACE");
  


  $filter=array();
  $ls = getChildDoc($dbaccess, 0 ,0,"ALL", $filter, $action->user->id, "TABLE","WORKSPACE");
  
  $action->lay->setBlockData("SPACES",$ls);
  $action->lay->set("ficon",$fdoc->geticon());

}