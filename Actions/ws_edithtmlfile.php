<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_edithtmlfile.php,v 1.1 2006/05/16 17:03:45 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * Display editor to modify HTML file
 * @param Action &$action current action
 * @global id Http var : document id to edi
 * @global attrid Http var : id of file attribute
 */
function ws_edithtmlfile(&$action) {
  $docid = GetHttpVars("id");
  $aid = GetHttpVars("attrid");

  $dbaccess = $action->GetParam("FREEDOM_DB");



  $doc=new_doc($dbaccess,$docid);
  if (!$doc->isAlive()) $action->exitError(sprintf(_("Document %s is not alive"),$docid));
    

  $err = $doc->lock(true); // autolock
  if ($err=="") $action->AddActionDone("LOCKFILE",$doc->id);
  
  if ($err != "") {    
      // test object permission before modify values (no access control on values yet)
      $err=$doc->CanUpdateDoc();
  }
  if ($err != "") $action->exitError($err);

  if ($aid=="") {
    
    $attr = $doc->GetFirstFileAttributes();
    $aid=$attr->id;

  }
  
  $fvalue=$doc->getValue($aid);

  if (  ereg ("(.*)\|(.*)", $fvalue, $reg)) {
    $vaultid= $reg[2];
    $mimetype=$reg[1];

    $vf = newFreeVaultFile($dbaccess);
    $err=$vf->Retrieve($vaultid, $info);
    if ($err != "") $action->exitError($err);
    
    $filename=$info->path;
    $content=file_get_contents($filename);

    $big=(strlen($content) > 100000);

    if (! $big) $action->lay->set("fullhtml",str_replace(array("'","\n"),array("\\'","\\\n"),$content));

    
  } else {
    $action->exitError(sprintf(_("%s attribute is not a file"),$aid));
  }

  $action->lay->set("edittitle",sprintf(_("%s : edit HTML file"),$doc->title));
  $action->lay->set("docid",$doc->id);
  $action->lay->set("attrid",$aid);
  $action->lay->set("BIG",$big);
}