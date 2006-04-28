<?php
/**
 * Download File in web client
 *
 * @author Anakeen 2006
 * @version $Id: ws_downloadfile.php,v 1.1 2006/04/28 15:19:48 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
  $dbaccess = $action->GetParam("FREEDOM_DB");
  
  $doc=new_doc($dbaccess,$docid);
  $err=$doc->control("view");
  if ($err != "") $action->exiterror($err);

  $ovalue = $doc->getValue("sfi_file");

    
    if ($ovalue == "") $action->exiterror(_("no file referenced"));
    
    ereg ("(.*)\|(.*)", $ovalue, $reg);
    $vaultid= $reg[2];
    // $mimetype=$reg[1];
    $mimetype= $doc->getValue("sfi_mimesys");

  
  DownloadVault($action, $vaultid, true, $mimetype,$imgheight);
  exit;
}