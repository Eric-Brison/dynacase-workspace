<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
include_once ("WORKSPACE/Lib.WsFtCommon.php");
/**
 * Put a doc in trash
 * @param Action &$action current action
 * @global string $id Http var : document id to trash
 * @global string $addft Http var : action to realize : [del]
 * @global string $paddid Http var : current folder of document comes
 */
function ws_deletedoc(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    
    $mb = microtime();
    $docid = $action->getArgument("id");
    $pdocid = $action->getArgument("paddid");
    $addft = $action->getArgument("addft", "del");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $action->lay->set("warning", "");
    $err = movementDocument($action, $dbaccess, false, $docid, $pdocid, $addft);
    if ($err) $action->lay->set("warning", $err);
    
    $action->lay->set("CODE", "OK");
    $action->lay->set("count", 1);
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
