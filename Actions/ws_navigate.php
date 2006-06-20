<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_navigate.php,v 1.7 2006/06/20 16:18:54 eric Exp $
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
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/subwindow.js");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/AnchorPosition.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/common.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/popupdoc.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/inserthtml.js");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/DHTMLapi.js");



    // css pour popup
  $action->parent->AddCssRef("FDL:POPUP.CSS",true);
  $action->parent->AddCssRef("WORKSPACE:system.css",true);
  $action->parent->AddCssRef("WORKSPACE:default.css",true);
  

  $tspaces = getChildDoc($dbaccess,0,"0","ALL",array(), $action->user->id, "ITEM","WORKSPACE");
  $tlayspaces=array();
  while ($doc=getNextDoc($dbaccess,$tspaces)) {
    $tlayspaces[]=array("stitle"=>$doc->title,
			"sicon"=>$doc->getIcon(),
			"sid"=>$doc->id);
  }

  $action->lay->setBlockData("SPACES",$tlayspaces);
  if (trashempty($dbaccess,$action->user->id)) $action->lay->set("imgtrash",$action->getImageUrl('trashempty.png'));
  else $action->lay->set("imgtrash",$action->getImageUrl('trash.png'));


}

function trashempty($dbaccess,$userid) {
  $q=new QueryDb($dbaccess,"Doc");
  $q->Query(0,0,"TABLE",
	    sprintf("select id from doc where doctype='Z' and owner=%d limit 1",$userid));

  return ($q->nb == 0);

}



?>