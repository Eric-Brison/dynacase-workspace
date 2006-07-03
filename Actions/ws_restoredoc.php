<?php
/**
 * UnTrash document
 *
 * @author Anakeen 2006
 * @version $Id: ws_restoredoc.php,v 1.5 2006/07/03 12:14:09 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");
include_once("WORKSPACE/Lib.WsFtCommon.php");


/**
 * Get a doc from the trash
 * @param Action &$action current action
 * @global id Http var : document id to restore
 * @global containt Http var : if 'yes' restore also folder items 
 */
function ws_restoredoc(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $containt = (GetHttpVars("containt")=="yes");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");

  $doc=new_doc($dbaccess,$docid);

  if ($doc->isAffected()) {
    if (! $doc->isAlive()) {
      $err=$doc->revive();
    }
  } else $err=sprintf(_("document [%s] not found"));

  if ($err) $action->lay->set("warning",$err);
  $taction=array();
  if ($err==""){
    $taction[]=array("actname"=>"ADDFILE",
		     "actdocid"=>$doc->prelid);
    $taction[]=array("actname"=>"UNTRASHFILE",
		     "actdocid"=>getIdFromName($dbaccess,"WS_MYTRASH"));

    if ($containt && $doc->doctype=="D") {
      $terr=$doc->reviveItems();
    }
  }
  $action->lay->setBlockData("ACTIONS",$taction);
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}