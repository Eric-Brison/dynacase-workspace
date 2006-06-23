<?php

  /**
   * Create 2 groups : one for collect view user and other for edit user privilege
   * set profil to itself : computed with admin prilege
   */
function postCreated() {
  $ref=$this->getValue("WSP_REF");
  if ($ref != "") {
    $gv=createDoc($this->dbaccess,"IGROUP",false);
    $ge=createDoc($this->dbaccess,"IGROUP",false);
    
    $gv->setValue("us_login","gv.".$ref);
    $gv->setValue("grp_name","gv.".$ref);
    $gv->setValue("grp_role",sprintf(_("Group of users that can view files in %s space"),$this->title));
    $gvname="GWS_V".strtoupper($ref);
    $gv->name=$gvname;

    $ge->setValue("us_login","ge.".$ref);
    $ge->setValue("grp_name","ge.".$ref);
    $ge->setValue("grp_role",sprintf(_("Group of users that can edit files in %s space"),$this->title));
    $gename="GWS_R".strtoupper($ref);
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
	  $err=$gv->AddFile($ge->id);
	}
      }
    }    
    if ($err=="") {
      // create 2 profil
      $pdoc=createDoc($this->dbaccess,"PDOC",false);
      $pdoc->setValue("ba_title",sprintf(_("%s files"),$ref));
      $pdoc->setValue("prf_desc",sprintf(_("default profile for %s - %s - space files"),$this->title,$ref));
      $err=$pdoc->Add();
      if ($err == "") {
	$pfld=createDoc($this->dbaccess,"PDIR",false);
	$pfld->setValue("ba_title",sprintf(_("%s directories"),$ref));
	$pfld->setValue("prf_desc",sprintf(_("default profile for %s - %s - space directories"),$this->title,$ref));
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
	$pdoc->addControl($gename,'edit');
	$pdoc->addControl($gename,'delete');
	$pdoc->addControl("GWSPADMIN","view");
	$pdoc->addControl("GWSPADMIN","edit");
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
  



  if ($err != "") print_r2($err);
  return $err;
}



?>