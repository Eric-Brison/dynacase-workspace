<?php
/*
 * Specific menu for family
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/popupdoc.php");
// -----------------------------------
function ws_popuplistfolder(Action & $action)
{
    // -----------------------------------
    // define accessibility
    $docid = $action->getArgument("id");
    $abstract = ($action->getArgument("abstract", 'N') == "Y");
    $zone = $action->getArgument("zone"); // special zone
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $doc = new_Doc($dbaccess, $docid);
    //  if ($doc->doctype=="C") return; // not for familly
    $tsubmenu = array();
    // -------------------- Menu menu ------------------
    $surl = $action->getParam("CORE_STANDURL");
    
    $tlink = array(
        "createfile" => array(
            "descr" => _("New file") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFILE&&dirid=$docid",
            "confirm" => "false",
            "control" => "false",
            "icon" => "Images/mime-document.png",
            "tconfirm" => "",
            "target" => "nresume",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "createtext" => array(
            "descr" => _("New text") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFILE&&dirid=$docid&zone=WORKSPACE:CREATETEXT:T",
            "confirm" => "false",
            "control" => "false",
            "icon" => "Images/mime-html.png",
            "tconfirm" => "",
            "target" => "nresume",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "createfolder" => array(
            "descr" => _("New directory") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_EDIT&classid=SIMPLEFOLDER&&dirid=$docid",
            "confirm" => "false",
            "control" => "false",
            "icon" => "Images/directory.gif",
            "tconfirm" => "",
            "target" => "nresume",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        )
    );
    
    if (($doc->Control("modify") != "") || ($doc->isLocked(true))) {
        $tlink["createfile"]["visibility"] = POPUP_INACTIVE;
        $tlink["createfolder"]["visibility"] = POPUP_INACTIVE;
        $tlink["createtext"]["visibility"] = POPUP_INACTIVE;
    }
    
    if ($doc->doctype != 'D') {
        $tlink["createfile"]["visibility"] = POPUP_INVISIBLE;
        $tlink["createfolder"]["visibility"] = POPUP_INVISIBLE;
        $tlink["createtext"]["visibility"] = POPUP_INVISIBLE;
    }
    
    if ((preg_match("/doctype[ ]*=[ ]*'Z'/", $doc->getrawValue("se_sqlselect")))) {
        $tlink["trash"] = array(
            "descr" => _("Empty trash") ,
            "jsfunction" => "emptytrash(event)",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "nresume",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        );
    }
    
    $tlink+= array(
        "sep1" => array(
            "separator" => true
        ) ,
        "properties" => array(
            "descr" => _("Properties") ,
            "url" => "$surl&app=FDL&action=FDL_CARD&id=$docid",
            "confirm" => "false",
            "control" => "false",
            "icon" => "Images/documentinfo.png",
            "tconfirm" => "",
            "target" => "nresume",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        )
    );
    popupdoc($action, $tlink, $tsubmenu);
}
?>
