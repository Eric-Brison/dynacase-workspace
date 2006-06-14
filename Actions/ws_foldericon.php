<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_foldericon.php,v 1.9 2006/06/14 16:25:50 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");
include_once("WORKSPACE/Lib.WsFtCommon.php");


/**
 * Add/Move document to clipboard
 * @param Action &$action current action
 * @global id Http var : basket id
 * @global addid Http var : document id to add/move to basket id
 * @global paddid Http var : current folder of document comes 
 * @global addft Http var : action to realize : [add|move]
 */
function ws_foldericon(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $pdocid = GetHttpVars("paddid");
  $addid = GetHttpVars("addid");
  $addft = GetHttpVars("addft");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");

  switch ($docid) {
  case "lock":
    // test locked
    $doc=createTmpDoc($dbaccess,5);
    $doc->title="locked";
    $doc->Add();
    if ($action->user->id>1)  $doc->addQuery("select * from doc where abs(locked) = ".$action->user->id);
    else  $doc->addQuery("select * from doc where locked = ".$action->user->id);
    break;
  default:

    $doc=new_doc($dbaccess,$docid);
  }


  $err=movementDocument($action,$dbaccess,$doc->id,$addid,$pdocid,$addft);
  if ($err) $action->lay->set("warning",utf8_encode($err));

  $action->lay->set("pid",$docid);
  $action->lay->set("CODE","KO");

  $action->lay->set("droppable",($doc->doctype=="D")?"yes":"no");

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