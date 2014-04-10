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
 * @global string $id Http var : document id to trash
 * @global string $addft Http var : action to realize : [del]
 * @global string $paddid Http var : current folder of document comes
 */
function ws_countfolder(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    
    $mb = microtime();
    $docid = GetHttpVars("id");
    
    $action->lay->set("warning", "");
    $taction = array();
    /**
     * @var Dir $doc
     */
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc !== null && $doc->isAlive()) {
        $tc = $doc->getContent();
        $taction[] = array(
            "actname" => "RENAMEBRANCH",
            "actdocid" => '[' . $doc->id . ',' . "'" . sprintf("%s (%d)", $doc->title, count($tc)) . "']"
        );
    }
    $action->lay->setBlockData("ACTIONS", $taction);
    $action->lay->set("CODE", "OK");
    $action->lay->set("count", 1);
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
