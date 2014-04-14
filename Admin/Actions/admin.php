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
 * view spaces to administrates them
 * @param Action &$action current action
 */
function admin(Action & $action)
{
    
    $fdoc = \Dcp\DocManager::getFamily("WORKSPACE");
    if ($fdoc === null) {
        $action->exitError(sprintf(_("Document %s is not alive") , "WORKSPACE"));
    }
    \Dcp\DocManager::cache()->addDocument($fdoc);
    
    $s = new SearchDoc($action->dbaccess, "WORKSPACE");
    $s->setObjectReturn(false);
    $ls = $s->search();
    
    foreach ($ls as $k => $v) {
        $ls[$k]["ICON"] = $fdoc->getIcon($v["icon"]);
    }
    
    $action->lay->setBlockData("SPACES", $ls);
    $action->lay->set("ficon", $fdoc->geticon());
}
