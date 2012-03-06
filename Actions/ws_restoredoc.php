<?php
/*
 * UnTrash document
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
include_once ("WORKSPACE/Lib.WsFtCommon.php");
/**
 * Get a doc from the trash
 * @param Action &$action current action
 * @global id Http var : document id to restore
 * @global reload Http var : [Y|N] if Y not xml but redirect to fdl_card
 * @global containt Http var : if 'yes' restore also folder items
 */
function ws_restoredoc(&$action)
{
    header('Content-type: text/xml; charset=utf-8');
    $action->lay->setEncoding("utf-8");
    
    $mb = microtime();
    $docid = GetHttpVars("id");
    $reload = (GetHttpVars("reload") == "Y");
    $containt = (GetHttpVars("containt") == "yes");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $action->lay->set("warning", "");
    
    $doc = new_doc($dbaccess, $docid);
    
    if ($doc->isAffected()) {
        if (!$doc->isAlive()) {
            $err = $doc->revive();
        }
    } else $err = sprintf(_("document [%s] not found"));
    
    if ($err) $action->lay->set("warning", $err);
    $taction = array();
    if ($err == "") {
        if ($reload) {
            $action->AddActionDone("ADDFILE", $doc->prelid);
            $action->AddActionDone("UNTRASHFILE", getIdFromName($dbaccess, "WS_MYTRASH"));
        } else {
            $taction[] = array(
                "actname" => "ADDFILE",
                "actdocid" => $doc->prelid
            );
            $taction[] = array(
                "actname" => "UNTRASHFILE",
                "actdocid" => getIdFromName($dbaccess, "WS_MYTRASH")
            );
        }
        if ($containt && $doc->doctype == "D") {
            $terr = $doc->reviveItems();
        }
    }
    
    if ($reload) {
        redirect($action, "FDL", "FDL_CARD&sole=Y&refreshfld=Y&id=$docid");
        exit;
    }
    
    $action->lay->setBlockData("ACTIONS", $taction);
    $action->lay->set("CODE", "OK");
    $action->lay->set("count", 1);
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
?>
