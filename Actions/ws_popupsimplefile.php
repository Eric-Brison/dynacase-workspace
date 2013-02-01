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
function ws_popupsimplefile(Action & $action)
{
    // -----------------------------------
    // define accessibility
    $docid = $action->getArgument("id");
    $abstract = ($action->getArgument("abstract", 'N') == "Y");
    $zone = $action->getArgument("zone"); // special zone
    $dbaccess = $action->GetParam("FREEDOM_DB");
    /**
     * @var _SIMPLEFILE $doc
     */
    $doc = new_Doc($dbaccess, $docid);
    //  if ($doc->doctype=="C") return; // not for familly
    $tsubmenu = array();
    $davserver = $action->getParam("FREEDAV_SERVEUR");
    $vid = '';
    if ($davserver) {
        $fvalue = $doc->getRawValue("sfi_file");
        if (preg_match(PREGEXPFILE, $fvalue, $reg)) {
            $vid = $reg[2];
            $mimetype = $reg[1];
        }
    }
    // -------------------- Menu menu ------------------
    $surl = $action->getParam("CORE_STANDURL");
    
    $tlink = array(
        "latest" => array(
            "descr" => _("View latest") ,
            "url" => "$surl&app=FDL&action=FDL_CARD&latest=Y&id=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "editdoc" => array(
            "descr" => _("Edit") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_EDIT&rzone=$zone&id=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "openineditor" => array(
            "descr" => _("Edit file") ,
            "jsfunction" => "getDavUrl(this,'$docid','$vid','$davserver');",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "reserve" => array(
            "descr" => _("Reserve") ,
            "jsfunction" => "subwindow(300,500,'editfile','$surl&app=WORKSPACE&action=WS_EDITMODFILE&id=$docid')",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "mail" => array(
            "descr" => _("Diffuse") ,
            "url" => "$surl&app=FDL&action=EDITMAIL&viewdoc=N&mid=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "mail",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "mwidth" => 500,
            "mheight" => 300,
            "barmenu" => "false"
        ) ,
        "affect" => array(
            "descr" => _("Workflow") ,
            "url" => "$surl&app=FDL&action=EDITAFFECT&id=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "affect",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "mwidth" => 550,
            "mheight" => 250,
            "barmenu" => "false"
        ) ,
        "desaffect" => array(
            "descr" => _("Desaffect") ,
            "url" => "$surl&app=FDL&action=DESAFFECT&id=$docid",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "lockdoc" => array(
            "descr" => _("Lock") ,
            "url" => "$surl&app=FDL&action=LOCKFILE&id=$docid",
            "confirm" => "false",
            "control" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "unlockdoc" => array(
            "descr" => _("Unlock") ,
            "url" => "$surl&app=FDL&action=UNLOCKFILE&id=$docid",
            "confirm" => "false",
            "control" => "false",
            "tconfirm" => "",
            "target" => "_self",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "delete" => array(
            "descr" => _("Delete") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_DEL&id=$docid",
            "confirm" => "true",
            "tconfirm" => sprintf(_("Sure delete %s ?") , $doc->title) ,
            "target" => "_self",
            "visibility" => POPUP_INACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "postit" => array(
            "descr" => _("Add a note") ,
            "jsfunction" => "postit('$surl&app=GENERIC&action=GENERIC_EDIT&classid=27&pit_title=&pit_idadoc=$docid',50,50,300,200)",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "duplicate" => array(
            "descr" => _("Duplicate") ,
            "url" => "$surl&app=GENERIC&action=GENERIC_DUPLICATE&id=$docid",
            "confirm" => "true",
            "tconfirm" => _("Sure duplicate ?") ,
            "target" => "_self",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "tobasket" => array(
            "descr" => _("Add to basket") ,
            "jsfunction" => "shortcutToFld(event,$docid,'" . $action->getParam("FREEDOM_IDBASKET") . "')",
            "confirm" => "false",
            "tconfirm" => "",
            "target" => "",
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "restore" => array(
            "descr" => _("restore") ,
            "url" => "$surl&app=WORKSPACE&action=WS_RESTOREDOC&id=$docid&reload=Y",
            "tconfirm" => "",
            "confirm" => "false",
            "target" => "_self",
            "visibility" => POPUP_INVISIBLE,
            "submenu" => "",
            "barmenu" => "false"
        ) ,
        "print" => array(
            "descr" => _("print") ,
            "url" => "$surl&app=FDL&action=IMPCARD&zone=WORKSPACE:PRINTSIMPLEFILE:T&id=$docid",
            "tconfirm" => "",
            "confirm" => "false",
            "target" => "properties$docid",
            "mwidth" => 520,
            "mheight" => 300,
            "visibility" => POPUP_ACTIVE,
            "submenu" => "",
            "barmenu" => "true"
        ) ,
        "properties" => array(
            "descr" => _("properties") ,
            "url" => "$surl&app=FDL&action=IMPCARD&zone=WORKSPACE:VIEWSIMPLEPROP:T&id=$docid",
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
        $tlink["restore"]["visibility"] = POPUP_ACTIVE;
        $tlink["duplicate"]["visibility"] = POPUP_INVISIBLE;
    }
    if ($davserver) {
        $tlink["openineditor"]["visibility"] = POPUP_ACTIVE;
    }
    $tlink["reserve"]["visibility"] = ($doc->fileIsNotInEdition() == MENU_ACTIVE) ? POPUP_ACTIVE : POPUP_INVISIBLE;
    
    if ($doc->hasUTag("AFFECTED")) {
        $tlink["affect"]["descr"] = _("Reaffect");
        $tlink["desaffect"]["visibility"] = POPUP_ACTIVE;
    }
    $err = $doc->CanLockFile();
    if ($err) {
        $tlink["affect"]["visibility"] = POPUP_INVISIBLE;
        $tlink["desaffect"]["visibility"] = POPUP_INVISIBLE;
        $tlink["openineditor"]["visibility"] = POPUP_INVISIBLE;
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
