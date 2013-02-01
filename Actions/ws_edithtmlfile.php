<?php
/*
 * Display editor to modify HTML file
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * Display editor to modify HTML file
 * @param Action &$action current action
 * @global string $id Http var : document id to edi
 * @global string $attrid Http var : id of file attribute
 */
function ws_edithtmlfile(Action & $action, $istext = false)
{
    $docid = $action->getArgument("id");
    $aid = $action->getArgument("attrid");
    
    $dbaccess = $action->GetParam("FREEDOM_DB");
    //  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/editattr.js");
    $doc = new_doc($dbaccess, $docid);
    if (!$doc->isAlive()) $action->exitError(sprintf(_("Document %s is not alive") , $docid));
    
    $err = $doc->lock(true); // autolock
    if ($err == "") $action->AddActionDone("LOCKDOC", $doc->id);
    
    if ($err != "") {
        // test object permission before modify values (no access control on values yet)
        $err = $doc->canEdit();
    }
    if ($err != "") $action->exitError($err);
    
    if ($aid == "") {
        
        $attr = $doc->GetFirstFileAttributes();
        $aid = $attr->id;
    }
    
    $fvalue = $doc->getRawValue($aid);
    $big = false;
    if (preg_match(PREGEXPFILE, $fvalue, $reg)) {
        $vaultid = $reg[2];
        $mimetype = $reg[1];
        
        $vf = newFreeVaultFile($dbaccess);
        $info = null;
        /**
         * @var VaultFileInfo $info
         */
        $err = $vf->Retrieve($vaultid, $info);
        if ($err != "") $action->exitError($err);
        
        $filename = $info->path;
        $content = file_get_contents($filename);
        
        $big = (strlen($content) > 100000);
        
        if (!$big) {
            if ($istext) $action->lay->set("fullhtml", $content);
            else $action->lay->set("fullhtml", str_replace(array(
                "'",
                "\n",
                "\r"
            ) , array(
                "\\'",
                "\\\n",
                ""
            ) , $content));
        }
    } else {
        $action->exitError(sprintf(_("%s attribute is not a file") , $aid));
    }
    
    $action->lay->set("edittitle", sprintf(_("%s : edit HTML file") , $doc->title));
    $action->lay->set("docid", $doc->id);
    $action->lay->set("attrid", $aid);
    $action->lay->set("BIG", $big);
}

function ws_edittextfile(&$action)
{
    ws_edithtmlfile($action, true);
}
?>
