<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("WORKSPACE/ws_navigate.php");
include_once ("WORKSPACE/ws_folderListFormat.php");
/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */
function ws_main(Action & $action)
{
    
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $nav = new ws_Navigate($action);
    $spaces = new SearchDoc($dbaccess, "WORKSPACE");
    $files = new SearchDoc($dbaccess, "SIMPLEFILE");
    
    $nav->setSpaces($spaces);
    $nav->setFolderListHeight($action->getParam("WS_COL3H1"));
    $nav->setFolderTreeHeight($action->getParam("WS_COL2H1"));
    $nav->setFolderTreeWidth($action->getParam("WS_ROW2W1"));
    $nav->setFolderListColumn("wsFolderListFormat::getColumnDescription()");
    $nav->setGlobalSearch($files);
    $action->lay->set("NAV", $nav->output());
}
