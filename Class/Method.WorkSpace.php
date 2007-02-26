<?php
 
public $eviews=array("WORKSPACE:ADMINWORKSPACE");
public $cviews=array("WORKSPACE:VIEWWORKSPACE");

public $defaultview="WORKSPACE:VIEWWORKSPACE:T";

/**
 * get view groupe name
 */
private function getViewGroupName() {
  $ref=$this->getValue("WSP_REF");
  return "GWS_V".strtoupper($ref);
}
/**
 * get edit groupe name
 */
private function getEditGroupName() {
  $ref=$this->getValue("WSP_REF");
  return "GWS_E".strtoupper($ref);
}
/**
 * get profil groupe name
 */
private function getProfilGroupName() {
  $ref=$this->getValue("WSP_REF");
  return "GWS_P".strtoupper($ref);
}

  /**
   * Create 2 groups : one for collect view user and other for edit user privilege
   * set profil to itself : computed with admin prilege
   */
function postCreated() {
  if ($this->revision > 0) return;
  $ref=unaccent($this->title);
  $ref=ereg_replace("[[:punct:]]","",$ref);
  $ref=strtolower(str_replace(" ","_",$ref));
  $this->setValue("WSP_REF",$ref);
  $this->modify();
  if ($ref != "") {
    $gv=createDoc($this->dbaccess,"IGROUP",false);
    $ge=createDoc($this->dbaccess,"IGROUP",false);
    
    $gv->setValue("us_login","gv.".$ref);
    $gv->setValue("grp_name",sprintf(_("%s readers"),$this->title));
    $gv->setValue("grp_role",sprintf(_("Group of users that can view files in [ADOC %d] space"),$this->id));
    $gvname=$this->getViewGroupName();
    $gv->name=$gvname;

    $ge->setValue("us_login","ge.".$ref);
    $ge->setValue("grp_name",sprintf(_("%s writers"),$this->title));
    $ge->setValue("grp_role",sprintf(_("Group of users that can edit files in [ADOC %d] space"),$this->id));
    $gename=$this->getEditGroupName();
    $ge->name=$gename;

    $err=$gv->Add();
    if ($err == "")  $err=$ge->Add();
    if ($err == "") {
      $err=$gv->Postmodify();
      if ($err=="-") $err="";
      if ($err == "")  $err=$ge->Postmodify();
      if ($err=="-") $err="";
      if ($err == "") {
	$gw=new_doc($this->dbaccess,"GWORKSPACE");

	if ($gw->isAlive()) {
	  $err=$gw->AddFile($gv->id);
	  $err.=$gv->AddFile($ge->id);
	}
      }
    }    
    if ($err=="") {
      // create 2 profil
      $pdoc=createDoc($this->dbaccess,"PDOC",false);
      $pdoc->setValue("ba_title",sprintf(_("%s files"),$ref));
      $pdoc->setValue("prf_desc",sprintf(_("default profile for [ADOC %d] - %s - space files"),$this->id,$ref));
      $err=$pdoc->Add();
      if ($err == "") {
	$pfld=createDoc($this->dbaccess,"PDIR",false);
	$pfld->setValue("ba_title",sprintf(_("%s directories"),$ref));
	$pfld->setValue("prf_desc",sprintf(_("default profile for [ADOC %d] - %s - space directories"),$this->id,$ref));
	$err=$pfld->Add();
      }
      if ($err == "") {
	// affect default profil sor space
	$this->setValue("fld_pdocid",$pdoc->id);
	$this->setValue("fld_pdoc",$pdoc->title);
	$this->setValue("fld_pdirid",$pfld->id);
	$this->setValue("fld_pdir",$pfld->title);
      }
      // affect acls in profil
      
      if ($err == "") {
	$err=$pdoc->setControl(false); //activate the profile
	$pdoc->addControl($gvname,'view');
	$pdoc->addControl($gvname,'send');
	$pdoc->addControl($gename,'edit');
	$pdoc->addControl($gename,'delete');
	$pdoc->addControl("GWSPADMIN","view");
	$pdoc->addControl("GWSPADMIN","edit");
	$pdoc->addControl("GWSPADMIN","unlock");
	$pdoc->addControl("GWSPADMIN","viewacl");
	$err=$pfld->setControl(false); //activate the profile
	$pfld->addControl($gvname,'view');
	$pfld->addControl($gvname,'open');
	$pfld->addControl($gename,'edit');
	$pfld->addControl($gename,'delete');
	$pfld->addControl($gename,'modify');
	$pdoc->addControl("GWSPADMIN","view");
	$pdoc->addControl("GWSPADMIN","edit");
	$pdoc->addControl("GWSPADMIN","modify");
	$pdoc->addControl("GWSPADMIN","open");
	$pdoc->addControl("GWSPADMIN","viewacl");
      }      
    }
  }
  
  if ($err=="") {
    // create this own profil
    $pspace=createDoc($this->dbaccess,"PDIR",false);
    $pspace->setValue("ba_title",sprintf(_("%s workspace profile"),$ref));
    $pspace->setValue("prf_desc",sprintf(_("workspace profile for [ADOC %d] - %s - space files"),$this->id,$ref));
    $pspace->setValue("dpdoc_famid",$this->fromid);
    $pspace->setValue("dpdoc_fam",$this->getTitle($this->fromid));
    $err=$pspace->Add();
    if ($err == "") {
      $pspace->setControl(false);
      $pspace->addControl("GWSPADMIN","view");
      $pspace->addControl("GWSPADMIN","edit");
      $pspace->addControl("GWSPADMIN","delete");
      $pspace->addControl("GWSPADMIN","viewacl");
      $pspace->addControl("GWSPADMIN","modifyacl");
      $pspace->addControl($gvname,'view');
      $pspace->addControl($gvname,'open');
      $pspace->addControl("WSP_IDADMIN",'view');
      $pspace->addControl("WSP_IDADMIN",'edit');
      $pspace->addControl($gename,'modify');

      //    $this->dprofid=$pspace->id;
      $this->setprofil($pspace->id);
      $this->modify(true,array("profid","dprofid"),true);
    }
  
  
    $pigroup=createDoc($this->dbaccess,"PDIR",false);
    $pigroup->setValue("ba_title",sprintf(_("%s group profile"),$ref));
    $pigroup->setValue("prf_desc",sprintf(_("intranet group profile for [ADOC %d] - %s - space files"),$this->id,$ref));
    $pigroup->name=$this->getProfilGroupName();
    $err=$pigroup->Add();
    if ($err == "") {
      // create profil for igroup of the spaces
      $pigroup->setControl(false);
      $this->recomputeIGroupProfil();
      $gv->setProfil($pigroup->id);
      $gv->modify(true,array("profid"),true);
      $ge->setProfil($pigroup->id);
      $ge->modify(true,array("profid"),true);
    }
  }

  return $err;
}
function recomputeIGroupProfil() {
  $p=new_doc($this->dbaccess,$this->getProfilGroupName());
  if ($p->isAlive()) {
    $p->RemoveControl();
    $p->addControl("GWSPADMIN","view");
    $p->addControl("GWSPADMIN","edit");
    $p->addControl("GWSPADMIN","modify");
    $p->addControl("GWSPADMIN","open");
    $p->addControl("GWSPADMIN","delete");
    $p->addControl("GWSPADMIN","viewacl");
    $p->addControl("GWSPADMIN","modifyacl");
    $idadmin=$this->getValue("wsp_idadmin");
    $ua=new_doc($this->dbaccess,$idadmin);
    $uida=$ua->getValue("us_whatid");
    if ($uida > 0) {      
      $p->addControl($uida,'view');
      $p->addControl($uida,'edit');
      $p->addControl($uida,'open');
      $p->addControl($uida,'modify');
    }
    $p->addControl($gvname,'view');
    $p->addControl($gvname,'open');
    $p->addControl($gename,'modify');
    
  }
  
}
/**
 * suppress profil & associated groups
 */
