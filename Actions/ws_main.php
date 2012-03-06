<?php
/*
 * Display doucment explorer
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("WORKSPACE/ws_navigate.php");
include_once ("WORKSPACE/ws_folderListFormat.php");
/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */
function ws_main(Action & $action)
{
    
    $dbaccess = $action->GetParam("FREEDOM_DB");
    
    $nav = new ws_Navigate($action);
    $spaces = new SearchDoc($dbaccess, "WORKSPACE");
    $files = new SearchDoc($dbaccess, "SIMPLEFILE");
    
    $nav->setSpaces($spaces);
    $nav->setFolderListHeight($action->getParam("WS_COL3H1"));
    $nav->setFolderTreeHeight($action->getParam("WS_COL2H1"));
    $nav->setFolderTreeWidth($action->getParam("WS_ROW2W1"));
    $nav->setFolderListColumn("wsFolderListFormat::getColumnDescription()");
    $nav->setGlobalSearch($files);
    /* $nav->setFolderListColumn(array(
            "icon" => array(
                "htitle" => _("icon"),
                "horder" => "title",
                "issort" => false,
                "method" => "wsFolderListFormat::getIcon(THIS, DIR)"
            ),
            "title" => array(
                "htitle" => _("Filename Menu"),
                "horder" => "title",
                "issort" => false,
                "method" => "::getHtmlTitle()"
            ),
            "date" => array(
                "htitle" => _("Modification Date Menu"),
                "horder" => "date",
                "issort" => false,
                "method" => "wsFolderListFormat::getMDate(THIS)"
            ),
            "size" => array(
                "htitle" => _("File Size Menu"),
                "horder" => "size",
                "issort" => false,
                "method" => "wsFolderListFormat::getFileSize(THIS)"
            ),
            "mime" => array(
                "htitle" => _("File Type Menu"),
                "horder" => "mime",
                "issort" => false,
                "method" => "wsFolderListFormat::getFileMime(THIS)"
            )
        ));*/
    $action->lay->set("NAV", $nav->output());
    /*
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
    $famid = getFamIdFromName($dbaccess,"SIMPLEFILE");
    $mode=getSearchMode($action,$famid);
    $action->lay->Set("FULLMODE",($mode=="FULL"));
    
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
    if ($action->getParam("WS_OFFLINE")=="yes") addOffline($action);
    $action->lay->set("persofldid",$persofldid);
    }
    
    function trashempty($dbaccess,$userid) {
    $q=new QueryDb($dbaccess,"Doc");
    $q->Query(0,0,"TABLE",
     sprintf("select id from doc where doctype='Z' and owner=%d limit 1",$userid));
    
    return ($q->nb == 0);
    
    }
    function addOffline(&$action) {
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $desktop=getTDoc($dbaccess,'FLDOFFLINE_'.Doc::getWhatUserId());
    if (! $desktop) {
    $desktop = createDoc($dbaccess,"DIR");  
    $desktop->title = _("Offline");
    $desktop->setTitle($desktop ->title);
    $desktop->setValue("ba_desc", sprintf(_("Offline folder of %s"),$action->user->firstname." ".$action->user->lastname));
    $desktop->icon = 'fldoffline.png';
    $desktop->name = 'FLDOFFLINE_'.$action->user->id;
    $desktop->Add();
    
    $home=$desktop->getHome();
    $home->addFile($desktop->initid);
    $action->lay->set("FREEDOM_IDOFFLINE",$desktop->initid);
    } else   $action->lay->set("FREEDOM_IDOFFLINE",$desktop["id"]);
    */
}
?>
