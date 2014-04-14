<?php
/*
 * Download File in web client
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/exportfile.php");
/**
 * Download the file from simplefile family document
 * @param Action &$action current action
 * @global string $id Http var : document id
 */
function ws_downloadfile(&$action)
{
    $docid = $action->getArgument("id");
    $inline = ($action->getArgument("inline") == "yes");
    
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc === null || !$doc->isAlive()) {
        $action->exitError(sprintf(_("Document %s is not alive") , $docid));
    }
    $err = $doc->control("view");
    if ($err != "") $action->exiterror($err);
    
    $ovalue = $doc->getRawValue("sfi_file");
    
    if ($ovalue == "") $action->exiterror(_("no file referenced"));
    
    preg_match(PREGEXPFILE, $ovalue, $reg);
    $vaultid = $reg[2];
    // $mimetype=$reg[1];
    $mimetype = $doc->getRawValue("sfi_mimesys");
    
    DownloadVault($action, $vaultid, true, $mimetype, $width = '', $inline, false);
    exit;
}
