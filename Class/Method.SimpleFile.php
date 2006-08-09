<?php

var $defaultview= "WORKSPACE:VIEWSIMPLEFILE:T";
var $defaultmview= "WORKSPACE:MAILSIMPLEFILE:T";
//var $defaultedit= "WORKSPACE:EDITSIMPLEFILE:T";

function postModify() {
  $this->computeMime();

  /*
  $fi=$this->getValue("sfi_file");
  $fiold=$this->getOldValue("sfi_file");
  if (($fiold !== false) && ($fi != $fiold))  $this->computeThumbnail();
  */

  $fi=$this->getValue("sfi_inedition");
  $fiold=$this->getOldValue("sfi_inedition");

  if (($fi == 0) && ($fiold==1)) {    
    $err=$this->unlock(); // auto unlock in not in edition mode
    if ($err=="") {
      global $action;
      $action->AddActionDone("UNLOCKFILE",$this->id);
    }
  }
}

/**
 * use for duplicate physicaly the file
 */
function postCopy() {
  
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {
	$cible=$info->path;
	if (file_exists($cible)) {
	  $err=$vf->Store($cible, false , $vid);
	  if ($err == "") {
	    $pp=strrpos($info->name,'.');
	    $base=substr($info->name,0,$pp). _(" (copy)").substr($info->name,$pp);
	    $vf->Rename($base);
	    $this->setValue("sfi_file",$reg[1]."|$vid");
	    $this->modify();
	  }
	}
      }
    }
  }
  return $err;
}
function specRefresh() {
  // $this->computeMime();
  //  if ($this->getValue("sfi_thumb")=="")   $this->computeThumbnail();
}

/**
 * return the converter for thumbnail based of mime type
 * @return string empty if no converter found
 */
function canThumbnail() {
  $mime=$this->getValue("sfi_mimesys");
	
  if (ereg("(.*)/(.*)",$mime,$reg) ) {
    $mimebase=$reg[1];
  }
  $convert="";
  if ($mimebase == "image") {
    $convert="convert";
  } else {
	  
    switch ($mime) {
    case "text/xml":
    case "text/html":
    case "application/pdf":
    case "application/postscript":
      $convert="convert";
      break;
    case "application/vnd.ms-excel":
      $convert="xlhtml";
      break;
    case "application/msword--":
      $convert="abiword";
      break;
    case "application/vnd.oasis.opendocument.presentation":
    case "application/vnd.oasis.opendocument.spreadsheet":
    case "application/vnd.oasis.opendocument.graphics":
    case "application/vnd.oasis.opendocument.text":
      $convert="unzip";
      break;
	    
    }
  }
  return $convert;
}

