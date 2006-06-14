<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_emptytrash.php,v 1.2 2006/06/14 16:25:50 eric Exp $
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
function ws_emptytrash(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $pdocid = GetHttpVars("paddid");
  $addft = GetHttpVars("addft");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");

  $uid=$action->user->id;

  if ($uid > 0) {
    $q=new QueryDb($dbaccess,"Doc");
    $lq=$q->Query(0,0,"TABLE","delete from doc where doctype='Z' and owner=$uid");

    if (! $lq) $err=_("the trash cannot be empty");
  } else {
    $err=_("no user defined");
  }

  $err=movementDocument($action,$dbaccess,false,$docid,$pdocid,$addft);
  if ($err) $action->lay->set("warning",utf8_encode($err));
 
  if ($err=="") $taction[]=array("actname"=>"EMPTYTRASH",
				 "actdocid"=>$pdoc->initid);
  $action->lay->setBlockData("ACTIONS",$taction);
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}