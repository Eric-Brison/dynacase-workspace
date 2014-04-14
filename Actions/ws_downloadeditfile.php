<?php
/*
 * Download File in web client
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("WORKSPACE/ws_downloadfile.php");
/**
 * Download the file from simplefile family document
 * @param Action &$action current action
 * @global string $id Http var : document id
 */
function ws_downloadeditfile(Action & $action)
{
    $docid = $action->getArgument("id");
    
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc === null || !$doc->isAlive()) {
        $action->exitError(sprintf(_("Document %s is not alive") , $docid));
    }
    $err = $doc->control("edit");
    if ($err != "") $action->exiterror($err);
    
    $err = $doc->lock(); // lock
    if ($err == "") {
        $action->AddActionDone("LOCKDOC", $doc->id);
        $doc->setValue("sfi_inedition", 1);
        $err = $doc->modify();
        if ($err == "") {
            global $_SERVER;
            $doc->addHistoryEntry(sprintf(_("%s file downloaded by %s on %s") , $doc->getRawValue("sfi_title") , $action->user->firstname . " " . $action->user->lastname, $_SERVER["REMOTE_ADDR"]));
        }
        ws_downloadfile($action);
    }
}
