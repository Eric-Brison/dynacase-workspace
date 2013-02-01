<?php
/*
 * Search document and return list of them
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
include_once ("WORKSPACE/Lib.WsFtCommon.php");
/**
 * View list of documents from one folder
 * @param Action &$action current action
 * @global string $famid Http var : family id where search document
 * @global string $key Http var : filter key on the title
 */
function ws_search(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    
    $mb = microtime();
    $famid = $action->getArgument("famid");
    $key = $action->getArgument("key");
    $noids = explode('|', $action->getArgument("noids"));
    
    $action->lay->set("warning", "");
    $action->lay->set("CODE", "OK");
    $limit = 20;
    $filter[] = "title ~* '" . pg_escape_string($key) . "'";
    
    $s = new SearchDoc($action->dbaccess, $famid);
    $s->addFilter("title ~* '%s'", $key);
    $s->setSlice($limit);
    $lq = $s->search();
    
    foreach ($lq as $k => $v) {
        if (!in_array($v["id"], $noids)) {
            // $lq[$k]["title"] = $lq[$k]["title"];
            $lq[$k]["stitle"] = str_replace("'", "\\'", ($lq[$k]["title"]));
        } else {
            unset($lq[$k]);
        }
    }
    
    $action->lay->setBlockData("DOCS", $lq);
    
    $action->lay->set("count", count($lq));
    $action->lay->set("delay", microtime_diff(microtime() , $mb));
}
?>
