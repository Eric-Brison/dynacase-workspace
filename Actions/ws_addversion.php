<?php
/*
 * Create new version for simple file
 *
 * @author Anakeen
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * Create new version
 * @param Action &$action current action
 * @global string $id Http var : document for file to edit (SIMPLEFILE family)
 * @global string $newstate Http var : new state
 */
function ws_addversion(Action & $action)
{
    
    $docid = $action->getArgument("id");
    $newversion = $action->getArgument("newversion");
    $newcomment = $action->getArgument("comversion");
    $newstate = $action->getArgument("newstate");
    $autoclose = ($action->getArgument("autoclose", "N") == "Y"); // close window after
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $doc = new_doc($dbaccess, $docid);
    if (!$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist") , $docid));
    
    $err = $doc->control("edit");
    if ($err != "") $action->exiterror($err);
    
    $err = $doc->unlock(true); // lock
    if ($err == "") {
        
        $action->AddActionDone("UNLOCKDOC", $doc->id);
        if ($err == "") {
            $err = $doc->revise($newcomment);
            if ($err == "") {
                $doc->setValue("sfi_version", $newversion);
                $err = $doc->modify();
            }
        }
    }
    if ($err) $action->AddWarningMsg($err);
    else {
        if ($newstate >= 0) {
            $commentstate = '';
            $err = $doc->changeFreeState($newstate, $commentstate, false);
            if ($err != "") $action->addWarningMsg($err);
            else {
                $action->addWarningMsg(sprintf(_("document %s has the new state %s") , $doc->title, $doc->getState()));
            }
        }
    }
    if (!$autoclose) redirect($action, "FDL", "FDL_CARD&id=" . $doc->id, $action->GetParam("CORE_STANDURL"));
}
?>
