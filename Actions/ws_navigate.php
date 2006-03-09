<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_navigate.php,v 1.2 2006/03/09 16:13:10 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */
function ws_navigate(&$action) {

  $dbaccess = $action->GetParam("FREEDOM_DB");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/geometry.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/common.js");
 

  $tspaces = getChildDoc($dbaccess,0,"0","ALL",array(), $action->user->id, "ITEM","WORKSPACE");
  $tlayspaces=array();
  while ($doc=getNextDoc($dbaccess,$tspaces)) {
    $tlayspaces[]=array("stitle"=>$doc->title,
			"sicon"=>$doc->getIcon(),
			"sid"=>$doc->id);
  }

  $action->lay->setBlockData("SPACES",$tlayspaces);
  

}