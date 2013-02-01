<?php
/*
 * View Document
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/Class.Doc.php");
include_once ("FDL/fdl_card.php");
/**
 * View a document
 * @param Action &$action current action
 * @global string $id Http var : document identificator to see
 * @global string $latest Http var : (Y|N|L|P) if Y force view latest revision, L : latest fixed revision, P : previous revision
 * @global string $abstract Http var : (Y|N) if Y view only abstract attribute
 * @global string $props Http var : (Y|N) if Y view properties also
 * @global string $zonebodycard Http var : if set, view other specific representation
 * @global string $vid Http var : if set, view represention describe in view control (can be use only if doc has controlled view)
 * @global string $ulink Http var : (Y|N)if N hyperlink are disabled
 * @global string $target Http var : is set target of hyperlink can change (default _self)
 * @global string $reload Http var : (Y|N) if Y update freedom folders in client navigator
 * @global string $dochead Http var :  (Y|N) if N don't see head of document (not title and icon)
 */
function ws_viewdoc(Action & $action)
{
    header('Content-type: text/xml; charset=utf-8');
    fdl_card($action);
    $action->lay->set("count", 1);
    $action->lay->set("CODE", "OK");
    $action->lay->set("warning", "");
    $action->lay->set("delay", "1");
    $a = _("object");
}
?>
