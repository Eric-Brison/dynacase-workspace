<?php
/*
 * Context menu view in folder list for a document
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

include_once ("FDL/popupdoc.php");
include_once ("FDL/popupdocdetail.php");
// -----------------------------------
function ws_popupdocfolder(Action & $action)
{
    // -----------------------------------
    // define accessibility
    $docid = $action->getArgument("id");
    $dirid = $action->getArgument("dirid");
    $abstract = ($action->getArgument("abstract", 'N') == "Y");
    $zone = $action->getArgument("zone"); // special zone
    $dbaccess = $action->GetParam("FREEDOM_DB");
    $doc = new_Doc($dbaccess, $docid);
    $fld = new_Doc($dbaccess, $dirid);
    //  if ($doc->doctype=="C") return; // not for familly
    $tsubmenu = array();
    $islink = ($doc->prelid != $fld->initid);
    // -------------------- Menu menu ------------------
    $surl = $action->getParam("CORE_STANDURL");
    
    $tlink = array(
        "latest" => array(
            "descr" => _("View latest") ,
            "url" => "$surl&app=FDL&action=FDL_CARD&latest=Y&id=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "latest",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "editdoc" => array(
            "descr" => _("Edit") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_EDIT&rzone=$zone&id=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "delete" => array(
            "descr" => $islink ? _("Delete link") : _("Delete") ,
            "jsfunction" => "deleteDoc(event,$docid)",
            "confirm" => $islink ? "false" : "true",
            "tconfirm" => $islink ? sprintf(_("This link will be deleted.\nSure delete %s ?") , $doc->title) : sprintf(_("This document will be dropped in the trash.\nSure delete %s ?") , $doc->title) ,
            "target" => "",
            "visibility" => POPUP_INACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "histo" => array(
            "descr" => _("History") ,
            "url" => "$surl&app=FREEDOM&action=HISTO&id=$docid&viewrev=N",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "duplicate" => array(
            "descr" => _("Duplicate") ,
            "jsfunction" => "copyDoc(event,$docid)",
            "confirm" => "true",
            "tconfirm" => _("Sure duplicate ?") ,
            "target" => "",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "tobasket" => array(
            "descr" => _("Add to basket") ,
            "jsfunction" => "addToBasket(event,$docid)",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "restore" => array(
            "descr" => _("restore") ,
            "jsfunction" => "restoreDoc(event,$docid)",
            "tconfirm" => "",
            "confirm" => "false",
            "target" => "",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "restorefld" => array(
            "descr" => _("restore folder and its containt") ,
            "jsfunction" => "restoreFld(event,$docid)",
            "tconfirm" => "",
            "confirm" => "false",
            "target" => "",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "properties" => array(
            "descr" => _("properties") ,
            "url" => "$surl&app=FDL&action=IMPCARD&zone=" . ((method_exists($doc, "viewsimpleprop")) ? "WORKSPACE:VIEWSIMPLEPROP:T" : "FDL:VIEWPROPERTIES:T") . "&id=$docid",
            "tconfirm" => "",
            "confirm" => "false",
            "target" => "properties$docid",
            "mwidth" => 400,
            "mheight" => 300,
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        )
    );
    changeMenuVisibility($action, $tlink, $doc);
    
    if ($doc->doctype == 'Z') {
        if ($doc->defDoctype == 'D') $tlink["restorefld"]["visibility"] = POPUP_ACTIVE;
        else $tlink["restore"]["visibility"] = POPUP_ACTIVE;
        $tlink["duplicate"]["visibility"] = POPUP_INVISIBLE;
    }
    
    if ($fld->doctype != 'D') {
        $tlink["delete"]["visibility"] = POPUP_INVISIBLE;
        $tlink["duplicate"]["visibility"] = POPUP_INVISIBLE;
    }
    
    if (($doc->doctype != 'S') && (preg_match("/doctype='Z'/", $doc->getRawValue("se_sqlselect")))) {
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
    //  addFamilyPopup($tlink,$doc);
    popupdoc($action, $tlink, $tsubmenu);
}
?>
