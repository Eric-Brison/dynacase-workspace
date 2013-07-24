<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/
namespace Dcp\Workspace;

use \Dcp\AttributeIdentifiers\Workspace as myAttribute;
use \Dcp\AttributeIdentifiers as Attribute;
use \Dcp\Family\Dir;
use \Dcp\Family\Igroup;

class WorkSpace extends Dir
{
    public $eviews = array(
        "WORKSPACE:ADMINWORKSPACE"
    );
    public $cviews = array(
        "WORKSPACE:VIEWWORKSPACE"
    );
    
    public $defaultview = "WORKSPACE:VIEWWORKSPACE:T";
    /**
     * get view groupe name
     */
    private function getViewGroupName()
    {
        $ref = $this->getRawValue(myAttribute::wsp_ref);
        return "GWS_V" . strtoupper($ref);
    }
    /**
     * get edit groupe name
     */
    private function getEditGroupName()
    {
        $ref = $this->getRawValue(myAttribute::wsp_ref);
        return "GWS_E" . strtoupper($ref);
    }
    /**
     * get profil groupe name
     */
    private function getProfilGroupName()
    {
        $ref = $this->getRawValue(myAttribute::wsp_ref);
        return "GWS_P" . strtoupper($ref);
    }
    /**
     * Create 2 groups : one for collect view user and other for edit user privilege
     * set profil to itself : computed with admin prilege
     */
    function postCreated()
    {
        if ($this->revision > 0) return '';
        $ref = unaccent($this->title);
        $ref = preg_replace("/[[:punct:]]/", "", $ref);
        $ref = strtolower(str_replace(" ", "_", $ref));
        $this->setValue(myAttribute::wsp_ref, $ref);
        $this->modify();
        $err = '';
        $gvname = $gename = '';
        $gv = null;
        $ge = null;
        if ($ref != "") {
            /**
             * @var IGROUP $gv
             * @var IGROUP $ge
             */
            $gv = createDoc($this->dbaccess, Igroup::familyName, false);
            $ge = createDoc($this->dbaccess, Igroup::familyName, false);
            
            $gv->setValue(Attribute\Igroup::us_login, "gv." . $ref);
            $gv->setValue(Attribute\Igroup::grp_name, sprintf(_("%s readers") , $this->title));
            $gvname = $this->getViewGroupName();
            $gv->name = $gvname;
            
            $ge->setValue(Attribute\Igroup::us_login, "ge." . $ref);
            $ge->setValue(Attribute\Igroup::grp_name, sprintf(_("%s writers") , $this->title));
            $gename = $this->getEditGroupName();
            $ge->name = $gename;
            
            $err = $gv->Add();
            if ($err == "") $err = $ge->Add();
            if ($err == "") {
                $err = $gv->postStore();
                if ($err == "-") $err = "";
                if ($err == "") $err = $ge->postStore();
                if ($err == "-") $err = "";
                if ($err == "") {
                    /**
                     * @var $gw IGROUP
                     */
                    $gw = new_doc($this->dbaccess, "GWORKSPACE");
                    
                    if ($gw->isAlive()) {
                        $err = $gw->insertDocument($gv->id);
                        $err.= $gv->insertDocument($ge->id);
                    }
                }
            }
            if ($err == "") {
                // create 2 profil
                $pdoc = createDoc($this->dbaccess, "PDOC", false);
                $pdoc->setValue(Attribute\PDoc::ba_title, sprintf(_("%s files") , $ref));
                $pdoc->setValue(Attribute\PDoc::prf_desc, sprintf(_("default profile for [ADOC %d] - %s - space files") , $this->id, $ref));
                $err = $pdoc->Add();
                $pfld = null;
                if ($err == "") {
                    $pfld = createDoc($this->dbaccess, "PDIR", false);
                    $pfld->setValue(Attribute\PDir::ba_title, sprintf(_("%s directories") , $ref));
                    $pfld->setValue(Attribute\PDir::prf_desc, sprintf(_("default profile for [ADOC %d] - %s - space directories") , $this->id, $ref));
                    $err = $pfld->Add();
                }
                if ($err == "") {
                    // affect default profil sor space
                    $this->setValue(myAttribute::fld_pdocid, $pdoc->id);
                    $this->setValue(myAttribute::fld_pdoc, $pdoc->title);
                    $this->setValue(myAttribute::fld_pdirid, $pfld->id);
                    $this->setValue(myAttribute::fld_pdir, $pfld->title);
                }
                // affect acls in profil
                if ($err == "") {
                    $err = $pdoc->setControl(false); //activate the profile
                    $pdoc->addControl($gvname, 'view');
                    $pdoc->addControl($gvname, 'send');
                    $pdoc->addControl($gename, 'edit');
                    $pdoc->addControl($gename, 'delete');
                    $pdoc->addControl("GWSPADMIN", "view");
                    $pdoc->addControl("GWSPADMIN", "edit");
                    $pdoc->addControl("GWSPADMIN", "unlock");
                    $pdoc->addControl("GWSPADMIN", "viewacl");
                    $err.= $pfld->setControl(false); //activate the profile
                    $pfld->addControl($gvname, 'view');
                    $pfld->addControl($gvname, 'open');
                    $pfld->addControl($gename, 'edit');
                    $pfld->addControl($gename, 'delete');
                    $pfld->addControl($gename, 'modify');
                    $pdoc->addControl("GWSPADMIN", "view");
                    $pdoc->addControl("GWSPADMIN", "edit");
                    $pdoc->addControl("GWSPADMIN", "modify");
                    $pdoc->addControl("GWSPADMIN", "open");
                    $pdoc->addControl("GWSPADMIN", "viewacl");
                }
            }
        }
        
        if ($err == "") {
            // create this own profil
            $pspace = createDoc($this->dbaccess, "PDIR", false);
            $pspace->setValue(Attribute\PDir::ba_title, sprintf(_("%s workspace profile") , $ref));
            $pspace->setValue(Attribute\PDir::prf_desc, sprintf(_("workspace profile for [ADOC %d] - %s - space files") , $this->id, $ref));
            $pspace->setValue(Attribute\PDir::dpdoc_famid, $this->fromid);
            $pspace->setValue(Attribute\PDir::dpdoc_fam, $this->getTitle($this->fromid));
            $err = $pspace->Add();
            if ($err == "") {
                $pspace->setControl(false);
                $pspace->addControl("GWSPADMIN", "view");
                $pspace->addControl("GWSPADMIN", "edit");
                $pspace->addControl("GWSPADMIN", "delete");
                $pspace->addControl("GWSPADMIN", "viewacl");
                $pspace->addControl("GWSPADMIN", "modifyacl");
                if ($gvname) {
                    $pspace->addControl($gvname, 'view');
                    $pspace->addControl($gvname, 'open');
                }
                $pspace->addControl("WSP_IDADMIN", 'view');
                $pspace->addControl("WSP_IDADMIN", 'edit');
                if ($gename) {
                    $pspace->addControl($gename, 'modify');
                }
                //    $this->dprofid=$pspace->id;
                $this->setprofil($pspace->id);
                $this->modify(true, array(
                    "profid",
                    "dprofid"
                ) , true);
            }
            
            $pigroup = createDoc($this->dbaccess, "PDIR", false);
            $pigroup->setValue(Attribute\PDir::ba_title, sprintf(_("%s group profile") , $ref));
            $pigroup->setValue(Attribute\PDir::prf_desc, sprintf(_("intranet group profile for [ADOC %d] - %s - space files") , $this->id, $ref));
            $pigroup->name = $this->getProfilGroupName();
            $err = $pigroup->Add();
            if ($err == "") {
                // create profil for igroup of the spaces
                $pigroup->setControl(false);
                $this->recomputeIGroupProfil();
                $gv->setProfil($pigroup->id);
                $gv->modify(true, array(
                    "profid"
                ) , true);
                $ge->setProfil($pigroup->id);
                $ge->modify(true, array(
                    "profid"
                ) , true);
            }
        }
        
        return $err;
    }
    function recomputeIGroupProfil()
    {
        $p = new_doc($this->dbaccess, $this->getProfilGroupName());
        if ($p->isAlive()) {
            $p->RemoveControl();
            $p->addControl("GWSPADMIN", "view");
            $p->addControl("GWSPADMIN", "edit");
            $p->addControl("GWSPADMIN", "modify");
            $p->addControl("GWSPADMIN", "open");
            $p->addControl("GWSPADMIN", "delete");
            $p->addControl("GWSPADMIN", "viewacl");
            $p->addControl("GWSPADMIN", "modifyacl");
            $idadmin = $this->getRawValue(myAttribute::wsp_idadmin);
            $ua = new_doc($this->dbaccess, $idadmin);
            $uida = $ua->getRawValue(Attribute\Iuser::us_whatid);
            if ($uida > 0) {
                $p->addControl($uida, 'view');
                $p->addControl($uida, 'edit');
                $p->addControl($uida, 'open');
                $p->addControl($uida, 'modify');
            }
            $gvname = $this->getViewGroupName();
            $gename = $this->getEditGroupName();
            $p->addControl($gvname, 'view');
            $p->addControl($gvname, 'open');
            $p->addControl($gename, 'modify');
        }
    }
    /**
     * suppress content before
     * if all content are not deleted the workspace is not deleted
     */
    function preDelete()
    {
        // delete content
        $err = '';
        $terr = $this->deleteItems();
        if (count($terr) > 0) {
            $dc = 0;
            foreach ($terr as $docid => $docerr) {
                if ($docerr == '') $dc++;
                else $err.= sprintf("%d : %s\n", $docid, $docerr);
            }
            addWarningMsg(sprintf(_("%d documents deleted") , $dc));
        }
        return $err;
    }
    /**
     * suppress profil & associated groups
     */
    function postDelete()
    {
        $gename = $this->getEditGroupName();
        $gvname = $this->getViewGroupName();
        // delete groups
        $err = '';
        $g = new_doc($this->dbaccess, $gename);
        if ($g->isAlive()) $err = $g->delete();
        if ($err) return $err;
        
        $g = new_doc($this->dbaccess, $gvname);
        if ($g->isAlive()) $err = $g->delete();
        if ($err) return $err;
        // delete profile
        $pid = $this->getRawValue(myAttribute::fld_pdocid);
        if ($pid) {
            $pdoc = new_doc($this->dbaccess, $pid);
            if ($pdoc->isAlive()) $err = $pdoc->delete();
            if ($err) return $err;
        }
        $pid = $this->getRawValue(myAttribute::fld_pdirid);
        if ($pid) {
            $pdoc = new_doc($this->dbaccess, $pid);
            if ($pdoc->isAlive()) $err = $pdoc->delete();
            if ($err) return $err;
        }
        return $err;
    }
    /**
     * change groups members
     * @global string $uchange Http var : array of document user id to indicate the change
     * @global string $uprof Http var : array of document user id to indicate the new group
     * @return string
     */
    function postStore()
    {
        $gename = $this->getEditGroupName();
        $gvname = $this->getViewGroupName();
        /**
         * @var IGROUP $ge
         * @var IGROUP $gv
         */
        $ge = new_doc($this->dbaccess, $gename);
        $gv = new_doc($this->dbaccess, $gvname);
        
        $changes = getHttpVars("uchange");
        $uprofs = getHttpVars("uprof");
        if ($changes) {
            /**
             * @var array $changes
             */
            foreach ($changes as $duid => $change) {
                if ($change == "nochange") continue;
                if ($change == "change") {
                    if ($uprofs[$duid] == "edit") {
                        $gv->removeDocument($duid);
                        $ge->insertDocument($duid);
                    } else {
                        $ge->removeDocument($duid);
                        $gv->insertDocument($duid);
                    }
                }
                if ($change == "new") {
                    if ($uprofs[$duid] == "edit") {
                        $ge->insertDocument($duid);
                    } else {
                        $gv->insertDocument($duid);
                    }
                }
                if ($change == "deleted") {
                    $ge->removeDocument($duid);
                    $gv->removeDocument($duid);
                }
            }
        }
        
        $fi = $this->getRawValue(myAttribute::wsp_idadmin);
        $fiold = $this->getOldRawValue(myAttribute::wsp_idadmin);
        
        if (($fiold !== false) && ($fi != $fiold)) $this->recomputeIGroupProfil();
        return '';
    }
    /**
     * @param Igroup $igroup
     * @return array
     */
    private function getMembersOfIGroup(Igroup & $igroup)
    {
        $account = $igroup->getAccount();
        $userList = $account->getAllMembers();
        foreach ($userList as & $user) {
            $user['title'] = htmlspecialchars(trim($user['lastname'] . " " . $user['firstname']));
        }
        unset($user);
        return $userList;
    }
    /**
     * @templateController
     */
    function adminworkspace()
    {
        global $action;
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/inserthtml.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/adminworkspace.js");
        $this->editattr();
        /**
         * @var \Dcp\Family\Igroup $gv
         * @var \Dcp\Family\Igroup $ge
         */
        $gv = new_doc($this->dbaccess, $this->getViewGroupName());
        $ge = new_doc($this->dbaccess, $this->getEditGroupName());
        
        if ($gv->isAlive()) {
            $tmv = array();
            $userList = $this->getMembersOfIGroup($gv);
            foreach ($userList as $user) {
                $tmv[$user['id']] = array(
                    'name' => $user['title'],
                    'iduser' => $user['fid'],
                    'viewselected' => 'selected',
                    'editselected' => ''
                );
            }
            
            if ($ge->isAlive()) {
                $userList = $this->getMembersOfIGroup($ge);
                foreach ($userList as $user) {
                    $tmv[$user['id']] = array(
                        'name' => $user['title'],
                        'iduser' => $user['fid'],
                        'viewselected' => '',
                        'editselected' => 'selected'
                    );
                }
            }
            
            $this->lay->setBlockData("MEMBERS", $tmv);
            $this->lay->set("nmembers", sprintf(_("%s members") , count($tmv)));
        }
    }
    /**
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @templateController
     */
    function viewworkspace($target = "_self", $ulink = true, $abstract = false)
    {
        $this->viewdefaultcard($target, $ulink, $abstract);
        
        $gvname = $this->getViewGroupName();
        $gv = new_doc($this->dbaccess, $gvname);
        
        $gename = $this->getEditGroupName();
        $ge = new_doc($this->dbaccess, $gename);
        
        $this->lay->set("geid", $ge->id);
        $this->lay->set("getitle", $ge->title);
        $this->lay->set("gvid", $gv->id);
        $this->lay->set("gvtitle", $gv->title);
        
        $this->lay->set("icon", $this->getIcon());
    }
}
