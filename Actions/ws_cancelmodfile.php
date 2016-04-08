<?php
/*
 * Display info before download file for editing and replace it
 *
 * @author Anakeen
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * Display info before download
 * @param Action &$action current action
 * @global string $d Http var : document for file to edit (SIMPLEFILE family)
 */
function ws_cancelmodfile(Action & $action)
{
    
    $docid = $action->getArgument("id");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $autoclose = ($action->getArgument("autoclose", "N") == "Y"); // close window after
    $doc = new_doc($dbaccess, $docid);
    if (!$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist") , $docid));
    
    if ($doc->getRawValue('sfi_inedition') != 1) $action->exitError(sprintf(_("document %s is not in edition") , $docid));
    
    $err = $doc->control("edit");
    if ($err != "") $action->exiterror($err);
    
    $err = $doc->unlock(); // lock
    if ($err == "") {
        $action->AddActionDone("UNLOCKDOC", $doc->id);
        $doc->clearValue("sfi_inedition");
        $err = $doc->modify();
        if ($err == "") {
            $doc->addHistoryEntry(_("file modification aborted"));
        }
    }
    if (!$autoclose) redirect($action, "FDL", "FDL_CARD&id=" . $doc->id, $action->GetParam("CORE_STANDURL"));
}
?>
