<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: admin.php,v 1.3 2007/10/15 16:34:25 eric Exp $
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
  foreach ($ls as $k=>$v) {
    $ls[$k]["ICON"]=$fdoc->getIcon($v["icon"]);
  }

  $action->lay->setBlockData("SPACES",$ls);
  $action->lay->set("ficon",$fdoc->geticon());
}
?>