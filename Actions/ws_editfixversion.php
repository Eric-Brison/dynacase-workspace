<?php
/**
 * Display interface to add a new version for simple file
 *
 * @author Anakeen 2006
 * @version $Id: ws_editfixversion.php,v 1.1 2007/04/13 15:40:59 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Display editor to fix a document version
 * @param Action &$action current action
 * @global id Http var : document id for add version
 */
function ws_editfixversion(&$action) {
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
		      "sstate"=>($v["initid"]==$doc->state)?"selected":"",
		      "dstate"=>nl2br(getv($v,"frst_desc")));
    }
  }
  $action->lay->set("viewstate",($doc->wid == 0));
  $state=$doc->getState();
  
    
  $action->lay->setBlockData("freestate",$tstate);


}