<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_deletedoc.php,v 1.1 2006/04/06 16:48:23 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Put a doc in trash
 * @param Action &$action current action
 * @global id Http var : document id to trash
 * @global paddid Http var : current folder of document comes 
 */
function ws_deletedoc(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 

  $mb=microtime();
  $docid = GetHttpVars("id");
  $pdocid = GetHttpVars("paddid");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");

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
  

  $action->lay->set("count",count($tc));
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}