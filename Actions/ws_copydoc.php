<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * duplicate a documment
 * @param Action &$action current action
 * @global string $id Http var : document id to trash
 * @global string $addft Http var : action to realize : [del]
 * @global string $paddid Http var : current folder of document comes
 */
function ws_copydoc(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    
    $mb = microtime();
    $docid = GetHttpVars("id");
    $dirid = GetHttpVars("paddid");
    
    $action->lay->set("warning", "");
    $taction = array();
    $copy = null;
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc === null) {
        $err = sprintf(_("cannot duplicate %s document") , $docid);
    } else {
        $copy = $doc->duplicate();
        if (is_object($copy)) {
            $copy->refresh();
            if (method_exists($copy, "renameCopy")) $copy->renameCopy();
            $copy->poststore();
            $err = $copy->modify();
        } else {
            $err = sprintf(_("cannot duplicate %s document") , $doc->title);
            $copy = null;
        }
    }
    
    if ($copy !== null) {
        if (($dirid == 0) && ($copy->id > 0)) {
            $dirid = $doc->prelid;
        }
        if (($dirid > 0) && ($copy->id > 0)) {
            /**
             * @var Dir $fld
             */
            $fld = \Dcp\DocManager::getDocument($dirid);
            if ($fld !== null) {
                $err = $fld->insertDocument($copy->id);
                $taction[] = array(
                    "actname" => "ADDFILE",
                    "actdocid" => $dirid
                );
            }
        }
    }
    
    if ($err) $action->lay->set("warning", $err);
    
    $action->lay->setBlockData("ACTIONS", $taction);
    $action->lay->set("CODE", "OK");
    $action->lay->set("count", 1);
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
