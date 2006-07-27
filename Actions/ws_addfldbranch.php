<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_addfldbranch.php,v 1.14 2006/07/27 16:07:27 eric Exp $
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
 * @global addid Http var : document id to add/move to basket id
 * @global paddid Http var : current folder of document id to add/move to basket id
 * @global addft Http var : action to realize : [add|move]
 * @global itself Http var : if Y view the folder (not the content) [Y|N]
 */
function ws_addfldbranch(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $addid = GetHttpVars("addid");
  $pdocid = GetHttpVars("paddid");
  $addft = GetHttpVars("addft");
  $itself = (GetHttpVars("itself")=="Y");

  $dbaccess = $action->GetParam("FREEDOM_DB");
  $action->lay->set("warning","");
  $doc=new_doc($dbaccess,$docid);
  $err=movementDocument($action,$dbaccess,$doc->id,$addid,$pdocid,$addft);
  if ($err) $action->lay->set("warning",$err);
  

  $action->lay->set("pid",$doc->id);
  $action->lay->set("CODE","KO");
  $top=false;
  if ($doc->isAlive()) {
    if ($itself) {
      $ls=array();
      $ls[$docid]=getTDoc($dbaccess,$docid);

      $trash=new_doc($dbaccess,"WS_MYTRASH");
      $ls[$trash->id]=getTDoc($dbaccess,$trash->id);
      $ls[$trash->id]["dropft"]='del';
      $ls[$trash->id]["title"].="(".count($trash->getContent()).")";

      $trash=new_doc($dbaccess,"WS_MYTOVIEWDOC");
      $ls[$trash->id]=getTDoc($dbaccess,$trash->id);
      $ls[$trash->id]["title"].="(".count($trash->getContent()).")";

      $trash=new_doc($dbaccess,"WS_MYAFFECTDOC");
      $ls[$trash->id]=getTDoc($dbaccess,$trash->id);
      $ls[$trash->id]["title"].="(".count($trash->getContent()).")";

      $trash=new_doc($dbaccess,"WS_MYLOCKEDFILE");
      $ls[$trash->id]=getTDoc($dbaccess,$trash->id);
      $ls[$trash->id]["title"].="(".count($trash->getContent()).")";

      $trash=new_doc($dbaccess,$action->getParam("FREEDOM_IDBASKET"));
      $ls[$trash->id]=getTDoc($dbaccess,$trash->id);
      $ls[$trash->id]["title"].="(".count($trash->getContent()).")";
      $ls[$trash->id]["dropft"]='shortcut';
      $top=true; // to not see link in top view
    } else {
      $ls=$doc->getContent(true,array("doctype ~ '^D|S$'"));
    }
    $tc=array();
    foreach ($ls as $k=>$v) {
      $tc[]=array("title"=>ucfirst(utf8_encode($v["title"])),
		  "id"=>$v["id"],
		  "linkfld"=>($top || ($v["prelid"]==$doc->initid))?false:true,
		  "droppable"=>(($v["doctype"]=="D") || $v["dropft"])?"yes":"no",
		  "icon"=>$doc->getIcon($v["icon"]),
		  "dropft"=>$v["dropft"]?$v["dropft"]:"move");
    }

    $action->lay->setBlockData("TREE",$tc);
    $action->lay->set("ulid",uniqid("ul"));
    $action->lay->set("CODE","OK"); 
    $taction=$action->lay->getBlockData("ACTIONS");    
    $taction[]=array("actname"=>(count($tc)>0)?"ADDBRANCH":"EMPTYBRANCH",
		     "actdocid"=>$doc->initid);
    $action->lay->setBlockData("ACTIONS",$taction);    
  } else {
    $action->lay->set("CODE","NOTALIVE");
  }
  $action->lay->set("count",count($tc));
  $action->lay->set("delay",microtime_diff(microtime(),$mb));
}