<?php
/*
 * Modify HTML file from editor
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * Modify file attribute from HTML editor
 * @param Action &$action current action
 * @global id Http var : document id to modify
 * @global attrid Http var : id of file attribute
 */
function ws_modhtmlfile(&$action)
{
    $docid = GetHttpVars("id");
    $aid = GetHttpVars("attrid");
    $value = GetHttpVars("wsfile");
    
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $doc = new_doc($dbaccess, $docid);
    if (!$doc->isAlive()) $action->exitError(sprintf(_("Document %s is not alive") , $docid));
    
    if ($value != "") {
        
        if ($err != "") {
            // test object permission before modify values (no access control on values yet)
            $err = $doc->CanUpdateDoc();
        }
        
        if ($err == "") {
            $a = $doc->getAttribute($attrid);
            if (!$a) $err = sprintf(_("unknown attribute %s for document %s") , $attrid, $doc->title);
            if ($err == "") {
                $vis = $a->mvisibility;
                if (strstr("WO", $vis) === false) $err = sprintf(_("visibility %s does not allow modify attribute %s for document %s") , $vis, $a->labelText, $doc->title);
                if ($err == "") {
                    //$err=$doc->setValue($attrid,$value);
                    if ($err == "") {
                        //$err=$doc->modify();
                        if ($err == "") $doc->AddComment(sprintf(_("modify [%s] attribute") , $a->labelText));
                    }
                }
                $action->lay->set("thetext", $doc->getHtmlAttrValue($attrid));
            }
        }
    } else {
        $action->lay->set("thetext", $doc->getHtmlAttrValue($attrid));
    }
}
?>
