<?php
/**
 * Common function for move/add/del document
 *
 * @author Anakeen 2006
 * @version $Id: Lib.WsFtCommon.php,v 1.1 2006/04/20 06:58:46 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package WORKSPACE
 * @subpackage 
 */
 /**
 */

  /**
   * @param int $cfldid current folder where place the document
   * @param int $cdocid current document to move/add/del
   * @param int $pfldid parent folder where comes the document
   * @param string $docft the function : [add|move|del]
   */
function movementDocument($dbaccess,$cfldid,$cdocid,$pfldid,$docft) {
  global $action;
  //  $action->lay->set("warning","prel:$dbaccess,$cfldid,$cdocid,$pfldid,$docft");
  $doc=new_doc($dbaccess,$cfldid);

  if (($docft == "move") || ($docft == "link")) {
    if ($doc->isAlive()) {
      if ($cdocid) {
	$adddoc=new_doc($dbaccess,$cdocid);
	if ($adddoc->isAlive()) {
	  $err=$doc->AddFile($adddoc->id);
	  if (($err=="")&&($docft == "move")) {
	    if ($adddoc->prelid == $pfldid) {
	      // change primary relation
	      $adddoc->prelid=$doc->initid;
	      $adddoc->modify(true,array("prelid"),true);
	    }
	  }
	}
      }
    }
  }

  if ($docft == "copy") {
    if ($doc->isAlive()) {
      if ($cdocid) {
	$adddoc=new_doc($dbaccess,$cdocid);
	if ($adddoc->isAlive()) {
	  $copy=$adddoc->copy();
	  if ($copy) {
	    if ($err=="") {

	      $err=$doc->AddFile($copy->id);
	      if ($err == "") {
		$copy->title = sprintf(_("duplication of %s "),$copy->title);
		$copy->prelid=$doc->initid;
		
		$copy->SetTitle($copy->title);
		$copy->refresh();
		$copy->postmodify();
		$copy->modify();

		
	      }
	    }
	  } else {
	$err=sprintf(_("failed to copy document %s"),$doc->title);
      }
    }
      }
    }
  }
  
  if ($err=="") {
    if (($docft == "move")) {
      $pdoc=new_doc($dbaccess,$pfldid);
      if ($pdoc->isAlive()) {
	$err=$pdoc->DelFile($adddoc->id);
      }
    }
  }
  if ($err=="") { 
    if (($docft == "del")) { 
      if ($cdocid) {
	$adddoc=new_doc($dbaccess,$cdocid);
	if ($adddoc->isAlive()) {	 
	  if ($adddoc->prelid == $pfldid) {
	    $err=$adddoc->delete(); 
	  } else {	      
	    $pdoc=new_doc($dbaccess,$pfldid);
	    if ($pdoc->isAlive()) {
	      $err=$pdoc->DelFile($adddoc->initid);
	    }
	  }	  
	}
      }   
    }
  }
  return $err;
}