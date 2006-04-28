<?php

var $defaultview= "WORKSPACE:VIEWSIMPLEFILE:T";
//var $defaultedit= "WORKSPACE:EDITSIMPLEFILE:T";

function postModify() {
  $this->computeMime();
}
function specRefresh() {
  $this->computeMime();
}
  /**
   * compute the mime type and the size
   */
function computeMime() {
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {

	include_once ("WORKSPACE/Lib.FileMime.php");

	$this->setValue("sfi_mimetxt",getTextMimeFile($info->path));
	$this->setValue("sfi_mimesys",getSysMimeFile($info->path,$info->name));
	$this->setValue("sfi_title",$info->name);
	$this->setValue("sfi_filesize",$info->size);

	
	$mime=$this->getValue("sfi_mimesys");
	$icon=getIconMimeFile($mime);
	if ($icon) {	  
	      $this->setValue("sfi_mimeicon",$icon);
	      $this->icon=$icon;
	} else {
	  $fdoc=$this->getFamDoc();
	  $this->icon=$fdoc->icon;
	}


      }
    }
  }
}


function viewsimplefile($target="_self",$ulink=true,$abstract=false) {
  $this->viewdefaultcard($target,$ulink,$abstract);

  if ($this->revision == 0) {
    $cdate=FrenchDateToUnixTs($this->cdate);
  } else {
    $idoc=new_doc($this->dbaccess,$this->initid);
    $cdate=FrenchDateToUnixTs($idoc->cdate);    
  }
  $this->lay->set("createdate",strftime("%A %d %B %Y",$cdate));
  $this->lay->set("moddate",strftime("%A %d %B %Y",$this->revdate));

  $size=$this->getValue("sfi_filesize");
  if ($size < 0) $dsize="";
  else if ($size < 1024) $dsize=sprintf(_("%d bytes"),$size);
  else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
  else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
  $this->lay->set("dsize",$dsize);

}


?>