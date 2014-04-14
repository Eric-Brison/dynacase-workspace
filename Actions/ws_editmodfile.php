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
 * @global string $id Http var : document for file to edit (SIMPLEFILE family)
 */
function ws_editmodfile(Action & $action)
{
    
    $docid = $action->getArgument("id");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/ws_editmodfile.js");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/getdoc.js");
    $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/setparamu.js");
    
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc === null || !$doc->isAlive()) {
        $action->exitError(sprintf(_("document %s does not exist") , $docid));
    }
    
    if ($doc->getRawValue('sfi_inedition') == 1) $action->exitError(sprintf(_("document %s already in edition") , $docid));
    
    $filename = $doc->getRawValue("sfi_title");
    $action->lay->set("downloadtext", sprintf(_("Download <i>%s</i> file<br> for modification") , $filename));
    $action->lay->set("oktext", sprintf(_("The file %s has been downloaded and the document has been locked and tagged : in edition") , $filename));
    $action->lay->set("docid", $doc->id);
    $action->lay->set("autodownload", ($action->getParam('WS_AUTODOWNLOAD') == 'yes'));
}
