<?php
/**
 * Context menu view in folder list for a document
 *
 * @author Anakeen 2006
 * @version $Id: ws_popupdocfolder.php,v 1.2 2006/04/28 06:44:36 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */


include_once("FDL/popupdoc.php");
include_once("FDL/popupdocdetail.php");
// -----------------------------------
function ws_popupdocfolder(&$action) {
  // -----------------------------------
  // define accessibility
  $docid = GetHttpVars("id");
  $abstract = (GetHttpVars("abstract",'N') == "Y");
  $zone = GetHttpVars("zone"); // special zone

  $dbaccess = $action->GetParam("FREEDOM_DB");
  $doc = new_Doc($dbaccess, $docid);

  //  if ($doc->doctype=="C") return; // not for familly

  

  $tsubmenu=array();

  // -------------------- Menu menu ------------------

  $surl=$action->getParam("CORE_STANDURL");

  $tlink=array(
	       "latest"=>array("descr"=>_("View latest"),
			       "url"=>"$surl&app=FDL&action=FDL_CARD&latest=Y&id=$docid",
			       "confirm"=>"false",
			       "tconfirm"=>"",
			       "target"=>"latest",
			       "visibility"=>POPUP_INVISIBLE,
			       "submenu"=>"",
			       "barmenu"=>"false"),
	       "editdoc"=>array( "descr"=>_("Edit"),
				 "url"=>"$surl&app=GENERIC&action=GENERIC_EDIT&rzone=$zone&id=$docid",
				 "confirm"=>"false",
				 "tconfirm"=>"",
				 "target"=>"",
				 "visibility"=>POPUP_ACTIVE,
				 "submenu"=>"",
				 "barmenu"=>"false"),
	       "delete"=>array( "descr"=>_("Delete"),
				"jsfunction"=>"deleteDoc(event,$docid)",
				"confirm"=>"true",
				"tconfirm"=>sprintf(_("Sure delete %s ?"),$doc->title),
				"target"=>"",
				"visibility"=>POPUP_INACTIVE,
				"submenu"=>"",
				"barmenu"=>"false"),
	       "editstate"=>array( "descr"=>_("Change state"),
				   "url"=>"$surl&app=FREEDOM&action=FREEDOM_EDITSTATE&id=&id=$docid",
				   "confirm"=>"false",
				   "tconfirm"=>"",
				   "target"=>"",
				   "visibility"=>POPUP_INVISIBLE,
				   "submenu"=>"",
				   "barmenu"=>"false"),
	       "revise"=>array( "descr"=>_("Revise"),
				"url"=>"$surl&app=FREEDOM&action=REVCOMMENT&id=$docid",
				"confirm"=>"false",
				"tconfirm"=>"",
				"target"=>"",
				"visibility"=>POPUP_INACTIVE,
				"submenu"=>"",
				"barmenu"=>"false"),
	       "histo"=>array( "descr"=>_("History"),
			       "url"=>"$surl&app=FREEDOM&action=HISTO&id=$docid",
			       "confirm"=>"false",
			       "tconfirm"=>"",
			       "target"=>"",
			       "visibility"=>POPUP_CTRLACTIVE,
			       "submenu"=>"",
			       "barmenu"=>"false"),
	       "duplicate"=>array( "descr"=>_("Duplicate"),
				   "url"=>"$surl&app=GENERIC&action=GENERIC_DUPLICATE&id=$docid",
				   "confirm"=>"true",
				   "tconfirm"=>_("Sure duplicate ?"),
				   "target"=>"",
				   "visibility"=>POPUP_CTRLACTIVE,
				   "submenu"=>"security",
				   "barmenu"=>"false"),
	       "lockdoc"=>array( "descr"=>_("Lock"),
				 "url"=>"$surl&app=FDL&action=LOCKFILE&id=$docid",
				 "confirm"=>"false",
				 "tconfirm"=>"",
				 "target"=>"",
				 "visibility"=>POPUP_ACTIVE,
				 "submenu"=>"security",
				 "barmenu"=>"false"),
	       "unlockdoc"=>array( "descr"=>_("Unlock"),
				   "url"=>"$surl&app=FDL&action=UNLOCKFILE&id=$docid",
				   "confirm"=>"false",
				   "tconfirm"=>"",
				   "target"=>"",
				   "visibility"=>POPUP_ACTIVE,
				   "submenu"=>"security",
				   "barmenu"=>"false"),
	       "editprof"=>array( "descr"=>_("Change profile"),
				  "url"=>"$surl&app=FREEDOM&action=EDITPROF&id=$docid",
				  "confirm"=>"false",
				  "tconfirm"=>"",
				  "target"=>"",
				  "visibility"=>POPUP_ACTIVE,
				  "submenu"=>"security",
				  "barmenu"=>"false"),
	       "access"=>array( "descr"=>_("goaccess"),
				"url"=>"$surl&app=FREEDOM&action=FREEDOM_GACCESS&id=".$doc->profid,
				"confirm"=>"false",
				"tconfirm"=>"",
				"target"=>"",
				"visibility"=>POPUP_ACTIVE,
				"submenu"=>"security"),
	       "tobasket"=>array( "descr"=>_("Add to basket"),
				  "url"=>"$surl&app=FREEDOM&action=ADDDIRFILE&docid=$docid&dirid=".$action->getParam("FREEDOM_IDBASKET"),
				  "confirm"=>"false",
				  "tconfirm"=>"",
				  "target"=>"",
				  "visibility"=>POPUP_CTRLACTIVE,
				  "submenu"=>"",
				  "barmenu"=>"false"),
	       "restore"=>array( "descr"=>_("restore"),
				 "jsfunction"=>"restoreDoc(event,$docid)",
				 "tconfirm"=>"",
				 "confirm"=>"false",
				 "target"=>"",
				 "visibility"=>POPUP_INVISIBLE,
				 "submenu"=>"",
				 "barmenu"=>"false"),
	       "reference"=>array( "descr"=>_("Search linked documents"),
				   "url"=>"$surl&app=GENERIC&action=GENERIC_ISEARCH&id=$docid",
				   "confirm"=>"false",
				   "tconfirm"=>"",
				   "target"=>"",
				   "visibility"=>POPUP_CTRLACTIVE,
				   "submenu"=>"",
				   "barmenu"=>"false"));
  changeMenuVisibility($action,$tlink,$doc);


  if ($doc->doctype=='Z')  $tlink["restore"]["visibility"]=POPUP_ACTIVE;
  
  
         
  if  (($doc->doctype != 'S') && (preg_match("/doctype='Z'/",$doc->getValue("se_sqlselect")))) {
    $tlink["trash"]=array("descr"=>_("Empty trash"),
			  "jsfunction"=>"emptytrash(event)",
			  "confirm"=>"false",
			  "tconfirm"=>"",
			  "target"=>"nresume",
			  "visibility"=>POPUP_ACTIVE,
			  "submenu"=>"",
			  "barmenu"=>"false");
  }

  addFamilyPopup($tlink,$doc);
  popupdoc($action,$tlink,$tsubmenu);
}


?>