function computeThumbnail() {
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {

	$convert=$this->canThumbnail();
	$shadow="";
	//	$shadow="\( +clone -background black -shadow 60x4+4+4  \)";

	$convertcmd="convert -thumbnail 200\\> %s[0] -crop 205x205+0+0 -mattecolor black -frame 5x5+2+2 $shadow  +swap    -background none -mosaic -crop 225x225+0+0  %s";

	//	$convertcmd="convert -thumbnail 200 %s[0] -crop 205x205+0+0  -mattecolor black -frame 5x5+2+2   %s";
	switch ($convert) {

	case "convert":
	  $pf=$info->path;
	  $cible=uniqid("/tmp/thumb").".png";

	  $cmd = sprintf($convertcmd,$pf, $cible);
	  system($cmd);
	  // print_r2 ($cmd);
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    $this->modify(true,array("sfi_thumb"),true);
	    unlink($cible);
	  }
	  break;
	case "abiword":
	  $pf=$info->path;
	  $ciblepng=uniqid("/tmp/thumb").".png";
	  
	  // $cmd = sprintf("abiword --to=pdf -o %s  %s",$ciblepdf, $pf );
	  $cmd = sprintf('abiword --print="|convert -[0] %s" %s',$ciblepng, $pf);
	  system($cmd);
	  //print ($cmd);
	  if (file_exists($ciblepng)) {




	    $cible=uniqid("/tmp/thumb").".png";
	    //	    $cmd = sprintf("convert -thumbnail 200 %s[0] -crop 205x205+0+0  -mattecolor black -frame 5x5+2+2 \( +clone -background black -shadow 4x4+4+4 \) +swap   -background none -mosaic  %s",$ciblepdf, $cible);
	    $cmd = sprintf($convertcmd,$ciblepng, $cible);
	  $c=system($cmd);
	  // print ($cmd."<br>$c");
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    $this->modify(true,array("sfi_thumb"),true);
	    unlink($cible);
	  }
	  unlink($ciblepng);
	  }
	  break;
	case "xlhtml":
	  $pf=$info->path;
	  $ciblepdf=uniqid("/tmp/thumb").".html";
	  
	  $cmd = sprintf("xlhtml -xp:0  %s > %s", $pf, $ciblepdf );
	  system($cmd);
	  //  print ($cmd);
	  if (file_exists($ciblepdf)) {




	  $cible=uniqid("/tmp/thumb").".png";

	  $cmd = sprintf($convertcmd ,$ciblepdf, $cible);
	  system($cmd);
	  //	  print ($cmd);
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    $this->modify(true,array("sfi_thumb"),true);
	    unlink($cible);
	  }
	  unlink($ciblepdf);
	  }
	  break;
	case "unzip":
	  $pf=$info->path;
	  $cibledir=uniqid("/tmp/thumb");
	  
	  $cmd = sprintf("unzip -j %s Thumbnails/thumbnail.png -d %s >/dev/null", $pf, $cibledir );
	  system($cmd);
	  //  print ($cmd);
	  $ciblepng=$cibledir."/thumbnail.png";

	  if ($ciblepng) {




	  $cible=uniqid("/tmp/thumb").".png";
	  $convertcmd="convert  %s[0]  -mattecolor black -frame 5x5+2+2 $shadow +swap    -background none -mosaic  %s";
	
	  $cmd = sprintf($convertcmd ,$ciblepng, $cible);
	  system($cmd);
	  // print ($cmd);
	  if (file_exists($cible)) {
	    $err=$vf->Store($cible, false , $vid);

	    $ft="image/png|$vid";
	    $this->setValue("sfi_thumb",$ft);
	    $this->modify(true,array("sfi_thumb"),true);
	    unlink($cible);
	  }
	  unlink($ciblepng);
	  rmdir($cibledir);
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
  static $vf;
  $f=$this->getValue("sfi_file");
  if ($f) {
    if (ereg ("(.*)\|(.*)", $f, $reg)) {
      if (!$vf) $vf = newFreeVaultFile($this->dbaccess);
      if ($vf->Show($reg[2], $info) == "") {
	include_once ("WORKSPACE/Lib.FileMime.php");

	$this->setValue("sfi_mimetxt",getTextMimeFile($info->path));
	$short=strtok($this->getValue("sfi_mimetxt"),",");
	if (! $short) $short=$this->getValue("sfi_mimetxt");
	$this->setValue("sfi_mimetxtshort",$short);
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

function mailsimplefile($target="_self",$ulink=true,$abstract=false) {
  $this->viewsimplefile($target,$ulink,$abstract);
  $this->lay->set("moddate",strftime("%A %d %B %Y %H:%M",$this->revdate));
}

function viewsimpleprop($target="_self",$ulink=true,$abstract=false) {
  $this->viewdefaultcard($target,$ulink,$abstract);
  if ($this->revision == 0) {
    $cdate=FrenchDateToUnixTs($this->cdate);
    $adate=FrenchDateToUnixTs($this->adate);
  } else {
    $idoc=new_doc($this->dbaccess,$this->initid);
    $cdate=FrenchDateToUnixTs($idoc->cdate);    
    $adate=FrenchDateToUnixTs($idoc->adate);    
  }
  $this->lay->set("createdate",strftime("%A %d %B %Y %H:%M",$cdate));
  $this->lay->set("accessdate",strftime("%A %d %B %Y %H:%M",$adate));
  $this->lay->set("moddate",strftime("%A %d %B %Y %H:%M",$this->revdate));
  $this->lay->set("theversion",($this->version!="")?$this->version:_("undefined"));
  if ($this->locked == -1)  $this->lay->set("thelocker",_("fixed"));
  elseif ($this->locked == 0) $this->lay->set("thelocker",_("nobody"));
  else {
    $uid=abs($this->locked);
    $u=new User("",$uid);
    if ($u->isAffected()) {
      $this->lay->set("thelocker", sprintf("%s %s",$u->firstname,$u->lastname));
    } else {
      $this->lay->set("thelocker", sprintf(_("unknow user %s"),$uid));
    }
  }
  



  $size=$this->getValue("sfi_filesize");
  if ($size < 0) $dsize="";
  else if ($size < 1024) $dsize=sprintf(_("%d bytes"),$size);
  else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
  else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
  $this->lay->set("dsize",$dsize);

  $path=$this->getMainPath();
  $spath="";
  foreach ($path as $k=>$v) {
    $spath=$v."/".$spath;
  }

  $this->lay->set("thepath", $spath);


}
function viewsimplefile($target="_self",$ulink=true,$abstract=false) {
  global $action;
  $recomputeThumbnail=(getHttpVars("recomputethumb")=="yes");
  if ($recomputeThumbnail) $this->computeThumbnail();



  $this->viewdefaultcard($target,$ulink,$abstract);
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/editattr.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/viewsimplefile.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDL/Layout/popupdoc.js");
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/FDC/Layout/inserthtml.js");


  $this->lay->set("emblem",$this->getEmblem());

  $thetitle=$this->getValue("sfi_titlew");
  if ($thetitle=="") $thetitle=sprintf(_("No title"));
  $this->lay->set("thetitle",$thetitle);


  $size=$this->getValue("sfi_filesize");
  if ($size < 0) $dsize="";
  else if ($size < 1024) $dsize=sprintf(_("%d bytes"),$size);
  else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
  else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
  $this->lay->set("dsize",$dsize);
  $this->lay->set("thumb",($this->getValue("sfi_thumb")!=""));
  $this->lay->set("ishtml",$this->getValue("sfi_mimesys")=="text/html");
  $this->lay->set("canedithtml",($this->getValue("sfi_mimesys")=="text/html")&&($this->getValue('sfi_inedition') != 1));
  $this->lay->set("isinedition",($this->fileIsInEdition()==MENU_ACTIVE));
  $this->lay->set("isnotinedition",($this->fileIsNotInEdition()==MENU_ACTIVE));
  $this->lay->set("canedit",($this->canEdit()==""));
  $this->lay->set("canversionned",($this->canVersionned()==MENU_ACTIVE));
    //$this->lay->set("ishtml",ereg("html|plain",$this->getValue("sfi_mimesys")));
  $this->lay->set("isinline",ereg("html|image|plain|text/xml",$this->getValue("sfi_mimesys")));

  $this->lay->set("thumbrecompute",$this->canThumbnail());


  $h=$this->getHisto(true);
  $parti=array();
  $tcomment=array();
  foreach ($h as $k=>$v) {
    $parti[$v["uname"]]=$v["uname"];
    if (($v["level"] == HISTO_INFO)||($v["level"] == HISTO_MESSAGE)) {      
      $tcomment[$k]=$v;
      $tcomment[$k]["jdate"]=strtok($v["date"]," ");
      if (count($tcomment) > 1) break;
    }      
  }

  $this->lay->set("participate",implode(", ",$parti));
  $this->lay->setBlockData("comments",$tcomment);

}


function createtext() {
  global $action;
  $a=$this->getAttribute("sfi_titlew");
  $a->needed="Y";

  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/fckeditor/fckeditor.js");
  $this->editattr();
  
}

function postCreated() {
  // convert html to file

  $html=getHttpVars("wscreatefile");

  if (($this->getValue("sfi_file")=="")  && $html) {
    $this->SetTextValueInFile("sfi_file",$html,$this->getValue("sfi_titlew").".html");
    $this->modify();
  }
  
}

/**
 * menu state to view upload file menu
 *  test if file is already downloaded to be changed
 */
function fileIsInEdition() {
  if ($this->CanEdit()!="") return MENU_INVISIBLE;

  if ($this->getValue('sfi_inedition') == 1) return MENU_ACTIVE;
  else return MENU_INVISIBLE;
}
  
/**
 * inverse of ::fileIsInEdition()
 */
function fileIsNotInEdition() {
  if ($this->CanEdit()!="") return MENU_INVISIBLE;
  if ($this->fileIsInEdition() == MENU_INVISIBLE) return MENU_ACTIVE;
  return MENU_INVISIBLE;
}
  
/**
 * menu state to view add version menu
 */
function canVersionned() {
  if ($this->CanEdit()!="") return MENU_INACTIVE;
  if ($this->getValue('sfi_version') == "") return MENU_INVISIBLE;
  if ($this->getValue('sfi_inedition') == 1) return MENU_INACTIVE;
  return MENU_ACTIVE;   
}

function editupload() {
  global $action;
  $action->parent->AddJsRef($action->GetParam("CORE_PUBURL")."/WORKSPACE/Layout/editupload.js");
  $this->viewprop();
  $this->editattr();  
}


function editversion() {
  $this->editattr();  
}

?>