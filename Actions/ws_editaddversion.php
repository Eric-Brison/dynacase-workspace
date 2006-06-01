<?php
/**
 * Display interface to add a new version for simple file
 *
 * @author Anakeen 2006
 * @version $Id: ws_editaddversion.php,v 1.1 2006/06/01 12:57:20 eric Exp $
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


}