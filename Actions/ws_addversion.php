<?php
/**
 * Create new version for simple file
 *
 * @author Anakeen 2006
 * @version $Id: ws_addversion.php,v 1.1 2006/06/01 12:57:20 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Create new version
 * @param Action &$action current action
 * @global id Http var : document for file to edit (SIMPLEFILE family)
 */
function ws_addversion(&$action) {
  
  $docid = GetHttpVars("id");
  $newversion = GetHttpVars("newversion");
  $newcomment = GetHttpVars("comversion");
  $autoclose = (GetHttpVars("autoclose","N")=="Y"); // close window after
  $dbaccess = $action->GetParam("FREEDOM_DB");

  $doc=new_doc($dbaccess,$docid);
  if (!$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist"),$docid));



  $err=$doc->control("edit");
  if ($err != "") $action->exiterror($err);

  $err = $doc->unlock(true); // lock
  if ($err=="") {

    $action->AddActionDone("UNLOCKFILE",$doc->id);
    if ($err == "") {
      $err=$doc->AddRevision($newcomment);
      if ($err=="") {
	$doc->setValue("sfi_version",$newversion);
	$err=$doc->modify();
      }
    }
  } 
  if ($err) $action->AddWarningMsg($err);
  if (! $autoclose)  redirect($action,"FDL","FDL_CARD&id=".$doc->id,$action->GetParam("CORE_STANDURL"));

  
}
?>