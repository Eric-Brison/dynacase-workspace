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
function admin(&$action)
{
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $fdoc = new_doc($dbaccess, "WORKSPACE");
    
    $filter = array();
    $ls = getChildDoc($dbaccess, 0, 0, "ALL", $filter, $action->user->id, "TABLE", "WORKSPACE");
    foreach ($ls as $k => $v) {
        $ls[$k]["ICON"] = $fdoc->getIcon($v["icon"]);
    }
    
    $action->lay->setBlockData("SPACES", $ls);
    $action->lay->set("ficon", $fdoc->geticon());
}
?>
