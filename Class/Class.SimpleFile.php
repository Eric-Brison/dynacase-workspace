<?php
/*
 * Fichier simple
 *
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package WORKSPACE
*/

namespace Dcp\Workspace;

use \Dcp\AttributeIdentifiers\SimpleFile as myAttribute;
use \Dcp\AttributeIdentifiers as Attribute;
use \Dcp\Family\Document;

Class SimpleFile extends Document
{
    
    var $defaultview = "WORKSPACE:VIEWSIMPLEFILE:T";
    var $defaultmview = "WORKSPACE:MAILSIMPLEFILE:T";
    //var $defaultedit= "WORKSPACE:EDITSIMPLEFILE:T";
    var $specialmenu = "WORKSPACE:WS_POPUPSIMPLEFILE";
    function postStore()
    {
        $this->computeMime();
        /*
        $fi=$this->getRawValue(myAttribute::sfi_file);
        $fiold=$this->getOldValue(myAttribute::sfi_file);
        if (($fiold !== false) && ($fi != $fiold))  $this->computeThumbnail();
        */
        
        $fi = $this->getRawValue(myAttribute::sfi_inedition);
        $fiold = $this->getOldRawValue(myAttribute::sfi_inedition);
        
        if (($fi == 0) && ($fiold == 1)) {
            $err = $this->unlock(); // auto unlock in not in edition mode
            if ($err == "") {
                global $action;
                $action->AddActionDone("UNLOCKDOC", $this->id);
            }
        }
        $this->setValue(myAttribute::sfi_pages, 0); // to be recomputed
        
    }
    /**
     * use for duplicate physicaly the file
     */
    function postDuplicate(&$copyfrom)
    {
        
        $f = $this->getRawValue(myAttribute::sfi_file);
        if ($f) {
            $this->setValue(myAttribute::sfi_file, $this->copyFile(myAttribute::sfi_file));
            $this->modify();
        }
        
        $this->clearValue(myAttribute::sfi_inedition);
        $err = $this->modify();
        
        return $err;
    }
    /**
     * rename file copy after un single copy
     */
    function renameCopy()
    {
        $f = $this->getRawValue(myAttribute::sfi_file);
        if ($f) {
            if (preg_match(PREGEXPFILE, $f, $reg)) {
                $vf = newFreeVaultFile($this->dbaccess);
                $vid = $reg[2];
                if ($vf->Show($vid, $info) == "") {
                    $cible = $info->path;
                    if (file_exists($cible)) {
                        
                        $pp = strrpos($info->name, '.');
                        $base = substr($info->name, 0, $pp) . _(" (copy)") . substr($info->name, $pp);
                        $vf->Rename($vid, $base);
                        $this->refresh();
                        $this->modify();
                    }
                }
            }
        }
    }
    
    function preRefresh()
    {
        $this->computeFileSize();
        //$this->setnumberpagePDF();
        //  if ($this->getRawValue(myAttribute::sfi_thumb)=="")   $this->computeThumbnail();
        
    }
    /**
     * return the converter for thumbnail based on mime type
     * @return string empty if no converter found
     */
    function canThumbnail()
    {
        $mime = $this->getRawValue(myAttribute::sfi_mimesys);
        $mimebase = '';
        if (preg_match("|(.*)/(.*)|", $mime, $reg)) {
            $mimebase = $reg[1];
        }
        $convert = "";
        if ($mimebase == "image") {
            $convert = "convert";
        } else {
            
            switch ($mime) {
                case "text/xml":
                case "text/html":
                case "application/pdf":
                case "application/postscript":
                    $convert = "convert";
                    break;

                case "application/vnd.ms-excel":
                    $convert = "xlhtml";
                    break;

                case "application/msword--":
                    $convert = "abiword";
                    break;

                case "application/vnd.oasis.opendocument.presentation":
                case "application/vnd.oasis.opendocument.spreadsheet":
                case "application/vnd.oasis.opendocument.graphics":
                case "application/vnd.oasis.opendocument.text":
                    $convert = "unzip";
                    break;
            }
        }
        return $convert;
    }
    
    function computeThumbnail()
    {
        $f = $this->getRawValue(myAttribute::sfi_file);
        if ($f) {
            if (preg_match(PREGEXPFILE, $f, $reg)) {
                $vf = newFreeVaultFile($this->dbaccess);
                if ($vf->Show($reg[2], $info) == "") {
                    
                    $convert = $this->canThumbnail();
                    $shadow = "";
                    //	$shadow="\( +clone -background black -shadow 60x4+4+4  \)";
                    $convertcmd = "convert -thumbnail 200\\> %s[0] -crop 205x205+0+0 -mattecolor grey -frame 4x4+2+2 $shadow  +swap    -background none -mosaic -crop 225x225+0+0  %s";
                    //	$convertcmd="convert -thumbnail 200 %s[0] -crop 205x205+0+0  -mattecolor black -frame 5x5+2+2   %s";
                    switch ($convert) {
                        case "convert":
                            $pf = $info->path;
                            $cible = uniqid("/var/tmp/thumb") . ".png";
                            
                            $cmd = sprintf($convertcmd, $pf, $cible);
                            system($cmd);
                            // print_r2 ($cmd);
                            if (file_exists($cible)) {
                                $vf->Store($cible, false, $vid);
                                
                                $ft = "image/png|$vid";
                                $this->setValue(myAttribute::sfi_thumb, $ft);
                                $this->modify(true, array(
                                    myAttribute::sfi_thumb
                                ) , true);
                                unlink($cible);
                            }
                            break;

                        case "abiword":
                            $pf = $info->path;
                            $ciblepng = uniqid("/var/tmp/thumb") . ".png";
                            // $cmd = sprintf("abiword --to=pdf -o %s  %s",$ciblepdf, $pf );
                            $cmd = sprintf('abiword --print="|convert -[0] %s" %s', $ciblepng, $pf);
                            system($cmd);
                            //print ($cmd);
                            if (file_exists($ciblepng)) {
                                
                                $cible = uniqid("/var/tmp/thumb") . ".png";
                                //	    $cmd = sprintf("convert -thumbnail 200 %s[0] -crop 205x205+0+0  -mattecolor black -frame 5x5+2+2 \( +clone -background black -shadow 4x4+4+4 \) +swap   -background none -mosaic  %s",$ciblepdf, $cible);
                                $cmd = sprintf($convertcmd, $ciblepng, $cible);
                                system($cmd);
                                if (file_exists($cible)) {
                                    $vf->Store($cible, false, $vid);
                                    
                                    $ft = "image/png|$vid";
                                    $this->setValue(myAttribute::sfi_thumb, $ft);
                                    $this->modify(true, array(
                                        myAttribute::sfi_thumb
                                    ) , true);
                                    unlink($cible);
                                }
                                unlink($ciblepng);
                            }
                            break;

                        case "xlhtml":
                            $pf = $info->path;
                            $ciblepdf = uniqid("/var/tmp/thumb") . ".html";
                            
                            $cmd = sprintf("xlhtml -xp:0 -xr:0-50  %s > %s", $pf, $ciblepdf);
                            system($cmd);
                            //  print ($cmd);
                            if (file_exists($ciblepdf)) {
                                
                                $cible = uniqid("/var/tmp/thumb") . ".png";
                                
                                $cmd = sprintf($convertcmd, $ciblepdf, $cible);
                                system($cmd);
                                //	  print ($cmd);
                                if (file_exists($cible)) {
                                    $vf->Store($cible, false, $vid);
                                    
                                    $ft = "image/png|$vid";
                                    $this->setValue(myAttribute::sfi_thumb, $ft);
                                    $this->modify(true, array(
                                        myAttribute::sfi_thumb
                                    ) , true);
                                    unlink($cible);
                                }
                                unlink($ciblepdf);
                            }
                            break;

                        case "unzip":
                            $pf = $info->path;
                            $cibledir = uniqid("/var/tmp/thumb");
                            
                            $cmd = sprintf("unzip -j %s Thumbnails/thumbnail.png -d %s >/dev/null", $pf, $cibledir);
                            system($cmd);
                            //  print ($cmd);
                            $ciblepng = $cibledir . "/thumbnail.png";
                            
                            if ($ciblepng) {
                                
                                $cible = uniqid("/var/tmp/thumb") . ".png";
                                $convertcmd = "convert  %s[0]  -mattecolor  grey -frame 4x4+2+2 $shadow +swap    -background none -mosaic  %s";
                                
                                $cmd = sprintf($convertcmd, $ciblepng, $cible);
                                system($cmd);
                                // print ($cmd);
                                if (file_exists($cible)) {
                                    $vf->Store($cible, false, $vid);
                                    
                                    $ft = "image/png|$vid|thumbnail.png";
                                    $this->setValue(myAttribute::sfi_thumb, $ft);
                                    $this->modify(true, array(
                                        myAttribute::sfi_thumb
                                    ) , true);
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
    function computeMime()
    {
        static $vf;
        $f = $this->getRawValue(myAttribute::sfi_file);
        if ($f) {
            if (preg_match(PREGEXPFILE, $f, $reg)) {
                if (!$vf) $vf = newFreeVaultFile($this->dbaccess);
                if ($vf->Show($reg[2], $info) == "") {
                    include_once ("WHAT/Lib.FileMime.php");
                    
                    $this->setValue(myAttribute::sfi_mimetxt, getTextMimeFile($info->path));
                    $short = strtok($this->getRawValue(myAttribute::sfi_mimetxt) , ",");
                    if (!$short) $short = $this->getRawValue(myAttribute::sfi_mimetxt);
                    $this->setValue(myAttribute::sfi_mimetxtshort, $short);
                    $this->setValue(myAttribute::sfi_mimesys, getSysMimeFile($info->path, $info->name));
                    $this->setValue(myAttribute::sfi_title, $info->name);
                    $this->setValue(myAttribute::sfi_filesize, $info->size);
                    
                    $mime = $this->getRawValue(myAttribute::sfi_mimesys);
                    
                    $icon = getIconMimeFile($mime);
                    if ($icon) {
                        $this->setValue(myAttribute::sfi_mimeicon, $icon);
                        $this->icon = $icon;
                    } else {
                        $fdoc = $this->getFamilyDocument();
                        $this->icon = $fdoc->icon;
                    }
                }
            }
        }
    }
    /**
     * compute only file size
     */
    function computeFileSize()
    {
        static $vf;
        $f = $this->getRawValue(myAttribute::sfi_file);
        if ($f) {
            if (preg_match(PREGEXPFILE, $f, $reg)) {
                if (!$vf) $vf = newFreeVaultFile($this->dbaccess);
                if ($vf->Show($reg[2], $info) == "") {
                    include_once ("WHAT/Lib.FileMime.php");
                    $this->setValue(myAttribute::sfi_filesize, $info->size);
                }
            }
        }
    }
    /**
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @templateController
     */
    function mailsimplefile($target = "_self", $ulink = true, $abstract = false)
    {
        $this->viewsimplefile($target, $ulink, $abstract);
        $this->lay->set("moddate", strftime("%A %d %B %Y %H:%M", $this->revdate));
    }
    /**
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @templateController
     */
    function printsimplefile($target = "_self", $ulink = true, $abstract = false)
    {
        $this->viewdefaultcard($target, $ulink, $abstract);
        
        $istext = preg_match("/text/", $this->getRawValue(myAttribute::sfi_mimesys));
        $this->lay->set("isimg", false);
        $this->lay->set("istext", $istext);
        if ($istext) {
            
            $this->getTextValueFromFile("sfi_file", $text);
            
            if (preg_match("|text/html|", $this->getRawValue(myAttribute::sfi_mimesys))) {
                $this->lay->set("filecontent", $text);
            } else {
                $this->lay->set("filecontent", nl2br(htmlentities($text)));
            }
        } else {
            $isimg = preg_match("/image/", $this->getRawValue(myAttribute::sfi_mimesys));
            $this->lay->set("isimg", $isimg);
        }
    }
    /**
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @templateController
     */
    function viewsimpleprop($target = "_self", $ulink = true, $abstract = false)
    {
        $this->computeMime();
        $this->modify();
        
        $this->viewdefaultcard($target, $ulink, $abstract);
        $cdate = FrenchDateToUnixTs($this->cdate);
        if ($this->revision != 0) {
            $idoc = \Dcp\DocManager::getDocument($this->initid);
            if ($idoc !== null) {
                $cdate = FrenchDateToUnixTs($idoc->cdate);
            }
        }
        $uid = 0;
        $adate = FrenchDateToUnixTs($this->adate);
        $this->lay->set("createdate", strftime("%A %d %B %Y %H:%M", $cdate));
        $this->lay->set("accessdate", strftime("%A %d %B %Y %H:%M", $adate));
        $this->lay->set("moddate", strftime("%A %d %B %Y %H:%M", $this->revdate));
        $this->lay->set("theversion", ($this->version != "") ? $this->version : _("undefined"));
        $this->lay->set("theallocate", _("nobody"));
        if ($this->locked == - 1) $this->lay->set("thelocker", _("fixed"));
        elseif ($this->locked == 0) $this->lay->set("thelocker", _("nobody"));
        else {
            $uid = abs($this->locked);
            // $u = new User("", $uid);
            $u = new \Account("", $uid);
            if ($u->isAffected()) {
                $this->lay->set("thelocker", sprintf("%s %s", $u->firstname, $u->lastname));
            } else {
                $this->lay->set("thelocker", sprintf(_("unknow user %s") , $uid));
            }
        }
        $auid = abs($this->allocated);
        if ($uid == $auid) {
            $this->lay->set("theallocate", $this->lay->get("thelocker"));
        } else {
            if ($auid > 0) {
                $u = new \Account("", $auid);
                if ($u->isAffected()) {
                    $this->lay->set("theallocate", sprintf("%s %s", $u->firstname, $u->lastname));
                } else {
                    $this->lay->set("theallocate", sprintf(_("unknow user %s") , $uid));
                }
            }
        }
        
        $size = $this->getRawValue(myAttribute::sfi_filesize);
        if ($size < 0) $dsize = "";
        else if ($size < 1024) $dsize = sprintf(_("ws %d bytes") , $size);
        else if ($size < 1048576) $dsize = sprintf(_("%d kb") , $size / 1024);
        else $dsize = sprintf(_("%.01f Mb") , $size / 1048576);
        $this->lay->set("dsize", $dsize);
        
        $path = $this->getMainPath();
        $spath = "";
        foreach ($path as $v) {
            $spath = $v . "/" . $spath;
        }
        
        $this->lay->set("thepath", $spath);
    }
    /**
     * @param string $target
     * @param bool $ulink
     * @param bool $abstract
     * @templateController
     */
    function viewsimplefile($target = "_self", $ulink = true, $abstract = false)
    {
        global $action;
        $recomputeThumbnail = (getHttpVars("recomputethumb") == "yes");
        if ($recomputeThumbnail) $this->computeThumbnail();
        
        $this->viewdefaultcard($target, $ulink, $abstract);
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FREEDOM/Layout/fdl_tooltip.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDL/Layout/editattr.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/viewsimplefile.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDL/Layout/popupdoc.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDC/Layout/inserthtml.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/DAV/Layout/getsessionid.js");
        
        $action->parent->AddCssRef("WORKSPACE:viewsimplefile.css", true);
        
        $this->lay->set("emblem", $this->getEmblem());
        $this->lay->set("locker", "");
        $this->lay->set("todo", "");
        $uid = abs($this->locked);
        if (($uid > 0) && ($uid != $this->userid)) {
            $u = new \Account("", $uid);
            $this->lay->set("locker", sprintf(_("locked by %s %s") , $u->firstname, $u->lastname));
        }
        $todo = $this->getUTag("AFFECTED");
        if ($todo) {
            $this->lay->set("todo", nl2br($todo->comment));
        }
        $thetitle = $this->getRawValue(myAttribute::sfi_titlew);
        if ($thetitle == "") $thetitle = sprintf(_("No title"));
        $this->lay->set("thetitle", $thetitle);
        
        $size = $this->getRawValue(myAttribute::sfi_filesize);
        if ($size < 0) $dsize = "";
        else if ($size < 1024) $dsize = sprintf(_("ws %d bytes") , $size);
        else if ($size < 1048576) $dsize = sprintf(_("%d kb") , $size / 1024);
        else $dsize = sprintf(_("%.01f Mb") , $size / 1048576);
        $this->lay->set("dsize", $dsize);
        $this->lay->set("thumb", ($this->getRawValue(myAttribute::sfi_thumb) != ""));
        $this->lay->set("istext", false);
        $this->lay->set("ishtml", $this->getRawValue(myAttribute::sfi_mimesys) == "text/html");
        $this->lay->set("haspdf", (($this->getRawValue(myAttribute::sfi_pdffile) != "") && (preg_match("|application/pdf|", $this->getRawValue(myAttribute::sfi_pdffile)))));
        
        $this->lay->set("canusereader", false);
        if ($this->lay->get("haspdf")) {
            $this->lay->set("canusereader", true);
        } else if (preg_match('/^(text|image)/', $this->getRawValue(myAttribute::sfi_mimesys))) {
            $this->lay->set("canusereader", true);
        }
        if (!$this->lay->get("ishtml")) $this->lay->set("istext", preg_match('|^text/|', $this->getRawValue(myAttribute::sfi_mimesys)));
        
        $this->lay->set("canedithtml", (preg_match('|^text/|', $this->getRawValue(myAttribute::sfi_mimesys)) && ($this->getRawValue(myAttribute::sfi_inedition) != 1)));
        
        $this->lay->set("isinedition", ($this->fileIsInEdition() == MENU_ACTIVE));
        $this->lay->set("isnotinedition", ($this->fileIsNotInEdition() == MENU_ACTIVE));
        $this->lay->set("canedit", ($this->canEdit() == ""));
        $this->lay->set("canversionned", ($this->canVersionned() == MENU_ACTIVE));
        //$this->lay->set("ishtml",ereg("html|plain",$this->getRawValue(myAttribute::sfi_mimesys)));
        $this->lay->set("isinline", preg_match("=html|image|plain|text/xml=", $this->getRawValue(myAttribute::sfi_mimesys)));
        $this->lay->set("ETITLE", str_replace("'", "\'", $this->title));
        
        $this->lay->set("thumbrecompute", $this->canThumbnail());
        $this->lay->set("DAV", getParam("FREEDAV_SERVEUR") != "");
        
        $h = $this->getHisto(true);
        $parti = array();
        foreach ($h as $v) {
            if (($v["code"] == "MODATTR") || ($v["code"] == "MODIFY") || ($v["code"] == "CREATE")) $parti[$v["uname"]] = $v["uname"];
        }
        
        $this->lay->set("noversiontext", ($this->locked == - 1) ? _("undefined version") : _("current version"));
        $this->lay->set("participate", implode(", ", $parti));
        $this->lay->set("thestate", $this->getState());
        $this->lay->set("stateid", ($this->state) ? $this->state : false);
        $this->lay->set("viewabstract", (($this->getRawValue(myAttribute::sfi_description) != "") && ($this->getRawValue(myAttribute::sfi_thumb) == "")));
        
        $dstate = \Dcp\DocManager::getDocument($this->state);
        $this->lay->set("thestatedesc", ($dstate !== null ? nl2br($dstate->getRawValue(Attribute\Freestate::frst_desc)) : ''));
        $fvalue = $this->getRawValue(myAttribute::sfi_file);
        
        if (preg_match(PREGEXPFILE, $fvalue, $reg)) {
            $vaultid = $reg[2];
            $this->lay->set("vaultid", $vaultid);
        }
    }
    /**
     * @templateController
     */
    function createtext()
    {
        global $action;
        /**
         * @var \NormalAttribute $a
         */
        $a = $this->getAttribute(myAttribute::sfi_titlew);
        $a->needed = "Y";
        
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/ckeditor/ckeditor.js");
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/FDL/Layout/edithtmltext.js");
        $this->editattr();
    }
    
    function postCreated()
    {
        // convert html to file
        $err = '';
        $html = getHttpVars("wscreatefile");
        
        if (($this->getRawValue(myAttribute::sfi_file) == "") && $html) {
            $err = $this->SetTextValueInFile(myAttribute::sfi_file, $html, $this->getRawValue(myAttribute::sfi_titlew) . ".html");
            if ($err == "") $err = $this->modify();
        }
        return $err;
    }
    /**
     * menu state to view upload file menu
     *  test if file is already downloaded to be changed
     */
    function fileIsInEdition()
    {
        if ($this->CanEdit() != "") return MENU_INVISIBLE;
        
        if ($this->getRawValue(myAttribute::sfi_inedition) == 1) return MENU_ACTIVE;
        else return MENU_INVISIBLE;
    }
    /**
     * inverse of ::fileIsInEdition()
     */
    function fileIsNotInEdition()
    {
        if ($this->CanEdit() != "") return MENU_INVISIBLE;
        if ($this->fileIsInEdition() == MENU_INVISIBLE) return MENU_ACTIVE;
        return MENU_INVISIBLE;
    }
    /**
     * menu state to view add version menu
     */
    function canVersionned()
    {
        if ($this->CanEdit() != "") return MENU_INACTIVE;
        if ($this->getRawValue(myAttribute::sfi_version) == "") return MENU_INVISIBLE;
        if ($this->getRawValue(myAttribute::sfi_inedition) == 1) return MENU_INACTIVE;
        return MENU_ACTIVE;
    }
    /**
     * @templateController
     */
    function editupload()
    {
        global $action;
        $action->parent->AddJsRef($action->GetParam("CORE_PUBURL") . "/WORKSPACE/Layout/editupload.js");
        $this->viewprop();
        $this->editattr();
    }
    /**
     * @templateController
     */
    function editversion()
    {
        $this->editattr();
    }
    static public function getNumPagesInPDF($file)
    {
        if (file_exists($file)) {
            //open the file for reading
            return trim(`grep -c "/Type[[:space:]]*/Page\>" $file`);
            /*if ($handle = @fopen($file, "rb")) {
                $count = 0;
                $i = 0;
                while (!feof($handle)) {
                    if ($i > 0) {
                        $contents = fread($handle, 8152);
                        if (preg_match("/\/Count\s+([0-9]+)(\n|\s)*>>/m", $contents, $found)) {
                            fclose($handle);
                            return $found[1];
                        }
                    } else {
                        $contents = fread($handle, 1000);
                        //In some pdf files, there is an N tag containing the number of
                        //of pages. This doesn't seem to be a result of the PDF version.
                        //Saves reading the whole file.
                        if (preg_match("/\/Count\s+([0-9]+)(\n|\s)*>>/m", $contents, $found)) {
                            fclose($handle);
                            return $found[1];
                        }
                        if (preg_match("/\/N\s+([0-9]+)/", $contents, $found)) {
                            fclose($handle);
                            return $found[1];
                        }
                    }
                    $i++;
                }
                fclose($handle);
            /*
                 //get all the trees with 'pages' and 'count'. the biggest number
                 //is the total number of pages, if we couldn't find the /N switch above.                
                 if(preg_match_all("/\/Type\s*\/Pages\s*.*\s*\/Count\s+([0-9]+)/", $contents, $capture, PREG_SET_ORDER)) {
                     foreach($capture as $c) {
                         if($c[1] > $count)
                             $count = $c[1];
                     }
                     return $count;            
                 }
            
            }*/
        }
        return 0;
    }
    
    function hasPDF()
    {
        return (($this->getRawValue(myAttribute::sfi_pdffile) != "") && (preg_match("|application/pdf|", $this->getRawValue(myAttribute::sfi_pdffile))));
    }
    function setnumberpagePDF()
    {
        if ($this->hasPDF() && (!$this->getRawValue(myAttribute::sfi_pages))) {
            $pdffile = $this->getRawValue(myAttribute::sfi_pdffile);
            if (preg_match(PREGEXPFILE, $pdffile, $reg)) {
                $vf = newFreeVaultFile($this->dbaccess);
                if ($vf->Show($reg[2], $info) == "") {
                    return $this->getNumPagesInPDF($info->path);
                }
            }
        }
        return "";
    }
}
