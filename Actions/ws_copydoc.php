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
    $addft = GetHttpVars("addft", "del");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $action->lay->set("warning", "");
    $taction = array();
    $doc = new_Doc($dbaccess, $docid);
    $copy = $doc->duplicate();
    if (is_object($copy)) {
        $copy->refresh();
        if (method_exists($copy, "renameCopy")) $copy->renameCopy();
        $copy->poststore();
        $err = $copy->modify();
    } else {
        
        $err = sprintf(_("cannot duplicate %s document") , $doc->title);
    }
    
    if ($err == "") {
        if (($dirid == 0) && ($copy->id > 0)) {
            $dirid = $doc->prelid;
        }
        if (($dirid > 0) && ($copy->id > 0)) {
            /**
             * @var Dir $fld
             */
            $fld = new_Doc($dbaccess, $dirid);
            $err = $fld->insertDocument($copy->id);
            $taction[] = array(
                "actname" => "ADDFILE",
                "actdocid" => $dirid
            );
        }
    }
    
    if ($err) $action->lay->set("warning", $err);
    
    $action->lay->setBlockData("ACTIONS", $taction);
    $action->lay->set("CODE", "OK");
    $action->lay->set("count", 1);
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
