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
 * Add branch in folder tree
 * @param Action &$action current action
 * @global string $addid Http var : document id to add/move to basket id
 * @global string $paddid Http var : current folder of document id to add/move to basket id
 * @global string $addft Http var : action to realize : [add|move]
 * @global string $itself Http var : if Y view the folder (not the content) [Y|N]
 */
function ws_addfldbranch(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    
    $err = '';
    $mb = microtime();
    $docid = GetHttpVars("id");
    $addid = GetHttpVars("addid");
    $pdocid = GetHttpVars("paddid");
    $addft = GetHttpVars("addft");
    $itself = (GetHttpVars("itself") == "Y");
    
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $action->lay->set("warning", "");
    /**
     * @var Dir $doc
     */
    $doc = \Dcp\DocManager::getDocument($docid);
    if ($doc !== null) {
        \Dcp\DocManager::cache()->addDocument($doc);
        $err = movementDocument($action, $dbaccess, $doc->id, $addid, $pdocid, $addft);
    }
    if ($err) $action->lay->set("warning", $err);
    
    $action->lay->set("pid", (($doc !== null) ? $doc->id : ''));
    $action->lay->set("CODE", "KO");
    $top = false;
    
    $tc = array();
    if ($doc !== null && $doc->isAlive()) {
        if ($itself) {
            $ls = array();
            $ls[$docid] = \Dcp\DocManager::getRawDocument($docid);
            /**
             * @var Dir $trash
             */
            $trash = \Dcp\DocManager::getDocument("WS_MYTRASH");
            if ($trash !== null && $trash->isAlive()) {
                \Dcp\DocManager::cache()->addDocument($trash);
                $ls[$trash->id] = \Dcp\DocManager::getRawDocument($trash->id);
                $ls[$trash->id]["dropft"] = 'del';
                $ls[$trash->id]["title"].= "(" . count($trash->getContent()) . ")";
            }
            /**
             * @var Dir $mytoviewdoc
             */
            $mytoviewdoc = \Dcp\DocManager::getDocument("WS_MYTOVIEWDOC");
            if ($mytoviewdoc !== null && $mytoviewdoc->isAlive()) {
                \Dcp\DocManager::cache()->addDocument($mytoviewdoc);
                $ls[$mytoviewdoc->id] = \Dcp\DocManager::getRawDocument($mytoviewdoc->id);
                $ls[$mytoviewdoc->id]["title"].= "(" . count($mytoviewdoc->getContent()) . ")";
            }
            /**
             * @var Dir $myaffectdoc
             */
            $myaffectdoc = \Dcp\DocManager::getDocument("WS_MYAFFECTDOC");
            if ($myaffectdoc !== null && $myaffectdoc->isAlive()) {
                \Dcp\DocManager::cache()->addDocument($myaffectdoc);
                $ls[$myaffectdoc->id] = \Dcp\DocManager::getRawDocument($myaffectdoc->id);
                $ls[$myaffectdoc->id]["title"].= "(" . count($myaffectdoc->getContent()) . ")";
            }
            /**
             * @var Dir $mylockedfile
             */
            $mylockedfile = \Dcp\DocManager::getDocument("WS_MYLOCKEDFILE");
            if ($mylockedfile !== null && $mylockedfile->isAlive()) {
                \Dcp\DocManager::cache()->addDocument($mylockedfile);
                $ls[$mylockedfile->id] = \Dcp\DocManager::getRawDocument($mylockedfile->id);
                $ls[$mylockedfile->id]["title"].= "(" . count($mylockedfile->getContent()) . ")";
            }
            /**
             * @var Dir $basket
             */
            $basket = \Dcp\DocManager::getDocument($action->getParam("FREEDOM_IDBASKET"));
            if ($basket === null || !$basket->isAlive()) {
                /**
                 * @var Dir $fld
                 */
                $fld = \Dcp\DocManager::createTemporaryDocument("DIR");
                $basket = $fld->getHome();
            }
            \Dcp\DocManager::cache()->addDocument($basket);
            $ls[$basket->id] = \Dcp\DocManager::getRawDocument($basket->id);
            $ls[$basket->id]["title"].= "(" . count($basket->getContent()) . ")";
            $ls[$basket->id]["dropft"] = 'shortcut';
            /**
             * @var Dir $offline
             */
            $offline = \Dcp\DocManager::getDocument('FLDOFFLINE_' . $action->user->id);
            if ($offline !== null && $offline->isAlive()) {
                \Dcp\DocManager::cache()->addDocument($offline);
                $ls[$offline->id] = \Dcp\DocManager::getRawDocument($offline->id);
                $ls[$offline->id]["title"].= "(" . count($offline->getContent()) . ")";
                $ls[$offline->id]["dropft"] = 'shortcut';
            }
            $top = true; // to not see link in top view
            
        } else {
            $ls = $doc->getContent(true, array(
                "doctype ~ '^D|S$'"
            ));
            uasort($ls, "titlesort");
        }
        
        foreach ($ls as $v) {
            $tc[] = array(
                "title" => ucfirst($v["title"]) ,
                "id" => $v["id"],
                "linkfld" => ($top || ($v["prelid"] == $doc->initid)) ? false : true,
                "droppable" => (($v["doctype"] == "D") || (!empty($v["dropft"]))) ? "yes" : "no",
                "icon" => $doc->getIcon($v["icon"]) ,
                "haschild" => hasChildFld($dbaccess, $v["initid"], ($v["doctype"] == 'S')) ,
                "dropft" => (!empty($v["dropft"])) ? $v["dropft"] : "move"
            );
        }
        
        $action->lay->setBlockData("TREE", $tc);
        $action->lay->set("ulid", uniqid("ul"));
        $action->lay->set("CODE", "OK");
        $taction = $action->lay->getBlockData("ACTIONS");
        $taction[] = array(
            "actname" => (count($tc) > 0) ? "ADDBRANCH" : "EMPTYBRANCH",
            "actdocid" => $doc->initid
        );
        $taction[] = array(
            "actname" => "IMGRESIZE",
            "actdocid" => $doc->initid
        );
        $action->lay->setBlockData("ACTIONS", $taction);
    } else {
        $action->lay->set("CODE", "NOTALIVE");
    }
    $action->lay->set("count", count($tc));
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
function titlesort($a, $b)
{
    return strcasecmp($a["title"], $b["title"]);
}
