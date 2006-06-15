<?php
/**
 * Specific menu for family
 *
 * @author Anakeen 2000 
 * @version $Id: ws_popuplistfolder.php,v 1.9 2006/06/15 16:01:42 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */


include_once("FDL/popupdoc.php");
// -----------------------------------
function ws_popuplistfolder(&$action) {
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

  $tlink=array("createfile"=>array("descr"=>_("Add new file"),
				   "url"=>"$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFILE&&dirid=$docid",
				   "confirm"=>"false",
				   "control"=>"false",
				   "icon" => "Images/mime-document.png",
				   "tconfirm"=>"",
				   "target"=>"nresume",
				   "visibility"=>POPUP_ACTIVE,
				   "submenu"=>"",
				   "barmenu"=>"false"),
	       "createtext"=>array("descr"=>_("Create new text"),
				   "url"=>"$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFILE&&dirid=$docid&zone=WORKSPACE:CREATETEXT:T",
				   "confirm"=>"false",
				   "control"=>"false",
				   "icon" => "Images/mime-html.png",
				   "tconfirm"=>"",
				   "target"=>"nresume",
				   "visibility"=>POPUP_ACTIVE,
				   "submenu"=>"",
				   "barmenu"=>"false"),
	       "createfolder"=>array("descr"=>_("Create new directory"),
				     "url"=>"$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFOLDER&&dirid=$docid",
				     "confirm"=>"false",
				     "control"=>"false",
				     "icon" => "Images/directory.gif",
				     "tconfirm"=>"",
				     "target"=>"nresume",
				     "visibility"=>POPUP_ACTIVE,
				     "submenu"=>"",
				     "barmenu"=>"false"),
	       "sep1"=>array("separator"=>true),
	       "properties"=>array("descr"=>_("Properties"),
				     "url"=>"$surl&app=FDL&action=FDL_CARD&id=$docid",
				     "confirm"=>"false",
				     "control"=>"false",
				     "icon" => "Images/directory.gif",
				     "tconfirm"=>"",
				     "target"=>"nresume",
				     "visibility"=>POPUP_ACTIVE,
				     "submenu"=>"",
				     "barmenu"=>"false"));


  if  ( ($doc->Control("modify") != "") || ($doc->isLocked(true))) {
    $tlink["createfile"]["visibility"]=POPUP_INACTIVE;
    $tlink["createfolder"]["visibility"]=POPUP_INACTIVE;
    $tlink["createtext"]["visibility"]=POPUP_INACTIVE;
  }
  
  if  ($doc->doctype != 'D')  {
    $tlink["createfile"]["visibility"]=POPUP_INVISIBLE;
    $tlink["createfolder"]["visibility"]=POPUP_INVISIBLE;
    $tlink["createtext"]["visibility"]=POPUP_INVISIBLE;
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

  popupdoc($action,$tlink,$tsubmenu);
}


?>