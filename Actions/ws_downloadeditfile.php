<?php
/**
 * Download File in web client
 *
 * @author Anakeen 2006
 * @version $Id: ws_downloadeditfile.php,v 1.1 2006/05/30 16:32:27 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
 * @subpackage 
 */
 /**
 */




include_once("WORKSPACE/ws_downloadfile.php");


/**
 * Download the file from simplefile family document
 * @param Action &$action current action
 * @global id Http var : document id
 */
function ws_downloadeditfile(&$action) {
  $docid = GetHttpVars("id");
  $dbaccess = $action->GetParam("FREEDOM_DB");
  
  $doc=new_doc($dbaccess,$docid);
  $err=$doc->control("edit");
  if ($err != "") $action->exiterror($err);

  $err = $doc->lock(); // lock
  if ($err=="") {
    $action->AddActionDone("LOCKDOC",$doc->id);
    $doc->setValue("sfi_inedition",1);
    $err=$doc->modify();
    if ($err == "") {
      global $_SERVER;
      $doc->AddComment(sprintf(_("%s file downloaded by %s on %s"),
			      $doc->getValue("sfi_title"),
			      $action->user->firstname." ".$action->user->lastname,
			      $_SERVER["REMOTE_ADDR"]));
			      
    }
    ws_downloadfile($action);
  }
}