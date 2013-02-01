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
 * Add/Move document to clipboard
 * @param Action &$action current action
 * @global string $id Http var : basket id
 * @global string $addid Http var : document id to add/move to basket id
 * @global string $paddid Http var : current folder of document comes
 * @global string $addft Http var : action to realize : [add|move]
 */
function ws_foldericon(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    
    $mb = microtime();
    $docid = $action->getArgument("id");
    $pdocid = $action->getArgument("paddid");
    $addid = $action->getArgument("addid");
    $addft = $action->getArgument("addft");
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $action->lay->set("warning", "");
    
    switch ($docid) {
        case "lock":
            // test locked
            
            /**
             * @var DocSearch $doc
             */
            $doc = createTmpDoc($dbaccess, 5);
            $doc->title = "locked";
            $doc->Add();
            if ($action->user->id > 1) $doc->addQuery("select * from doc where abs(locked) = " . $action->user->id);
            else $doc->addQuery("select * from doc where locked = " . $action->user->id);
            break;

        default:
            
            $doc = new_doc($dbaccess, $docid);
        }
        
        $err = movementDocument($action, $dbaccess, $doc->id, $addid, $pdocid, $addft);
        if ($err) $action->lay->set("warning", $err);
        
        $action->lay->set("pid", $docid);
        $action->lay->set("CODE", "KO");
        
        $action->lay->set("droppable", ($doc->doctype == "D") ? "yes" : "no");
        $tc = array();
        if ($doc->isAlive()) {
            
            $ls = $doc->getContent();
            foreach ($ls as $k => $v) {
                $tc[] = array(
                    "title" => $v["title"],
                    "id" => $v["id"],
                    "folder" => ($v["doctype"] == 'D') ,
                    "icon" => $doc->getIcon($v["icon"])
                );
            }
            
            $action->lay->setBlockData("TREE", $tc);
            $action->lay->set("ulid", uniqid("ul"));
            $action->lay->set("CODE", "OK");
        } else {
            $action->lay->set("CODE", "NOTALIVE");
        }
        $action->lay->set("count", count($tc));
        $action->lay->set("delay", microtime_diff(microtime() , $mb));
    }
    
    