<?php
/*
 * Download File in web client
 *
 * @author Anakeen
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
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $doc = new_doc($dbaccess, $docid);
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
