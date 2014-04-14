<?php
/*
 * Display interface to add a new version for simple file
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * Display editor to fix a document version
 * @param Action &$action current action
 * @global string $id Http var : document id for add version
 */
function ws_editfixversion(Action & $action)
{
    $docid = $action->getArgument("id");
    
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/ws_editaddversion.js");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/getdoc.js");
    
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc === null || !$doc->isAlive()) {
        $action->exitError(sprintf(_("Document %s is not alive") , $docid));
    }
    
    $err = $doc->lock(true); // autolock
    if ($err == "") $action->AddActionDone("LOCKDOC", $doc->id);
    
    if ($err != "") {
        // test object permission before modify values (no access control on values yet)
        $err = $doc->canEdit();
    }
    if ($err != "") $action->exitError($err);
    
    $action->lay->set("version", $doc->version);
    $action->lay->set("title", $doc->title);
    $action->lay->set("docid", $doc->id);
    // search free states
    $s = new SearchDoc($action->dbaccess, "FREESTATE");
    $s->setObjectReturn(false);
    $tfree = $s->search();
    $tstate = array();
    if ($doc->wid == 0) {
        foreach ($tfree as $v) {
            $tstate[] = array(
                "fstate" => $v["initid"],
                "lstate" => $v["title"],
                "sstate" => ($v["initid"] == $doc->state) ? "selected" : "",
                "dstate" => nl2br(getv($v, "frst_desc"))
            );
        }
    }
    $action->lay->set("viewstate", ($doc->wid == 0));
    
    $action->lay->setBlockData("freestate", $tstate);
}
