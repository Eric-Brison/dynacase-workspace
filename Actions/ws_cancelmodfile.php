<?php
/**
 * Display info before download file for editing and replace it
 *
 * @author Anakeen 2006
 * @version $Id: ws_cancelmodfile.php,v 1.1 2006/05/30 16:32:27 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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

  $filename=$doc->getValue("sfi_title");
  $action->lay->set("downloadtext",sprintf(_("Download <i>%s</i> file<br> for modification"),$filename));
  $action->lay->set("oktext",sprintf(_("The file %s has been downloaded and the document has been locked and tagged : in edition"),$filename));
  $action->lay->set("docid",$doc->id);
  $doc=new_doc($dbaccess,$docid);
  $err=$doc->control("edit");
  if ($err != "") $action->exiterror($err);

  $err = $doc->unlock(); // lock
  if ($err=="") {
    $action->AddActionDone("UNLOCKFILE",$doc->id);
    $doc->deleteValue("sfi_inedition");
    $err=$doc->modify();
    if ($err == "") {
      $doc->AddComment(_("file modification aborted"));
			      
    }
  } 
  if (! $autoclose)  redirect($action,"FDL","FDL_CARD&id=".$doc->id,$action->GetParam("CORE_STANDURL"));

  
}
?>