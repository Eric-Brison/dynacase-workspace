<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @package WORKSPACE
*/

include_once ("FDL/Lib.Dir.php");
/**
 * view spaces to administrates them
 * @param Action &$action current action
 */
function admin(Action & $action)
{
    
    $fdoc = new_doc($action->dbaccess, "WORKSPACE");
    
    $s = new SearchDoc($action->dbaccess, "WORKSPACE");
    $s->setObjectReturn(false);
    $ls = $s->search();
    
    foreach ($ls as $k => $v) {
        $ls[$k]["ICON"] = $fdoc->getIcon($v["icon"]);
    }
    
    $action->lay->setBlockData("SPACES", $ls);
    $action->lay->set("ficon", $fdoc->geticon());
}
?>
