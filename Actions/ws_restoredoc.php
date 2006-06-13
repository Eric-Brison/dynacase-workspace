<?php
/**
 * UnTrash document
 *
 * @author Anakeen 2006
 * @version $Id: ws_restoredoc.php,v 1.2 2006/06/13 15:48:00 eric Exp $
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

  if ($err) $action->lay->set("warning",utf8_encode($err));
  $taction=array();
  if ($err==""){
    $taction[]=array("actname"=>"ADDFILE",
		     "actdocid"=>$doc->prelid);
    $taction[]=array("actname"=>"DELFILE",
		     "actdocid"=>'trash');

    if ($containt && $doc->doctype=="D") {
      $terr=$doc->reviveItems();
    }
  }
  $action->lay->setBlockData("ACTIONS",$taction);
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}