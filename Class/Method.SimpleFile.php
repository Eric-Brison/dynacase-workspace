<?php


  /**
   * compute the mime type and the size
   */
function postModify() {
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {


	$this->setValue("sfi_mimetxt",trim(`file -b "$info->path"`));
	$this->setValue("sfi_mimesys",trim(`file -bi "$info->path"`));
	$this->setValue("sfi_title",$info->name);
	$this->setValue("sfi_filesize",$info->size);

	include ("WORKSPACE/Lib.FileMime.php");
	
	$mime=$this->getValue("sfi_mimesys");
	if ($mime) {
	  $tmime=explode(",; ",$mime);
	  $mime=trim($tmime[0]);
	  if (isset($mimeIcon[$mime])) {
	    $this->setValue("sfi_mimeicon",$mimeIcon[$mime].".png");
	    $this->icon=$this->getValue("sfi_mimeicon");	  
	  } else {
	    $p=strpos($mime, '/');
	    $mime=substr($mime,0,$p);
	    if (isset($mimeIcon[$mime])) {
	      $this->setValue("sfi_mimeicon",$mimeIcon[$mime].".png");
	      $this->icon=$this->getValue("sfi_mimeicon");
	    } 
	  }
	}
      }
    }
  }
}

function getFileMimeType($f) {
  if ($f) {
    
  }
}

?>