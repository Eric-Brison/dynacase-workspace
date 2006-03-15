<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_foldericon.php,v 1.2 2006/03/15 18:17:25 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Add/Move document to clipboard
 * @param Action &$action current action
 * @global id Http var : basket id
 * @global addid Http var : document id to add/move to basket id
 * @global paddid Http var : current folder of document id to add/move to basket id
 * @global addft Http var : action to realize : [add|move]
 */
function ws_foldericon(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 

  $mb=microtime();
  $docid = GetHttpVars("id");
  $pdocid = GetHttpVars("paddid");
  $addid = GetHttpVars("addid");
  $addft = GetHttpVars("addft");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");
  $doc=new_doc($dbaccess,$docid);


  if ($addid) {
    $adddoc=new_doc($dbaccess,$addid);
    if ($adddoc->isAlive()) {
      $err=$doc->AddFile($adddoc->id);
    }
    if ($err=="") {
      if ($addft == "move") {
	$pdoc=new_doc($dbaccess,$pdocid);
	if ($pdoc->isAlive()) {
	  $err=$pdoc->DelFile($adddoc->id);
	}
      }
    }
  }

  $action->lay->set("CODE","KO");
  if ($doc->isAlive()) {

    $ls=$doc->getContent();
    $tc=array();
    foreach ($ls as $k=>$v) {
      $tc[]=array("title"=>utf8_encode($v["title"]),
		  "id"=>$v["id"],
		  "icon"=>$doc->getIcon($v["icon"]));
    }

    $action->lay->setBlockData("TREE",$tc);
    $action->lay->set("ulid",uniqid("ul"));
    $action->lay->set("CODE","OK");
  } else {
    $action->lay->set("CODE","NOTALIVE");
  }
  $action->lay->set("count",count($tc));
  $action->lay->set("delay",microtime_diff(microtime(),$mb));


}