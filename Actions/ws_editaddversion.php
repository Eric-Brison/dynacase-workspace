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
 * Display editor to modify HTML file
 * @param Action &$action current action
 * @global string $id Http var : document id for add version
 * @global string $attrid Http var : id of file attribute
 */
function ws_editaddversion(Action & $action)
{
    $docid = $action->getArgument("id");
    
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/ws_editaddversion.js");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/getdoc.js");
    
    $doc = new_doc($dbaccess, $docid);
    if (!$doc->isAlive()) $action->exitError(sprintf(_("Document %s is not alive") , $docid));
    
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
        foreach ($tfree as $k => $v) {
            $tstate[] = array(
                "fstate" => $v["initid"],
                "lstate" => $v["title"],
                "dstate" => nl2br(getv($v, "frst_desc"))
            );
        }
    }
    $action->lay->set("viewstate", ($doc->wid == 0));
    $state = $doc->getState();
    if ($state) $action->lay->set("textstate", sprintf(_("From %s state to") , $state));
    else $action->lay->set("textstate", _("New state"));
    
    $action->lay->setBlockData("freestate", $tstate);
}
