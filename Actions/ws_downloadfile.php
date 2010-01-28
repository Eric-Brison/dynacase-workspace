<?php
/**
 * Download File in web client
 *
 * @author Anakeen 2006
 * @version $Id: ws_downloadfile.php,v 1.4 2008/03/13 11:10:16 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
 * @subpackage 
 */
 /**
 */




include_once("FDL/exportfile.php");


/**
 * Download the file from simplefile family document
 * @param Action &$action current action
 * @global id Http var : document id
 */
function ws_downloadfile(&$action) {
  $docid = GetHttpVars("id");
  $inline = (GetHttpVars("inline")=="yes");
  $dbaccess = $action->GetParam("FREEDOM_DB");
  
  $doc=new_doc($dbaccess,$docid);
  $err=$doc->control("view");
  if ($err != "") $action->exiterror($err);

  $ovalue = $doc->getValue("sfi_file");

    
    if ($ovalue == "") $action->exiterror(_("no file referenced"));
    
    preg_match(PREGEXPFILE, $ovalue, $reg);
    $vaultid= $reg[2];
    // $mimetype=$reg[1];
    $mimetype= $doc->getValue("sfi_mimesys");

  
    DownloadVault($action, $vaultid, true, $mimetype,$imgheight,$inline,false);
  exit;
}