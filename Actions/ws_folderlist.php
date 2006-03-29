<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_folderlist.php,v 1.3 2006/03/29 14:52:00 eric Exp $
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

  $action->lay->set("pid",$doc->initid);
  $action->lay->set("CODE","KO");
  if ($doc->isAlive()) {

    $ls=$doc->getContent();
    $tc=array();
    foreach ($ls as $k=>$v) {
      $size=getv($v,"sfi_filesize",-1);
      if ($size < 0) $dsize="";
      else if ($size < 1024) $dsize=sprintf(_("%d bytes"),$size);
      else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
      else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
   //    $icon=getv($v,"sfi_mimeicon");
//       if (! $icon) $icon=$doc->getIcon($v["icon"]);
//       else $icon=$doc->getIcon($icon);

	$icon=$doc->getIcon($v["icon"]);


      $tc[]=array("title"=>utf8_encode($v["title"]),
		  "id"=>$v["id"],
		  "size"=>$dsize,
		  "mime"=>getv($v,"sfi_mimetxt"),
		  "mdate"=>utf8_encode(strftime("%d %b %Y %H:%M",getv($v,"revdate"))),
		  "icon"=>$icon);
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