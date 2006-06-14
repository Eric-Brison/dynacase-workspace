<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_copydoc.php,v 1.3 2006/06/14 16:25:50 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");



/**
 * duplicate a documment
 * @param Action &$action current action
 * @global id Http var : document id to trash
 * @global addft Http var : action to realize : [del]
 * @global paddid Http var : current folder of document comes 
 */
function ws_copydoc(&$action) {
  header('Content-type: text/xml; charset=utf-8'); 
  $action->lay->setEncoding("utf-8");

  $mb=microtime();
  $docid = GetHttpVars("id");
  $dirid = GetHttpVars("paddid");
  $addft = GetHttpVars("addft","del");
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $action->lay->set("warning","");
  $taction=array();
  $doc= new_Doc($dbaccess, $docid);
  $copy= $doc->copy();
  if (is_object($copy)) {
    $copy->refresh();
    $copy->postmodify();
    $err=$copy->modify();
    
  } else {
    
    $err=sprintf(_("cannot duplicate %s document"),$doc->title);
  }

  if ($err=="") {
    if (($dirid == 0) && ($copy->id > 0)) {
      $dirid=$doc->prelid;
    }
    if (($dirid > 0) && ($copy->id > 0)) {
      $fld = new_Doc($dbaccess, $dirid);    
      $err = $fld->AddFile($copy->id);
      $taction[]=array("actname"=>"ADDFILE",
		       "actdocid"=>$dirid);
    }
  }

  if ($err) $action->lay->set("warning",utf8_encode($err));
  
  $action->lay->setBlockData("ACTIONS",$taction);
  $action->lay->set("CODE","OK");
  $action->lay->set("count",1);
  $action->lay->set("delay",microtime_diff(microtime(),$mb));					

}