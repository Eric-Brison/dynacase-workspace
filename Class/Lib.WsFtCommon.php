<?php
/**
 * Common function for move/add/del document
 *
 * @author Anakeen 2006
 * @version $Id: Lib.WsFtCommon.php,v 1.3 2006/04/28 06:44:36 eric Exp $
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
function movementDocument(&$action,$dbaccess,$cfldid,$cdocid,$pfldid,$docft) {

  //  $action->lay->set("warning","prel:$dbaccess,$cfldid,$cdocid,$pfldid,$docft");
  $doc=new_doc($dbaccess,$cfldid);
  $taction=array();

  if (($docft == "move") || ($docft == "link")) {
    if ($doc->isAlive()) {
      if ($cdocid) {
	$adddoc=new_doc($dbaccess,$cdocid);
	if ($adddoc->isAlive()) {
	  $err=$doc->AddFile($adddoc->id);
	  if ($err=="") $taction[]=array("actname"=>"ADDFILE",
					 "actdocid"=>$doc->initid);
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
	      if ($err=="") $taction[]=array("actname"=>"ADDFILE",
					     "actdocid"=>$doc->initid);
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
	  if ($err=="") $taction[]=array("actname"=>"DELFILE",
					 "actdocid"=>$pdoc->initid);
      }
    }
  }
  if ($err=="") { 
    if (($docft == "del")) { 
      if ($cdocid) {
	$adddoc=new_doc($dbaccess,$cdocid);
	if ($adddoc->isAlive()) {
	  $pdoc=new_doc($dbaccess,$pfldid);	 
	  if (($adddoc->prelid == $pfldid) || ($pdoc->doctype=='S')) {
	    $err=$adddoc->delete(); 
	    if ($err=="") {
	      $taction[]=array("actname"=>"TRASHFILE",
			       "actdocid"=>$pdoc->initid);
	      $taction[]=array("actname"=>"DELFILE",
			       "actdocid"=>$pfldid);
	    }
	  } else {	      
	    if ($pdoc->isAlive()) {
	      $err=$pdoc->DelFile($adddoc->initid);
	      if ($err=="") $taction[]=array("actname"=>"DELFILE",
					     "actdocid"=>$pdoc->initid);
	    }
	  }	  
	}
      }   
    }
  }

  $action->lay->setBlockData("ACTIONS",$taction);
  return $err;
}