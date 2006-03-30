<?php

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


?>