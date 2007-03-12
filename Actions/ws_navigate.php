<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_navigate.php,v 1.11 2007/03/12 09:06:48 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package FREEDOM
 * @subpackage 
 */
 /**
 */



include_once("FDL/Lib.Dir.php");


/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */
function ws_navigate(&$action) {

  $dbaccess = $action->GetParam("FREEDOM_DB");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/geometry.js");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/subwindow.js");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/AnchorPosition.js");
  if ($action->Read("navigator")=="EXPLORER") $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/iehover.js");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/resizeimg.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/common.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/popupdoc.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/inserthtml.js");
  $action->parent->AddJsRef($action->GetParam("CORE_JSURL")."/DHTMLapi.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/setparamu.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/displayws.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/mechanism.js");


    // css pour popup
  $action->parent->AddCssRef("FDL:POPUP.CSS",true);
  $action->parent->AddCssRef("WORKSPACE:system.css",true);
  $action->parent->AddCssRef("WORKSPACE:default.css",true);
  

  $tspaces = getChildDoc($dbaccess,0,"0","ALL",array(), $action->user->id, "ITEM","WORKSPACE");
  $tlayspaces=array();
  while ($doc=getNextDoc($dbaccess,$tspaces)) {
    $tlayspaces[]=array("stitle"=>$doc->title,
			"sicon"=>$doc->getIcon(),
			"sid"=>$doc->id);
  }

  $action->lay->setBlockData("SPACES",$tlayspaces);
  if (trashempty($dbaccess,$action->user->id)) $action->lay->set("imgtrash",$action->getImageUrl('trashempty.png'));
  else $action->lay->set("imgtrash",$action->getImageUrl('trash.png'));

  $homename="WS_PERSOFLD_".Doc::getWhatUserId();
  $perso=getTDoc($dbaccess,$homename);
  if (! $perso) {
    // create "my space" folder
    
    $perso = createDoc($dbaccess,"SIMPLEFOLDER",false);
    $perso->name=$homename;
    
    $perso->setValue("ba_title",_("My space"));
    $perso->setValue("ba_desc",sprintf(_("personal space of %s"),$perso->getUserName(true)));
    $perso->icon='gnome-fs-home.png';
    $err=$perso->Add();
    if ($err != "") $action->addWarningMsg($err);
    if ($err =="") {
      $persofldid=$perso->id;
      $home=$perso->getHome();
      if ($home) $home->AddFile($persofldid);//add in general home
    }
  } else {    
    $persofldid=$perso["id"];
  }

  $action->lay->set("persofldid",$persofldid);
}

function trashempty($dbaccess,$userid) {
  $q=new QueryDb($dbaccess,"Doc");
  $q->Query(0,0,"TABLE",
	    sprintf("select id from doc where doctype='Z' and owner=%d limit 1",$userid));

  return ($q->nb == 0);

}



?>