<?php
/**
 * Context menu view in folder list for a document
 *
 * @author Anakeen 2006
 * @version $Id: ws_popupsimplefile.php,v 1.5 2006/08/10 15:40:05 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */


include_once("FDL/popupdoc.php");
include_once("FDL/popupdocdetail.php");
// -----------------------------------
function ws_popupsimplefile(&$action) {
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
				 "target"=>"_self",
				 "visibility"=>POPUP_ACTIVE,
				 "submenu"=>"",
				 "barmenu"=>"false"),
	       "mail"=>array( "descr"=>_("Diffuse"),
				"url"=>"$surl&app=FDL&action=EDITMAIL&viewdoc=N&mid=$docid",
				"confirm"=>"false",
				"tconfirm"=>"",
				"target"=>"mail",
				"visibility"=>POPUP_ACTIVE,
				"submenu"=>"",
				"mwidth"=>500,
				"mheight"=>300,
				"barmenu"=>"false"),
	       "affect"=>array( "descr"=>_("Affect"),
				"url"=>"$surl&app=FDL&action=EDITAFFECT&id=$docid",
				"confirm"=>"false",
				"tconfirm"=>"",
				"target"=>"affect",
				"visibility"=>POPUP_ACTIVE,
				"submenu"=>"",
				"mwidth"=>300,
				"mheight"=>200,
				"barmenu"=>"false"),
	       "desaffect"=>array( "descr"=>_("Desaffect"),
				"url"=>"$surl&app=FDL&action=DESAFFECT&id=$docid",
				"confirm"=>"false",
				"tconfirm"=>"",
				"target"=>"_self",
				"visibility"=>POPUP_INVISIBLE,
				"submenu"=>"",
				"barmenu"=>"false"),
	       "delete"=>array( "descr"=>_("Delete"),
				"url"=>"$surl&app=GENERIC&action=GENERIC_DEL&id=$docid",
				"confirm"=>"true",
				"tconfirm"=>sprintf(_("Sure delete %s ?"),$doc->title),
				"target"=>"_self",
				"visibility"=>POPUP_INACTIVE,
				"submenu"=>"",
				"barmenu"=>"false"),
	       "postit"=>array( "descr"=>_("Add a note"),
				"jsfunction"=>"postit('$surl&app=GENERIC&action=GENERIC_EDIT&classid=27&pit_title=&pit_idadoc=$docid',50,50,300,200)",
			       "confirm"=>"false",
			       "tconfirm"=>"",
			       "target"=>"",
			       "visibility"=>POPUP_ACTIVE,
			       "submenu"=>"",
			       "barmenu"=>"false"),
	       "duplicate"=>array( "descr"=>_("Duplicate"),
				   "url"=>"$surl&app=GENERIC&action=GENERIC_DUPLICATE&id=$docid",
				   "confirm"=>"true",
				   "tconfirm"=>_("Sure duplicate ?"),
				   "target"=>"_self",
				   "visibility"=>POPUP_ACTIVE,
				   "submenu"=>"",
				   "barmenu"=>"false"),
	       "tobasket"=>array( "descr"=>_("Add to basket"),
				  "jsfunction"=>"shortcutToFld(event,$docid,'".$action->getParam("FREEDOM_IDBASKET")."')",
				  "confirm"=>"false",
				  "tconfirm"=>"",
				  "target"=>"",
				  "visibility"=>POPUP_ACTIVE,
				  "submenu"=>"",
				  "barmenu"=>"false"),
	       "restore"=>array( "descr"=>_("restore"),
				 "jsfunction"=>"restoreDoc(event,$docid)",
				 "tconfirm"=>"",
				 "confirm"=>"false",
				 "target"=>"",
				 "visibility"=>POPUP_INVISIBLE,
				 "submenu"=>"",
				 "barmenu"=>"false"));
  changeMenuVisibility($action,$tlink,$doc);


  if ($doc->doctype=='Z') {
    $tlink["restore"]["visibility"]=POPUP_ACTIVE;
    $tlink["duplicate"]["visibility"]=POPUP_INVISIBLE;
  }
  
  if ($doc->hasUTag("AFFECTED")) {
    $tlink["affect"]["descr"]=_("Reaffect");    
    $tlink["desaffect"]["visibility"]=POPUP_ACTIVE;    
  }
  $err=$doc->CanLockFile();
  if ($err) {
    $tlink["affect"]["visibility"]=POPUP_INVISIBLE;  
    $tlink["desaffect"]["visibility"]=POPUP_INVISIBLE;    
  }
  
         
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

  //  addFamilyPopup($tlink,$doc);
  popupdoc($action,$tlink,$tsubmenu);
}


?>