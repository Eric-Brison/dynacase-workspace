<?php
/*
 * Create new version for simple file
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * Create new version
 * @param Action &$action current action
 * @global id Http var : document for file to edit (SIMPLEFILE family)
 * @global newstate Http var : new state
 */
function ws_fixversion(&$action)
{
    
    $docid = GetHttpVars("id");
    $newversion = GetHttpVars("newversion");
    $newcomment = GetHttpVars("comversion");
    $newstate = GetHttpVars("newstate");
    $autoclose = (GetHttpVars("autoclose", "N") == "Y"); // close window after
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $doc = new_doc($dbaccess, $docid);
    if (!$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist") , $docid));
    
    $err = $doc->control("edit");
    if ($err != "") $action->exiterror($err);
    
    $err = $doc->unlock(true); // lock
    if ($err == "") {
        
        $action->AddActionDone("UNLOCKDOC", $doc->id);
        if ($err == "") {
            $doc->setValue("sfi_version", $newversion);
            $err = $doc->modify();
            if ($err == "") {
                if (($newstate >= 0) && ($newstate != $doc->state)) {
                    $err = $doc->changeFreeState($newstate, $commentstate, false);
                    if ($err != "") $action->addWarningMsg($err);
                }
                if ($err == "") {
                    $err = $doc->AddRevision($newcomment);
                    $doc->deleteValue("sfi_version");
                    $doc->state = '';
                    $err = $doc->modify();
                }
            }
        }
    }
    if ($err) $action->AddWarningMsg($err);
    if (!$autoclose) redirect($action, "FDL", "FDL_CARD&id=" . $doc->id, $action->GetParam("CORE_STANDURL"));
}
?>
