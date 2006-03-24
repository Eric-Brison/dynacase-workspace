<?php


  /**
   * compute the mime type and the size
   */
function specRefresh() {
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {
	print_r2($info);
	print mime_content_type($info->path);
	$this->setValue("sfi_mimetxt",trim(`file -ib "$info->path"`));
	$this->setValue("sfi_title",$info->name);
	$this->setValue("sfi_filesize",$info->size);
	print "<br>".trim(`file -ib "$info->path"`);
	print "<br>".trim(`file -b "$info->path"`);
      }
    }
    
  }
}

function getFileMimeType($f) {
  if ($f) {
    
  }
}

?>