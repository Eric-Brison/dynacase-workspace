<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_addfldbranch.php,v 1.8 2006/06/14 16:25:50 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package WORKSPACE
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");
include_once("WORKSPACE/Lib.WsFtCommon.php");


/**
 * Add branch in folder tree
 * @param Action &$action current action
 */
function ws_addfldbranch(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $addid = GetHttpVars("addid");
  $pdocid = GetHttpVars("paddid");
  $addft = GetHttpVars("addft");


  $dbaccess = $action->GetParam("FREEDOM_DB");
  $action->lay->set("warning","");
  $doc=new_doc($dbaccess,$docid);
  $err=movementDocument($action,$dbaccess,$doc->id,$addid,$pdocid,$addft);
  if ($err) $action->lay->set("warning",utf8_encode($err));
  

  $action->lay->set("pid",$doc->id);
  $action->lay->set("CODE","KO");
  if ($doc->isAlive()) {

    $ls=$doc->getContent(true,array("doctype ~ '^D|S$'"));
    $tc=array();
    foreach ($ls as $k=>$v) {
      $tc[]=array("title"=>utf8_encode($v["title"]),
		  "id"=>$v["id"],
		  "linkfld"=>($v["prelid"]==$doc->initid)?false:true,
		  "droppable"=>($v["doctype"]=="D")?"yes":"no",
		  "icon"=>$doc->getIcon($v["icon"]));
    }

    $action->lay->setBlockData("TREE",$tc);
    $action->lay->set("ulid",uniqid("ul"));
    $action->lay->set("CODE","OK"); 
    $taction=$action->lay->getBlockData("ACTIONS");
    if (count($taction) == 0) {
      $taction[]=array("actname"=>(count($tc)>0)?"ADDBRANCH":"EMPTYBRANCH",
		       "actdocid"=>$doc->initid);
      $action->lay->setBlockData("ACTIONS",$taction);
    }
  } else {
    $action->lay->set("CODE","NOTALIVE");
  }
  $action->lay->set("count",count($tc));
  $action->lay->set("delay",microtime_diff(microtime(),$mb));


}