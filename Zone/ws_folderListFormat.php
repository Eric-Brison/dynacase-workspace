<?php
/**
 * Display doucment explorer
 *
 * @author Anakeen 2006
 * @version $Id: ws_navigate.php,v 1.12 2007/07/30 16:03:37 eric Exp $
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FREEDOM
 * @subpackage
 */
/**
 */





/**
 * View folders and document for exchange them
 * @param Action &$action current action
 */




class wsFolderListFormat {
    
    static public function getIcon(Doc &$doc, DocCollection &$dir=null) {
        $icon=$doc->getIcon('',20);
        $link='';
        if (($dir->doctype == 'D') && ($doc->prelid != $dir->initid)) {
            global $action;
            $link=sprintf('<img class="ilink" src="%s">', $action->getImageUrl('link.gif'));
        }
        return sprintf('<img class="icon" src="%s">%s', $icon, $link);
       
    }
    static public function getMDate(Doc &$doc) {
        return strftime("%d %b %Y %H:%M",$doc->revdate);
       
    }
    static public function getFileSize(Doc &$doc) {
        $size=$doc->getValue("sfi_filesize",-1);
            if ($size < 0) $dsize="";
            else if ($size < 1024) $dsize=_("<1 kb");
            else if ($size < 1048576) $dsize=sprintf(_("%d kb"),$size/1024);
            else $dsize=sprintf(_("%.01f Mb"),$size/1048576);
        return $dsize;
    }
    static public function getFileMime(Doc &$doc) {
        return $doc->getValue("sfi_mimetxtshort");
       
    }
    
}
?>