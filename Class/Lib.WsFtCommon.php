<?php
/**
 * Common function for move/add/del document
 *
 * @author Anakeen 2006
 * @version $Id: Lib.WsFtCommon.php,v 1.7 2006/06/15 16:01:42 eric Exp $
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

  if (($docft == "move") || ($docft == "link")|| ($docft == "shortcut") ) {
    if ($doc->isAlive()) {
      if ($cdocid) {
	$adddoc=new_doc($dbaccess,$cdocid);
	if ($adddoc->isAlive()) {
	  if (($docft == "move")&& ($adddoc->doctype=='D')){
	    // verify to not loop in folder : normaly it is permit but for this kind of application we don't permit this
	    $prel=$doc->getMainPath();
	    $prelid=array_keys($prel);
	    if (in_array($adddoc->initid ,$prelid)) $err=sprintf(_("cannot move folder %s in %s cause loop"),$adddoc->title,$doc->title);
	  }

	  if ($err=="") {
	    $err=$doc->AddFile($adddoc->id);
	    if ($err=="") {
	      if (strstr("SD", $adddoc->doctype) === false) {
		$taction[]=array("actname"=>"ADDFILE",
				 "actdocid"=>$doc->initid);
	      } else {
		$taction[]=array("actname"=>"ADDFOLDER",
				 "actdocid"=>$doc->initid);	    
	      }
	    }
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
	      if ($err=="") {
		if (strstr("SD", $copy->doctype) === false) {
		  $taction[]=array("actname"=>"ADDFILE",
				   "actdocid"=>$doc->initid);
		} else {
		  $taction[]=array("actname"=>"ADDFOLDER",
				   "actdocid"=>$doc->initid);	    
		}
	      }

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
	if ($err=="") {
	  if (strstr("SD", $adddoc->doctype) === false) {
	    $taction[]=array("actname"=>"DELFILE",
			     "actdocid"=>$pdoc->initid);
	  } else {
	    $taction[]=array("actname"=>"DELFOLDER",
			     "actdocid"=>$pdoc->initid);	    
	  }
	}
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
	    $isnotfld=(strstr("SD", $adddoc->doctype) === false);
	    
	    if ($adddoc->doctype=='D')   $err=$adddoc->deleteRecursive(); 
	    else  $err=$adddoc->delete(); 
	    if ($err=="") {
	      $taction[]=array("actname"=>"TRASHFILE",
			       "actdocid"=>$pdoc->initid);
	      if ($isnotfld) {
		$taction[]=array("actname"=>"DELFILE",
				 "actdocid"=>$pdoc->initid);
	      } else {
		$taction[]=array("actname"=>"DELFOLDER",
				 "actdocid"=>$pdoc->initid);	    
	      }
	    }
	  } else {	      
	    if ($pdoc->isAlive()) {
	      $err=$pdoc->DelFile($adddoc->initid);
	      if ($err=="") {
		if (strstr("SD", $adddoc->doctype) === false) {
		  $taction[]=array("actname"=>"DELFILE",
				   "actdocid"=>$pdoc->initid);
		} else {
		  $taction[]=array("actname"=>"DELFOLDER",
				   "actdocid"=>$pdoc->initid);	    
		}
	      } 
	    }
	  }	  
	}
      }   
    }
  }

  $action->lay->setBlockData("ACTIONS",$taction);
  return $err;
}