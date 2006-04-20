<?php
/**
 * Specific menu for family
 *
 * @author Anakeen 2000 
 * @version $Id: ws_popuplistfolder.php,v 1.1 2006/04/20 18:15:07 eric Exp $
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

  $tlink=array("createfile"=>array("descr"=>_("Create new file"),
				"url"=>"$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFILE&&dirid=$docid",
				"confirm"=>"false",
				"control"=>"false",
				"tconfirm"=>"",
				"target"=>"nresume",
				"visibility"=>POPUP_ACTIVE,
				"submenu"=>"",
				   "barmenu"=>"false"),
	       "createfolder"=>array("descr"=>_("Create new folder"),
				"url"=>"$surl&app=GENERIC&action=GENERIC_EDIT&classid=2&&dirid=$docid",
				"confirm"=>"false",
				"control"=>"false",
				"tconfirm"=>"",
				"target"=>"nresume",
				"visibility"=>POPUP_ACTIVE,
				"submenu"=>"",
				"barmenu"=>"false"));


  if  (($doc->Control("modify") != "") || ($doc->isLocked(true))) {
    $tlink["createfile"]["visibility"]=POPUP_INACTIVE;
    $tlink["createfolder"]["visibility"]=POPUP_INACTIVE;
  }
         
  popupdoc($action,$tlink,$tsubmenu);
}


?>