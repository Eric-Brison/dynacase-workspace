<?php
/*
 * Display info before download file for editing and replace it
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
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
    $autoclose = ($action->getArgument("autoclose", "N") == "Y"); // close window after
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc === null || !$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist") , $docid));
    
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
