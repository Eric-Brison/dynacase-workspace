<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_emptytrash.php,v 1.4 2007/01/15 11:37:34 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
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
    $lq=$q->Query(0,0,"TABLE","begin;delete from docvaultindex where docid in (select id from doc where doctype='Z' and owner=$uid);delete from doc where doctype='Z' and owner=$uid;commit");

    if (! $lq) $err=_("the trash cannot be empty");
  } else {
    $err=_("no user defined");
  }

  $err=movementDocument($action,$dbaccess,false,$docid,$pdocid,$addft);
  if ($err) $action->lay->set("warning",$err);
 
  if ($err=="") $taction[]=array("actname"=>"EMPTYTRASH",
				 "actdocid"=>$pdoc->initid);
  $action->lay->setBlockData("ACTIONS",$taction);
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}