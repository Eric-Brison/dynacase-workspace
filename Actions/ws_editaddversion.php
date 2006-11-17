<?php
/**
 * Display interface to add a new version for simple file
 *
 * @author Anakeen 2006
 * @version $Id: ws_editaddversion.php,v 1.2 2006/11/17 14:54:15 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Display editor to modify HTML file
 * @param Action &$action current action
 * @global id Http var : document id for add version
 * @global attrid Http var : id of file attribute
 */
function ws_editaddversion(&$action) {
  $docid = GetHttpVars("id");

  $dbaccess = $action->GetParam("FREEDOM_DB");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/ws_editaddversion.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/getdoc.js");



  $doc=new_doc($dbaccess,$docid);
  if (!$doc->isAlive()) $action->exitError(sprintf(_("Document %s is not alive"),$docid));
    

  $err = $doc->lock(true); // autolock
  if ($err=="") $action->AddActionDone("LOCKFILE",$doc->id);
  
  if ($err != "") {    
      // test object permission before modify values (no access control on values yet)
      $err=$doc->CanUpdateDoc();
  }
  if ($err != "") $action->exitError($err);
  
  $action->lay->set("version",$doc->version);
  $action->lay->set("title",$doc->title);
  $action->lay->set("docid",$doc->id);

 // search free states
  $sqlfilters=array();
  $tfree = getChildDoc($dbaccess,0,"0","ALL",$sqlfilters, $action->user->id, "TABLE","FREESTATE");
  $tstate=array();
  if ($doc->wid == 0) {
    foreach ($tfree as $k=>$v) {
      $tstate[]=array("fstate"=>$v["initid"],
		      "lstate"=>$v["title"],
		      "dstate"=>nl2br(getv($v,"frst_desc")));
    }
  }
  $action->lay->set("viewstate",($doc->wid == 0));
  $state=$doc->getState();
  if ($state)   $action->lay->set("textstate",sprintf(_("From %s state to"),$state));
  else $action->lay->set("textstate",_("New state"));
    
  $action->lay->setBlockData("freestate",$tstate);


}