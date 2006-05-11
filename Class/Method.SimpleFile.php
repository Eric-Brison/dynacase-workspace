<?php

var $defaultview= "WORKSPACE:VIEWSIMPLEFILE:T";
//var $defaultedit= "WORKSPACE:EDITSIMPLEFILE:T";

function postModify() {
  $this->computeMime();


  $fi=$this->getValue("sfi_file");
  $fiold=$this->getOldValue("sfi_file");
  if (($fiold !== false) && ($fi != $fiold))  $this->computeThumbnail();

}



function specRefresh() {
  $this->computeMime();
  print_r2($this->getValues());
  //  if ($this->getValue("sfi_thumb")=="") 
$this->computeThumbnail();
}

function computeThumbnail() {
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {

	$mime=$this->getValue("sfi_mimesys");
	
	print "$mime<br>";
	if (ereg("(.*)/(.*)",$mime,$reg) ) {
	  $mimebase=$reg[1];
	}
	$convert="";
	if ($mimebase == "image") {
	  $convert="convert";
	} else {
	  
	  switch ($mime) {
	  case "text/xml":
	  case "application/pdf":
	  case "application/postscript":
	    $convert="convert";
	    break;
	  case "application/vnd.ms-excel":
	    $convert="xlhtml";
	    break;
	  case "application/vnd.oasis.opendocument.text":
	    $convert="abiword";
	    break;
	    
	  }
	}


	$convertcmd="convert -thumbnail 200 %s[0] -crop 205x205+0+0 -mattecolor black -frame 5x5+2+2 \( +clone -background navy -shadow 60x4+4+4  \) +swap    -background none -mosaic -crop 225x225+0+0  %s";

	//	$convertcmd="convert -thumbnail 200 %s[0] -crop 205x205+0+0  -mattecolor black -frame 5x5+2+2   %s";
	switch ($convert) {

	case "convert":
	  $pf=$info->path;
	  $cible=uniqid("/tmp/thumb").".png";

	  $cmd = sprintf($convertcmd,$pf, $cible);
	  system($cmd);
	  print ($cmd);
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    unlink($cible);
	  }
	  break;
	case "abiword":
	  $pf=$info->path;
	  $ciblepdf=uniqid("/tmp/thumb").".pdf";
	  
	  $cmd = sprintf("abiword --to=pdf -o %s  %s",$ciblepdf, $pf );
	  system($cmd);
	  print ($cmd);
	  if (file_exists($ciblepdf)) {




	    $cible=uniqid("/tmp/thumb").".png";
	    //	    $cmd = sprintf("convert -thumbnail 200 %s[0] -crop 205x205+0+0  -mattecolor black -frame 5x5+2+2 \( +clone -background black -shadow 4x4+4+4 \) +swap   -background none -mosaic  %s",$ciblepdf, $cible);
	    $cmd = sprintf($convertcmd,$ciblepdf, $cible);
	  system($cmd);
	  print ($cmd);
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    unlink($cible);
	  }
	  unlink($ciblepdf);
	  }
	  break;
	case "xlhtml":
	  $pf=$info->path;
	  $ciblepdf=uniqid("/tmp/thumb").".html";
	  
	  $cmd = sprintf("xlhtml -xp:0  %s > %s", $pf, $ciblepdf );
	  system($cmd);
	  print ($cmd);
	  if (file_exists($ciblepdf)) {




	  $cible=uniqid("/tmp/thumb").".png";

	  $cmd = sprintf($convertcmd ,$ciblepdf, $cible);
	  system($cmd);
	  print ($cmd);
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    unlink($cible);
	  }
	  unlink($ciblepdf);
	  }
	  break;
	}
	//	      print "computeThumbnail $icon";
      } 
    }
  }  
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
  global $action;
  $this->viewdefaultcard($target,$ulink,$abstract);
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/editattr.js");


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
  $this->lay->set("thumb",($this->getValue("sfi_thumb")!=""));

}


?>