<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_folderlist.php,v 1.1 2006/03/23 19:22:37 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * View list of documents from one folder
 * @param Action &$action current action
 * @global id Http var : basket id
 * @global addid Http var : document id to add/move to basket id
 * @global paddid Http var : current folder of document id to add/move to basket id
 * @global addft Http var : action to realize : [add|move]
 */
function ws_folderlist(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 

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
    $doc->addQuery("select * from doc where locked = ".$action->user->id);
    break;
  default:

    $doc=new_doc($dbaccess,$docid);
  }


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
		  "size"=>getv($v,"sfi_filesize"),
		  "mime"=>getv($v,"sfi_mimetxt"),
		  "mdate"=>strftime("%d %b %Y %H:%M",getv($v,"revdate")),
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