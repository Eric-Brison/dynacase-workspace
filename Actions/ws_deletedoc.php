<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_deletedoc.php,v 1.2 2006/04/20 06:58:46 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");
include_once("WORKSPACE/Lib.WsFtCommon.php");


/**
 * Put a doc in trash
 * @param Action &$action current action
 * @global id Http var : document id to trash
 * @global addft Http var : action to realize : [del]
 * @global paddid Http var : current folder of document comes 
 */
function ws_deletedoc(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 

  $mb=microtime();
  $docid = GetHttpVars("id");
  $pdocid = GetHttpVars("paddid");
  $addft = GetHttpVars("addft");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");
  $err=movementDocument($dbaccess,false,$docid,$pdocid,$addft);
  if ($err) $action->lay->set("warning",utf8_encode($err));
  /*
  $pdoc=new_doc($dbaccess,$pdocid);
  
  if ($pdoc->isAlive()) {

    $doc=new_doc($dbaccess,$docid);
    if ($doc->isAlive()) {
      $err=$pdoc->DelFile($doc->id);
      //$err=$doc->delete(); 
    }
  } else {
    $action->lay->set("CODE","NOTALIVE");
  }
  if ($err == "") {
    $action->lay->set("CODE","OK");
    
  } else {
    $action->lay->set("CODE","NOTALIVE");
    $action->lay->set("warning",utf8_encode($err));
  }
  */
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}