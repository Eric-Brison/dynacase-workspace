<?php
/**
 * Display info before download file for editing and replace it
 *
 * @author Anakeen 2006
 * @version $Id: ws_cancelmodfile.php,v 1.2 2006/06/01 12:57:20 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Display info before download
 * @param Action &$action current action
 * @global id Http var : document for file to edit (SIMPLEFILE family)
 */
function ws_cancelmodfile(&$action) {
  
  $docid = GetHttpVars("id");
  $dbaccess = $action->GetParam("FREEDOM_DB");
  $autoclose = (GetHttpVars("autoclose","N")=="Y"); // close window after

  $doc=new_doc($dbaccess,$docid);
  if (!$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist"),$docid));

  if ($doc->getValue('sfi_inedition') != 1) $action->exitError(sprintf(_("document %s is not in edition"),$docid));


  $err=$doc->control("edit");
  if ($err != "") $action->exiterror($err);

  $err = $doc->unlock(); // lock
  if ($err=="") {
    $action->AddActionDone("UNLOCKDOC",$doc->id);
    $doc->deleteValue("sfi_inedition");
    $err=$doc->modify();
    if ($err == "") {
      $doc->AddComment(_("file modification aborted"));
			      
    }
  } 
  if (! $autoclose)  redirect($action,"FDL","FDL_CARD&id=".$doc->id,$action->GetParam("CORE_STANDURL"));

  
}
?>