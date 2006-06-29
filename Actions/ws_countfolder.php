<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_countfolder.php,v 1.1 2006/06/29 14:23:33 eric Exp $
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
function ws_countfolder(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $dbaccess = $action->GetParam("FREEDOM_DB");


  $action->lay->set("warning","");

  $doc=new_doc($dbaccess,$docid);
  if ($doc->isAlive()) {
    $tc=$doc->getContent();
    $taction[]=array("actname"=>"RENAMEBRANCH",
		     "actdocid"=>'['.$doc->id.','."'".utf8_encode(sprintf("%s (%d)",$doc->title,count($tc)))."']");
  }
  $action->lay->setBlockData("ACTIONS",$taction);
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}