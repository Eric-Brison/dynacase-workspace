<?php
/**
 * Display info before download file for editing and replace it
 *
 * @author Anakeen 2006
 * @version $Id: ws_editmodfile.php,v 1.3 2006/06/23 09:50:52 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Display info before download
 * @param Action &$action current action
 * @global id Http var : document for file to edit (SIMPLEFILE family)
 */
function ws_editmodfile(&$action) {
  
  $docid = GetHttpVars("id");
  $dbaccess = $action->GetParam("FREEDOM_DB");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/ws_editmodfile.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/getdoc.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/setparamu.js");

  $doc=new_doc($dbaccess,$docid);
  if (!$doc->isAlive()) $action->exitError(sprintf(_("document %s does not exist"),$docid));

  if ($doc->getValue('sfi_inedition') == 1) $action->exitError(sprintf(_("document %s already in edition"),$docid));

  $filename=$doc->getValue("sfi_title");
  $action->lay->set("downloadtext",sprintf(_("Download <i>%s</i> file<br> for modification"),$filename));
  $action->lay->set("oktext",sprintf(_("The file %s has been downloaded and the document has been locked and tagged : in edition"),$filename));
  $action->lay->set("docid",$doc->id);
  $action->lay->set("autodownload",($action->getParam('WS_AUTODOWNLOAD')=='yes'));
}
?>