function postDelete() {
  $gename=$this->getEditGroupName();
  $gvname=$this->getViewGroupName();

  $g=new_doc($this->dbaccess,$gename);
  if ($g->isAlive()) $g->delete();
  $g=new_doc($this->dbaccess,$gvname);
  if ($g->isAlive()) $g->delete();

  
}

/**
 * change groups members
 * @global uchange Http var : array of document user id to indicate the change
 * @global uprof Http var : array of document user id to indicate the new group
 */
function postModify() {
  $gename=$this->getEditGroupName();
  $gvname=$this->getViewGroupName();

  $ge=new_doc($this->dbaccess,$gename);
  $gv=new_doc($this->dbaccess,$gvname);


  $changes=getHttpVars("uchange");
  $uprofs=getHttpVars("uprof");
  if ($changes) {
    foreach ($changes as $duid=>$change) {
      if ($change=="nochange") continue;
      if ($change=="change") {
	if ($uprofs[$duid]=="edit") {
	  $gv->delFile($duid);
	  $ge->addFile($duid);
	} else {
	  $ge->delFile($duid);
	  $gv->addFile($duid);	
	}
      }
      if ($change=="new") {
	if ($uprofs[$duid]=="edit") {
	  $ge->addFile($duid);
	} else {
	  $gv->addFile($duid);	
	}
      }
      if ($change=="deleted") {
	$ge->delFile($duid);
	$gv->delFile($duid);      
      }
    }
  }
  
  $fi=$this->getValue("wsp_idadmin");
  $fiold=$this->getOldValue("wsp_idadmin");

  if (($fiold!==false)&&($fi != $fiold)) $this->recomputeIGroupProfil();
}
function adminworkspace() {
  global $action;
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/inserthtml.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/adminworkspace.js");
  $this->editattr();
  
  $gv=new_doc($this->dbaccess,$this->getViewGroupName());
  $ge=new_doc($this->dbaccess,$this->getEditGroupName());

  if ($gv->isAlive()) {
    $tuvid=$gv->getTValue("grp_idruser");
    $tuv=$gv->getTValue("grp_ruser");
    $tmv=array();
    foreach ($tuvid as $k=>$v) {
      $tmv[$v]=array("name"=>$tuv[$k],
		     "iduser"=>$v,
		     "viewselected"=>"selected",
		     "editselected"=>"");      
    }

    if ($ge->isAlive()) {
      $tuvid=$ge->getTValue("grp_idruser");
      $tuv=$ge->getTValue("grp_ruser");
      foreach ($tuvid as $k=>$v) {
	$tmv[$v]=array("name"=>$tuv[$k],
		       "iduser"=>$v,
		       "viewselected"=>"",
		       "editselected"=>"selected");      
      }
    }

    $this->lay->setBlockData("MEMBERS",$tmv);
    $this->lay->set("nmembers",sprintf(_("%s members"),count($tmv)));
  }

}
function viewworkspace($target="_self",$ulink=true,$abstract=false) {  
  $this->viewdefaultcard($target,$ulink,$abstract);

  
  $gvname=$this->getViewGroupName();
  $gv=new_doc($this->dbaccess,$gvname);
  
  $gename=$this->getEditGroupName();
  $ge=new_doc($this->dbaccess,$gename);

  $this->lay->set("geid",$ge->id);
  $this->lay->set("getitle",$ge->title);
  $this->lay->set("gvid",$gv->id);
  $this->lay->set("gvtitle",$gv->title);

  $this->lay->set("icon",$this->getIcon());
}



?>