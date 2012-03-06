<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
include_once ("WORKSPACE/Lib.WsFtCommon.php");
/**
 * Put a doc in trash
 * @param Action &$action current action
 * @global id Http var : document id to trash
 * @global addft Http var : action to realize : [del]
 * @global paddid Http var : current folder of document comes
 */
function ws_deletedoc(&$action)
{
    header('Content-type: text/xml; charset=utf-8');
    $action->lay->setEncoding("utf-8");
    
    $mb = microtime();
    $docid = GetHttpVars("id");
    $pdocid = GetHttpVars("paddid");
    $addft = GetHttpVars("addft", "del");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $action->lay->set("warning", "");
    $err = movementDocument($action, $dbaccess, false, $docid, $pdocid, $addft);
    if ($err) $action->lay->set("warning", $err);
    
    $action->lay->set("CODE", "OK");
    $action->lay->set("count", 1);
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
