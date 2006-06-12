<?php
/**
 * View Document
 *
 * @author Anakeen 2000 
 * @version $Id: ws_viewdoc.php,v 1.2 2006/06/12 16:03:51 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Class.Doc.php");
include_once("FDL/fdl_card.php");


/**
 * View a document
 * @param Action &$action current action
 * @global id Http var : document identificator to see
 * @global latest Http var : (Y|N|L|P) if Y force view latest revision, L : latest fixed revision, P : previous revision
 * @global abstract Http var : (Y|N) if Y view only abstract attribute
 * @global props Http var : (Y|N) if Y view properties also
 * @global zonebodycard Http var : if set, view other specific representation
 * @global vid Http var : if set, view represention describe in view control (can be use only if doc has controlled view)
 * @global ulink Http var : (Y|N)if N hyperlink are disabled
 * @global target Http var : is set target of hyperlink can change (default _self)
 * @global reload Http var : (Y|N) if Y update freedom folders in client navigator
 * @global dochead Http var :  (Y|N) if N don't see head of document (not title and icon)
 */
function ws_viewdoc(&$action) {
  header('Content-type: text/xml; charset=iso-8859-1'); 
  fdl_card($action);
  $action->lay->set("count",1);
  $action->lay->set("CODE","OK");
  $action->lay->set("warning","");
  $action->lay->set("delay","1");
  $a= _("object")
}